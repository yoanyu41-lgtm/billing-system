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
    public function index()
    {
        $user = auth()->user();
        $query = Installment::has('customer')->has('product')->with('customer', 'product')
            ->where('remaining_balance', '>', 0)
            ->where('status', 'active');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->where('created_by', $user->id);
        }

        $allActive = $query->get();

        $lateInstallments = $allActive->filter(function ($installment) {
            return $installment->daysLate() > 0;
        })->values();

        return view('late-payments.index', compact('lateInstallments'));
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
