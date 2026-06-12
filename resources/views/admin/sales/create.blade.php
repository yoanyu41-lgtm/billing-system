@extends('layouts.app')

@section('content')
<div class="content">
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-cash-register text-blue-600"></i>
                {{ __('app.new_direct_sale') }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ __('app.direct_sale_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.sales.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-list"></i> {{ __('app.sales_list') }}
        </a>
    </div>

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.sales.store') }}" id="saleForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: form --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Customer info --}}
                <div class="card">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i> {{ __('app.customer_information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ __('app.customer_name') }} <span class="text-xs text-gray-400">({{ __('app.optional') }})</span>
                            </label>
                            <input type="text" name="customer_name" id="customerName" value="{{ old('customer_name') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="{{ __('app.enter_customer_name') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.customer_phone') }}</label>
                            <input type="text" name="customer_phone" id="customerPhone" value="{{ old('customer_phone') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="012 345 678">
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shopping-cart text-blue-600"></i> {{ __('app.sale_items') }}
                        </h3>
                        <button type="button" id="addItem"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus"></i> {{ __('app.add_product') }}
                        </button>
                    </div>

                    <div id="items" class="space-y-3"></div>
                </div>
            </div>

            {{-- Right: summary --}}
            <div class="space-y-6">
                <div class="card sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-receipt text-blue-600"></i> {{ __('app.receipt') }}
                    </h3>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.sale_date') }}</label>
                            <input type="date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.payment_method') }}</label>
                            <input type="text" name="payment_method" value="{{ old('payment_method', __('app.cash')) }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="{{ __('app.cash') }}">
                        </div>
                    </div>

                    <div class="space-y-2 border-t pt-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('app.subtotal') }}</span>
                            <span class="font-semibold text-gray-900" id="subtotalLabel">$0.00</span>
                        </div>
                        <div class="flex items-center justify-between" id="taxRow" style="display: none;">
                            <span class="text-gray-600">{{ __('app.tax') }} <span id="taxRateDisplay"></span></span>
                            <span class="font-semibold text-gray-900" id="taxLabel">$0.00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('app.discount') }}</span>
                            <div class="flex items-center gap-1">
                                <span class="text-gray-400">$</span>
                                <input type="number" step="0.01" min="0" name="discount" id="discountInput"
                                       value="{{ old('discount', 0) }}"
                                       class="w-24 px-2 py-1.5 text-sm text-right border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       onchange="calculateTotal()" oninput="calculateTotal()">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ __('app.grand_total') }}</span>
                            <span class="text-3xl font-extrabold text-blue-600" id="grandTotal">$0.00</span>
                        </div>
                        <div class="flex items-center justify-between mt-1 text-xs text-gray-500">
                            <span>{{ __('app.items_count') }}</span>
                            <span id="totalItems">0</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.note') ?? 'Note' }}</label>
                        <textarea name="note" rows="2"
                                  class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit"
                            class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-check-circle"></i> {{ __('app.complete_sale') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const productsData = {!! json_encode($products->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->price,
            'stock' => $p->stock,
            'is_taxable' => $p->is_taxable ?? false,
            'tax_rate' => $p->tax_rate ?? 0,
            'tax_type' => $p->tax_type ?? 'exclusive'
        ];
    })) !!};
    
    const taxEnabled = {{ \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1' ? 'true' : 'false' }};
    const defaultTaxRate = {{ (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0) }};
    const taxLabel = '{{ \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT' }}';
    
    const T = {
        product: {!! json_encode(__('app.product')) !!},
        selectProduct: {!! json_encode(__('app.select_product')) !!},
        stock: {!! json_encode(__('app.stock')) !!},
        quantity: {!! json_encode(__('app.quantity')) !!},
        unitPrice: {!! json_encode(__('app.unit_price')) !!},
        subtotal: {!! json_encode(__('app.subtotal')) !!},
    };

    let idx = 0;

    function itemRow(index) {
        let options = `<option value="">${T.selectProduct}</option>`;
        productsData.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}" data-taxable="${p.is_taxable}" data-tax-rate="${p.tax_rate}" data-tax-type="${p.tax_type}">${p.name} (${T.stock}: ${p.stock})</option>`;
        });
        const div = document.createElement('div');
        div.className = 'item bg-gray-50 p-3 rounded-lg border border-gray-200';
        div.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-5">
                    <label class="block text-xs font-medium text-gray-500 mb-1">${T.product}</label>
                    <select name="items[${index}][product_id]" required onchange="onProductChange(this)"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 item-product">
                        ${options}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">${T.quantity}</label>
                    <input type="number" name="items[${index}][quantity]" value="1" min="1" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 item-qty"
                           oninput="calculateTotal()">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">${T.unitPrice} ($)</label>
                    <input type="number" step="0.01" min="0" name="items[${index}][price]" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 item-price"
                           placeholder="0.00" oninput="calculateTotal()">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">${T.subtotal} ($)</label>
                    <input type="text" readonly value="0.00"
                           class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-200 rounded-lg font-semibold text-gray-700 item-subtotal">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="button" onclick="removeItem(this)"
                            class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>`;
        return div;
    }

    function addItem() {
        document.getElementById('items').appendChild(itemRow(idx));
        idx++;
        updateRemoveButtons();
        calculateTotal();
    }

    function onProductChange(select) {
        const opt = select.options[select.selectedIndex];
        const price = opt.getAttribute('data-price');
        const row = select.closest('.item');
        const priceInput = row.querySelector('.item-price');
        // Always pull the product's price when a product is selected
        if (price) priceInput.value = parseFloat(price).toFixed(2);
        calculateTotal();
    }

    function removeItem(btn) {
        btn.closest('.item').remove();
        updateRemoveButtons();
        calculateTotal();
    }

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.item');
        items.forEach(item => {
            const btn = item.querySelector('.remove-item');
            if (items.length > 1) btn.classList.remove('hidden');
            else btn.classList.add('hidden');
        });
    }

    function calculateTotal() {
        let subtotal = 0;
        let totalTax = 0;
        let hasTaxableItem = false;
        
        document.querySelectorAll('.item').forEach(item => {
            const qty = parseFloat(item.querySelector('.item-qty')?.value || 0);
            const price = parseFloat(item.querySelector('.item-price')?.value || 0);
            const productSelect = item.querySelector('.item-product');
            const opt = productSelect.options[productSelect.selectedIndex];
            
            const line = qty * price;
            item.querySelector('.item-subtotal').value = line.toFixed(2);
            subtotal += line;
            
            // Calculate tax for this item
            if (taxEnabled && opt) {
                const isTaxable = opt.getAttribute('data-taxable') === '1' || opt.getAttribute('data-taxable') === 'true';
                const taxRate = parseFloat(opt.getAttribute('data-tax-rate') || 0);
                const taxType = opt.getAttribute('data-tax-type') || 'exclusive';
                
                if (isTaxable) {
                    hasTaxableItem = true;
                    const itemTaxRate = taxRate > 0 ? taxRate : defaultTaxRate;
                    
                    if (taxType === 'inclusive') {
                        // Tax included
                        const itemTax = line - (line / (1 + itemTaxRate / 100));
                        totalTax += itemTax;
                    } else {
                        // Tax exclusive
                        const itemTax = line * (itemTaxRate / 100);
                        totalTax += itemTax;
                    }
                }
            }
        });
        
        const discount = parseFloat(document.getElementById('discountInput')?.value || 0);
        
        // Apply discount proportionally to tax
        if (discount > 0 && subtotal > 0) {
            const discountRatio = Math.max(subtotal - discount, 0) / subtotal;
            totalTax = totalTax * discountRatio;
        }
        
        const subtotalAfterDiscount = Math.max(subtotal - discount, 0);
        const total = subtotalAfterDiscount + totalTax;
        
        // Display tax row if applicable
        const taxRow = document.getElementById('taxRow');
        if (hasTaxableItem && totalTax > 0) {
            taxRow.style.display = 'flex';
            document.getElementById('taxRateDisplay').textContent = `(${taxLabel})`;
            document.getElementById('taxLabel').textContent = '$' + totalTax.toFixed(2);
        } else {
            taxRow.style.display = 'none';
        }
        
        document.getElementById('subtotalLabel').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
        document.getElementById('totalItems').textContent = document.querySelectorAll('.item').length;
    }

    document.getElementById('addItem').addEventListener('click', addItem);

    // first row
    addItem();
</script>
@endsection
