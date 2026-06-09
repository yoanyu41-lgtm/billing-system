@extends('layouts.app')

@section('content')
<h1 class="mb-4 text-2xl font-bold text-slate-900">Payments</h1>

<div class="mb-4 flex flex-wrap items-center gap-3">
    <a href="{{ route('payments.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Add Payment</a>
    <form method="GET" class="flex items-center gap-2">
        <select name="status" class="rounded-lg border border-slate-300 px-3 py-2">
            <option value="">All</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-600 px-4 py-2 text-white">Filter</button>
    </form>
</div>

<div class="overflow-x-auto rounded-xl bg-white shadow">
    <table class="w-full">
        <thead>
            <tr class="bg-slate-200 text-slate-800">
                <th class="p-3 text-left">Customer</th>
                <th class="p-3 text-left">Method</th>
                <th class="p-3 text-left">Amount</th>
                <th class="p-3 text-left">Date</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr class="border-t border-slate-100">
                    <td class="p-3">{{ $payment->installment?->customer?->name ?? 'N/A' }}</td>
                    <td class="p-3">{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                    <td class="p-3 font-semibold">${{ number_format($payment->amount, 2) }}</td>
                    <td class="p-3">{{ $payment->payment_date }}</td>
                    <td class="p-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold
                            @if($payment->status == 'approved') bg-green-100 text-green-700
                            @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-700
                            @else bg-rose-100 text-rose-700
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="p-3">
                        <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:underline">View</a>
                        @if($payment->status == 'pending' && auth()->user()->role == 'admin')
                            <form method="POST" action="{{ route('payments.approve', $payment) }}" class="ml-2 inline">
                                @csrf
                                <button type="submit" class="text-emerald-600 hover:underline">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('payments.reject', $payment) }}" class="ml-2 inline">
                                @csrf
                                <button type="submit" class="text-rose-600 hover:underline">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-500">No payments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $payments->links() }}
</div>
@endsection