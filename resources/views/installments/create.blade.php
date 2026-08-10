@extends('layouts.app')

@section('content')

<style>
    /* ─── Page Theme ─── */
    .ic-page-bg {
        background: #f8fafc;
        min-height: 100vh;
    }

    /* ─── Form Card ─── */
    .ic-card {
        background: #ffffff;
        border-radius: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 10px 25px -5px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }

    /* ─── Section Divider Label ─── */
    .ic-section-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #4f46e5;
        margin-bottom: 1.25rem;
    }
    .ic-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, #e2e8f0, transparent);
    }

    /* ─── Inputs ─── */
    .ic-field-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        font-size: 0.85rem;
    }
    .ic-input {
        width: 100%;
        border: 1.5px solid #cbd5e1;
        border-radius: 0.75rem;
        padding: 0.65rem 1rem 0.65rem 2.5rem;
        font-size: 0.9rem;
        color: #0f172a;
        background: #ffffff;
        transition: all 0.18s ease-in-out;
        outline: none;
    }
    .ic-input:focus {
        border-color: #6366f1;
        background: #ffffff;
        box-shadow: 0 0 0 3.5px rgba(99,102,241,0.12);
    }
    .ic-input.ic-has-error { border-color: #ef4444; }
    .ic-input::placeholder { color: #94a3b8; }

    .ic-prefix {
        position: absolute;
        left: 0; top: 0; bottom: 0;
        display: flex; align-items: center;
        padding: 0 0.65rem 0 0.875rem;
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 600;
        pointer-events: none;
    }
    .ic-suffix {
        position: absolute;
        right: 0; top: 0; bottom: 0;
        display: flex; align-items: center;
        padding: 0 0.875rem;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 600;
        pointer-events: none;
    }
    .ic-input-with-prefix { padding-left: 2.2rem; }
    .ic-input-with-suffix { padding-right: 2.75rem; }

    /* ─── Search Dropdowns ─── */
    .ic-dropdown-toggle {
        position: absolute; right: 0.75rem; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: #94a3b8;
        padding: 0.25rem;
    }
    .ic-dropdown-toggle:hover { color: #4f46e5; }
    .ic-dropdown-toggle.open i { transform: rotate(180deg); }

    .ic-dropdown-menu {
        position: absolute; z-index: 9999;
        left: 0; top: calc(100% + 6px);
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 1rem;
        box-shadow: 0 20px 50px rgba(15,23,42,0.12);
        max-height: 18rem;
        overflow-y: auto;
        min-width: 100%;
        animation: ic-dropdown-in 0.15s ease;
    }
    @keyframes ic-dropdown-in {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .ic-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.65rem 0.875rem;
        cursor: pointer;
        transition: background 0.12s;
        border-bottom: 1px solid #f1f5f9;
    }
    .ic-option:last-child { border-bottom: none; }
    .ic-option:hover { background: #f1f5f9; }
    .ic-option.selected { background: #e0e7ff; }

    /* ─── Calculator Panel ─── */
    .ic-calc-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        position: sticky;
        top: 5.5rem;
    }
    .ic-calc-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
    .ic-calc-row:last-of-type { border-bottom: none; }
</style>

<div class="ic-page-bg px-4 py-8">
    <div class="max-w-6xl mx-auto">

        <!-- Clean Header Bar -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <nav class="text-xs text-slate-400 mb-1 flex items-center gap-1.5">
                    <a href="{{ route('installments.index') }}" class="hover:text-indigo-600 transition font-medium">{{ __('app.installment') }}</a>
                    <span>/</span>
                    <span class="text-slate-600 font-medium" lang="km">បង្កើតកម្រោងបង់រំលស់</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-inner">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </span>
                    <span lang="km">បង្កើតកម្រោងបង់រំលស់ថ្មី</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1" lang="km">
                    កំណត់លក្ខខណ្ឌកិច្ចសន្យា ជ្រើសរើសអតិថិជន និង គណនាប្រាក់បង់រំលស់ប្រចាំខែ
                </p>
            </div>
            
            <a href="{{ route('installments.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl hover:bg-slate-50 transition shadow-sm text-sm">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>{{ __('app.back') }}</span>
            </a>
        </div>

        <!-- Main Form Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Left 2 Cols: Form -->
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('installments.store') }}" id="installmentForm">
                    @csrf

                    <!-- Section 1: Customer & Product -->
                    <div class="ic-card p-6 mb-6">
                        <div class="ic-section-label" lang="km">
                            <i class="fas fa-users"></i>
                            <span>ព័ត៌មានអតិថិជន និង ទំនិញ / Customer & Product</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Customer Selection -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    អតិថិជន / Customer <span class="text-red-500">*</span>
                                </label>
                                <input type="hidden" name="customer_id" id="customerId" value="{{ old('customer_id') }}" required>
                                <div class="relative customer-select-container">
                                    <div class="relative">
                                        <i class="fas fa-search ic-field-icon"></i>
                                        <input type="text" id="customerSearchInput"
                                               class="ic-input {{ $errors->has('customer_id') ? 'ic-has-error' : '' }}"
                                               placeholder="{{ __('app.select') }} {{ __('app.customer') }}..."
                                               autocomplete="off"
                                               onfocus="openCustomerDropdown(this)"
                                               oninput="filterCustomerDropdown(this)">
                                        <button type="button" tabindex="-1" class="ic-dropdown-toggle customer-dropdown-toggle" onclick="toggleCustomerDropdown(this)">
                                            <i class="fas fa-chevron-down customer-dropdown-arrow"></i>
                                        </button>
                                    </div>
                                    <div class="customer-dropdown-menu ic-dropdown-menu hidden" style="min-width:100%;"></div>
                                </div>
                                @error('customer_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Product Selection -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    ទំនិញ / Product <span class="text-red-500">*</span>
                                </label>
                                <input type="hidden" name="product_id" id="productId" value="{{ old('product_id') }}" required>
                                <div class="relative product-select-container">
                                    <div class="relative">
                                        <i class="fas fa-search ic-field-icon"></i>
                                        <input type="text" id="productSearchInput"
                                               class="ic-input {{ $errors->has('product_id') ? 'ic-has-error' : '' }}"
                                               placeholder="{{ __('app.select') }} {{ __('app.product') }}..."
                                               autocomplete="off"
                                               onfocus="openProductDropdown(this)"
                                               oninput="filterProductDropdown(this)">
                                        <button type="button" tabindex="-1" class="ic-dropdown-toggle product-dropdown-toggle" onclick="toggleProductDropdown(this)">
                                            <i class="fas fa-chevron-down product-dropdown-arrow"></i>
                                        </button>
                                    </div>
                                    <div class="product-dropdown-menu ic-dropdown-menu hidden" style="min-width:100%;"></div>
                                </div>
                                @error('product_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Pricing & Terms -->
                    <div class="ic-card p-6 mb-6">
                        <div class="ic-section-label" lang="km">
                            <i class="fas fa-calculator"></i>
                            <span>ព័ត៌មានតម្លៃ និង លក្ខខណ្ឌហិរញ្ញវត្ថុ / Financial Terms</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Total Price -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    {{ __('app.total_price') }} ($) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="ic-prefix">$</span>
                                    <input type="number" step="0.01" min="0" name="total_price" id="totalPrice"
                                           value="{{ old('total_price') }}" required
                                           class="ic-input ic-input-with-prefix font-bold text-slate-900 {{ $errors->has('total_price') ? 'ic-has-error' : '' }}"
                                           placeholder="0.00">
                                </div>
                                @error('total_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Down Payment -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    {{ __('app.down_payment') }} ($) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="ic-prefix">$</span>
                                    <input type="number" step="0.01" min="0" name="down_payment" id="downPayment"
                                           value="{{ old('down_payment', 0) }}" required
                                           class="ic-input ic-input-with-prefix font-bold text-emerald-700 {{ $errors->has('down_payment') ? 'ic-has-error' : '' }}"
                                           placeholder="0.00">
                                </div>
                                @error('down_payment')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Interest Rate -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    {{ __('app.interest_rate') }} (%)
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0" name="interest_rate" id="interestRate"
                                           value="{{ old('interest_rate', 0) }}"
                                           class="ic-input ic-input-with-suffix font-semibold {{ $errors->has('interest_rate') ? 'ic-has-error' : '' }}"
                                           placeholder="0.00">
                                    <span class="ic-suffix">%</span>
                                </div>
                                @error('interest_rate')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    {{ __('app.duration_months') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" min="1" name="duration_months" id="durationMonths"
                                           value="{{ old('duration_months', 12) }}" required
                                           class="ic-input ic-input-with-suffix font-semibold {{ $errors->has('duration_months') ? 'ic-has-error' : '' }}"
                                           placeholder="12">
                                    <span class="ic-suffix">{{ __('app.months') ?? 'mo' }}</span>
                                </div>
                                @error('duration_months')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- First Payment Date -->
                            <div class="sm:col-span-2">
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    កាលបរិច្ឆេទបង់ប្រាក់ដំបូង / First Payment Due Date
                                </label>
                                <div class="relative">
                                    <i class="fas fa-calendar-day ic-field-icon"></i>
                                    <input type="date" name="first_payment_date" id="firstPaymentDate"
                                           value="{{ old('first_payment_date', date('Y-m-d')) }}"
                                           class="ic-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action Bar -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('installments.index') }}"
                           class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition shadow-sm">
                            {{ __('app.cancel') }}
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md transition cursor-pointer flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <span lang="km">{{ __('app.save') }}</span>
                        </button>
                    </div>

                </form>
            </div>

            <!-- Right Col: Clean Calculator Panel -->
            <div class="lg:col-span-1">
                <div class="ic-calc-card p-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm leading-tight" lang="km">គណនាតម្លៃបង់រំលស់</h3>
                                <p class="text-[11px] text-slate-400" lang="km">គណនាផ្ទាល់ក្នុងប្រព័ន្ធ</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="ic-calc-row">
                            <span class="text-slate-500" lang="km">តម្លៃទំនិញដើម</span>
                            <span class="font-bold text-slate-900" id="previewSubtotal">$0.00</span>
                        </div>
                        <div class="ic-calc-row" id="taxRow" style="display:none;">
                            <span class="text-slate-500">{{ __('app.tax') }} (<span id="taxRateLabel">0</span>%)</span>
                            <span class="font-bold text-slate-900" id="previewTax">$0.00</span>
                        </div>
                        <div class="ic-calc-row pt-2 border-t border-slate-100">
                            <span class="font-bold text-slate-800" lang="km">តម្លៃសរុបរួម</span>
                            <span class="font-extrabold text-indigo-950 text-sm" id="previewTotal">$0.00</span>
                        </div>
                        <div class="ic-calc-row">
                            <span class="text-slate-500" lang="km">ប្រាក់កក់</span>
                            <span class="font-bold text-emerald-600" id="previewDownPayment">$0.00</span>
                        </div>
                        <div class="ic-calc-row">
                            <span class="text-slate-500" lang="km">សមតុល្យដើម</span>
                            <span class="font-bold text-slate-800" id="previewPrincipal">$0.00</span>
                        </div>
                        <div class="ic-calc-row">
                            <span class="text-slate-500" lang="km">ការប្រាក់ប្រចាំខែ</span>
                            <span class="font-bold text-purple-600" id="previewInterest">$0.00</span>
                        </div>
                        <div class="ic-calc-row pt-2 border-t border-slate-100">
                            <span class="font-bold text-slate-700" lang="km">តុល្យភាពបំណុលសរុប</span>
                            <span class="font-bold text-amber-600 text-sm" id="previewRemaining">$0.00</span>
                        </div>
                    </div>

                    <!-- Highlight Monthly Payment Box -->
                    <div class="mt-5 p-4 rounded-xl bg-indigo-50/80 border border-indigo-100 text-center">
                        <span class="block text-[11px] font-bold text-indigo-600 uppercase tracking-wider mb-1" lang="km">
                            ប្រាក់រំលស់ប្រចាំខែ / Monthly Payment
                        </span>
                        <div class="text-2xl font-black text-indigo-700" id="previewMonthly">$0.00</div>
                        <div class="text-[11px] font-semibold text-slate-500 mt-1" id="previewMonthlyKhr">≈ 0 KHR</div>
                    </div>

                    <div class="mt-4 p-3 rounded-lg bg-slate-50 border border-slate-200/60 text-[11px] text-slate-500 leading-relaxed">
                        <i class="fas fa-info-circle text-indigo-500 mr-1"></i>
                        <span lang="km">អត្រាប្តូរប្រាក់បច្ចុប្បន្ន: 1 USD = {{ number_format($exchangeRate ?? 4100) }} KHR</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Pass Backend Data to JS --}}
<script>
    const customersData = {!! json_encode($customers->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'phone' => $c->phone ?? 'N/A',
        'code' => $c->customer_code ?? ''
    ])) !!};

    const productsData = {!! json_encode($products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'price' => (float) $p->price,
        'code' => $p->code ?? $p->sku ?? '',
        'stock' => (int) $p->quantity,
        'is_taxable' => (bool) ($p->is_taxable ?? false),
        'tax_rate' => (float) ($p->tax_rate ?? 0),
        'tax_type' => $p->tax_type ?? 'exclusive'
    ])) !!};

    const taxEnabled = {{ \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1' ? 'true' : 'false' }};
    const defaultTaxRate = {{ (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0) }};
    const exchangeRate = {{ $exchangeRate ?? 4100 }};

    let selectedProduct = null;

    // Elements
    const totalPriceInput = document.getElementById('totalPrice');
    const downPaymentInput = document.getElementById('downPayment');
    const interestRateInput = document.getElementById('interestRate');
    const durationInput = document.getElementById('durationMonths');

    const previewSubtotal = document.getElementById('previewSubtotal');
    const previewTax = document.getElementById('previewTax');
    const previewTotal = document.getElementById('previewTotal');
    const previewDownPayment = document.getElementById('previewDownPayment');
    const taxRow = document.getElementById('taxRow');
    const taxRateLabel = document.getElementById('taxRateLabel');
    const previewPrincipal = document.getElementById('previewPrincipal');
    const previewInterest = document.getElementById('previewInterest');
    const previewRemaining = document.getElementById('previewRemaining');
    const previewMonthly = document.getElementById('previewMonthly');
    const previewMonthlyKhr = document.getElementById('previewMonthlyKhr');

    function formatCurrency(val) {
        return '$' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function calculateInstallment() {
        const subtotalInput = parseFloat(totalPriceInput.value) || 0;
        const downPayment = parseFloat(downPaymentInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;
        const duration = parseInt(durationInput.value) || 1;

        let taxAmount = 0;
        let taxRate = 0;
        let totalPrice = subtotalInput;

        if (taxEnabled && selectedProduct && selectedProduct.is_taxable) {
            taxRate = selectedProduct.tax_rate > 0 ? selectedProduct.tax_rate : defaultTaxRate;
            if (selectedProduct.tax_type === 'inclusive') {
                taxAmount = subtotalInput - (subtotalInput / (1 + taxRate / 100));
            } else {
                taxAmount = subtotalInput * (taxRate / 100);
                totalPrice = subtotalInput + taxAmount;
            }
            taxRow.style.display = 'flex';
            taxRateLabel.innerText = Number(taxRate).toFixed(0);
        } else {
            taxRow.style.display = 'none';
        }

        const principal = Math.max(0, totalPrice - downPayment);
        const monthlyInterest = duration > 0 ? ((principal * interestRate / 100) / 12) : 0;
        const monthlyPayment = duration > 0 ? ((principal / duration) + monthlyInterest) : 0;
        const remaining = (monthlyPayment * duration);
        const monthlyKhr = monthlyPayment * exchangeRate;

        previewSubtotal.innerText = formatCurrency(subtotalInput);
        previewTax.innerText = formatCurrency(taxAmount);
        previewTotal.innerText = formatCurrency(totalPrice);
        previewDownPayment.innerText = formatCurrency(downPayment);
        previewPrincipal.innerText = formatCurrency(principal);
        previewInterest.innerText = formatCurrency(monthlyInterest);
        previewRemaining.innerText = formatCurrency(remaining);
        previewMonthly.innerText = formatCurrency(monthlyPayment) + ' / ខែ';
        previewMonthlyKhr.innerText = '≈ ' + Math.round(monthlyKhr).toLocaleString('en-US') + ' KHR';
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Customer Dropdown logic
    function buildCustomerList(filter = '') {
        const menu = document.querySelector('.customer-dropdown-menu');
        const q = filter.toLowerCase().trim();
        const list = customersData.filter(c => c.name.toLowerCase().includes(q) || c.phone.includes(q) || c.code.toLowerCase().includes(q));

        let html = '<div class="p-2 border-b border-slate-100 bg-slate-50"><input type="text" class="w-full text-xs px-3 py-1.5 rounded-md border border-slate-200 outline-none" placeholder="ស្វែងរកអតិថិជន..." oninput="filterCustomerDropdown(this)" onclick="event.stopPropagation()"></div>';
        if (list.length === 0) {
            html += '<div class="p-3 text-xs text-slate-400 text-center">មិនមានទិន្នន័យអតិថិជន</div>';
        } else {
            list.forEach(c => {
                html += `<div class="ic-option" onclick="selectCustomer(${c.id})">
                    <div>
                        <div class="ic-option-name font-semibold text-slate-800">${escapeHtml(c.name)}</div>
                        <div class="text-[11px] text-slate-400">${escapeHtml(c.phone)}</div>
                    </div>
                </div>`;
            });
        }
        menu.innerHTML = html;
    }

    function openCustomerDropdown(input) {
        document.querySelectorAll('.ic-dropdown-menu').forEach(m => m.classList.add('hidden'));
        const menu = document.querySelector('.customer-dropdown-menu');
        buildCustomerList('');
        menu.classList.remove('hidden');
    }

    function filterCustomerDropdown(input) {
        buildCustomerList(input.value);
    }

    function toggleCustomerDropdown(btn) {
        const menu = document.querySelector('.customer-dropdown-menu');
        if (menu.classList.contains('hidden')) {
            openCustomerDropdown(document.getElementById('customerSearchInput'));
        } else {
            menu.classList.add('hidden');
        }
    }

    function selectCustomer(id) {
        const cust = customersData.find(c => c.id == id);
        if (!cust) return;
        document.getElementById('customerId').value = cust.id;
        document.getElementById('customerSearchInput').value = cust.name;
        document.querySelector('.customer-dropdown-menu').classList.add('hidden');
    }

    // Product Dropdown logic
    function buildProductList(filter = '') {
        const menu = document.querySelector('.product-dropdown-menu');
        const q = filter.toLowerCase().trim();
        const list = productsData.filter(p => p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q));

        let html = '<div class="p-2 border-b border-slate-100 bg-slate-50"><input type="text" class="w-full text-xs px-3 py-1.5 rounded-md border border-slate-200 outline-none" placeholder="ស្វែងរកទំនិញ..." oninput="filterProductDropdown(this)" onclick="event.stopPropagation()"></div>';
        if (list.length === 0) {
            html += '<div class="p-3 text-xs text-slate-400 text-center">មិនមានទិន្នន័យទំនិញ</div>';
        } else {
            list.forEach(p => {
                html += `<div class="ic-option" onclick="selectProduct(${p.id})">
                    <div>
                        <div class="ic-option-name font-semibold text-slate-800">${escapeHtml(p.name)}</div>
                        <div class="text-[11px] text-slate-400">${p.code ? 'Code: ' + escapeHtml(p.code) : ''}</div>
                    </div>
                    <span class="font-extrabold text-indigo-600 text-xs px-2 py-0.5 bg-indigo-50 rounded border border-indigo-100">$${p.price.toFixed(2)}</span>
                </div>`;
            });
        }
        menu.innerHTML = html;
    }

    function openProductDropdown(input) {
        document.querySelectorAll('.ic-dropdown-menu').forEach(m => m.classList.add('hidden'));
        const menu = document.querySelector('.product-dropdown-menu');
        buildProductList('');
        menu.classList.remove('hidden');
    }

    function filterProductDropdown(input) {
        buildProductList(input.value);
    }

    function toggleProductDropdown(btn) {
        const menu = document.querySelector('.product-dropdown-menu');
        if (menu.classList.contains('hidden')) {
            openProductDropdown(document.getElementById('productSearchInput'));
        } else {
            menu.classList.add('hidden');
        }
    }

    function selectProduct(id) {
        const prod = productsData.find(p => p.id == id);
        if (!prod) return;
        selectedProduct = prod;
        document.getElementById('productId').value = prod.id;
        document.getElementById('productSearchInput').value = prod.name;
        totalPriceInput.value = prod.price.toFixed(2);
        document.querySelector('.product-dropdown-menu').classList.add('hidden');
        calculateInstallment();
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.customer-select-container') && !e.target.closest('.product-select-container')) {
            document.querySelectorAll('.ic-dropdown-menu').forEach(m => m.classList.add('hidden'));
        }
    });

    [totalPriceInput, downPaymentInput, interestRateInput, durationInput].forEach(el => {
        if (el) el.addEventListener('input', calculateInstallment);
    });

    // Handle old inputs if validation failed
    @if(old('customer_id'))
        const oldCust = customersData.find(c => c.id == {{ old('customer_id') }});
        if (oldCust) selectCustomer(oldCust.id, oldCust.name);
    @endif
    @if(old('product_id'))
        const oldProd = productsData.find(p => p.id == {{ old('product_id') }});
        if (oldProd) {
            selectedProduct = oldProd;
            document.getElementById('productId').value = oldProd.id;
            document.getElementById('productSearchInput').value = oldProd.name;
        }
    @endif

    calculateInstallment();
</script>

@endsection