@extends('layouts.app')

@section('content')
<h1 class="mb-4 text-2xl font-bold text-slate-900">Payment Details</h1>

<div class="space-y-3 rounded-xl bg-white p-6 shadow">
    <p><strong>Customer:</strong> {{ $payment->installment->customer->name }}</p>
    <p><strong>Payment Method:</strong> {{ $payment->paymentMethod->name ?? 'N/A' }}</p>
    <p><strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}</p>
    <p><strong>Date:</strong> {{ $payment->payment_date }}</p>
    <p><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>
    @if($payment->qr_image)
        <div>
            <p><strong>QR Image:</strong></p>
            <img src="{{ asset('storage/' . $payment->qr_image) }}" alt="QR" class="mt-2 w-32 rounded-lg border border-slate-200">
        </div>
    @endif
    @if($payment->approved_by)
        <p><strong>Approved By:</strong> {{ $payment->user->name }}</p>
    @endif
</div>
@endsection