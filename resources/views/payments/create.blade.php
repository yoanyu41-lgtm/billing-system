@extends('layouts.app')

@section('content')
<div class="max-w-3xl">
    <h1 class="mb-2 text-2xl font-bold text-slate-900">Add Payment</h1>
    <p class="mb-6 text-sm text-slate-600">Select the installment, choose the payment channel, and attach a QR or receipt image when needed.</p>

    <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="space-y-5 rounded-xl bg-white p-6 shadow">
        @csrf

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block font-medium text-slate-700">Installment</label>
                <select name="installment_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    @foreach($installments as $installment)
                        <option value="{{ $installment->id }}" {{ old('installment_id') == $installment->id ? 'selected' : '' }}>
                            {{ $installment->customer->name }} - {{ $installment->product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block font-medium text-slate-700">Payment Method</label>
                <select name="payment_method_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Select method</option>
                    @foreach($paymentMethods as $method)
                        <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                            {{ $method->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block font-medium text-slate-700">Amount</label>
                <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="mb-1 block font-medium text-slate-700">Payment Date</label>
                <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
        </div>

        <div class="rounded-lg border border-dashed border-cyan-300 bg-cyan-50 p-4">
            <label class="mb-1 block font-medium text-slate-700">QR / Receipt Image</label>
            <input type="file" name="qr_image" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2">
            <p class="mt-2 text-xs text-slate-500">Optional for QR, card slip, or third-party payment proof.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Submit Payment</button>
            <a href="{{ route('payments.index') }}" class="rounded-lg bg-slate-200 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-300">Cancel</a>
        </div>
    </form>
</div>
@endsection