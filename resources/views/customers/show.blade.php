@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">{{ $customer->name }}</h1>

<div class="bg-white p-6 rounded shadow mb-4">
    <h2 class="text-lg font-semibold mb-2">Customer Details</h2>
    <p><strong>Name:</strong> {{ $customer->name }}</p>
    <p><strong>Phone:</strong> {{ $customer->phone }}</p>
    <p><strong>Address:</strong> {{ $customer->address }}</p>
    <p><strong>Telegram ID:</strong> {{ $customer->telegram_id }}</p>
</div>

<div class="bg-white p-6 rounded shadow mb-4">
    <h2 class="text-lg font-semibold mb-2">Installments</h2>
    <ul>
        @foreach($installments as $installment)
            <li>{{ $installment->product->name }} - ${{ $installment->monthly_payment }} / month - Remaining: ${{ $installment->remaining_balance }}</li>
        @endforeach
    </ul>
</div>

<div class="bg-white p-6 rounded shadow">
    <h2 class="text-lg font-semibold mb-2">Payment History</h2>
    <table class="w-full">
        <thead>
            <tr>
                <th class="p-2">Date</th>
                <th class="p-2">Amount</th>
                <th class="p-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td class="p-2">{{ $payment->payment_date }}</td>
                <td class="p-2">${{ $payment->amount }}</td>
                <td class="p-2">{{ $payment->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection