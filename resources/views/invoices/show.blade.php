@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Invoice {{ $invoice->invoice_number }}</h1>

<div class="bg-white p-6 rounded shadow">
    <h2 class="text-lg font-semibold mb-4">City Tech Computer</h2>
    <p>Address: Phnom Penh, Cambodia</p>
    <p>Phone: 012 345 678</p>
    <hr class="my-4">
    <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
    <p><strong>Date:</strong> {{ $invoice->created_at->format('Y-m-d') }}</p>
    <p><strong>Staff:</strong> {{ $invoice->payment->installment->user->name }}</p>
    <hr class="my-4">
    <p><strong>Customer:</strong> {{ $invoice->payment->installment->customer->name }}</p>
    <p><strong>Phone:</strong> {{ $invoice->payment->installment->customer->phone }}</p>
    <p><strong>Product:</strong> {{ $invoice->payment->installment->product->name }}</p>
    <p><strong>Amount Paid:</strong> ${{ $invoice->payment->amount }}</p>
    <p><strong>Remaining Balance:</strong> ${{ $invoice->payment->installment->remaining_balance }}</p>
</div>
@endsection