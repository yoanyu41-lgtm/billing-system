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
                    <span class="text-slate-600 font-medium" lang="km">កែប្រែកម្រោងបង់រំលស់</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-inner">
                        <i class="fas fa-edit"></i>
                    </span>
                    <span lang="km">កែប្រែកម្រោងបង់រំលស់</span>
                    <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs font-bold font-mono">
                        #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}
                    </span>
                </h1>
                <p class="text-xs text-slate-500 mt-1" lang="km">
                    កែប្រែប្រាក់បង់រំលស់ ប្រាក់កក់ អត្រាការប្រាក់ រយៈពេល ឬ ស្ថានភាពកិច្ចសន្យា
                </p>
            </div>
            
            <a href="{{ route('installments.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl hover:bg-slate-50 transition shadow-sm text-sm">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>{{ __('app.back') }}</span>
            </a>
        </div>

        <form method="POST" action="{{ route('installments.update', $installment) }}" id="editInstallmentForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- Left 2 Cols: Form -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Customer & Product Section -->
                    <div class="ic-card p-6">
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
                                <div class="relative">
                                    <i class="fas fa-user ic-field-icon"></i>
                                    <select name="customer_id" id="customerId" class="ic-input pl-9 font-medium" required>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}" {{ old('customer_id', $installment->customer_id) == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }} ({{ $c->phone ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Product Selection -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    ទំនិញ / Product <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-box ic-field-icon"></i>
                                    <select name="product_id" id="productId" class="ic-input pl-9 font-medium" required>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" 
                                                    data-price="{{ $p->price }}" 
                                                    data-taxable="{{ $p->is_taxable ? '1' : '0' }}"
                                                    data-taxrate="{{ $p->tax_rate }}"
                                                    data-taxtype="{{ $p->tax_type ?? 'exclusive' }}"
                                                    {{ old('product_id', $installment->product_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} (${{ number_format($p->price, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Financial Terms -->
                    <div class="ic-card p-6">
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
                                    <i class="fas fa-dollar-sign ic-field-icon"></i>
                                    @php
                                        $initialTotalPrice = old('total_price', ($installment->subtotal_before_tax > 0 ? $installment->subtotal_before_tax : $installment->total_price));
                                    @endphp
                                    <input type="number" step="0.01" min="0" name="total_price" id="totalPrice"
                                           value="{{ number_format((float)$initialTotalPrice, 2, '.', '') }}"
                                           required class="ic-input pl-9 font-bold text-slate-900" placeholder="0.00">
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
                                    <i class="fas fa-hand-holding-usd ic-field-icon"></i>
                                    <input type="number" step="0.01" min="0" name="down_payment" id="downPayment"
                                           value="{{ number_format((float)old('down_payment', $installment->down_payment), 2, '.', '') }}"
                                           required class="ic-input pl-9 font-bold text-emerald-700" placeholder="0.00">
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
                                    <i class="fas fa-percent ic-field-icon"></i>
                                    <input type="number" step="0.01" min="0" name="interest_rate" id="interestRate"
                                           value="{{ old('interest_rate', $installment->interest_rate) }}"
                                           class="ic-input pl-9 font-semibold" placeholder="0.00">
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
                                    <i class="fas fa-calendar-alt ic-field-icon"></i>
                                    <input type="number" min="1" name="duration_months" id="durationMonths"
                                           value="{{ old('duration_months', $installment->duration_months) }}"
                                           required class="ic-input pl-9 font-semibold" placeholder="12">
                                </div>
                                @error('duration_months')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- First Payment Date -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    កាលបរិច្ឆេទបង់ដំបូង / First Payment Due Date
                                </label>
                                <div class="relative">
                                    <i class="fas fa-calendar-day ic-field-icon"></i>
                                    <input type="date" name="first_payment_date" id="firstPaymentDate"
                                           value="{{ old('first_payment_date', $installment->first_payment_date ? \Carbon\Carbon::parse($installment->first_payment_date)->format('Y-m-d') : ($installment->next_due_date ? \Carbon\Carbon::parse($installment->next_due_date)->format('Y-m-d') : '')) }}"
                                           class="ic-input">
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    {{ __('app.status') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-info-circle ic-field-icon"></i>
                                    <select name="status" required class="ic-input pl-9 font-bold">
                                        <option value="active" {{ old('status', $installment->status) == 'active' ? 'selected' : '' }} class="text-green-600">Active (សកម្ម)</option>
                                        <option value="cancelled" {{ old('status', $installment->status) == 'cancelled' ? 'selected' : '' }} class="text-red-600">Cancelled (បោះបង់)</option>
                                        <option value="completed" {{ old('status', $installment->status) == 'completed' || old('status', $installment->status) == 'paid' ? 'selected' : '' }} class="text-blue-600">Completed (បានបញ្ចប់)</option>
                                    </select>
                                </div>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                            <a href="{{ route('installments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition shadow-sm">
                                {{ __('app.cancel') }}
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md transition cursor-pointer flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                <span lang="km">{{ __('app.save') }}</span>
                            </button>
                        </div>
                    </div>

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

                        <div class="mt-4 p-3 rounded-lg bg-slate-50 border border-slate-200/60 text-[11px] text-slate-500 leading-relaxed space-y-1">
                            <div class="font-bold text-slate-700">Contract Meta:</div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Customer:</span>
                                <span class="font-semibold text-slate-800" id="metaCustomerName">{{ $installment->customer?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Product:</span>
                                <span class="font-semibold text-slate-800" id="metaProductName">{{ $installment->product?->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    const totalPriceInput = document.getElementById('totalPrice');
    const downPaymentInput = document.getElementById('downPayment');
    const interestRateInput = document.getElementById('interestRate');
    const durationInput = document.getElementById('durationMonths');
    const productSelect = document.getElementById('productId');
    const customerSelect = document.getElementById('customerId');

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
    const metaCustomerName = document.getElementById('metaCustomerName');
    const metaProductName = document.getElementById('metaProductName');

    const exchangeRate = {{ $exchangeRate ?? 4100 }};
    const taxEnabled = {{ \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1' ? 'true' : 'false' }};
    const defaultTaxRate = {{ (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0) }};

    function formatCurrency(val) {
        return '$' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function calculateInstallment() {
        const subtotalInput = parseFloat(totalPriceInput.value) || 0;
        const downPayment = parseFloat(downPaymentInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;
        const duration = parseInt(durationInput.value) || 1;

        const selectedProductOption = productSelect.options[productSelect.selectedIndex];
        const isTaxable = selectedProductOption ? selectedProductOption.getAttribute('data-taxable') === '1' : false;
        const prodTaxRate = selectedProductOption ? parseFloat(selectedProductOption.getAttribute('data-taxrate')) || 0 : 0;
        const prodTaxType = selectedProductOption ? selectedProductOption.getAttribute('data-taxtype') : 'exclusive';

        let taxAmount = 0;
        let taxRate = 0;
        let totalPrice = subtotalInput;

        if (taxEnabled && isTaxable) {
            taxRate = prodTaxRate > 0 ? prodTaxRate : defaultTaxRate;
            if (prodTaxType === 'inclusive') {
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

        if (customerSelect.options[customerSelect.selectedIndex]) {
            metaCustomerName.innerText = customerSelect.options[customerSelect.selectedIndex].text.split('(')[0].trim();
        }
        if (selectedProductOption) {
            metaProductName.innerText = selectedProductOption.text.split('(')[0].trim();
        }
    }

    [totalPriceInput, downPaymentInput, interestRateInput, durationInput, productSelect, customerSelect].forEach(el => {
        if (el) el.addEventListener('input', calculateInstallment);
        if (el) el.addEventListener('change', calculateInstallment);
    });

    calculateInstallment();
</script>

@endsection