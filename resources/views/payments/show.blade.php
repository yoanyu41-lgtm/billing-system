@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Payment Details</h1>

<div class="bg-white p-6 rounded shadow">
    <p><strong>Customer:</strong> {{ $payment->installment->customer->name }}</p>
    <p><strong>Amount:</strong> ${{ $payment->amount }}</p>
    <p><strong>Date:</strong> {{ $payment->payment_date }}</p>
    <p><strong>Status:</strong> {{ $payment->status }}</p>
    @if($payment->qr_image)
        <p><strong>QR Image:</strong> <img src="{{ asset('storage/' . $payment->qr_image) }}" alt="QR" class="w-32"></p>
    @endif
    @if($payment->approved_by)
        <p><strong>Approved By:</strong> {{ $payment->user->name }}</p>
    @endif
</div>
@endsection