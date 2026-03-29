@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Daily Report</h1>

<form method="GET" class="mb-4">
    <input type="date" name="date" value="{{ $date }}" class="border px-2 py-1">
    <button type="submit" class="bg-blue-500 text-white px-4 py-1">Filter</button>
    <a href="{{ route('admin.reports.export', ['type' => 'daily', 'date' => $date]) }}" class="bg-green-500 text-white px-4 py-1 ml-2">Export PDF</a>
</form>

<p class="mb-4">Total: ${{ $total }}</p>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Customer</th>
            <th class="p-2">Amount</th>
            <th class="p-2">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr>
            <td class="p-2">{{ $payment->installment->customer->name }}</td>
            <td class="p-2">${{ $payment->amount }}</td>
            <td class="p-2">{{ $payment->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection