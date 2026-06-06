@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Purchase History</h1>
<div class="mb-4">
    <a href="{{ route('admin.purchases.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">New Purchase</a>
</div>
<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Purchase ID</th>
            <th class="p-2">Supplier</th>
            <th class="p-2">Products</th>
            <th class="p-2">Total</th>
            <th class="p-2">Purchase Date</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchases as $purchase)
        <tr>
            <td class="p-2">#{{ $purchase->id }}</td>
            <td class="p-2">{{ $purchase->supplier?->name ?? '—' }}</td>
            <td class="p-2">{{ $purchase->items->count() }}</td>
            <td class="p-2">${{ number_format($purchase->total, 2) }}</td>
            <td class="p-2">{{ $purchase->purchase_date?->format('Y-m-d') ?? $purchase->created_at->format('Y-m-d') }}</td>
            <td class="p-2">
                <a href="{{ route('admin.purchases.edit', $purchase) }}" class="text-green-500">Edit</a>
                <form method="POST" action="{{ route('admin.purchases.destroy', $purchase) }}" class="inline ml-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">{{ $purchases->links() }}</div>
@endsection
