@extends('layouts.app')

@section('content')
<div class="content">
    {{-- ចំណងជើង --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-cash-register text-blue-600"></i>
            {{ __('app.record_sale') }} ({{ __('app.stock_out') }})
        </h1>
        <p class="text-base text-gray-600">{{ __('app.record_product_sales') }}</p>
    </div>

    {{-- Sale Form --}}
    <div class="card">
        <form method="POST" action="{{ route('admin.sales.store') }}" id="saleForm">
            @csrf

            {{-- ព័ត៌មានអតិថិជន --}}
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i>
                    {{ __('app.customer_information') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">
                            {{ __('app.customer_name') }} <span class="text-sm text-gray-400">({{ __('app.optional') }})</span>
                        </label>
                        <input 
                            type="text" 
                            name="customer_name" 
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="{{ __('app.enter_customer_name') }}"
                        >
                    </div>

                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">
                            {{ __('app.sale_date') }}
                        </label>
                        <input 
                            type="date" 
                            name="sale_date" 
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>

            {{-- ផ្នែកផលិតផល --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                        {{ __('app.products') }}
                    </h3>
                    <button 
                        type="button" 
                        id="addItem" 
                        class="btn-brand-blue flex items-center gap-2 text-base"
                    >
                        <i class="fas fa-plus"></i>
                        {{ __('app.add_product') }}
                    </button>
                </div>

                {{-- ទំនិញដំបូង --}}
                <div id="items" class="space-y-3">
                    <div class="item bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            {{-- ជ្រើសរើសផលិតផល --}}
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.product') }}</label>
                                <select 
                                    name="items[0][product_id]" 
                                    class="w-full px-3 py-2.5 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">{{ __('app.select_product') }}</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->name }} ({{ __('app.stock') }}: {{ $p->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- បរិមាណ --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.quantity') }}</label>
                                <input 
                                    type="number" 
                                    name="items[0][quantity]" 
                                    value="1" 
                                    min="1" 
                                    class="w-full px-3 py-2.5 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                    onchange="calculateTotal()"
                                >
                            </div>

                            {{-- តម្លៃឯកតា --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.unit_price') }} ($)</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="items[0][price]" 
                                    class="w-full px-3 py-2.5 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 item-price"
                                    placeholder="0.00"
                                    onchange="calculateTotal()"
                                >
                            </div>

                            {{-- សរុបរង --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ __('app.subtotal') }} ($)</label>
                                <input 
                                    type="text" 
                                    class="w-full px-3 py-2.5 text-base bg-gray-100 border border-gray-200 rounded-lg font-semibold text-gray-700 item-subtotal"
                                    value="0.00"
                                    readonly
                                >
                            </div>

                            {{-- ប៊ូតុងលុប --}}
                            <div class="md:col-span-1 flex items-end">
                                <button 
                                    type="button" 
                                    class="w-full px-3 py-2.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition remove-item hidden"
                                    onclick="removeItem(this)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ផ្នែកសរុប --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base text-gray-600 mb-1">{{ __('app.total_amount') }}</p>
                        <p class="text-4xl font-bold text-blue-600" id="grandTotal">$0.00</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">{{ __('app.total_items') }}</p>
                        <p class="text-2xl font-semibold text-gray-700" id="totalItems">0</p>
                    </div>
                </div>
            </div>

            {{-- ប៊ូតុងអនុវត្ត --}}
            <div class="flex items-center justify-between gap-3 pt-4 border-t">
                <a 
                    href="{{ route('admin.products.stock') }}" 
                    class="px-6 py-3 text-base border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center gap-2"
                >
                    <i class="fas fa-times"></i>
                    {{ __('app.cancel') }}
                </a>
                
                <button 
                    type="submit" 
                    class="btn-brand-blue flex items-center gap-2 text-base"
                >
                    <i class="fas fa-save"></i>
                    {{ __('app.record_sale') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.btn-brand-blue {
    background: #2563eb;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
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

// Translation strings
const translations = {
    product: "{{ __('app.product') }}",
    selectProduct: "{{ __('app.select_product') }}",
    stock: "{{ __('app.stock') }}",
    quantity: "{{ __('app.quantity') }}",
    unitPrice: "{{ __('app.unit_price') }}",
    subtotal: "{{ __('app.subtotal') }}"
};

// Product data
const productsData = @json($products);

// បន្ថែមទំនិញ
document.getElementById('addItem').addEventListener('click', () => {
    const div = document.createElement('div');
    div.className = 'item bg-gray-50 p-4 rounded-lg border border-gray-200';
    
    let optionsHtml = `<option value="">${translations.selectProduct}</option>`;
    productsData.forEach(p => {
        optionsHtml += `<option value="${p.id}">${p.name} (${translations.stock}: ${p.stock})</option>`;
    });
    
    div.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-600 mb-1">${translations.product}</label>
                <select 
                    name="items[${idx}][product_id]" 
                    class="w-full px-3 py-2.5 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
                    ${optionsHtml}
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-1">${translations.quantity}</label>
                <input 
                    type="number" 
                    name="items[${idx}][quantity]" 
                    value="1" 
                    min="1" 
                    class="w-full px-3 py-2.5 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    onchange="calculateTotal()"
                >
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-1">${translations.unitPrice} ($)</label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="items[${idx}][price]" 
                    class="w-full px-3 py-2.5 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 item-price"
                    placeholder="0.00"
                    onchange="calculateTotal()"
                >
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-1">${translations.subtotal} ($)</label>
                <input 
                    type="text" 
                    class="w-full px-3 py-2.5 text-base bg-gray-100 border border-gray-200 rounded-lg font-semibold text-gray-700 item-subtotal"
                    value="0.00"
                    readonly
                >
            </div>
            <div class="md:col-span-1 flex items-end">
                <button 
                    type="button" 
                    class="w-full px-3 py-2.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition remove-item"
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

// លុបទំនិញ
function removeItem(button) {
    button.closest('.item').remove();
    updateRemoveButtons();
    calculateTotal();
}

// ធ្វើបច្ចុប្បន្នភាពប៊ូតុងលុប
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

// គណនាសរុប
function calculateTotal() {
    let total = 0;
    const items = document.querySelectorAll('.item');
    
    items.forEach(item => {
        const quantity = parseFloat(item.querySelector('input[name*="[quantity]"]')?.value || 0);
        const price = parseFloat(item.querySelector('input[name*="[price]"]')?.value || 0);
        const subtotal = quantity * price;
        
        // ធ្វើបច្ចុប្បន្នភាពសរុបរង
        const subtotalInput = item.querySelector('.item-subtotal');
        if (subtotalInput) {
            subtotalInput.value = subtotal.toFixed(2);
        }
        
        total += subtotal;
    });
    
    // ធ្វើបច្ចុប្បន្នភាពសរុបទាំងអស់
    document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
    document.getElementById('totalItems').textContent = items.length;
}

// ចាប់ផ្តើម
updateRemoveButtons();
calculateTotal();
</script>
@endsection
