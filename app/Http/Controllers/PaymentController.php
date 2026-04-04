<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Payment::with('installment.customer', 'paymentMethod', 'user');

        if ($user->role === 'user') {
            $query->whereHas('installment', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(10);
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $user = auth()->user();
        $installments = Installment::with('customer', 'product');

        if ($user->role === 'user') {
            $installments->where('created_by', $user->id);
        }

        $installments = $installments->where('status', 'active')->get();
        $paymentMethods = $this->getPaymentMethods();

        return view('payments.create', compact('installments', 'paymentMethods'));
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

        Payment::create([
            'installment_id' => $request->installment_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'qr_image' => $qrPath,
            'status' => 'pending',
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment submitted successfully.');
    }

    public function approve(Payment $payment)
    {
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
        $payment->update(['status' => 'rejected']);
        return redirect()->route('payments.index')->with('success', 'Payment rejected.');
    }

    public function show(Payment $payment)
    {
        $payment->load('installment.customer', 'paymentMethod', 'user');

        return view('payments.show', compact('payment'));
    }

    private function getPaymentMethods()
    {
        $defaults = [
            ['name' => 'QR', 'details' => 'QR code payment option for fast scanning.'],
            ['name' => 'Credit Card', 'details' => 'Standard card payment via Visa or Mastercard.'],
            ['name' => 'Other Third Party', 'details' => 'External payment gateway or partner service.'],
            ['name' => 'Direct Credit Card', 'details' => 'Direct card charge flow without extra gateway steps.'],
            ['name' => 'Test Basic Security', 'details' => 'Basic security check flow for payment testing.'],
            ['name' => 'AI Predict', 'details' => 'AI-assisted payment screening and prediction option.'],
        ];

        foreach ($defaults as $method) {
            PaymentMethod::firstOrCreate(
                ['name' => $method['name']],
                ['details' => $method['details']]
            );
        }

        return PaymentMethod::orderBy('name')->get();
    }
}
