<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Payment::with('installment.customer', 'user');

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
        $installments = Installment::with('customer');

        if ($user->role === 'user') {
            $installments->where('created_by', $user->id);
        }

        $installments = $installments->where('status', 'active')->get();
        return view('payments.create', compact('installments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'installment_id' => 'required|exists:installments,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'qr_image' => 'nullable|image',
        ]);

        $installment = Installment::find($request->installment_id);
        $qrPath = null;

        if ($request->hasFile('qr_image')) {
            $qrPath = $request->file('qr_image')->store('qr_images', 'public');
        }

        Payment::create([
            'installment_id' => $request->installment_id,
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

        // Update remaining balance
        $installment = $payment->installment;
        $installment->remaining_balance -= $payment->amount;
        $installment->save();

        // Generate invoice
        Invoice::create([
            'payment_id' => $payment->id,
            'invoice_number' => 'INV-' . $payment->id,
        ]);

        // Send Telegram notification (simulate)
        // Here you would call Telegram API

        return redirect()->route('payments.index')->with('success', 'Payment approved.');
    }

    public function reject(Payment $payment)
    {
        $payment->update(['status' => 'rejected']);
        return redirect()->route('payments.index')->with('success', 'Payment rejected.');
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }
}
