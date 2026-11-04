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
        box-shadow: 0 20px 50px rgba(15,23,42,0.15);
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
        padding: 0.55rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .ic-calc-row:last-of-type { border-bottom: none; }

    /* ─── Plan Cards ─── */
    .plan-card {
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        padding: 0.875rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        position: relative;
    }
    .plan-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .plan-card.active {
        border-color: #4f46e5;
        background: #f5f3ff;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }

    /* ─── Product Item Box ─── */
    .product-item-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem;
        background: #ffffff;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .product-item-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
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
                    កំណត់លក្ខខណ្ឌកិច្ចសន្យា ជ្រើសរើសអតិថិជន ទំនិញច្រើនមុខ និង គណនាប្រាក់បង់រំលស់ប្រចាំខែ
                </p>
            </div>
            
            <a href="{{ route('installments.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl hover:bg-slate-50 transition shadow-sm text-sm">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>{{ __('app.back') }}</span>
            </a>
        </div>

        @if(session('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm flex items-center gap-2 mb-6">
                <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Left 2 Cols: Form -->
            <div class="lg:col-span-2 space-y-6">
                <form method="POST" action="{{ route('installments.store') }}" id="installmentForm">
                    @csrf

                    <!-- Section 1: Customer Selection -->
                    <div class="ic-card p-6 mb-6">
                        <div class="ic-section-label" lang="km">
                            <i class="fas fa-user-check"></i>
                            <span>ព័ត៌មានអតិថិជន / Customer Information</span>
                        </div>

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

                            <!-- Selected Customer Information Badge -->
                            <div id="selectedCustomerCard" class="hidden mt-3 p-3.5 rounded-xl bg-indigo-50/70 border border-indigo-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm" id="selectedCustomerName">-</div>
                                        <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                            <span id="selectedCustomerPhone">-</span>
                                            <span class="text-slate-300">•</span>
                                            <span id="selectedCustomerCode" class="text-indigo-600 font-mono">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="selectedCustomerActiveBadge" class="hidden">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                                        <i class="fas fa-layer-group text-[10px]"></i>
                                        <span id="selectedCustomerActiveCount">0</span> គម្រោងសកម្ម
                                    </span>
                                </div>
                            </div>

                            @error('customer_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 2: Financed Products (Multi-Product Builder) -->
                    <div class="ic-card p-6 mb-6">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-600">
                                <i class="fas fa-boxes-stacked"></i>
                                <span lang="km">ទំនិញបង់រំលស់ / Financed Products</span>
                            </div>
                            <button type="button" id="addProductRowBtn" onclick="addProductRow()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition shadow-xs cursor-pointer">
                                <i class="fas fa-plus"></i>
                                <span lang="km">+ បន្ថែមទំនិញ</span>
                            </button>
                        </div>

                        <!-- Product Rows Container -->
                        <div id="productRowsContainer" class="space-y-3.5">
                            <!-- Rows dynamically populated by JS -->
                        </div>

                        <!-- Products Summary Footer inside Card -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between text-xs text-slate-600 gap-2">
                            <div class="flex items-center gap-3">
                                <span>ចំនួនទំនិញ: <b id="totalItemsCount" class="text-slate-900 font-bold">0</b> មុខ</span>
                                <span class="text-slate-300">|</span>
                                <span>សរុបបរិមាណ: <b id="totalQtyCount" class="text-slate-900 font-bold">0</b> គ្រឿង</span>
                            </div>
                            <div class="font-semibold text-slate-700">
                                តម្លៃទំនិញសរុប: <span id="summaryItemsTotal" class="text-indigo-600 font-bold text-sm">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Financial Terms -->
                    <div class="ic-card p-6 mb-6">
                        <div class="ic-section-label" lang="km">
                            <i class="fas fa-calculator"></i>
                            <span>ព័ត៌មានតម្លៃ និង លក្ខខណ្ឌហិរញ្ញវត្ថុ / Financial Terms</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Total Price (Auto calculated from items) -->
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    {{ __('app.total_price') }} ($) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="ic-prefix">$</span>
                                    <input type="number" step="0.01" min="0" name="total_price" id="totalPrice"
                                           value="{{ old('total_price', 0) }}" required readonly
                                           class="ic-input ic-input-with-prefix font-bold text-slate-900 bg-slate-100/80 cursor-not-allowed select-none {{ $errors->has('total_price') ? 'ic-has-error' : '' }}"
                                           placeholder="0.00" title="គណនាដោយស្វ័យប្រវត្តិតាមបញ្ជីទំនិញខាងលើ">
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1" lang="km">
                                    * គណនាដោយស្វ័យប្រវត្តិតាមផលបូកតម្លៃទំនិញទាំងអស់ខាងលើ
                                </p>
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
                                    {{ __('app.interest_rate') }} (% / {{ __('app.duration_unit') }})
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

                            <!-- Duration Months -->
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
                                
                                <!-- Quick Duration Buttons -->
                                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                    <span class="text-[11px] font-medium text-slate-400 mr-1" lang="km">រហ័ស:</span>
                                    @foreach([3, 6, 9, 12, 18, 24] as $m)
                                    <button type="button" onclick="setQuickDuration({{ $m }})"
                                            class="px-2.5 py-1 text-xs font-bold rounded-lg border border-slate-200 bg-slate-50 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition cursor-pointer">
                                        {{ $m }} ខែ
                                    </button>
                                    @endforeach
                                </div>

                                @error('duration_months')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Interest Type Selector (Visual Interactive Cards) -->
                            <div class="sm:col-span-2">
                                <label class="block text-slate-700 text-xs font-bold mb-2 uppercase tracking-wide" lang="km">
                                    <i class="fas fa-layer-group text-indigo-600 mr-1"></i>
                                    ជ្រើសរើសប្រភេទគម្រោងបង់រំលស់ / Repayment Plan Type <span class="text-red-500">*</span>
                                </label>
                                
                                <input type="hidden" name="interest_type" id="interestType" value="{{ old('interest_type', 'flat') }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-2" id="planCardsContainer">
                                    <!-- Plan 1: Flat Rate -->
                                    <div class="plan-card {{ old('interest_type', 'flat') === 'flat' ? 'active' : '' }}" onclick="selectPlan('flat', this)">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black shrink-0">1</span>
                                            <span class="font-bold text-slate-900 text-xs">រំលស់ការប្រាក់ថេរ (Flat Rate)</span>
                                        </div>
                                    </div>

                                    <!-- Plan 2: Amortization / Declining Balance EMI -->
                                    <div class="plan-card {{ old('interest_type') === 'amortization' ? 'active' : '' }}" onclick="selectPlan('amortization', this)">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-black shrink-0">2</span>
                                            <span class="font-bold text-slate-900 text-xs">ការរំលស់ថយដើមថយការ (Declining Balance / EMI)</span>
                                        </div>
                                    </div>

                                    <!-- Plan 3: 0% Interest -->
                                    <div class="plan-card {{ old('interest_type') === 'zero_interest' ? 'active' : '' }}" onclick="selectPlan('zero_interest', this)">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-xs font-black shrink-0">3</span>
                                            <span class="font-bold text-slate-900 text-xs">រំលស់ការប្រាក់ ០% (0% Interest)</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200/70 text-xs flex items-center gap-2">
                                    <i class="fas fa-info-circle text-indigo-500"></i>
                                    <span class="text-slate-600 font-medium" id="interestTypeDesc" lang="km">
                                        * គណនាការប្រាក់ថេរលើប្រាក់ដើមដំបូងរហូតដល់ផុតកិច្ចសន្យា
                                    </span>
                                </div>
                            </div>

                            <!-- First Payment Date -->
                            <div>
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

                            <!-- Payoff Policy for this contract -->
                            @php
                                $currentPayoffMin = (int) (\App\Models\Setting::where('key', 'payoff_min_month')->value('value') ?? 0);
                            @endphp
                            <div>
                                <label class="block text-slate-700 text-xs font-bold mb-1.5 uppercase tracking-wide" lang="km">
                                    លក្ខខណ្ឌបង់ផ្តាច់ / Payoff Min Months
                                </label>
                                <div class="relative">
                                    <i class="fas fa-hand-holding-usd ic-field-icon"></i>
                                    <input type="number" min="0" step="1" name="payoff_min_month" id="payoffMinMonth"
                                           value="{{ old('payoff_min_month', $currentPayoffMin) }}"
                                           class="ic-input ic-input-with-suffix font-semibold"
                                           placeholder="0">
                                    <span class="ic-suffix">{{ __('app.months') ?? 'mo' }}</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1" lang="km">
                                    * ចំនួនខែអប្បបរមាត្រូវបង់មុនអនុញ្ញាតបង់ផ្តាច់ (០ = អាចបង់ផ្តាច់បានគ្រប់ពេល)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action Bar -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                        <div class="text-xs text-amber-700 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 flex items-center gap-2">
                            <i class="fas fa-clock text-amber-600"></i>
                            <span lang="km">{{ app()->getLocale() === 'km' ? 'គម្រោងដែលបានបង្កើតនឹងមានស្ថានភាព «រង់ចាំការឯកភាព» (Pending) ហើយតម្រូវឱ្យ Admin ចុចឯកភាពទើបចូលជាធរមាន។' : 'New installment contracts are created in Pending status and require Admin approval to be officially activated.' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('installments.index') }}"
                               class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition shadow-sm">
                                {{ __('app.cancel') }}
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md transition cursor-pointer flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                <span lang="km">{{ __('app.save') }}</span>
                            </button>
                        </div>
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
                            <span class="text-slate-500" lang="km">តម្លៃទំនិញដើម (Subtotal)</span>
                            <span class="font-bold text-slate-900" id="previewSubtotal">$0.00</span>
                        </div>
                        <div class="ic-calc-row" id="taxRow" style="display:none;">
                            <span class="text-slate-500">{{ __('app.tax') }}</span>
                            <span class="font-bold text-slate-900" id="previewTax">$0.00</span>
                        </div>
                        <div class="ic-calc-row pt-2 border-t border-slate-100">
                            <span class="font-bold text-slate-800" lang="km">តម្លៃសរុបរួម (Total Price)</span>
                            <span class="font-extrabold text-indigo-950 text-sm" id="previewTotal">$0.00</span>
                        </div>
                        <div class="ic-calc-row">
                            <span class="text-slate-500" lang="km">ប្រាក់កក់ (Down Payment)</span>
                            <span class="font-bold text-emerald-600" id="previewDownPayment">$0.00</span>
                        </div>
                        <div class="ic-calc-row">
                            <span class="text-slate-500" lang="km">សមតុល្យដើម (Principal)</span>
                            <span class="font-bold text-slate-800" id="previewPrincipal">$0.00</span>
                        </div>
                        <div class="ic-calc-row">
                            <span class="text-slate-500" lang="km">ការប្រាក់ប្រចាំខែ (Monthly Interest)</span>
                            <span class="font-bold text-purple-600" id="previewInterest">$0.00</span>
                        </div>
                        <div class="ic-calc-row pt-2 border-t border-slate-100">
                            <span class="font-bold text-slate-700" lang="km">តុល្យភាពបំណុលសរុប (Total Debt)</span>
                            <span class="font-bold text-amber-600 text-sm" id="previewRemaining">$0.00</span>
                        </div>
                    </div>

                    <!-- Highlight Monthly Payment Box -->
                    <div class="mt-5 p-4 rounded-xl bg-indigo-50/80 border border-indigo-100 text-center">
                        <span class="block text-[11px] font-bold text-indigo-600 uppercase tracking-wider mb-1" lang="km">
                            ប្រាក់រំលស់ប្រចាំខែ / Monthly Payment
                        </span>
                        <div class="text-2xl font-black text-indigo-700" id="previewMonthly">$0.00</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Pass Backend Data to JS --}}
<script>
    const customersData = {!! json_encode($customers->map(fn($c) => [
        'id'           => $c->id,
        'name'         => $c->name,
        'phone'        => $c->phone ?? 'N/A',
        'code'         => $c->customer_code ?? '',
        'active_count' => (int) ($c->active_installments_count ?? 0)
    ])) !!};

    const productsData = {!! json_encode($products->map(fn($p) => [
        'id'         => $p->id,
        'name'       => $p->name,
        'price'      => (float) $p->price,
        'code'       => $p->code ?? $p->sku ?? '',
        'stock'      => (int) $p->stock,
        'is_taxable' => (bool) ($p->is_taxable ?? false),
        'tax_rate'   => (float) ($p->tax_rate ?? 0),
        'tax_type'   => $p->tax_type ?? 'exclusive'
    ])) !!};

    const taxEnabled = {{ \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1' ? 'true' : 'false' }};
    const defaultTaxRate = {{ (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0) }};
    const exchangeRate = {{ $exchangeRate ?? 4100 }};

    // Form elements
    const totalPriceInput = document.getElementById('totalPrice');
    const downPaymentInput = document.getElementById('downPayment');
    const interestRateInput = document.getElementById('interestRate');
    const durationInput = document.getElementById('durationMonths');
    const interestTypeInput = document.getElementById('interestType');
    const interestTypeDesc = document.getElementById('interestTypeDesc');

    // Preview elements
    const previewSubtotal = document.getElementById('previewSubtotal');
    const previewTax = document.getElementById('previewTax');
    const previewTotal = document.getElementById('previewTotal');
    const previewDownPayment = document.getElementById('previewDownPayment');
    const taxRow = document.getElementById('taxRow');
    const previewPrincipal = document.getElementById('previewPrincipal');
    const previewInterest = document.getElementById('previewInterest');
    const previewRemaining = document.getElementById('previewRemaining');
    const previewMonthly = document.getElementById('previewMonthly');

    function formatCurrency(val) {
        return '$' + Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

    /* ─── Plan Cards Logic ─── */
    function selectPlan(type, el) {
        if (interestTypeInput) {
            interestTypeInput.value = type;
        }
        document.querySelectorAll('.plan-card').forEach(card => card.classList.remove('active'));
        if (el) {
            el.classList.add('active');
        }
        calculateInstallment();
    }

    function setQuickDuration(months) {
        if (durationInput) {
            durationInput.value = months;
            calculateInstallment();
        }
    }

    /* ─── Customer Dropdown Logic ─── */
    function buildCustomerList(filter = '') {
        const menu = document.querySelector('.customer-dropdown-menu');
        const q = filter.toLowerCase().trim();
        const list = customersData.filter(c => 
            c.name.toLowerCase().includes(q) || 
            c.phone.toLowerCase().includes(q) || 
            c.code.toLowerCase().includes(q)
        );

        let html = '<div class="p-2 border-b border-slate-100 bg-slate-50"><input type="text" class="w-full text-xs px-3 py-1.5 rounded-md border border-slate-200 outline-none" placeholder="ស្វែងរកអតិថិជន..." oninput="filterCustomerDropdown(this)" onclick="event.stopPropagation()"></div>';
        if (list.length === 0) {
            html += '<div class="p-3 text-xs text-slate-400 text-center">មិនមានទិន្នន័យអតិថិជន</div>';
        } else {
            list.forEach(c => {
                const activeBadge = c.active_count > 0 
                    ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 whitespace-nowrap"><i class="fas fa-layer-group text-[9px] mr-1"></i>${c.active_count} គម្រោងសកម្ម</span>`
                    : `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-50 text-slate-500 border border-slate-200 whitespace-nowrap">អត់ទាន់មានគម្រោង</span>`;

                html += `<div class="ic-option" onclick="selectCustomer(${c.id})">
                    <div class="min-w-0 pr-2">
                        <div class="ic-option-name font-semibold text-slate-800 truncate">${escapeHtml(c.name)}</div>
                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                            <span>${escapeHtml(c.phone)}</span>
                            ${c.code ? `<span>• ${escapeHtml(c.code)}</span>` : ''}
                        </div>
                    </div>
                    <div class="shrink-0">
                        ${activeBadge}
                    </div>
                </div>`;
            });
        }
        menu.innerHTML = html;
    }

    function openCustomerDropdown(input) {
        closeAllDropdowns();
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

        // Update selected customer information badge
        const card = document.getElementById('selectedCustomerCard');
        document.getElementById('selectedCustomerName').innerText = cust.name;
        document.getElementById('selectedCustomerPhone').innerText = cust.phone || 'N/A';
        document.getElementById('selectedCustomerCode').innerText = cust.code ? ('#' + cust.code) : '';
        
        const badgeWrap = document.getElementById('selectedCustomerActiveBadge');
        if (cust.active_count > 0) {
            document.getElementById('selectedCustomerActiveCount').innerText = cust.active_count;
            badgeWrap.classList.remove('hidden');
        } else {
            badgeWrap.classList.add('hidden');
        }
        card.classList.remove('hidden');
    }

    /* ─── Multi-Product Line Items Builder ─── */
    let itemRowCounter = 0;

    function addProductRow(prefillData = null) {
        const container = document.getElementById('productRowsContainer');
        const rowIndex = itemRowCounter++;

        const rowDiv = document.createElement('div');
        rowDiv.className = 'product-item-card relative item-row';
        rowDiv.setAttribute('data-row-index', rowIndex);

        rowDiv.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <!-- Product Selector (6 cols on md) -->
                <div class="md:col-span-6 relative product-select-container">
                    <label class="block text-slate-700 text-[11px] font-bold mb-1 uppercase tracking-wider" lang="km">
                        ទំនិញ / Product <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="items[${rowIndex}][product_id]" class="row-product-id" value="" required>
                    
                    <div class="relative">
                        <i class="fas fa-box ic-field-icon"></i>
                        <input type="text" class="ic-input row-product-search text-xs"
                               placeholder="ជ្រើសរើសទំនិញ..."
                               autocomplete="off"
                               onfocus="openRowProductDropdown(this)"
                               oninput="filterRowProductDropdown(this)">
                        <button type="button" tabindex="-1" class="ic-dropdown-toggle" onclick="toggleRowProductDropdown(this)">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="product-dropdown-menu ic-dropdown-menu hidden" style="min-width: 100%;"></div>
                </div>

                <!-- Quantity (2 cols on md) -->
                <div class="md:col-span-2">
                    <label class="block text-slate-700 text-[11px] font-bold mb-1 uppercase tracking-wider" lang="km">
                        ចំនួន / Qty <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" min="1" step="1" name="items[${rowIndex}][quantity]" value="1" required
                               class="ic-input text-xs font-bold text-center row-quantity px-2"
                               oninput="handleRowChange(this)">
                    </div>
                </div>

                <!-- Unit Price (2 cols on md) -->
                <div class="md:col-span-2">
                    <label class="block text-slate-700 text-[11px] font-bold mb-1 uppercase tracking-wider" lang="km">
                        តម្លៃឯកតា ($) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="ic-prefix text-xs">$</span>
                        <input type="number" min="0" step="0.01" name="items[${rowIndex}][price]" value="0.00" required
                               class="ic-input ic-input-with-prefix text-xs font-bold text-right row-price"
                               placeholder="0.00"
                               oninput="handleRowChange(this)">
                    </div>
                </div>

                <!-- Row Subtotal & Delete Action (2 cols on md) -->
                <div class="md:col-span-2 flex items-center justify-between gap-2">
                    <div class="w-full">
                        <label class="block text-slate-500 text-[11px] font-medium mb-1 uppercase tracking-wider text-right" lang="km">
                            សរុប ($)
                        </label>
                        <div class="h-[38px] px-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-end font-bold text-slate-900 text-xs row-subtotal-display">
                            $0.00
                        </div>
                    </div>
                    
                    <div class="pt-5 shrink-0">
                        <button type="button" onclick="removeProductRow(this)"
                                class="w-9 h-9 rounded-xl border border-rose-200 text-rose-500 hover:bg-rose-50 hover:text-rose-700 flex items-center justify-center transition cursor-pointer remove-row-btn"
                                title="លុបទំនិញនេះ">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row-stock-warning text-[11px] text-amber-600 mt-1 hidden"></div>
        `;

        container.appendChild(rowDiv);

        if (prefillData) {
            const prod = productsData.find(p => p.id == prefillData.product_id);
            if (prod) {
                rowDiv.querySelector('.row-product-id').value = prod.id;
                rowDiv.querySelector('.row-product-search').value = prod.name;
                rowDiv.querySelector('.row-quantity').value = prefillData.quantity || 1;
                rowDiv.querySelector('.row-price').value = prefillData.price !== undefined ? Number(prefillData.price).toFixed(2) : prod.price.toFixed(2);
            }
        }

        updateRowSubtotal(rowDiv);
        updateAllCalculations();
        refreshRemoveButtons();
    }

    function removeProductRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length <= 1) {
            alert('កិច្ចសន្យាត្រូវមានទំនិញយ៉ាងតិច ១ មុខ! / Installment requires at least 1 product.');
            return;
        }
        const card = btn.closest('.item-row');
        card.remove();
        updateAllCalculations();
        refreshRemoveButtons();
    }

    function refreshRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(r => {
            const btn = r.querySelector('.remove-row-btn');
            if (btn) {
                if (rows.length === 1) {
                    btn.classList.add('opacity-40', 'cursor-not-allowed');
                } else {
                    btn.classList.remove('opacity-40', 'cursor-not-allowed');
                }
            }
        });
    }

    function handleRowChange(input) {
        const rowDiv = input.closest('.item-row');
        const prodId = rowDiv.querySelector('.row-product-id').value;
        const qtyInput = rowDiv.querySelector('.row-quantity');
        const stockWarn = rowDiv.querySelector('.row-stock-warning');

        if (prodId) {
            const prod = productsData.find(p => p.id == prodId);
            if (prod) {
                const qty = parseInt(qtyInput.value) || 0;
                if (qty > prod.stock) {
                    stockWarn.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ចំនួនលើសស្តុកដែលនៅសល់ (ស្តុកនៅសល់: ${prod.stock})`;
                    stockWarn.classList.remove('hidden');
                } else {
                    stockWarn.classList.add('hidden');
                }
            }
        }

        updateRowSubtotal(rowDiv);
        updateAllCalculations();
    }

    function updateRowSubtotal(rowDiv) {
        const qty = parseInt(rowDiv.querySelector('.row-quantity').value) || 0;
        const price = parseFloat(rowDiv.querySelector('.row-price').value) || 0;
        const subtotal = Math.max(0, qty * price);
        const display = rowDiv.querySelector('.row-subtotal-display');
        if (display) {
            display.innerText = formatCurrency(subtotal);
        }
    }

    /* ─── Product Dropdown for each row ─── */
    function renderRowProductList(container, keyword = '') {
        const menu = container.querySelector('.product-dropdown-menu');
        const q = keyword.toLowerCase().trim();
        const list = productsData.filter(p => 
            p.name.toLowerCase().includes(q) || 
            p.code.toLowerCase().includes(q)
        );

        let html = '<div class="p-2 border-b border-slate-100 bg-slate-50"><input type="text" class="w-full text-xs px-3 py-1.5 rounded-md border border-slate-200 outline-none" placeholder="ស្វែងរកទំនិញ..." oninput="filterRowProductDropdown(this)" onclick="event.stopPropagation()"></div>';
        if (list.length === 0) {
            html += '<div class="p-3 text-xs text-slate-400 text-center">មិនមានទិន្នន័យទំនិញ</div>';
        } else {
            list.forEach(p => {
                const isOutOfStock = p.stock <= 0;
                const stockBadge = isOutOfStock
                    ? `<span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-600 font-semibold">អស់ស្តុក</span>`
                    : `<span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 font-medium border border-emerald-100">ស្តុក: ${p.stock}</span>`;
                
                html += `<div class="ic-option ${isOutOfStock ? 'opacity-50 cursor-not-allowed bg-slate-50' : ''}" onclick="${isOutOfStock ? `alert('ទំនិញនេះបានអស់ពីស្តុកហើយ!')` : `selectRowProduct(this, ${p.id})`}">
                    <div class="min-w-0 pr-2">
                        <div class="ic-option-name font-semibold text-slate-800 truncate">${escapeHtml(p.name)}</div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px] text-slate-400 font-mono">${p.code ? escapeHtml(p.code) : ''}</span>
                            ${stockBadge}
                        </div>
                    </div>
                    <span class="font-extrabold text-indigo-600 text-xs px-2 py-0.5 bg-indigo-50 rounded border border-indigo-100 shrink-0">$${p.price.toFixed(2)}</span>
                </div>`;
            });
        }
        menu.innerHTML = html;
    }

    function openRowProductDropdown(input) {
        closeAllDropdowns();
        const container = input.closest('.product-select-container');
        const menu = container.querySelector('.product-dropdown-menu');
        renderRowProductList(container, input.value);
        menu.classList.remove('hidden');
    }

    function filterRowProductDropdown(input) {
        const container = input.closest('.product-select-container');
        renderRowProductList(container, input.value);
    }

    function toggleRowProductDropdown(btn) {
        const container = btn.closest('.product-select-container');
        const menu = container.querySelector('.product-dropdown-menu');
        if (menu.classList.contains('hidden')) {
            openRowProductDropdown(container.querySelector('.row-product-search'));
        } else {
            menu.classList.add('hidden');
        }
    }

    function selectRowProduct(optionEl, prodId) {
        const prod = productsData.find(p => p.id == prodId);
        if (!prod) return;
        const rowDiv = optionEl.closest('.item-row');
        rowDiv.querySelector('.row-product-id').value = prod.id;
        rowDiv.querySelector('.row-product-search').value = prod.name;
        
        // Auto prefill price if 0 or empty
        const priceInput = rowDiv.querySelector('.row-price');
        priceInput.value = prod.price.toFixed(2);

        // Max quantity check
        const qtyInput = rowDiv.querySelector('.row-quantity');
        qtyInput.max = prod.stock;

        rowDiv.querySelector('.product-dropdown-menu').classList.add('hidden');
        updateRowSubtotal(rowDiv);
        updateAllCalculations();
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.ic-dropdown-menu').forEach(m => m.classList.add('hidden'));
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.customer-select-container') && !e.target.closest('.product-select-container')) {
            closeAllDropdowns();
        }
    });

    /* ─── Overall Multi-Item & Installment Calculations ─── */
    function updateAllCalculations() {
        const rows = document.querySelectorAll('.item-row');
        let subtotalAll = 0;
        let totalTaxAll = 0;
        let totalItems = 0;
        let totalQty = 0;

        rows.forEach(r => {
            const prodId = r.querySelector('.row-product-id').value;
            const qty = parseInt(r.querySelector('.row-quantity').value) || 0;
            const price = parseFloat(r.querySelector('.row-price').value) || 0;
            const rowSub = Math.max(0, qty * price);

            if (prodId && qty > 0) {
                totalItems++;
                totalQty += qty;
                subtotalAll += rowSub;

                const prod = productsData.find(p => p.id == prodId);
                if (taxEnabled && prod && prod.is_taxable) {
                    const rate = prod.tax_rate > 0 ? prod.tax_rate : defaultTaxRate;
                    if (prod.tax_type === 'inclusive') {
                        const tax = rowSub - (rowSub / (1 + rate / 100));
                        totalTaxAll += tax;
                    } else {
                        const tax = rowSub * (rate / 100);
                        totalTaxAll += tax;
                    }
                }
            }
        });

        // If exclusive tax was added, total price = subtotalAll + totalTaxAll; if inclusive, already included
        let grandTotal = subtotalAll;
        // Check for exclusive taxes
        let exclusiveTax = 0;
        rows.forEach(r => {
            const prodId = r.querySelector('.row-product-id').value;
            const qty = parseInt(r.querySelector('.row-quantity').value) || 0;
            const price = parseFloat(r.querySelector('.row-price').value) || 0;
            const rowSub = Math.max(0, qty * price);
            if (taxEnabled && prodId) {
                const prod = productsData.find(p => p.id == prodId);
                if (prod && prod.is_taxable && prod.tax_type === 'exclusive') {
                    const rate = prod.tax_rate > 0 ? prod.tax_rate : defaultTaxRate;
                    exclusiveTax += rowSub * (rate / 100);
                }
            }
        });
        grandTotal += exclusiveTax;

        document.getElementById('totalItemsCount').innerText = totalItems;
        document.getElementById('totalQtyCount').innerText = totalQty;
        document.getElementById('summaryItemsTotal').innerText = formatCurrency(grandTotal);

        if (totalPriceInput) {
            totalPriceInput.value = grandTotal.toFixed(2);
        }

        calculateInstallment(subtotalAll, totalTaxAll, grandTotal);
    }

    function calculateInstallment(subtotalInput, taxAmount, grandTotal) {
        if (subtotalInput === undefined) {
            // Read from items
            let calculatedSub = 0;
            let calculatedTax = 0;
            document.querySelectorAll('.item-row').forEach(r => {
                const prodId = r.querySelector('.row-product-id').value;
                const qty = parseInt(r.querySelector('.row-quantity').value) || 0;
                const price = parseFloat(r.querySelector('.row-price').value) || 0;
                const rowSub = Math.max(0, qty * price);
                calculatedSub += rowSub;
                if (taxEnabled && prodId) {
                    const prod = productsData.find(p => p.id == prodId);
                    if (prod && prod.is_taxable) {
                        const rate = prod.tax_rate > 0 ? prod.tax_rate : defaultTaxRate;
                        if (prod.tax_type === 'inclusive') {
                            calculatedTax += rowSub - (rowSub / (1 + rate / 100));
                        } else {
                            calculatedTax += rowSub * (rate / 100);
                        }
                    }
                }
            });
            subtotalInput = calculatedSub;
            taxAmount = calculatedTax;
            grandTotal = parseFloat(totalPriceInput.value) || 0;
        }

        const downPayment = parseFloat(downPaymentInput.value) || 0;
        let interestRate = parseFloat(interestRateInput.value) || 0;
        const duration = parseInt(durationInput.value) || 1;
        const interestType = interestTypeInput ? interestTypeInput.value : 'flat';

        if (interestType === 'zero_interest') {
            interestRate = 0;
            if (interestRateInput) {
                interestRateInput.value = 0;
                interestRateInput.disabled = true;
            }
        } else {
            if (interestRateInput) {
                interestRateInput.disabled = false;
            }
        }

        // Update description note
        if (interestTypeDesc) {
            const descriptions = {
                'flat': '* គណនាការប្រាក់ថេរលើប្រាក់ដើមដំបូងរហូតដល់ផុតកិច្ចសន្យា',
                'declining': '* ប្រាក់ដើមបង់ស្មើគ្នារៀងរាល់ខែ ការប្រាក់ថយចុះជាលំដាប់តាមសមតុល្យជាក់ស្តែង',
                'amortization': '* ទឹកប្រាក់ត្រូវបង់សរុបស្មើគ្នាជារៀងរាល់ខែ (PMT) ការប្រាក់ថយចុះ ប្រាក់ដើមកើនឡើង',
                'bullet': '* រៀងរាល់ខែបង់តែការប្រាក់សុទ្ធ ខែចុងក្រោយបង់ផ្តាច់ប្រាក់ដើមទាំងអស់',
                'zero_interest': '* គ្មានការប្រាក់ (0%) ចែកប្រាក់ដើមស្មើគ្នាតាមចំនួនខែ'
            };
            interestTypeDesc.innerText = descriptions[interestType] || '';
        }

        if (taxAmount > 0) {
            taxRow.style.display = 'flex';
            previewTax.innerText = formatCurrency(taxAmount);
        } else {
            taxRow.style.display = 'none';
        }

        const principal = Math.max(0, grandTotal - downPayment);
        const monthlyRate = interestRate / 100;

        let firstMonthPayment = 0;
        let firstMonthInterest = 0;
        let totalInterest = 0;
        let totalRemaining = 0;

        if (interestType === 'zero_interest') {
            firstMonthPayment = duration > 0 ? (principal / duration) : 0;
            firstMonthInterest = 0;
            totalInterest = 0;
            totalRemaining = principal;
        } else if (interestType === 'flat') {
            firstMonthInterest = duration > 0 ? (principal * monthlyRate) : 0;
            firstMonthPayment = duration > 0 ? ((principal / duration) + firstMonthInterest) : 0;
            totalInterest = firstMonthInterest * duration;
            totalRemaining = principal + totalInterest;
        } else if (interestType === 'declining') {
            const basePrincipal = duration > 0 ? (principal / duration) : 0;
            firstMonthInterest = principal * monthlyRate;
            firstMonthPayment = basePrincipal + firstMonthInterest;

            let balance = principal;
            for (let m = 1; m <= duration; m++) {
                const interest = balance * monthlyRate;
                totalInterest += interest;
                balance = Math.max(0, balance - basePrincipal);
            }
            totalRemaining = principal + totalInterest;
        } else if (interestType === 'amortization') {
            if (monthlyRate > 0 && duration > 0) {
                const factor = Math.pow(1 + monthlyRate, duration);
                firstMonthPayment = principal * (monthlyRate * factor) / (factor - 1);
            } else {
                firstMonthPayment = duration > 0 ? (principal / duration) : 0;
            }
            firstMonthInterest = principal * monthlyRate;
            totalRemaining = firstMonthPayment * duration;
            totalInterest = Math.max(0, totalRemaining - principal);
        }

        previewSubtotal.innerText = formatCurrency(subtotalInput);
        previewTotal.innerText = formatCurrency(grandTotal);
        previewDownPayment.innerText = formatCurrency(downPayment);
        previewPrincipal.innerText = formatCurrency(principal);
        previewInterest.innerText = formatCurrency(firstMonthInterest);
        previewRemaining.innerText = formatCurrency(totalRemaining);
        previewMonthly.innerText = formatCurrency(firstMonthPayment) + ' / ខែ';
    }

    [downPaymentInput, interestRateInput, durationInput].forEach(el => {
        if (el) el.addEventListener('input', () => calculateInstallment());
    });

    /* ─── Initialization on Page Load ─── */
    document.addEventListener('DOMContentLoaded', function() {
        // Handle prefilled or old items
        @if(old('items'))
            const oldItems = {!! json_encode(old('items')) !!};
            if (Array.isArray(oldItems) && oldItems.length > 0) {
                oldItems.forEach(item => addProductRow(item));
            } else {
                addProductRow();
            }
        @elseif(old('product_id'))
            addProductRow({
                product_id: {{ old('product_id') }},
                quantity: 1,
                price: {{ old('total_price', 0) }}
            });
        @else
            addProductRow();
        @endif

        // Handle customer prefill
        @if(old('customer_id'))
            selectCustomer({{ old('customer_id') }});
        @endif

        updateAllCalculations();
    });
</script>

@endsection
