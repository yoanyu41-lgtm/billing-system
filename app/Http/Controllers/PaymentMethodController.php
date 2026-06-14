<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $this->ensureDefaults();

        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'details' => 'nullable|string|max:1000',
        ]);

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method added successfully.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name,' . $paymentMethod->id,
            'details' => 'nullable|string|max:1000',
        ]);

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method deleted successfully.');
    }

    private function ensureDefaults(): void
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
            $hasPayments = \App\Models\Payment::where('payment_method_id', $method->id)->exists();
            if (!$hasPayments) {
                $method->delete();
            }
        }
    }
}
