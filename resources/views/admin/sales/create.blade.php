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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
        let options = `<option value="">${T.selectProduct}</option>`;
        productsData.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}" data-taxable="${p.is_taxable}" data-tax-rate="${p.tax_rate}" data-tax-type="${p.tax_type}">${p.name} (${T.stock}: ${p.stock})</option>`;
        });
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
                <div class="sm:col-span-5">
                    <label class="block text-xs font-medium text-slate-500 mb-1">${T.product}</label>
                    <select name="items[${index}][product_id]" required onchange="onProductChange(this)"
                            class="w-full px-3 py-2 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 item-product">
                        ${options}
                    </select>
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
                <div class="sm:col-span-3">
                    <label class="block text-xs font-medium text-slate-500 mb-1">${T.subtotal} ($)</label>
                    <input type="text" readonly value="0.00"
                           class="w-full px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 bg-slate-100 border border-slate-200 rounded-lg text-right item-subtotal">
                </div>
            </div>`;
        return div;
    }

    function addItem() {
        document.getElementById('items').appendChild(itemRow(idx));
        idx++;
        updateNumbers();
        updateRemoveButtons();
        calculateTotal();
    }

    function onProductChange(select) {
        const opt = select.options[select.selectedIndex];
        const price = opt.getAttribute('data-price');
        const row = select.closest('.item');
        const priceInput = row.querySelector('.item-price');
        if (price) priceInput.value = parseFloat(price).toFixed(2);
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
            const productSelect = item.querySelector('.item-product');
            const opt = productSelect ? productSelect.options[productSelect.selectedIndex] : null;

            const line = qty * price;
            const subInput = item.querySelector('.item-subtotal');
            if (subInput) subInput.value = line.toFixed(2);
            originalSubtotal += line;

            if (taxEnabled && opt) {
                const isTaxable = opt.getAttribute('data-taxable') === '1' || opt.getAttribute('data-taxable') === 'true';
                const taxRate = parseFloat(opt.getAttribute('data-tax-rate') || 0);
                const taxType = opt.getAttribute('data-tax-type') || 'exclusive';

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
