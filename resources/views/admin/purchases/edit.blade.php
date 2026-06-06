@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Purchase</h1>
<form method="POST" action="{{ route('admin.purchases.update', $purchase) }}" class="bg-white p-6 rounded shadow">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label>Supplier</label>
        <select name="supplier_id" class="w-full border px-2 py-1" required>
            <option value="">-- Select supplier --</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ old('supplier_id', $purchase->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label>Purchase Date</label>
        <input type="date" name="purchase_date" value="{{ old('purchase_date', $purchase->purchase_date?->format('Y-m-d')) }}" class="w-full border px-2 py-1">
    </div>

    <div id="items">
        @foreach($purchase->items as $idx => $item)
        <div class="item mb-3">
            <select name="items[{{ $idx }}][product_id]" class="w-1/2 border px-2 py-1 inline-block">
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ old('items.'.$idx.'.product_id', $item->product_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
            <input type="number" name="items[{{ $idx }}][quantity]" value="{{ old('items.'.$idx.'.quantity', $item->quantity) }}" min="1" class="w-1/6 border px-2 py-1 inline-block">
            <input type="number" step="0.01" name="items[{{ $idx }}][cost_price]" value="{{ old('items.'.$idx.'.cost_price', $item->cost_price) }}" class="w-1/6 border px-2 py-1 inline-block">
        </div>
        @endforeach
    </div>

    <button type="button" id="addItem" class="bg-gray-200 px-3 py-1 rounded">Add item</button>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Purchase</button>
</form>

<script>
let idx = {{ $purchase->items->count() }};
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
