<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\TelegramService;
use App\Services\WingPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function __construct(
        private readonly TelegramService $telegramService,
        private readonly WingPayService $wingPayService
    ) {
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Payment::with('installment.customer', 'paymentMethod', 'user');



        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('installment.customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('paymentMethod', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('amount', 'like', "%{$search}%")
                ->orWhere('payment_date', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest('id')->paginate(10);
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        
        return view('payments.index', compact('payments', 'exchangeRate'));
    }

    public function create()
    {
        $user = auth()->user();
        $installments = Installment::has('customer')
            ->has('product')
            ->with('customer', 'product', 'payments')
            ->where('status', 'active')
            ->get();
            
        $paymentMethods = $this->getPaymentMethods();
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        // Resolve default payment method ID from settings
        $defaultPmKey = \App\Models\Setting::where('key', 'default_payment_method')->value('value');
        $defaultMethodName = match($defaultPmKey) {
            'pm_cash'    => 'Cash',
            'pm_card'    => 'Credit Card',
            'qr_aba'     => 'ABA Bank',
            'qr_acleda'  => 'ACLEDA Bank',
            'qr_wing'    => 'Wing Bank',
            'qr_truemoney' => 'TrueMoney',
            default      => null,
        };
        $defaultPaymentMethodId = $defaultMethodName
            ? ($paymentMethods->firstWhere('name', $defaultMethodName)?->id ?? $paymentMethods->first()?->id)
            : $paymentMethods->first()?->id;

        return view('payments.create', compact('installments', 'paymentMethods', 'exchangeRate', 'defaultPaymentMethodId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'installment_id' => 'required|exists:installments,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'penalty_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'qr_image' => 'nullable|image',
            'card_holder_name' => 'nullable|string|max:255',
            'card_number' => 'nullable|string|max:4',
            'card_brand' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $installment = Installment::findOrFail($request->installment_id);
        
        if ($installment->remaining_balance <= 0) {
            return back()->withErrors(['amount' => 'This installment plan is already fully paid off.'])->withInput();
        }

        if ((float) $request->amount > (float) $installment->remaining_balance + 0.009) {
            return back()->withErrors(['amount' => 'Payment amount ($' . number_format($request->amount, 2) . ') cannot exceed remaining balance ($' . number_format($installment->remaining_balance, 2) . ').'])->withInput();
        }

        $qrPath = null;
        if ($request->hasFile('qr_image')) {
            $qrPath = $request->file('qr_image')->store('qr_images', 'public');
        }

        $tempPayment = new Payment(['payment_method_id' => $request->payment_method_id]);
        $approveNow = $request->has('approve_now') && Gate::allows('approve-payment', $tempPayment);

        $method = PaymentMethod::find($request->payment_method_id);
        $isCreditCard = $method && strtolower(str_replace(' ', '_', $method->name)) === 'credit_card';

        $title = $request->notes ? trim($request->notes) : null;

        $feeRate = 0;
        if ($isCreditCard) {
            $cardHolder = $request->card_holder_name ? trim($request->card_holder_name) : 'N/A';
            $cardNumber = $request->card_number ? trim($request->card_number) : 'N/A';
            $cardBrand = $request->card_brand ? trim($request->card_brand) : 'N/A';
            
            $feePercentage = (float) (\App\Models\Setting::where('key', 'card_processing_fee')->value('value') ?? 2);
            $feeRate = $feePercentage / 100;
            $principal = (float) $request->amount + (float) ($request->penalty_amount ?? 0);
            $fee = $principal * $feeRate;
            $total = $principal + $fee;
            
            $formattedFee = number_format($fee, 2);
            $formattedTotal = number_format($total, 2);

            $cardTitle = "Cardholder: {$cardHolder} | Card: {$cardBrand} ****{$cardNumber} | Fee: \${$formattedFee} | Total: \${$formattedTotal}";
            $title = $title ? "{$title} | {$cardTitle}" : $cardTitle;
        }

        $payment = Payment::create([
            'installment_id' => $request->installment_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'penalty_amount' => $request->penalty_amount ?? 0,
            'payment_date' => $request->payment_date,
            'qr_image' => $qrPath,
            'status' => 'pending',
            'approved_by' => null,
            'title' => $title,
        ]);

        if ($isCreditCard && $this->wingPayService->isConfigured()) {
            $checkoutUrl = $this->wingPayService->createCheckoutSession([
                'payment_id' => $payment->id,
                'amount' => ($payment->amount + $payment->penalty_amount) * (1 + $feeRate),
                'currency' => 'USD',
                'installment_id' => $payment->installment_id,
            ]);

            if ($checkoutUrl) {
                return redirect()->away($checkoutUrl);
            }
        }

        if ($approveNow) {
            $this->approvePaymentRecord($payment, auth()->id());
        } else {
            \App\Models\Notification::createSystemNotification(
                'payment', 'New Payment Submitted',
                'A new payment of $' . number_format($payment->amount, 2) . ' is pending approval for customer ' . $payment->installment->customer->name,
                'dollar-sign', 'blue',
                route('payments.index')
            );
        }

        $successMsg = ($approveNow) ? 'Payment recorded and approved successfully.' : 'Payment submitted successfully.';

        if ($request->has('save_and_print')) {
            $payment->load('invoice');
            if ($payment->invoice) {
                return redirect()->route('invoices.print', $payment->invoice->id)->with('success', $successMsg);
            } else {
                return redirect()->route('payments.show', $payment->id)->with('success', $successMsg)->with('auto_print', true);
            }
        }

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', $successMsg);
        }

        return redirect()->route('payments.index')->with('success', $successMsg);
    }

    public function approve(Payment $payment)
    {
        Gate::authorize('approve-payment', $payment);

        $this->approvePaymentRecord($payment, auth()->id());

        return redirect()->route('payments.index')->with('success', 'Payment approved and Telegram message sent.');
    }

    /**
     * Handle browser redirect from Wing Pay.
     */
    public function wingReturn(Request $request)
    {
        \Log::info('Wing Pay Return received', $request->all());

        if (empty($request->order_id)) {
            return redirect()->route('payments.index')->with('error', 'Wing Pay return details missing.');
        }

        // Extract payment ID from order_id (e.g. WING-INV-12-1678234)
        if (preg_match('/WING-INV-(\d+)-/', $request->order_id, $matches)) {
            $paymentId = $matches[1];
            $payment = Payment::find($paymentId);
            
            if ($payment) {
                if ($request->status === 'success') {
                    $this->approvePaymentRecord($payment, null);
                    return redirect()->route('payments.index')->with('success', 'Wing Pay transaction completed and approved.');
                } else {
                    $payment->update(['status' => 'rejected']);
                    return redirect()->route('payments.index')->with('error', 'Wing Pay transaction cancelled or failed.');
                }
            }
        }

        return redirect()->route('payments.index')->with('error', 'Wing Pay transaction could not be processed.');
    }

    /**
     * Handle server-to-server webhook callback from Wing Bank.
     */
    public function wingCallback(Request $request)
    {
        \Log::info('Wing Pay Webhook received', $request->all());

        if (!$this->wingPayService->verifyCallback($request->all())) {
            \Log::warning('Wing Pay webhook signature verification failed');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if (preg_match('/WING-INV-(\d+)-/', $request->order_id, $matches)) {
            $paymentId = $matches[1];
            $payment = Payment::find($paymentId);

            if ($payment) {
                if ($request->status === 'success') {
                    $this->approvePaymentRecord($payment, null);
                    return response()->json(['status' => 'success']);
                } else {
                    $payment->update(['status' => 'rejected']);
                    return response()->json(['status' => 'rejected']);
                }
            }
        }

        return response()->json(['message' => 'Order not found'], 404);
    }

    /**
     * Private helper to encapsulate payment approval workflows.
     */
    private function approvePaymentRecord(Payment $payment, ?int $userId = null): void
    {
        if ($payment->status === 'approved') {
            return;
        }

        $payment->update([
            'status' => 'approved',
            'approved_by' => $userId,
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
        $khmerPenalty = $payment->penalty_amount > 0 ? number_format($payment->penalty_amount, 2) : null;
        $totalPaidText = $khmerPenalty ? "*\${$khmerAmount}* (រួមទាំងប្រាក់ពិន័យ *\${$khmerPenalty}*)" : "*\${$khmerAmount}*";
        $khmerRemaining = number_format($installment->remaining_balance, 2);
        $downloadLink = route('public.invoices.download', $invoice->id);

        $message = "🙏 *សូមអរគុណ!*\n"
            . "ការបង់ប្រាក់ចំនួន {$totalPaidText} {$paymentType} ត្រូវបានអនុម័តជោគជ័យ។\n"
            . "• តុល្យភាពប្រាក់នៅសល់គឺ៖ *\${$khmerRemaining}*\n"
            . "• ទាញយកវិក្កយបត្រ PDF ទីនេះ៖ [ទាញយកវិក្កយបត្រ]({$downloadLink})";

        $this->telegramService->sendToCustomer($installment->customer_id, $message);

        \App\Models\Notification::createSystemNotification(
            'payment', 'Payment Approved',
            'Payment of $' . number_format($payment->amount, 2) . ' from ' . $installment->customer->name . ' has been approved.',
            'dollar-sign', 'green',
            route('payments.index')
        );
    }

    private function toKhmerNumerals($num): string
    {
        $khmerDigits = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];
        return strtr((string)$num, $khmerDigits);
    }

    public function reject(Payment $payment)
    {
        Gate::authorize('approve-payment', $payment);
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

        \App\Models\Notification::createSystemNotification(
            'payment', 'Payment Rejected',
            'Payment of $' . number_format($payment->amount, 2) . ' from ' . $installment->customer->name . ' has been rejected.',
            'x-circle', 'red',
            route('payments.index')
        );

        return redirect()->route('payments.index')->with('success', $flashMessage);
    }

    public function destroy(Payment $payment)
    {
        Gate::authorize('delete-payment');

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

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('installment.customer', 'paymentMethod', 'user', 'invoice');
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        return view('payments.show', compact('payment', 'exchangeRate'));
    }

    private function getPaymentMethods()
    {
        $defaults = [
            ['name' => 'Cash', 'details' => 'សាច់ប្រាក់ - Cash payment.'],
            ['name' => 'ABA Bank', 'details' => 'ធនាគារ ABA - ABA Pay / Mobile Banking.'],
            ['name' => 'ACLEDA Bank', 'details' => 'ធនាគារ អេស៊ីលីដា - ACLEDA Mobile.'],
            ['name' => 'Wing Bank', 'details' => 'ធនាគារ វីង - Wing Bank / Wing Money.'],
            ['name' => 'TrueMoney', 'details' => 'ទ្រូម៉ានី - TrueMoney Agent / App.'],
            ['name' => 'Credit Card', 'details' => 'កាតឥណទាន - Credit/Debit Card payment.'],
        ];

        foreach ($defaults as $method) {
            PaymentMethod::firstOrCreate(
                ['name' => $method['name']],
                ['details' => $method['details']]
            );
        }

        // Clean up unused QR Code and Bank Transfer generic methods if no payments use them
        $genericNames = ['QR Code', 'Bank Transfer', 'QR'];
        $generics = PaymentMethod::whereIn('name', $genericNames)->get();
        foreach ($generics as $gm) {
            $hasPayments = \App\Models\Payment::where('payment_method_id', $gm->id)->exists();
            if (!$hasPayments) {
                $gm->delete();
            }
        }

        return PaymentMethod::getAvailable();
    }

    public function restore($id)
    {
        Gate::authorize('delete-payment');
        $payment = Payment::onlyTrashed()->findOrFail($id);
        $payment->restore();

        // Adjust installment remaining balance and status
        $installment = $payment->installment;
        if ($installment && $payment->status === 'approved') {
            $installment->remaining_balance = max($installment->remaining_balance - $payment->amount, 0);
            if ($installment->remaining_balance <= 0) {
                $installment->status = 'completed';
            }
            $installment->save();

            // Recalculate due date
            $schedule = $installment->getPaymentSchedule();
            $nextUnpaidRow = collect($schedule)->first(fn($row) => $row['status'] !== 'paid');
            if ($nextUnpaidRow) {
                $installment->next_due_date = $nextUnpaidRow['due_date']->toDateString();
            } else {
                $installment->next_due_date = null;
            }
            $installment->save();
        }

        return redirect()->route('customers.trash', ['tab' => 'payments'])->with('success', 'Payment restored successfully.');
    }

    public function forceDelete($id)
    {
        Gate::authorize('delete-payment');
        $payment = Payment::onlyTrashed()->findOrFail($id);

        // Delete invoice and QR image permanently
        $payment->invoice()?->delete();
        if ($payment->qr_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($payment->qr_image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($payment->qr_image);
        }

        $payment->forceDelete();
        return redirect()->route('customers.trash', ['tab' => 'payments'])->with('success', 'Payment permanently deleted.');
    }
}
