@extends('layouts.app')

@section('content')
<div class="content">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            <i class="fas fa-cash-register text-blue-600"></i>
            {{ __('Record Sale') }} (Stock Out)
        </h1>
        <p class="text-sm text-gray-600">{{ __('Record product sales and reduce stock') }}</p>
    </div>

    {{-- Sale Form --}}
    <div class="card max-w-4xl">
        <form method="POST" action="{{ route('admin.sales.store') }}" id="saleForm">
            @csrf

            {{-- Customer Info --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i>
                    {{ __('Customer Information') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Customer Name') }} <span class="text-gray-400">({{ __('Optional') }})</span>
                        </label>
                        <input 
                            type="text" 
                            name="customer_name" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="{{ __('Enter customer name') }}"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Sale Date') }}
                        </label>
                        <input 
                            type="date" 
                            name="sale_date" 
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>

            {{-- Products Section --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                        {{ __('Products') }}
                    </h3>
                    <button 
                        type="button" 
                        id="addItem" 
                        class="btn-brand-blue flex items-center gap-2"
                    >
                        <i class="fas fa-plus"></i>
                        {{ __('Add Product') }}
                    </button>
                </div>

                {{-- Items Container --}}
                <div id="items" class="space-y-3">
                    {{-- First Item --}}
                    <div class="item bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            {{-- Product Select --}}
                            <div class="md:col-span-5">
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Product') }}</label>
                                <select 
                                    name="items[0][product_id]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                    required
                                >
                                    <option value="">{{ __('Select product') }}</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->name }} ({{ __('Stock') }}: {{ $p->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quantity --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Quantity') }}</label>
                                <input 
                                    type="number" 
                                    name="items[0][quantity]" 
                                    value="1" 
                                    min="1" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                    required
                                    onchange="calculateTotal()"
                                >
                            </div>

                            {{-- Unit Price --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Unit Price') }} ($)</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="items[0][price]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm item-price"
                                    placeholder="0.00"
                                    onchange="calculateTotal()"
                                >
                            </div>

                            {{-- Subtotal --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Subtotal') }} ($)</label>
                                <input 
                                    type="text" 
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 item-subtotal"
                                    value="0.00"
                                    readonly
                                >
                            </div>

                            {{-- Remove Button --}}
                            <div class="md:col-span-1 flex items-end">
                                <button 
                                    type="button" 
                                    class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition remove-item hidden"
                                    onclick="removeItem(this)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Section --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ __('Total Amount') }}</p>
                        <p class="text-3xl font-bold text-blue-600" id="grandTotal">$0.00</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">{{ __('Total Items') }}</p>
                        <p class="text-xl font-semibold text-gray-700" id="totalItems">0</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between gap-3 pt-4 border-t">
                <a 
                    href="{{ route('admin.products.stock') }}" 
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center gap-2"
                >
                    <i class="fas fa-times"></i>
                    {{ __('Cancel') }}
                </a>
                
                <button 
                    type="submit" 
                    class="btn-brand-blue flex items-center gap-2"
                >
                    <i class="fas fa-save"></i>
                    {{ __('Record Sale') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.btn-brand-blue {
    background: #2563eb;
    color: white;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-brand-blue:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}
</style>

<script>
let idx = 1;

// Add Item
document.getElementById('addItem').addEventListener('click', () => {
    const div = document.createElement('div');
    div.className = 'item bg-gray-50 p-4 rounded-lg border border-gray-200';
    div.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Product') }}</label>
                <select 
                    name="items[${idx}][product_id]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    required
                >
                    <option value="">{{ __('Select product') }}</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ __('Stock') }}: {{ $p->stock }})</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Quantity') }}</label>
                <input 
                    type="number" 
                    name="items[${idx}][quantity]" 
                    value="1" 
                    min="1" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    required
                    onchange="calculateTotal()"
                >
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Unit Price') }} ($)</label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="items[${idx}][price]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm item-price"
                    placeholder="0.00"
                    onchange="calculateTotal()"
                >
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Subtotal') }} ($)</label>
                <input 
                    type="text" 
                    class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 item-subtotal"
                    value="0.00"
                    readonly
                >
            </div>
            <div class="md:col-span-1 flex items-end">
                <button 
                    type="button" 
                    class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition remove-item"
                    onclick="removeItem(this)"
                >
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    document.getElementById('items').appendChild(div);
    idx++;
    updateRemoveButtons();
    calculateTotal();
});

// Remove Item
function removeItem(button) {
    button.closest('.item').remove();
    updateRemoveButtons();
    calculateTotal();
}

// Update Remove Buttons visibility
function updateRemoveButtons() {
    const items = document.querySelectorAll('.item');
    items.forEach((item, index) => {
        const removeBtn = item.querySelector('.remove-item');
        if (items.length > 1) {
            removeBtn.classList.remove('hidden');
        } else {
            removeBtn.classList.add('hidden');
        }
    });
}

// Calculate Total
function calculateTotal() {
    let total = 0;
    const items = document.querySelectorAll('.item');
    
    items.forEach(item => {
        const quantity = parseFloat(item.querySelector('input[name*="[quantity]"]')?.value || 0);
        const price = parseFloat(item.querySelector('input[name*="[price]"]')?.value || 0);
        const subtotal = quantity * price;
        
        // Update subtotal display
        const subtotalInput = item.querySelector('.item-subtotal');
        if (subtotalInput) {
            subtotalInput.value = subtotal.toFixed(2);
        }
        
        total += subtotal;
    });
    
    // Update grand total
    document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
    document.getElementById('totalItems').textContent = items.length;
}

// Initialize
updateRemoveButtons();
calculateTotal();
</script>
@endsection
