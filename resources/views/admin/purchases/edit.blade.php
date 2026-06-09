@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Purchase</h1>
            <p class="text-sm text-gray-500 mt-1">Modify the items, supplier, or date of purchase record #{{ $purchase->id }}.</p>
        </div>
        <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('app.back') }} to {{ __('app.purchase_history') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.purchases.update', $purchase) }}" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Supplier -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.supplier') }} <span class="text-red-500">*</span></label>
                <select name="supplier_id" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" required>
                    <option value="">-- Select supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id', $purchase->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Purchase Date -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.purchase_date') }}</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date', $purchase->purchase_date?->format('Y-m-d')) }}" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150">
            </div>
        </div>

        <!-- Items Table Section -->
        <div class="border-t border-gray-100 pt-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('app.purchase_items') }}</h3>

            <!-- Grid Header -->
            <div class="hidden md:grid grid-cols-12 gap-4 mb-2 px-2 font-semibold text-gray-500 text-xs uppercase tracking-wider">
                <div class="col-span-6">{{ __('app.product') }}</div>
                <div class="col-span-3">{{ __('app.quantity') }}</div>
                <div class="col-span-2">{{ __('app.cost_price') }} ($)</div>
                <div class="col-span-1 text-center"></div>
            </div>

            <div id="items-container" class="space-y-4">
                @foreach($purchase->items as $idx => $item)
                <!-- Existing Item Row -->
                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-gray-50 md:bg-transparent p-4 md:p-0 rounded-xl md:rounded-none border border-gray-100 md:border-0">
                    <div class="col-span-6">
                        <label class="block md:hidden text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('app.product') }}</label>
                        <select name="items[{{ $idx }}][product_id]" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition duration-150" required>
                            <option value="">-- Choose Product --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('items.'.$idx.'.product_id', $item->product_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block md:hidden text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('app.quantity') }}</label>
                        <input type="number" name="items[{{ $idx }}][quantity]" value="{{ old('items.'.$idx.'.quantity', $item->quantity) }}" min="1" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition duration-150" required>
                    </div>
                    <div class="col-span-2">
                        <label class="block md:hidden text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('app.cost_price') }}</label>
                        <input type="number" step="0.01" name="items[{{ $idx }}][cost_price]" value="{{ old('items.'.$idx.'.cost_price', $item->cost_price) }}" placeholder="0.00" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition duration-150">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" class="remove-row-btn p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition duration-150">
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" id="addItem" class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                {{ __('app.add_item') }}
            </button>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.purchases.index') }}" class="px-6 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150 shadow-sm">
                {{ __('app.cancel') }}
            </a>
            <button type="submit" class="px-6 py-2.5 font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition duration-150">
                Update Purchase
            </button>
        </div>
    </form>
</div>

<script>
let idx = {{ $purchase->items->count() }};
const productsJson = @json($products);

document.getElementById('addItem').addEventListener('click', () => {
    const container = document.getElementById('items-container');
    const div = document.createElement('div');
    div.className = 'item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-gray-50 md:bg-transparent p-4 md:p-0 rounded-xl md:rounded-none border border-gray-100 md:border-0';
    
    let optionsHtml = '<option value="">-- Choose Product --</option>';
    productsJson.forEach(p => {
        optionsHtml += `<option value="${p.id}">${p.name} (${p.code})</option>`;
    });

    div.innerHTML = `
        <div class="col-span-6">
            <label class="block md:hidden text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('app.product') }}</label>
            <select name="items[${idx}][product_id]" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition duration-150" required>
                ${optionsHtml}
            </select>
        </div>
        <div class="col-span-3">
            <label class="block md:hidden text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('app.quantity') }}</label>
            <input type="number" name="items[${idx}][quantity]" value="1" min="1" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition duration-150" required>
        </div>
        <div class="col-span-2">
            <label class="block md:hidden text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('app.cost_price') }}</label>
            <input type="number" step="0.01" name="items[${idx}][cost_price]" placeholder="0.00" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition duration-150">
        </div>
        <div class="col-span-1 text-center">
            <button type="button" class="remove-row-btn p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition duration-150">
                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    `;
    container.appendChild(div);
    idx++;
    updateRemoveButtons();
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.item-row');
    rows.forEach((row, i) => {
        const btn = row.querySelector('.remove-row-btn');
        if (btn) {
            if (rows.length === 1) {
                btn.classList.add('hidden');
            } else {
                btn.classList.remove('hidden');
            }
        }
    });
}

document.getElementById('items-container').addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-row-btn');
    if (btn) {
        const row = btn.closest('.item-row');
        row.remove();
        updateRemoveButtons();
    }
});

updateRemoveButtons();
</script>
@endsection
