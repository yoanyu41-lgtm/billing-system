@extends('layouts.app')

@section('content')
<div class="content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    {{-- Top Bar / Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                <a href="{{ route('admin.sales.index') }}" class="hover:text-blue-600 transition flex items-center gap-1" style="text-decoration: none;">
                    <i class="fas fa-arrow-left text-[10px]"></i> {{ __('app.sales_list') }}
                </a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">{{ __('app.new_direct_sale') }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm border border-blue-100">
                    <i class="fas fa-cash-register"></i>
                </span>
                {{ __('app.new_direct_sale') }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('app.direct_sale_subtitle') }}</p>
        </div>

        <a href="{{ route('admin.sales.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition" style="text-decoration: none;">
            <i class="fas fa-list text-slate-400"></i> {{ __('app.sales_list') }}
        </a>
    </div>

    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.sales.store') }}" id="saleForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- Left Column: Form Details (8 cols) --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- Customer Info Card --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <span class="w-7 h-7 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                            <i class="fas fa-user"></i>
                        </span>
                        <h2 class="text-base font-bold text-slate-800">{{ __('app.customer_information') }}</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="relative customer-select-container">
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                {{ __('app.select_customer') ?? 'Select Existing Customer' }}
                            </label>
                            <input type="hidden" name="customer_id" id="customerId">
                            <div class="relative">
                                <input type="text" id="customerSelectSearch"
                                       class="w-full px-3.5 py-2.5 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition placeholder:text-slate-400 truncate"
                                       placeholder="{{ __('app.search_customer') ?? 'Search existing customer...' }}"
                                       autocomplete="off"
                                       onfocus="openCustomerDropdown(this)"
                                       oninput="filterCustomerDropdown(this)">
                                <button type="button" tabindex="-1" onclick="toggleCustomerDropdown(this)" 
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 customer-dropdown-arrow"></i>
                                </button>
                            </div>
                            <div class="customer-dropdown-menu absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto hidden divide-y divide-slate-100 text-xs sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                {{ __('app.customer_name') }} <span class="text-slate-400 normal-case font-normal">({{ __('app.optional') }})</span>
                            </label>
                            <input type="text" name="customer_name" id="customerName" value="{{ old('customer_name') }}"
                                   class="w-full px-3.5 py-2.5 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition placeholder:text-slate-400"
                                   placeholder="{{ __('app.enter_customer_name') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                {{ __('app.customer_phone') }}
                            </label>
                            <input type="text" name="customer_phone" id="customerPhone" value="{{ old('customer_phone') }}"
                                   class="w-full px-3.5 py-2.5 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition placeholder:text-slate-400"
                                   placeholder="012 345 678">
                        </div>
                    </div>
                </div>

                {{-- Sale Items Card --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                            <h2 class="text-base font-bold text-slate-800">{{ __('app.sale_items') }}</h2>
                        </div>
                        <button type="button" id="addItem"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition shadow-xs">
                            <i class="fas fa-plus"></i> {{ __('app.add_product') }}
                        </button>
                    </div>

                    <div id="items" class="space-y-3.5"></div>
                </div>
            </div>

            {{-- Right Column: Receipt Summary (4 cols) --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden sticky top-6">
                    
                    {{-- Card Header --}}
                    <div class="bg-slate-900 px-5 py-4 text-white flex items-center justify-between">
                        <h3 class="text-sm font-bold flex items-center gap-2 tracking-wide">
                            <i class="fas fa-receipt text-blue-400"></i> {{ __('app.receipt') }}
                        </h3>
                        <span class="text-[11px] font-mono bg-slate-800 text-slate-300 px-2 py-0.5 rounded border border-slate-700">
                            DIRECT SALE
                        </span>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Sale Date & Payment Method --}}
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                    {{ __('app.sale_date') }}
                                </label>
                                <input type="date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}"
                                       class="w-full px-3 py-2 text-sm text-slate-800 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                    {{ __('app.payment_method') }}
                                </label>
                                <select name="payment_method"
                                        class="w-full px-3 py-2 text-sm text-slate-800 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 font-medium">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->name }}" {{ old('payment_method', 'Cash') == $pm->name ? 'selected' : '' }}>
                                            {{ $pm->name === 'Cash' ? __('app.cash') : ($pm->name === 'Credit Card' ? 'កាតឥណទាន (Credit Card)' : $pm->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Dotted Divider --}}
                        <div class="border-t border-dashed border-slate-200 pt-3 space-y-2.5 text-xs sm:text-sm">
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="font-medium">{{ __('app.subtotal') }}</span>
                                <div class="text-right font-semibold text-slate-900">
                                    <span id="subtotalLabel" class="block">$0.00</span>
                                    <span id="subtotalLabelRiel" class="block text-[11px] text-slate-400 font-normal">0 ៛</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-slate-600" id="taxRow" style="display: none;">
                                <span class="font-medium">{{ __('app.tax') }} <span id="taxRateDisplay" class="text-slate-400 text-xs"></span></span>
                                <div class="text-right font-semibold text-slate-900">
                                    <span id="taxLabel" class="block">$0.00</span>
                                    <span id="taxLabelRiel" class="block text-[11px] text-slate-400 font-normal">0 ៛</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-slate-600 pt-1">
                                <span class="font-medium self-center">{{ __('app.discount') }}</span>
                                <div class="text-right">
                                    <div class="inline-flex items-center gap-1 bg-slate-50 border border-slate-300 rounded-md px-2 py-1">
                                        <span class="text-slate-400 font-semibold text-xs">$</span>
                                        <input type="number" step="0.01" min="0" name="discount" id="discountInput"
                                               value="{{ old('discount', 0) }}"
                                               class="w-16 text-right text-xs font-bold text-slate-800 bg-transparent border-none p-0 focus:outline-none focus:ring-0"
                                               onchange="calculateTotal()" oninput="calculateTotal()">
                                    </div>
                                    <span id="discountLabelRiel" class="block text-[11px] text-slate-400 font-normal mt-0.5">0 ៛</span>
                                </div>
                            </div>
                        </div>

                        {{-- Grand Total Accent Box --}}
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-xl p-4 shadow-sm space-y-1">
                            <div class="flex items-center justify-between text-xs font-medium text-blue-100">
                                <span>{{ __('app.grand_total') }}</span>
                                <span class="text-[11px] bg-white/20 px-2 py-0.5 rounded text-white font-mono">
                                    <span id="totalItems">0</span> {{ __('app.items') ?? 'items' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <div id="grandTotal" class="text-2xl sm:text-3xl font-black tracking-tight font-mono">$0.00</div>
                                <div id="grandTotalRiel" class="text-xs font-semibold text-blue-200 mt-0.5">0 ៛</div>
                            </div>
                        </div>

                        {{-- Note --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                {{ __('app.note') ?? 'Note' }}
                            </label>
                            <textarea name="note" rows="2"
                                      class="w-full px-3 py-2 text-xs sm:text-sm text-slate-800 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 resize-none placeholder:text-slate-400"
                                      placeholder="...">{{ old('note') }}</textarea>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-check-circle text-base"></i> {{ __('app.complete_sale') }}
                        </button>
                    </div>
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

    const customersData = {!! json_encode(($customers ?? collect())->map(function($c) {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'phone' => $c->phone ?? ''
        ];
    })) !!};

    const taxEnabled = {{ \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1' ? 'true' : 'false' }};
    const defaultTaxRate = {{ (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0) }};
    const taxLabel = '{{ \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT' }}';
    const exchangeRate = {{ (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100) }};

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
        const div = document.createElement('div');
        div.className = 'item bg-slate-50/80 border border-slate-200 rounded-xl p-3.5 sm:p-4 transition hover:border-slate-300 relative';
        const num = document.querySelectorAll('.item').length + 1;
        div.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-md text-xs font-bold font-mono item-num">
                    #${num}
                </span>
                <button type="button" onclick="removeItem(this)" class="text-slate-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition text-xs remove-item" title="Remove">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6 relative product-select-container">
                    <label class="block text-xs font-medium text-slate-500 mb-1">${T.product}</label>
                    <input type="hidden" name="items[${index}][product_id]" class="item-product-id" required>
                    <div class="relative">
                        <input type="text" 
                               class="w-full px-3 py-2 pr-8 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 item-product-search truncate" 
                               placeholder="${T.selectProduct}"
                               autocomplete="off"
                               onfocus="openProductDropdown(this)"
                               oninput="filterProductDropdown(this)">
                        <button type="button" tabindex="-1" onclick="toggleProductDropdown(this)" 
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1">
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200 dropdown-arrow"></i>
                        </button>
                    </div>
                    <div class="product-dropdown-menu absolute z-50 left-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl max-h-80 overflow-y-auto hidden divide-y divide-slate-100 text-xs sm:text-sm min-w-full sm:min-w-[520px] max-w-2xl border-slate-300">
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">${T.quantity}</label>
                    <input type="number" name="items[${index}][quantity]" value="1" min="1" required
                           class="w-full px-3 py-2 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-lg text-center focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 item-qty"
                           oninput="calculateTotal()">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">${T.unitPrice} ($)</label>
                    <input type="number" step="0.01" min="0" name="items[${index}][price]" required
                           class="w-full px-3 py-2 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-lg text-right focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 item-price"
                           placeholder="0.00" oninput="calculateTotal()">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">${T.subtotal} ($)</label>
                    <input type="text" readonly value="0.00"
                           class="w-full px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 bg-slate-100 border border-slate-200 rounded-lg text-right item-subtotal">
                </div>
            </div>`;
        return div;
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function renderProductOptions(container, searchKeyword = '') {
        const menu = container.querySelector('.product-dropdown-menu');
        const hiddenInput = container.querySelector('.item-product-id');
        const selectedId = hiddenInput ? hiddenInput.value : '';

        const keyword = searchKeyword.trim().toLowerCase();
        const filtered = productsData.filter(p => {
            return p.name.toLowerCase().includes(keyword);
        });

        if (filtered.length === 0) {
            menu.innerHTML = `<div class="p-3.5 text-slate-400 text-center italic text-xs">No matching products found</div>`;
            return;
        }

        let html = '';
        filtered.forEach(p => {
            const isSelected = String(p.id) === String(selectedId);
            const stockBadge = p.stock > 5 
                ? `<span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">${T.stock}: ${p.stock}</span>`
                : `<span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">${T.stock}: ${p.stock}</span>`;
            
            html += `
                <div class="product-option px-3.5 py-2.5 hover:bg-blue-50/90 ${isSelected ? 'bg-blue-50 font-semibold text-blue-700' : ''} cursor-pointer flex items-center justify-between gap-3 transition"
                     data-id="${p.id}"
                     data-name="${escapeHtml(p.name)}"
                     data-price="${p.price}"
                     data-stock="${p.stock}"
                     data-taxable="${p.is_taxable ? '1' : '0'}"
                     data-tax-rate="${p.tax_rate || 0}"
                     data-tax-type="${p.tax_type || 'exclusive'}"
                     onclick="selectProductOption(this)">
                    <span class="font-medium text-slate-800 text-xs sm:text-sm whitespace-normal leading-snug">${escapeHtml(p.name)}</span>
                    ${stockBadge}
                </div>
            `;
        });

        menu.innerHTML = html;
    }

    function openProductDropdown(input) {
        const container = input.closest('.product-select-container');
        closeAllProductDropdowns();
        closeAllCustomerDropdowns();
        const menu = container.querySelector('.product-dropdown-menu');
        const arrow = container.querySelector('.dropdown-arrow');
        renderProductOptions(container, input.value);
        menu.classList.remove('hidden');
        if (arrow) arrow.classList.add('rotate-180');
    }

    function filterProductDropdown(input) {
        const container = input.closest('.product-select-container');
        const hiddenInput = container.querySelector('.item-product-id');
        const menu = container.querySelector('.product-dropdown-menu');
        
        if (hiddenInput.value) {
            hiddenInput.value = '';
            hiddenInput.removeAttribute('data-taxable');
            hiddenInput.removeAttribute('data-tax-rate');
            hiddenInput.removeAttribute('data-tax-type');
            const row = container.closest('.item');
            const priceInput = row.querySelector('.item-price');
            if (priceInput) priceInput.value = '';
            calculateTotal();
        }
        
        renderProductOptions(container, input.value);
        menu.classList.remove('hidden');
    }

    function toggleProductDropdown(button) {
        const container = button.closest('.product-select-container');
        const input = container.querySelector('.item-product-search');
        const menu = container.querySelector('.product-dropdown-menu');
        if (menu.classList.contains('hidden')) {
            openProductDropdown(input);
            input.focus();
        } else {
            menu.classList.add('hidden');
            const arrow = container.querySelector('.dropdown-arrow');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    }

    function selectProductOption(optElement) {
        const container = optElement.closest('.product-select-container');
        const hiddenInput = container.querySelector('.item-product-id');
        const searchInput = container.querySelector('.item-product-search');
        const menu = container.querySelector('.product-dropdown-menu');
        const arrow = container.querySelector('.dropdown-arrow');

        const id = optElement.getAttribute('data-id');
        const name = optElement.getAttribute('data-name');
        const price = optElement.getAttribute('data-price');
        const taxable = optElement.getAttribute('data-taxable');
        const taxRate = optElement.getAttribute('data-tax-rate');
        const taxType = optElement.getAttribute('data-tax-type');

        hiddenInput.value = id;
        hiddenInput.setAttribute('data-taxable', taxable);
        hiddenInput.setAttribute('data-tax-rate', taxRate);
        hiddenInput.setAttribute('data-tax-type', taxType);
        
        searchInput.value = name;
        searchInput.title = name;

        const row = container.closest('.item');
        const priceInput = row.querySelector('.item-price');
        if (priceInput) {
            priceInput.value = parseFloat(price).toFixed(2);
        }

        menu.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');

        calculateTotal();
    }

    function closeAllProductDropdowns() {
        document.querySelectorAll('.product-dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
        document.querySelectorAll('.dropdown-arrow').forEach(arrow => {
            arrow.classList.remove('rotate-180');
        });
    }

    // Customer Dropdown logic
    function renderCustomerOptions(container, searchKeyword = '') {
        const menu = container.querySelector('.customer-dropdown-menu');
        const selectedId = document.getElementById('customerId')?.value || '';
        const keyword = searchKeyword.trim().toLowerCase();

        const filtered = customersData.filter(c => {
            return c.name.toLowerCase().includes(keyword) || (c.phone && c.phone.includes(keyword));
        });

        if (filtered.length === 0) {
            menu.innerHTML = `<div class="p-3 text-slate-400 text-center italic text-xs">No matching customers found</div>`;
            return;
        }

        let html = '';
        filtered.forEach(c => {
            const isSelected = String(c.id) === String(selectedId);
            html += `
                <div class="customer-option px-3 py-2 hover:bg-blue-50/80 ${isSelected ? 'bg-blue-50 font-semibold' : ''} cursor-pointer flex items-center justify-between gap-2 transition"
                     data-id="${c.id}"
                     data-name="${escapeHtml(c.name)}"
                     data-phone="${escapeHtml(c.phone)}"
                     onclick="selectCustomerOption(this)">
                    <span class="font-medium text-slate-800 truncate">${escapeHtml(c.name)}</span>
                    <span class="shrink-0 text-[11px] text-slate-500">${escapeHtml(c.phone)}</span>
                </div>
            `;
        });

        menu.innerHTML = html;
    }

    function openCustomerDropdown(input) {
        const container = input.closest('.customer-select-container');
        closeAllProductDropdowns();
        closeAllCustomerDropdowns();
        const menu = container.querySelector('.customer-dropdown-menu');
        const arrow = container.querySelector('.customer-dropdown-arrow');
        renderCustomerOptions(container, input.value);
        menu.classList.remove('hidden');
        if (arrow) arrow.classList.add('rotate-180');
    }

    function filterCustomerDropdown(input) {
        const container = input.closest('.customer-select-container');
        const menu = container.querySelector('.customer-dropdown-menu');
        document.getElementById('customerId').value = '';
        renderCustomerOptions(container, input.value);
        menu.classList.remove('hidden');
    }

    function toggleCustomerDropdown(button) {
        const container = button.closest('.customer-select-container');
        const input = container.querySelector('#customerSelectSearch');
        const menu = container.querySelector('.customer-dropdown-menu');
        if (menu.classList.contains('hidden')) {
            openCustomerDropdown(input);
            input.focus();
        } else {
            menu.classList.add('hidden');
            const arrow = container.querySelector('.customer-dropdown-arrow');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    }

    function selectCustomerOption(optElement) {
        const container = optElement.closest('.customer-select-container');
        const searchInput = container.querySelector('#customerSelectSearch');
        const menu = container.querySelector('.customer-dropdown-menu');
        const arrow = container.querySelector('.customer-dropdown-arrow');

        const id = optElement.getAttribute('data-id');
        const name = optElement.getAttribute('data-name');
        const phone = optElement.getAttribute('data-phone');

        document.getElementById('customerId').value = id;
        searchInput.value = name;
        document.getElementById('customerName').value = name;
        document.getElementById('customerPhone').value = phone;

        menu.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    }

    function closeAllCustomerDropdowns() {
        document.querySelectorAll('.customer-dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
        document.querySelectorAll('.customer-dropdown-arrow').forEach(arrow => {
            arrow.classList.remove('rotate-180');
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.product-select-container')) {
            closeAllProductDropdowns();
        }
        if (!e.target.closest('.customer-select-container')) {
            closeAllCustomerDropdowns();
        }
    });

    function addItem() {
        document.getElementById('items').appendChild(itemRow(idx));
        idx++;
        updateNumbers();
        updateRemoveButtons();
        calculateTotal();
    }

    function removeItem(btn) {
        btn.closest('.item').remove();
        updateNumbers();
        updateRemoveButtons();
        calculateTotal();
    }

    function updateNumbers() {
        document.querySelectorAll('.item-num').forEach((el, i) => {
            el.textContent = '#' + (i + 1);
        });
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
        let originalSubtotal = 0;
        let subtotalBeforeTax = 0;
        let totalTax = 0;
        let hasTaxableItem = false;
        let hasInclusiveTax = false;

        document.querySelectorAll('.item').forEach(item => {
            const qty = parseFloat(item.querySelector('.item-qty')?.value || 0);
            const price = parseFloat(item.querySelector('.item-price')?.value || 0);
            const hiddenInput = item.querySelector('.item-product-id');

            const line = qty * price;
            const subInput = item.querySelector('.item-subtotal');
            if (subInput) subInput.value = line.toFixed(2);
            originalSubtotal += line;

            if (taxEnabled && hiddenInput && hiddenInput.value) {
                const isTaxable = hiddenInput.getAttribute('data-taxable') === '1' || hiddenInput.getAttribute('data-taxable') === 'true';
                const taxRate = parseFloat(hiddenInput.getAttribute('data-tax-rate') || 0);
                const taxType = hiddenInput.getAttribute('data-tax-type') || 'exclusive';

                if (isTaxable) {
                    hasTaxableItem = true;
                    const itemTaxRate = taxRate > 0 ? taxRate : defaultTaxRate;

                    if (taxType === 'inclusive') {
                        const itemTax = line - (line / (1 + itemTaxRate / 100));
                        totalTax += itemTax;
                        subtotalBeforeTax += (line - itemTax);
                        hasInclusiveTax = true;
                    } else {
                        const itemTax = line * (itemTaxRate / 100);
                        totalTax += itemTax;
                        subtotalBeforeTax += line;
                    }
                } else {
                    subtotalBeforeTax += line;
                }
            } else {
                subtotalBeforeTax += line;
            }
        });

        const discount = parseFloat(document.getElementById('discountInput')?.value || 0);
        const totalBeforeDiscount = subtotalBeforeTax + totalTax;
        const total = Math.max(totalBeforeDiscount - discount, 0);

        let finalTax = totalTax;
        if (discount > 0 && totalBeforeDiscount > 0) {
            const discountRatio = total / totalBeforeDiscount;
            finalTax = totalTax * discountRatio;
        }

        const taxRow = document.getElementById('taxRow');
        if (hasTaxableItem && finalTax > 0) {
            taxRow.style.display = 'flex';
            document.getElementById('taxRateDisplay').textContent = hasInclusiveTax ? `(${taxLabel} Included)` : `(${taxLabel})`;
            document.getElementById('taxLabel').textContent = '$' + finalTax.toFixed(2);
            const rielTax = Math.round(finalTax * exchangeRate);
            document.getElementById('taxLabelRiel').textContent = rielTax.toLocaleString('en-US') + ' ៛';
        } else {
            taxRow.style.display = 'none';
        }

        document.getElementById('subtotalLabel').textContent = '$' + originalSubtotal.toFixed(2);
        const rielSubtotal = Math.round(originalSubtotal * exchangeRate);
        document.getElementById('subtotalLabelRiel').textContent = rielSubtotal.toLocaleString('en-US') + ' ៛';

        const rielDiscount = Math.round(discount * exchangeRate);
        document.getElementById('discountLabelRiel').textContent = rielDiscount.toLocaleString('en-US') + ' ៛';

        document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
        const rielTotal = Math.round(total * exchangeRate);
        document.getElementById('grandTotalRiel').textContent = rielTotal.toLocaleString('en-US') + ' ៛';
        document.getElementById('totalItems').textContent = document.querySelectorAll('.item').length;
    }

    document.getElementById('addItem').addEventListener('click', addItem);

    // Initial item row
    addItem();
</script>
@endsection
