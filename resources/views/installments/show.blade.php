@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Installment Details</h1>

<div class="bg-white p-6 rounded shadow">
    <p><strong>Customer:</strong> {{ $installment->customer->name }}</p>
    <p><strong>Product:</strong> {{ $installment->product->name }}</p>
    <p><strong>Total Price:</strong> ${{ $installment->total_price }}</p>
    <p><strong>Down Payment:</strong> ${{ $installment->down_payment }}</p>
    <p><strong>Interest Rate:</strong> {{ $installment->interest_rate }}%</p>
    <p><strong>Duration:</strong> {{ $installment->duration_months }} months</p>
    <p><strong>Monthly Payment:</strong> ${{ $installment->monthly_payment }}</p>
    <p><strong>Remaining Balance:</strong> ${{ $installment->remaining_balance }}</p>
    <p><strong>Status:</strong> {{ $installment->status }}</p>
</div>
@endsection