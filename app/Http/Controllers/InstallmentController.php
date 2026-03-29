<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InstallmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Installment::with('customer', 'product');

        if ($user->role === 'user') {
            $query->where('created_by', $user->id);
        }

        $installments = $query->paginate(10);
        return view('installments.index', compact('installments'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('installments.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'total_price' => 'required|numeric',
            'down_payment' => 'required|numeric',
            'interest_rate' => 'nullable|numeric',
            'duration_months' => 'required|integer',
        ]);

        $totalPrice = $request->total_price;
        $downPayment = $request->down_payment;
        $interestRate = $request->interest_rate ?? 0;
        $duration = $request->duration_months;

        $principal = $totalPrice - $downPayment;
        $monthlyInterest = ($principal * $interestRate / 100) / 12;
        $monthlyPayment = ($principal / $duration) + $monthlyInterest;
        $remainingBalance = $principal + ($monthlyInterest * $duration);

        Installment::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'total_price' => $totalPrice,
            'down_payment' => $downPayment,
            'interest_rate' => $interestRate,
            'duration_months' => $duration,
            'monthly_payment' => $monthlyPayment,
            'remaining_balance' => $remainingBalance,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('installments.index')->with('success', 'Installment created successfully.');
    }

    public function show(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        return view('installments.show', compact('installment'));
    }

    public function edit(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);
        $customers = Customer::all();
        $products = Product::all();
        return view('installments.edit', compact('installment', 'customers', 'products'));
    }

    public function update(Request $request, Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);

        $request->validate([
            'total_price' => 'required|numeric',
            'down_payment' => 'required|numeric',
            'interest_rate' => 'nullable|numeric',
            'duration_months' => 'required|integer',
            'status' => 'required|in:active,cancelled',
        ]);

        $totalPrice = $request->total_price;
        $downPayment = $request->down_payment;
        $interestRate = $request->interest_rate ?? 0;
        $duration = $request->duration_months;

        $principal = $totalPrice - $downPayment;
        $monthlyInterest = ($principal * $interestRate / 100) / 12;
        $monthlyPayment = ($principal / $duration) + $monthlyInterest;
        $remainingBalance = $principal + ($monthlyInterest * $duration);

        $installment->update([
            'total_price' => $totalPrice,
            'down_payment' => $downPayment,
            'interest_rate' => $interestRate,
            'duration_months' => $duration,
            'monthly_payment' => $monthlyPayment,
            'remaining_balance' => $remainingBalance,
            'status' => $request->status,
        ]);

        return redirect()->route('installments.index')->with('success', 'Installment updated successfully.');
    }

    public function destroy(Installment $installment)
    {
        Gate::authorize('manage-installment', $installment);

        $installment->update(['status' => 'cancelled']);
        return redirect()->route('installments.index')->with('success', 'Installment cancelled successfully.');
    }
}
