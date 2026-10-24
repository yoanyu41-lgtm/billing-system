<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LatePaymentController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Installment::has('customer')->has('product')->with('customer', 'product')
            ->where('remaining_balance', '>', 0)
            ->where('status', 'active');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('id_card', 'like', "%{$search}%");
                })->orWhereHas('product', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $allActive = $query->get();

        $lateInstallments = $allActive->filter(function ($installment) use ($request) {
            $days = $installment->daysLate();
            if ($days <= 0) return false;

            if ($request->filled('days_range')) {
                if ($request->days_range === '1-7' && ($days < 1 || $days > 7)) return false;
                if ($request->days_range === '8-15' && ($days < 8 || $days > 15)) return false;
                if ($request->days_range === '16+' && $days < 16) return false;
            }

            return true;
        })->values();

        $products = \App\Models\Product::orderBy('name')->get();
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return view('late-payments.index', compact('lateInstallments', 'products', 'settings'));
    }

    public function applyFee(Request $request, Installment $installment)
    {
        $validated = $request->validate([
            'late_fee_method' => 'required|in:fixed,percentage',
            'days_late' => 'required|integer|min:0',
            'rate_or_amount' => 'required|numeric|min:0',
            'late_fee' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        // Automatically store as note or prepare for payment collection
        return redirect()->route('payments.create', [
            'installment_id' => $installment->id,
            'penalty_amount' => $validated['late_fee'],
            'note' => $validated['note'] ?? ("ប្រាក់ពិន័យបង់យឺត " . $validated['days_late'] . " ថ្ងៃ"),
        ])->with('success', app()->getLocale() === 'km' 
            ? 'បានគណនាប្រាក់ពិន័យ $' . number_format($validated['late_fee'], 2) . ' រួចរាល់! សូមទទួលការទូទាត់។' 
            : 'Late fee $' . number_format($validated['late_fee'], 2) . ' applied! Proceed to collect payment.');
    }

    public function sendReminder(Request $request, Installment $installment)
    {
        $installment->loadMissing('customer', 'product');

        $daysLate = $installment->daysLate();
        $penalty = $installment->calculatePenalty();
        $remaining = number_format($installment->remaining_balance, 2);
        $monthlyPayment = number_format($installment->monthly_payment, 2);
        $customerName = $installment->customer?->name ?? 'អតិថិជន';
        $productName = $installment->product?->name ?? 'ទំនិញ';

        if ($request->filled('message')) {
            $message = trim($request->input('message'));
        } else {
            $message = "⏰ *សេចក្តីជូនដំណឹងអំពីការទូទាត់ប្រាក់យឺតយ៉ាវ*\n\n"
                . "សូមជម្រាបជូនអតិថិជន៖ *{$customerName}*\n"
                . "• ទំនិញ៖ *{$productName}*\n"
                . "• ទឹកប្រាក់ប្រចាំខែ៖ *\${$monthlyPayment}*\n"
                . "• ទឹកប្រាក់នៅសល់សរុប៖ *\${$remaining}*\n"
                . ($daysLate > 0 ? "• ចំនួនថ្ងៃយឺតយ៉ាវ៖ *{$daysLate} ថ្ងៃ*\n" : "")
                . ($penalty > 0 ? "• ប្រាក់ពិន័យយឺតយ៉ាវ៖ *\${$penalty}*\n" : "")
                . "\nសូមអតិថិជនមេត្តាធ្វើការទូទាត់ប្រាក់ឱ្យបានឆាប់រហ័ស។ ប្រសិនបើលោកអ្នកបានទូទាត់រួចរាល់ សូមមេត្តាទាក់ទងមកកាន់ហាងដើម្បីផ្ទៀងផ្ទាត់។ សូមអរគុណ! 🙏";
        }

        $telegramResult = $this->telegramService->sendToCustomer($installment->customer_id, $message);

        if ($telegramResult['ok']) {
            $installment->update(['last_reminder_sent_at' => now()]);
            $flashMessage = app()->getLocale() === 'km' 
                ? 'បានផ្ញើសាររំលឹកជាភាសាខ្មែរតាម Telegram ដោយជោគជ័យ។' 
                : 'Reminder sent in Khmer via Telegram.';
            return redirect()->back()->with('success', $flashMessage);
        }

        $reason = $telegramResult['reason'] ?? 'Unknown error';
        $flashMessage = app()->getLocale() === 'km'
            ? 'មិនអាចផ្ញើសារតាម Telegram បានទេ៖ ' . $reason
            : 'Reminder was not sent: ' . $reason;

        return redirect()->back()->with('error', $flashMessage);
    }

    public function sendDueDateReminders()
    {
        $today = Carbon::today()->toDateString();

        $installments = Installment::has('customer')->with('customer', 'product')
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '<=', $today)
            ->get();

        $sent = 0;

        foreach ($installments as $installment) {
            $monthlyPayment = number_format($installment->monthly_payment, 2);
            $remaining = number_format($installment->remaining_balance, 2);
            $dueDate = $installment->next_due_date ? Carbon::parse($installment->next_due_date)->format('d/m/Y') : date('d/m/Y');
            $customerName = $installment->customer?->name ?? 'អតិថិជន';
            $productName = $installment->product?->name ?? 'ទំនិញ';

            $message = "⏰ *សេចក្តីជូនដំណឹងអំពីថ្ងៃកំណត់បង់ប្រាក់*\n\n"
                . "សូមជម្រាបជូនអតិថិជន៖ *{$customerName}*\n"
                . "• ទំនិញ៖ *{$productName}*\n"
                . "• ថ្ងៃកំណត់បង់ប្រាក់៖ *{$dueDate}*\n"
                . "• ទឹកប្រាក់ត្រូវបង់៖ *\${$monthlyPayment}*\n"
                . "• ទឹកប្រាក់នៅសល់សរុប៖ *\${$remaining}*\n"
                . "\nសូមលោកអ្នកមេត្តាធ្វើការទូទាត់ប្រាក់ឱ្យបានទាន់ពេលវេលា ដើម្បីជៀសវាងការផាកពិន័យ។ សូមអរគុណ! 🙏";

            $result = $this->telegramService->sendToCustomer($installment->customer_id, $message);

            if ($result['ok']) {
                $installment->update([
                    'last_reminder_sent_at' => now(),
                    'next_due_date' => Carbon::parse($installment->next_due_date)->addMonth()->toDateString(),
                ]);
                $sent++;
            }
        }

        $status = $sent > 0 ? 'success' : 'error';
        if (app()->getLocale() === 'km') {
            $message = $sent > 0 
                ? "បានផ្ញើសាររំលឹកជាភាសាខ្មែរចំនួន {$sent} នាក់រួចរាល់។" 
                : 'ពុំមានអតិថិជនត្រូវផ្ញើសាររំលឹកនៅថ្ងៃនេះទេ។';
        } else {
            $message = $sent > 0 ? "{$sent} due reminders sent in Khmer." : 'No due reminders sent.';
        }

        return redirect()->back()->with($status, $message);
    }
}
