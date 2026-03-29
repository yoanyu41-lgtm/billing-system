@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Payments</h1>

<div class="mb-4">
    <a href="{{ route('payments.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add Payment</a>
    <form method="GET" class="inline ml-4">
        <select name="status" class="border px-2 py-1">
            <option value="">All</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="bg-gray-500 text-white px-4 py-1">Filter</button>
    </form>
</div>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Customer</th>
            <th class="p-2">Amount</th>
            <th class="p-2">Date</th>
            <th class="p-2">Status</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr>
            <td class="p-2">{{ $payment->installment->customer->name }}</td>
            <td class="p-2">${{ $payment->amount }}</td>
            <td class="p-2">{{ $payment->payment_date }}</td>
            <td class="p-2">{{ $payment->status }}</td>
            <td class="p-2">
                <a href="{{ route('payments.show', $payment) }}" class="text-blue-500">View</a>
                @if($payment->status == 'pending' && auth()->user()->role == 'admin')
                    <form method="POST" action="{{ route('payments.approve', $payment) }}" class="inline ml-2">
                        @csrf
                        <button type="submit" class="text-green-500">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('payments.reject', $payment) }}" class="inline ml-2">
                        @csrf
                        <button type="submit" class="text-red-500">Reject</button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $payments->links() }}
@endsection