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

        $telegramResult = $this->telegramService->sendToCustomer($installment->customer_id, $message);

        $flashMessage = $telegramResult['ok']
            ? 'Payment approved and Telegram message sent.'
            : 'Payment approved. Telegram notice: ' . $telegramResult['reason'];

        return redirect()->route('payments.index')->with('success', $flashMessage);
    }

    public function reject(Payment $payment)
    {
        Gate::authorize('approve-payment');
        $payment->update(['status' => 'rejected']);
        return redirect()->route('payments.index')->with('success', 'Payment rejected.');
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
