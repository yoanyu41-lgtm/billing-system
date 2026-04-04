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
    }
}
