@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Late Payments</h1>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Customer</th>
            <th class="p-2">Remaining Balance</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lateInstallments as $installment)
        <tr>
            <td class="p-2">{{ $installment->customer->name }}</td>
            <td class="p-2">${{ $installment->remaining_balance }}</td>
            <td class="p-2">
                <form method="POST" action="{{ route('late-payments.remind', $installment) }}">
                    @csrf
                    <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Send Reminder</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection