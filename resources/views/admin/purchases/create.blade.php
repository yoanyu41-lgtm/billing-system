@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Record Purchase (Stock In)</h1>
<form method="POST" action="{{ route('admin.purchases.store') }}" class="bg-white p-6 rounded shadow">
    @csrf
    <div class="mb-4">
        <label>Supplier</label>
        @if($suppliers->isEmpty())
            <div class="p-3 bg-yellow-100 text-yellow-800 rounded mb-3">
                No suppliers found. Please <a href="{{ route('admin.suppliers.create') }}" class="text-blue-600 underline">add a supplier</a> first.
            </div>
        @endif
        <select name="supplier_id" class="w-full border px-2 py-1" required {{ $suppliers->isEmpty() ? 'disabled' : '' }}>
            <option value="">-- Select supplier --</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label>Purchase Date</label>
        <input type="date" name="purchase_date" class="w-full border px-2 py-1">
    </div>

    <div id="items">
        <div class="item mb-3">
            <select name="items[0][product_id]" class="w-1/2 border px-2 py-1 inline-block">
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ (isset($selectedProductId) && $selectedProductId == $p->id) ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
            <input type="number" name="items[0][quantity]" value="1" min="1" class="w-1/6 border px-2 py-1 inline-block">
            <input type="number" step="0.01" name="items[0][cost_price]" class="w-1/6 border px-2 py-1 inline-block">
        </div>
    </div>

    <button type="button" id="addItem" class="bg-gray-200 px-3 py-1 rounded">Add item</button>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" {{ $suppliers->isEmpty() ? 'disabled' : '' }}>Record Purchase</button>
</form>

<script>
let idx = 1;
document.getElementById('addItem').addEventListener('click', ()=>{
    const div = document.createElement('div');
    div.className = 'item mb-3';
    div.innerHTML = `
        <select name="items[${idx}][product_id]" class="w-1/2 border px-2 py-1 inline-block">` +
        `@foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach` +
        `</select>
        <input type="number" name="items[${idx}][quantity]" value="1" min="1" class="w-1/6 border px-2 py-1 inline-block">
        <input type="number" step="0.01" name="items[${idx}][cost_price]" class="w-1/6 border px-2 py-1 inline-block">
    `;
    document.getElementById('items').appendChild(div);
    idx++;
});
</script>
@endsection
