<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Payment::with('installment.customer', 'paymentMethod', 'user');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $query->whereHas('installment', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(10);
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        return view('payments.index', compact('payments', 'exchangeRate'));
    }

    public function create()
    {
        $user = auth()->user();
        $installments = Installment::with('customer', 'product');

        if (!in_array($user->role, ['admin', 'staff'])) {
            $installments->where('created_by', $user->id);
        }

        $installments = $installments->where('status', 'active')->get();
        $paymentMethods = $this->getPaymentMethods();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        return view('payments.create', compact('installments', 'paymentMethods', 'exchangeRate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'installment_id' => 'required|exists:installments,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'qr_image' => 'nullable|image',
        ]);

        $qrPath = null;

        if ($request->hasFile('qr_image')) {
            $qrPath = $request->file('qr_image')->store('qr_images', 'public');
        }

        $approveNow = $request->has('approve_now') && Gate::allows('approve-payment');

        $payment = Payment::create([
            'installment_id' => $request->installment_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'qr_image' => $qrPath,
            'status' => $approveNow ? 'approved' : 'pending',
            'approved_by' => $approveNow ? auth()->id() : null,
        ]);

        if ($approveNow) {
            $installment = $payment->installment;
            $installment->remaining_balance -= $payment->amount;
            $installment->save();

            Invoice::create([
                'payment_id' => $payment->id,
                'invoice_number' => 'INV-' . $payment->id,
            ]);

            $message = "✅ Payment approved\n"
                . "Customer: {$installment->customer->name}\n"
                . "Amount: $" . number_format($payment->amount, 2) . "\n"
                . "Method: " . ($payment->paymentMethod->name ?? 'System') . "\n"
                . "Remaining: $" . number_format($installment->remaining_balance, 2);

            $this->telegramService->sendToCustomer($installment->customer_id, $message);
        }

        $successMsg = $approveNow ? 'Payment recorded and approved successfully.' : 'Payment submitted successfully.';

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', $successMsg);
        }

        return redirect()->route('payments.index')->with('success', $successMsg);
    }

    public function approve(Payment $payment)
    {
        Gate::authorize('approve-payment');

        $payment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        $installment = $payment->installment;
        $installment->remaining_balance -= $payment->amount;
        
        if ($installment->remaining_balance <= 0) {
            $installment->status = 'completed';
        }

        $installment->save();

        $invoice = Invoice::create([
            'payment_id' => $payment->id,
            'invoice_number' => 'INV-' . $payment->id,
        ]);

        // Dynamically find and update next unpaid due date
        $schedule = $installment->getPaymentSchedule();
        $nextUnpaidRow = collect($schedule)->first(fn($row) => $row['status'] !== 'paid');
        if ($nextUnpaidRow) {
            $installment->next_due_date = $nextUnpaidRow['due_date']->toDateString();
        } else {
            $installment->next_due_date = null; // Paid off completely
        }
        $installment->save();

        $paymentCount = $installment->payments()->where('status', 'approved')->count();
        $khmerMonth = $this->toKhmerNumerals($paymentCount);
        $paymentType = $payment->is_settlement ? 'សម្រាប់ការទូទាត់ផ្តាច់ (Payoff)' : "សម្រាប់ខែទី {$khmerMonth}";
        
        $khmerAmount = number_format($payment->amount, 2);
        $khmerRemaining = number_format($installment->remaining_balance, 2);
        $downloadLink = route('public.invoices.download', $invoice->id);

        $message = "🙏 *សូមអរគុណ!*\n"
            . "ការបង់ប្រាក់ចំនួន *\${$khmerAmount}* {$paymentType} ត្រូវបានអនុម័តជោគជ័យ។\n"
            . "• តុល្យភាពប្រាក់នៅសល់គឺ៖ *\${$khmerRemaining}*\n"
            . "• ទាញយកវិក្កយបត្រ PDF ទីនេះ៖ [ទាញយកវិក្កយបត្រ]({$downloadLink})";

        $telegramResult = $this->telegramService->sendToCustomer($installment->customer_id, $message);

        $flashMessage = $telegramResult['ok']
            ? 'Payment approved and Telegram message sent.'
            : 'Payment approved. Telegram notice: ' . $telegramResult['reason'];

        return redirect()->route('payments.index')->with('success', $flashMessage);
    }

    private function toKhmerNumerals($num): string
    {
        $khmerDigits = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];
        return strtr((string)$num, $khmerDigits);
    }

    public function reject(Payment $payment)
    {
        Gate::authorize('approve-payment');
        $payment->update(['status' => 'rejected']);

        $installment = $payment->installment;
        $customerName = $installment->customer->name;
        $productName = $installment->product->name;
        $amount = number_format($payment->amount, 2);

        $message = "❌ *ការបង់ប្រាក់ត្រូវបានបដិសេធ!*\n\n"
            . "សូមជម្រាបជូនអតិថិជន *{$customerName}*៖\n"
            . "ការទូទាត់ប្រាក់ចំនួន *\${$amount}* សម្រាប់គម្រោងបង់រំលស់៖ *{$productName}* ត្រូវបានបដិសេធ (មិនមានការអនុម័ត)។\n"
            . "សូមលោកអ្នកទាក់ទងមកកាន់ហាង ឬពិនិត្យផ្ទៀងផ្ទាត់ឡើងវិញ រួចផ្ញើបង្កាន់ដៃបង់ប្រាក់ម្តងទៀត។ សូមអរគុណ! 🙏";

        $telegramResult = $this->telegramService->sendToCustomer($installment->customer_id, $message);

        $flashMessage = $telegramResult['ok']
            ? 'Payment rejected and Telegram notification sent.'
            : 'Payment rejected. Telegram notice: ' . $telegramResult['reason'];

        return redirect()->route('payments.index')->with('success', $flashMessage);
    }

    public function destroy(Payment $payment)
    {
        Gate::authorize('approve-payment');

        $installment = $payment->installment;

        if ($payment->status === 'approved') {
            $installment->remaining_balance += $payment->amount;
            
            if ($installment->status === 'completed') {
                $installment->status = 'active';
            }
            
            $installment->save();

            // Recalculate and reset next unpaid due date
            $schedule = $installment->getPaymentSchedule();
            $nextUnpaidRow = collect($schedule)->first(fn($row) => $row['status'] !== 'paid');
            if ($nextUnpaidRow) {
                $installment->next_due_date = $nextUnpaidRow['due_date']->toDateString();
            } else {
                $installment->next_due_date = null;
            }
            $installment->save();
        }

        // Delete associated invoice if exists
        $payment->invoice()?->delete();

        // Delete payment QR code image from storage if exists
        if ($payment->qr_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($payment->qr_image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($payment->qr_image);
        }

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('installment.customer', 'paymentMethod', 'user');
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        return view('payments.show', compact('payment', 'exchangeRate'));
    }

    private function getPaymentMethods()
    {
        $defaults = [
            ['name' => 'Cash', 'details' => 'សាច់ប្រាក់ - Cash payment.'],
            ['name' => 'QR Code', 'details' => 'QR Code - Scan to pay.'],
            ['name' => 'Credit Card', 'details' => 'កាតឥណទាន - Credit/Debit Card payment.'],
        ];

        // Rename legacy name 'QR' to 'QR Code' if it exists in DB
        PaymentMethod::where('name', 'QR')->update(['name' => 'QR Code']);

        foreach ($defaults as $method) {
            PaymentMethod::firstOrCreate(
                ['name' => $method['name']],
                ['details' => $method['details']]
            );
        }

        // Clean up unused other payment methods
        $defaultNames = ['Cash', 'QR Code', 'Credit Card'];
        $unused = PaymentMethod::whereNotIn('name', $defaultNames)->get();
        foreach ($unused as $method) {
            $hasPayments = Payment::where('payment_method_id', $method->id)->exists();
            if (!$hasPayments) {
                $method->delete();
            }
        }

        return PaymentMethod::orderBy('name')->get();
    }
}
