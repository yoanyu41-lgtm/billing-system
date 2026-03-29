@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Installments</h1>

<div class="mb-4">
    <a href="{{ route('installments.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Create Installment</a>
</div>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Customer</th>
            <th class="p-2">Product</th>
            <th class="p-2">Monthly Payment</th>
            <th class="p-2">Remaining Balance</th>
            <th class="p-2">Status</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($installments as $installment)
        <tr>
            <td class="p-2">{{ $installment->customer->name }}</td>
            <td class="p-2">{{ $installment->product->name }}</td>
            <td class="p-2">${{ $installment->monthly_payment }}</td>
            <td class="p-2">${{ $installment->remaining_balance }}</td>
            <td class="p-2">{{ $installment->status }}</td>
            <td class="p-2">
                <a href="{{ route('installments.show', $installment) }}" class="text-blue-500">View</a>
                <a href="{{ route('installments.edit', $installment) }}" class="text-green-500 ml-2">Edit</a>
                <form method="POST" action="{{ route('installments.destroy', $installment) }}" class="inline ml-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500">Cancel</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $installments->links() }}
@endsection