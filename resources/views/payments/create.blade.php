@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('payments.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1.5 transition" style="text-decoration: none;">
                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
            </a>
            <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">{{ __('app.new_payment') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                {{ app()->getLocale() === 'km' 
                    ? 'ជ្រើសរើសគម្រោងបង់រំលស់ វិធីទូទាត់ និងបញ្ចូលទឹកប្រាក់ ដើម្បីទូទាត់ប្រាក់ឱ្យបានត្រឹមត្រូវ។'
                    : 'Select installment plan, payment method, and input amount to record payment.' }}
            </p>
        </div>
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-xs font-semibold">
                <i class="fas fa-circle-info"></i>
                {{ app()->getLocale() === 'km' ? 'ប្រព័ន្ធត្រួតពិនិត្យសមតុល្យស្វ័យប្រវត្តិ' : 'Auto Balance Control Active' }}
            </span>
        </div>
    </div>

    <!-- Dynamic Customer Info Header Card (Appears when contract selected) -->
    <div id="customerInfoCard" class="hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-800 text-white rounded-2xl shadow-md p-5 transition-all duration-300 transform">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-white text-2xl font-black shadow-inner">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 id="cardCustomerName" class="text-lg font-bold tracking-wide">SOK DARA</h3>
                        <span id="cardContractNo" class="text-xs bg-white/20 text-white px-2 py-0.5 rounded-md font-mono">#INV-00001</span>
                    </div>
                    <p class="text-xs text-blue-100 flex items-center gap-2 mt-1">
                        <span><i class="fas fa-phone-alt mr-1"></i><span id="cardCustomerPhone">012 345 678</span></span>
                        <span>•</span>
                        <span><i class="fas fa-box mr-1"></i><span id="cardProductName">iPhone 15 Pro Max</span></span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto border-t md:border-t-0 border-white/10 pt-3 md:pt-0">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2 text-right flex-1 md:flex-none">
                    <span class="block text-[10px] uppercase tracking-wider text-blue-200">{{ app()->getLocale() === 'km' ? 'ប្រាក់នៅសល់' : 'Remaining Balance' }}</span>
                    <span id="cardRemainingBalance" class="text-base font-extrabold text-amber-300 font-mono">$0.00</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2 text-right flex-1 md:flex-none">
                    <span class="block text-[10px] uppercase tracking-wider text-blue-200">{{ app()->getLocale() === 'km' ? 'ថ្ងៃត្រូវបង់បន្ទាប់' : 'Next Due Date' }}</span>
                    <span id="cardNextDueDate" class="text-sm font-bold text-white font-mono">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Dashboard Summary Cards (KPI Grid) -->
    <div id="summaryCardsGrid" class="hidden grid grid-cols-2 md:grid-cols-4 gap-3.5 transition-all duration-300">
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-sm">
            <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">{{ app()->getLocale() === 'km' ? 'សរុបកុងត្រា' : 'Total Contract' }}</span>
            <p id="kpiTotalContract" class="text-lg sm:text-xl font-bold text-slate-800 mt-1 font-mono">$0.00</p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">{{ app()->getLocale() === 'km' ? 'តម្លៃទំនិញសរុប' : 'Full price' }}</span>
        </div>
        <div class="bg-white rounded-xl border border-emerald-100 bg-emerald-50/30 p-4 shadow-sm">
            <span class="text-xs text-emerald-700 font-medium uppercase tracking-wider block">{{ app()->getLocale() === 'km' ? 'បានបង់រួច' : 'Total Paid' }}</span>
            <p id="kpiTotalPaid" class="text-lg sm:text-xl font-bold text-emerald-600 mt-1 font-mono">$0.00</p>
            <span class="text-[11px] text-emerald-600/80 mt-0.5 block">{{ app()->getLocale() === 'km' ? 'ទូទាត់រួចរាល់' : 'Cleared so far' }}</span>
        </div>
        <div class="bg-white rounded-xl border border-amber-100 bg-amber-50/40 p-4 shadow-sm">
            <span class="text-xs text-amber-700 font-medium uppercase tracking-wider block">{{ app()->getLocale() === 'km' ? 'នៅខ្វះ' : 'Remaining' }}</span>
            <p id="kpiRemaining" class="text-lg sm:text-xl font-bold text-amber-600 mt-1 font-mono">$0.00</p>
            <span class="text-[11px] text-amber-600/80 mt-0.5 block">{{ app()->getLocale() === 'km' ? 'សមតុល្យត្រូវបង់' : 'Balance due' }}</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-sm">
            <div class="flex justify-between items-center">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">{{ app()->getLocale() === 'km' ? 'វឌ្ឍនភាព' : 'Progress' }}</span>
                <span id="kpiProgressText" class="text-xs font-bold text-blue-600">0%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden">
                <div id="kpiProgressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
            <span class="text-[11px] text-slate-400 mt-1.5 block">{{ app()->getLocale() === 'km' ? 'ភាគរយបង់រួច' : 'Percentage cleared' }}</span>
        </div>
    </div>

    <!-- Main Form Card -->
    <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" id="paymentForm" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-6">
        @csrf

        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-xs text-red-700 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ app()->getLocale() === 'km' ? 'មានបញ្ហាក្នុងការបញ្ចូលទិន្នន័យ៖' : 'Validation Error:' }}
                </div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 1. Contract / Installment Selection --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center justify-between">
                <span><i class="fas fa-file-contract text-blue-600 mr-1"></i> {{ __('app.installment_plans') }} <span class="text-red-500">*</span></span>
                <span class="text-[11px] font-normal text-slate-400">{{ app()->getLocale() === 'km' ? 'ជ្រើសរើសដើម្បីបង្ហាញព័ត៌មានអតិថិជន' : 'Select plan to load customer details' }}</span>
            </label>
            <select name="installment_id" required id="installmentSelect" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-slate-50/30 transition">
                <option value="">{{ app()->getLocale() === 'km' ? '--- ជ្រើសរើសកុងត្រាបង់រំលស់អតិថិជន ---' : '--- Select Customer Contract Plan ---' }}</option>
                @foreach($installments as $installment)
                    @php
                        $schedule = $installment->getPaymentSchedule();
                        $unpaidRow = collect($schedule)->first(fn($row) => $row['status'] !== 'paid');
                        $dueAmount = $unpaidRow ? $unpaidRow['amount'] : 0;
                        $penaltyAmount = $installment->calculatePenalty();
                        
                        $totalPrice = (float) $installment->total_price;
                        $remaining = (float) $installment->remaining_balance;
                        $paid = max($totalPrice - $remaining, 0);
                        $progress = $totalPrice > 0 ? round(($paid / $totalPrice) * 100) : 0;
                        $contractNo = '#INV-' . str_pad($installment->id, 5, '0', STR_PAD_LEFT);
                    @endphp
                    <option value="{{ $installment->id }}" 
                            data-customer-name="{{ $installment->customer?->name ?? 'N/A' }}"
                            data-customer-phone="{{ $installment->customer?->phone ?? 'N/A' }}"
                            data-contract-no="{{ $contractNo }}"
                            data-product-name="{{ $installment->product?->name ?? 'N/A' }}"
                            data-total-price="{{ $totalPrice }}"
                            data-total-paid="{{ $paid }}"
                            data-remaining-balance="{{ $remaining }}"
                            data-monthly-amount="{{ $dueAmount }}"
                            data-penalty-amount="{{ $penaltyAmount }}"
                            data-next-due-date="{{ $installment->next_due_date ? \Carbon\Carbon::parse($installment->next_due_date)->format('Y-m-d') : '-' }}"
                            data-progress="{{ $progress }}"
                            {{ old('installment_id') == $installment->id ? 'selected' : '' }}>
                        {{ $contractNo }} | {{ $installment->customer?->name }} | {{ $installment->product?->name }} | Remaining: ${{ number_format($remaining, 2) }} | Monthly: ${{ number_format($dueAmount, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 2. Payment Amount & Quick Buttons --}}
        <div class="bg-slate-50/50 rounded-xl border border-slate-200/60 p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" id="amountLabel">{{ __('app.amount') }} (USD) <span class="text-red-500">*</span></label>
                        <div class="inline-flex rounded-lg border border-slate-200 p-0.5 bg-white shadow-sm" role="group">
                            <button type="button" id="toggleCurrencyUsd" class="px-2.5 py-0.5 text-xs font-bold rounded bg-blue-600 text-white shadow-sm transition cursor-pointer">USD ($)</button>
                            <button type="button" id="toggleCurrencyKhr" class="px-2.5 py-0.5 text-xs font-bold rounded text-slate-600 hover:text-blue-600 transition cursor-pointer">KHR (៛)</button>
                        </div>
                    </div>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="amount" 
                            id="amountInput" 
                            step="0.01" 
                            min="0.01" 
                            value="{{ old('amount') }}" 
                            required 
                            placeholder="0.00"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base font-bold font-mono text-slate-800 bg-white"
                        >
                    </div>
                    <span class="mt-1 block text-xs font-semibold text-indigo-600" id="amountRiel">0 ៛</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" id="penaltyLabel">{{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }} (USD)</label>
                    <input 
                        type="number" 
                        name="penalty_amount" 
                        id="penaltyInput" 
                        step="0.01" 
                        min="0" 
                        value="{{ old('penalty_amount', '0.00') }}" 
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base font-bold font-mono text-slate-800 bg-white"
                    >
                    <span class="mt-1 block text-xs font-semibold text-indigo-600" id="penaltyRiel">0 ៛</span>
                </div>
            </div>

            <!-- Quick Payment Amount Action Buttons -->
            <div>
                <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ app()->getLocale() === 'km' ? 'ជ្រើសរើសប្រាក់បង់រហ័ស (Quick Amount):' : 'Quick Amount Options:' }}</span>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="btnPayMonthly" class="px-3.5 py-1.5 bg-white border border-blue-200 text-blue-700 hover:bg-blue-50 text-xs font-bold rounded-lg transition duration-150 shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-calendar-check text-blue-500"></i>
                        <span>{{ app()->getLocale() === 'km' ? 'បង់ប្រចាំខែ' : 'Pay Monthly' }}</span>
                        <span id="labelMonthlyVal" class="bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.5 rounded font-mono">$0.00</span>
                    </button>
                    <button type="button" id="btnPayFull" class="px-3.5 py-1.5 bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 text-xs font-bold rounded-lg transition duration-150 shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-check-double text-emerald-500"></i>
                        <span>{{ app()->getLocale() === 'km' ? 'បង់ផ្តាច់ទាំងស្រុង' : 'Pay Full' }}</span>
                        <span id="labelFullVal" class="bg-emerald-100 text-emerald-800 text-[10px] px-1.5 py-0.5 rounded font-mono">$0.00</span>
                    </button>
                    <button type="button" id="btnPayCustom" class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-medium rounded-lg transition duration-150 shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-edit text-slate-400"></i>
                        <span>{{ app()->getLocale() === 'km' ? 'បញ្ចូលតាមចិត្ត' : 'Pay Custom' }}</span>
                    </button>
                </div>
            </div>

            <!-- Dynamic Amount Validation Warning Banner -->
            <div id="validationWarning" class="hidden bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-700 flex items-start gap-2 animate-fade-in">
                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 text-sm"></i>
                <div>
                    <span class="font-bold" id="valWarningTitle">Payment amount exceeds remaining balance!</span>
                    <p class="mt-0.5 text-[11px] text-red-600" id="valWarningDesc">You cannot pay more than the outstanding contract balance.</p>
                </div>
            </div>
        </div>

        {{-- 3. Payment Methods Selector Grid --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                <i class="fas fa-wallet text-blue-600 mr-1"></i> {{ __('app.payment_method') }} <span class="text-red-500">*</span>
            </label>

            <!-- Branded Payment Method Radio Tiles -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mb-3" id="methodTilesContainer">
                @foreach($paymentMethods as $method)
                    @php
                        $methodKey = strtolower(str_replace(' ', '_', $method->name));
                        $translatedName = __('app.' . $methodKey);
                        if ($translatedName === 'app.' . $methodKey) {
                            $translatedName = $method->name;
                        }

                        $iconClass = 'fa-money-bill-wave';
                        $colorClass = 'text-emerald-600 bg-emerald-50 border-emerald-200';
                        
                        if (str_contains($methodKey, 'aba')) {
                            $iconClass = 'fa-university';
                            $colorClass = 'text-sky-600 bg-sky-50 border-sky-200';
                        } elseif (str_contains($methodKey, 'acleda')) {
                            $iconClass = 'fa-building-columns';
                            $colorClass = 'text-indigo-600 bg-indigo-50 border-indigo-200';
                        } elseif (str_contains($methodKey, 'wing')) {
                            $iconClass = 'fa-wallet';
                            $colorClass = 'text-lime-600 bg-lime-50 border-lime-200';
                        } elseif (str_contains($methodKey, 'truemoney')) {
                            $iconClass = 'fa-mobile-screen';
                            $colorClass = 'text-orange-600 bg-orange-50 border-orange-200';
                        } elseif (str_contains($methodKey, 'bank') || str_contains($methodKey, 'transfer')) {
                            $iconClass = 'fa-money-check-dollar';
                            $colorClass = 'text-blue-600 bg-blue-50 border-blue-200';
                        } elseif (str_contains($methodKey, 'qr')) {
                            $iconClass = 'fa-qrcode';
                            $colorClass = 'text-purple-600 bg-purple-50 border-purple-200';
                        } elseif (str_contains($methodKey, 'credit') || str_contains($methodKey, 'card')) {
                            $iconClass = 'fa-credit-card';
                            $colorClass = 'text-slate-700 bg-slate-100 border-slate-300';
                        }
                    @endphp
                    <label class="relative flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer transition-all hover:border-blue-500 method-tile group text-center select-none" data-id="{{ $method->id }}">
                        <input type="radio" name="payment_method_id" value="{{ $method->id }}" data-type="{{ $methodKey }}" class="sr-only method-radio" {{ old('payment_method_id', $paymentMethods->first()?->id) == $method->id ? 'checked' : '' }}>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg mb-1.5 transition {{ $colorClass }}">
                            <i class="fas {{ $iconClass }}"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-800 block truncate max-w-full">{{ $translatedName }}</span>
                        <span class="text-[10px] text-slate-400 font-medium block truncate max-w-full mt-0.5">{{ $method->name }}</span>
                    </label>
                @endforeach
            </div>

            <!-- Fallback Hidden Select to maintain compatibility -->
            <select name="payment_method_id_fallback" id="methodSelectHidden" class="hidden">
                @foreach($paymentMethods as $method)
                    @php $methodKey = strtolower(str_replace(' ', '_', $method->name)); @endphp
                    <option value="{{ $method->id }}" data-type="{{ $methodKey }}">{{ $method->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- 4. Payment Date --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    <i class="fas fa-calendar-alt text-blue-600 mr-1"></i> {{ __('app.payment_date') }} <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    name="payment_date" 
                    value="{{ old('payment_date', now()->toDateString()) }}" 
                    required 
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white"
                >
            </div>
        </div>

        <!-- Dynamic Bank QR Code Display Container (Hidden by default) -->
        @php
            $bankQr = \App\Models\Setting::where('key', 'company_bank_qr')->value('value');
            $bankQrPayload = \App\Models\Setting::where('key', 'company_bank_qr_payload')->value('value');
        @endphp
        @if($bankQr)
        <div id="bankQrContainer" class="hidden rounded-2xl border-2 border-dashed border-purple-200 bg-purple-50/30 p-5 flex flex-col items-center justify-center text-center space-y-3">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">
                <i class="fas fa-qrcode"></i>
                {{ app()->getLocale() === 'km' ? 'ស្កេន KHQR ដើម្បីទូទាត់ប្រាក់' : 'Scan KHQR Code to Pay' }}
            </div>
            <img id="bankQrImage" src="{{ asset('storage/' . $bankQr) }}" data-static-src="{{ asset('storage/' . $bankQr) }}" alt="Shop Bank QR Code" class="w-48 h-48 rounded-xl border border-slate-200 shadow-md object-contain bg-white p-2">
            <p id="qrInstructionMessage" class="text-xs font-bold text-purple-700"></p>
            <p class="text-xs text-slate-500">
                {{ app()->getLocale() === 'km' 
                    ? 'បន្ទាប់ពីស្កេនរួច សូមបញ្ចូលរូបភាពបង្កាន់ដៃ (ស្លីបផ្ទេរប្រាក់) នៅខាងក្រោម'
                    : 'After scanning, please upload transfer receipt slip below.' }}
            </p>
        </div>
        @endif

        <!-- Credit Card Fields Display Container (Hidden by default) -->
        <div id="creditCardContainer" class="hidden rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/50 p-6 space-y-6">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center justify-between">
                <span class="flex items-center gap-1.5">
                    <i class="fas fa-credit-card text-blue-600"></i>
                    {{ app()->getLocale() === 'km' ? 'ព័ត៌មានកាតឥណទានស្តង់ដារ' : 'Credit Card Payment Gateway' }}
                </span>
                <span class="text-[10px] bg-blue-100 text-blue-800 px-2 py-0.5 rounded font-medium">PCI-DSS Tokenized Simulation</span>
            </h4>

            <!-- Interactive 3D Card Preview -->
            <div class="card-container">
                <div class="credit-card-preview" id="cardPreview">
                    <div class="card-front">
                        <div class="flex justify-between items-start">
                            <div class="card-chip"></div>
                            <div class="card-brand-logo text-xl font-black italic tracking-wide" id="cardBrandLogoPreview">Visa</div>
                        </div>
                        <div class="text-xl font-mono tracking-widest text-center my-4" id="cardNumberPreview">•••• •••• •••• ••••</div>
                        <div class="flex justify-between items-end">
                            <div>
                                <div class="text-[9px] uppercase tracking-wider opacity-75">{{ app()->getLocale() === 'km' ? 'ឈ្មោះម្ចាស់កាត' : 'Cardholder' }}</div>
                                <div class="text-sm font-semibold tracking-wide truncate max-w-[200px]" id="cardHolderPreview">SOK DARA</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[9px] uppercase tracking-wider opacity-75">{{ app()->getLocale() === 'km' ? 'ផុតកំណត់' : 'Expires' }}</div>
                                <div class="text-sm font-semibold tracking-wide font-mono" id="cardExpiryPreview">MM/YY</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-back">
                        <div class="w-full h-10 bg-black mt-2"></div>
                        <div class="px-6 mt-4">
                            <div class="text-[9px] text-right uppercase tracking-wider text-slate-300 mb-1">CVC/CVV</div>
                            <div class="bg-white text-slate-900 h-9 px-3 flex items-center justify-end font-mono font-bold rounded shadow-inner" id="cardCvvPreview">•••</div>
                        </div>
                        <div class="px-6 mt-2 text-right">
                            <div class="text-[9px] text-slate-400 italic">PCI DSS compliant payment token logic.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Inputs Form -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'km' ? 'លេខកាតពេញ (១៦ ខ្ទង់)' : 'Card Number (16-digit)' }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="card_number_full" placeholder="4000 1234 5678 9010" class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute left-3.5 top-2.5 text-slate-400 text-sm">
                            <i class="fas fa-credit-card" id="cardInputIcon"></i>
                        </div>
                    </div>
                    <span id="cardNumberError" class="text-xs text-red-500 mt-1 hidden">Invalid card number (Luhn check failed)</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'km' ? 'ឈ្មោះម្ចាស់កាត' : 'Cardholder Name' }} <span class="text-red-500">*</span></label>
                    <input type="text" id="card_holder_name_input" placeholder="SOK DARA" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'km' ? 'ថ្ងៃផុតកំណត់ (ខែ/ឆ្នាំ)' : 'Expiration Date (MM/YY)' }} <span class="text-red-500">*</span></label>
                    <input type="text" id="card_expiry" placeholder="MM/YY" maxlength="5" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm text-center font-mono focus:ring-2 focus:ring-blue-500">
                    <span id="cardExpiryError" class="text-xs text-red-500 mt-1 hidden">Expired card date</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">CVV/CVC <span class="text-red-500">*</span></label>
                    <input type="password" id="card_cvv" placeholder="•••" maxlength="4" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm text-center font-mono focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'km' ? 'ប្រភេទកាតទូទាត់' : 'Detected Brand' }}</label>
                    <div class="w-full px-3 py-2 bg-slate-100 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold text-center select-none" id="detectedBrandBadge">
                        Other
                    </div>
                </div>
            </div>

            <input type="hidden" name="card_holder_name" id="card_holder_name_hidden">
            <input type="hidden" name="card_number" id="card_number_hidden">
            <input type="hidden" name="card_brand" id="card_brand_hidden">

            <div class="border-t border-slate-200 pt-4 bg-slate-100/60 rounded-xl p-4">
                <div class="text-xs font-bold text-slate-800 mb-2 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ព័ត៌មានលម្អិតអំពីការគណនា' : 'Fee Breakdown' }}</div>
                <div class="space-y-1.5 text-sm text-slate-600">
                    <div class="flex justify-between">
                        <span>Principal Amount:</span>
                        <span class="font-semibold text-slate-900 font-mono" id="calcPrincipal">$0.00</span>
                    </div>
                    @php $cardProcessingFeePercent = \App\Models\Setting::where('key', 'card_processing_fee')->value('value') ?? '2'; @endphp
                    <div class="flex justify-between">
                        <span>Processing Fee ({{ $cardProcessingFeePercent }}%):</span>
                        <span class="font-semibold text-blue-600 font-mono" id="calcFee">+$0.00</span>
                    </div>
                    <div class="border-t border-slate-200 my-2 pt-2 flex justify-between text-base font-bold text-slate-900">
                        <span>Total Charge:</span>
                        <div class="text-right">
                            <span id="calcTotalUSD" class="font-mono">$0.00</span>
                            <span class="block text-xs font-medium text-blue-600 mt-0.5" id="calcTotalKHR">0 ៛</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Drag & Drop Receipt / Slip Attachment --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                <i class="fas fa-file-image text-blue-600 mr-1"></i>
                {{ __('app.qr_receipt_image') }}
            </label>
            <div id="dropZone" class="relative rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-400 p-6 flex flex-col items-center justify-center text-center cursor-pointer transition">
                <input type="file" name="qr_image" id="fileInput" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                
                <div id="dropZonePrompt" class="space-y-2">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto text-xl">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-slate-700 block">
                            {{ app()->getLocale() === 'km' ? 'ទម្លាក់រូបភាពនៅទីនេះ ឬ ចុចដើម្បីជ្រើសរើស' : 'Drag & drop image here or click to browse' }}
                        </span>
                        <span class="text-xs text-slate-400 mt-0.5 block">PNG, JPG, WEBP up to 5MB (Transfer slip, QR receipt, payment proof)</span>
                    </div>
                </div>

                <div id="dropZonePreview" class="hidden w-full flex items-center justify-between bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <img id="previewThumbnail" src="" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-sm">
                        <div class="text-left">
                            <span id="previewFileName" class="text-xs font-bold text-slate-800 block truncate max-w-[200px]">receipt.png</span>
                            <span id="previewFileSize" class="text-[11px] text-slate-400 block font-mono">250 KB</span>
                        </div>
                    </div>
                    <button type="button" id="btnRemoveImage" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold rounded-lg transition border border-red-200">
                        <i class="fas fa-times mr-1"></i> {{ app()->getLocale() === 'km' ? 'លុប' : 'Remove' }}
                    </button>
                </div>
            </div>
        </div>

        {{-- 6. Payment Notes --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                <i class="fas fa-comment-alt text-blue-600 mr-1"></i> {{ app()->getLocale() === 'km' ? 'ចំណាំអំពីការទូទាត់ (Payment Note)' : 'Payment Notes' }}
            </label>
            <textarea name="notes" rows="2" placeholder="{{ app()->getLocale() === 'km' ? 'ឧទាហរណ៍៖ អតិថិជនបានបង់មុនកាលកំណត់...' : 'Example: Customer paid earlier than due date...' }}" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">{{ old('notes') }}</textarea>
        </div>

        {{-- 7. Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('payments.index') }}" class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition text-xs text-center cursor-pointer" style="text-decoration: none;">
                <i class="fas fa-times mr-1"></i> {{ __('app.cancel') }}
            </a>

            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <button type="submit" name="save_and_print" value="1" id="saveAndPrintBtn" class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition text-xs shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fas fa-print"></i>
                    <span>{{ app()->getLocale() === 'km' ? 'រក្សាទុក & បោះពុម្ពបង្កាន់ដៃ' : 'Save & Print Receipt' }}</span>
                </button>

                <button type="submit" id="submitBtn" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition text-xs shadow-md flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fas fa-check-circle"></i>
                    <span id="btnText">{{ __('app.submit_payment') }}</span>
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .card-container { perspective: 1000px; width: 100%; max-width: 320px; height: 190px; margin: 0 auto 1.5rem auto; }
    .credit-card-preview { width: 100%; height: 100%; position: relative; transform-style: preserve-3d; transition: transform 0.6s ease; cursor: pointer; }
    .credit-card-preview.flipped { transform: rotateY(180deg); }
    .card-front, .card-back { width: 100%; height: 100%; position: absolute; top: 0; left: 0; backface-visibility: hidden; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; justify-between; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.25); color: white; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%); overflow: hidden; }
    .card-back { transform: rotateY(180deg); background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); padding: 20px 0; }
    .card-chip { width: 38px; height: 28px; background: linear-gradient(135deg, #ffe082 0%, #ffb300 100%); border-radius: 5px; position: relative; }
    .method-tile.selected { border-color: #2563eb !important; background-color: #eff6ff !important; box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2); }
</style>

<!-- 3D Secure Simulated OTP Modal -->
<div id="otpModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all duration-300">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="otpModalContent">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
            <span class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                <i class="fas fa-shield-alt text-emerald-500"></i>
                {{ app()->getLocale() === 'km' ? 'ការផ្ទៀងផ្ទាត់សុវត្ថិភាព 3D Secure' : '3D Secure Bank Verification' }}
            </span>
            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">Verified by VISA</span>
        </div>

        <div class="space-y-4" id="otpModalMainBody">
            <p class="text-xs text-slate-600">A 6-digit OTP code has been generated for demo verification.</p>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs space-y-1.5 font-mono">
                <div class="flex justify-between"><span class="text-slate-500">Card:</span><span id="otpCardType" class="font-bold text-slate-800">Visa ****1234</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Charge:</span><span id="otpTotalAmount" class="font-bold text-blue-700">$0.00</span></div>
            </div>
            <div class="rounded-lg bg-amber-50 border border-amber-200 p-2.5 text-xs text-amber-800 flex items-center justify-between">
                <span>Demo OTP Code:</span>
                <code class="px-2 py-0.5 bg-white border border-amber-300 font-mono font-black text-sm rounded" id="simulatedOtpCode">123456</code>
            </div>
            <div>
                <input type="text" id="otpInput" placeholder="••••••" maxlength="6" class="w-full tracking-[0.5em] text-center font-mono font-black text-lg px-4 py-2 border-2 border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                <span id="otpErrorMsg" class="text-xs text-red-500 mt-1 block hidden text-center">Incorrect OTP code.</span>
            </div>
        </div>

        <div id="otpProcessingOverlay" class="hidden flex flex-col items-center justify-center py-8 text-center space-y-3">
            <div class="animate-spin rounded-full h-9 w-9 border-t-2 border-b-2 border-blue-600"></div>
            <h5 class="text-xs font-bold text-slate-800" id="otpProcessingTitle">Authorizing...</h5>
        </div>

        <div class="mt-5 flex items-center justify-end gap-2 border-t border-slate-100 pt-3" id="otpModalButtons">
            <button type="button" id="cancelOtpBtn" class="px-3.5 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg text-xs">Cancel</button>
            <button type="button" id="verifyOtpBtn" class="px-4 py-1.5 bg-blue-600 text-white font-bold rounded-lg text-xs shadow">Verify & Pay</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('paymentForm');
        const installmentSelect = id('installmentSelect');
        const amountInput = id('amountInput');
        const penaltyInput = id('penaltyInput');
        const amountRiel = id('amountRiel');
        const penaltyRiel = id('penaltyRiel');
        const submitBtn = id('submitBtn');
        const btnText = id('btnText');
        const saveAndPrintBtn = id('saveAndPrintBtn');

        const exchangeRate = {{ $exchangeRate }};
        const baseQrPayload = @json($bankQrPayload);
        let qrCurrencyMode = '{{ session('display_currency', 'USD') }}';
        let currentRemainingBalance = 0;
        let currentMonthlyDue = 0;

        function id(el) { return document.getElementById(el); }

        // Method Tile Select Behavior
        const methodTiles = document.querySelectorAll('.method-tile');
        const methodSelectHidden = id('methodSelectHidden');

        function selectMethod(radioInput) {
            methodTiles.forEach(t => t.classList.remove('selected'));
            const parentTile = radioInput.closest('.method-tile');
            if (parentTile) parentTile.classList.add('selected');

            const val = radioInput.value;
            if (methodSelectHidden) {
                methodSelectHidden.value = val;
                toggleMethodDisplay();
            }
        }

        methodTiles.forEach(tile => {
            tile.addEventListener('click', function() {
                const radio = this.querySelector('.method-radio');
                if (radio) {
                    radio.checked = true;
                    selectMethod(radio);
                }
            });
        });

        // Initialize checked radio
        const checkedRadio = document.querySelector('.method-radio:checked') || document.querySelector('.method-radio');
        if (checkedRadio) {
            checkedRadio.checked = true;
            selectMethod(checkedRadio);
        }

        function getSelectedMethodType() {
            const radio = document.querySelector('.method-radio:checked');
            return radio ? radio.getAttribute('data-type') : '';
        }

        function toggleMethodDisplay() {
            const type = getSelectedMethodType();
            const qrBox = id('bankQrContainer');
            const ccBox = id('creditCardContainer');

            if (qrBox) {
                if (type === 'qr_code') qrBox.classList.remove('hidden');
                else qrBox.classList.add('hidden');
            }

            if (ccBox) {
                if (type === 'credit_card') ccBox.classList.remove('hidden');
                else ccBox.classList.add('hidden');
            }
            updateCalculations();
        }

        // Installment Change Logic
        function handleInstallmentChange() {
            const option = installmentSelect.options[installmentSelect.selectedIndex];
            const infoCard = id('customerInfoCard');
            const kpiGrid = id('summaryCardsGrid');

            if (!option || !option.value) {
                if (infoCard) infoCard.classList.add('hidden');
                if (kpiGrid) kpiGrid.classList.add('hidden');
                currentRemainingBalance = 0;
                currentMonthlyDue = 0;
                amountInput.value = '';
                if (penaltyInput) penaltyInput.value = '0.00';
                updateRiel();
                updateCalculations();
                validateAmount();
                return;
            }

            // Read Attributes
            const custName = option.getAttribute('data-customer-name');
            const custPhone = option.getAttribute('data-customer-phone');
            const contractNo = option.getAttribute('data-contract-no');
            const prodName = option.getAttribute('data-product-name');
            const totalPrice = parseFloat(option.getAttribute('data-total-price')) || 0;
            const totalPaid = parseFloat(option.getAttribute('data-total-paid')) || 0;
            const remaining = parseFloat(option.getAttribute('data-remaining-balance')) || 0;
            const monthly = parseFloat(option.getAttribute('data-monthly-amount')) || 0;
            const penalty = parseFloat(option.getAttribute('data-penalty-amount')) || 0;
            const dueDate = option.getAttribute('data-next-due-date');
            const progress = option.getAttribute('data-progress');

            currentRemainingBalance = remaining;
            currentMonthlyDue = monthly;

            // Populate Info Card
            id('cardCustomerName').innerText = custName;
            id('cardCustomerPhone').innerText = custPhone;
            id('cardContractNo').innerText = contractNo;
            id('cardProductName').innerText = prodName;
            id('cardRemainingBalance').innerText = '$' + remaining.toFixed(2);
            id('cardNextDueDate').innerText = dueDate;

            // Populate KPI Grid
            id('kpiTotalContract').innerText = '$' + totalPrice.toFixed(2);
            id('kpiTotalPaid').innerText = '$' + totalPaid.toFixed(2);
            id('kpiRemaining').innerText = '$' + remaining.toFixed(2);
            id('kpiProgressText').innerText = progress + '%';
            id('kpiProgressBar').style.width = progress + '%';

            // Show Cards
            if (infoCard) infoCard.classList.remove('hidden');
            if (kpiGrid) kpiGrid.classList.remove('hidden');

            // Quick Button Labels
            id('labelMonthlyVal').innerText = '$' + monthly.toFixed(2);
            id('labelFullVal').innerText = '$' + remaining.toFixed(2);

            // Auto-fill Amount
            if (monthly > 0) {
                setAmountValue(monthly);
            } else if (remaining > 0) {
                setAmountValue(remaining);
            } else {
                amountInput.value = '0.00';
            }

            if (penaltyInput) {
                if (penalty > 0) {
                    penaltyInput.value = qrCurrencyMode === 'KHR' ? Math.round(penalty * exchangeRate) : penalty.toFixed(2);
                } else {
                    penaltyInput.value = qrCurrencyMode === 'KHR' ? '0' : '0.00';
                }
            }

            updateRiel();
            updatePenaltyRiel();
            updateDynamicQr();
            updateCalculations();
            validateAmount();
        }

        function setAmountValue(valUsd) {
            if (qrCurrencyMode === 'KHR') {
                amountInput.value = Math.round(valUsd * exchangeRate);
            } else {
                amountInput.value = valUsd.toFixed(2);
            }
        }

        installmentSelect.addEventListener('change', handleInstallmentChange);

        // Quick Amount Action Buttons
        id('btnPayMonthly').addEventListener('click', function() {
            if (currentMonthlyDue > 0) setAmountValue(currentMonthlyDue);
            else if (currentRemainingBalance > 0) setAmountValue(currentRemainingBalance);
            updateRiel(); updateDynamicQr(); updateCalculations(); validateAmount();
        });

        id('btnPayFull').addEventListener('click', function() {
            if (currentRemainingBalance > 0) setAmountValue(currentRemainingBalance);
            updateRiel(); updateDynamicQr(); updateCalculations(); validateAmount();
        });

        id('btnPayCustom').addEventListener('click', function() {
            amountInput.value = '';
            amountInput.focus();
            updateRiel(); updateDynamicQr(); updateCalculations(); validateAmount();
        });

        // Amount Validation Logic
        function validateAmount() {
            const val = parseFloat(amountInput.value) || 0;
            const usd = qrCurrencyMode === 'KHR' ? (val / exchangeRate) : val;
            const warnBox = id('validationWarning');
            const warnTitle = id('valWarningTitle');
            const warnDesc = id('valWarningDesc');

            if (!installmentSelect.value) {
                if (warnBox) warnBox.classList.add('hidden');
                submitBtn.disabled = false;
                if (saveAndPrintBtn) saveAndPrintBtn.disabled = false;
                return;
            }

            if (currentRemainingBalance <= 0) {
                if (warnBox) warnBox.classList.remove('hidden');
                if (warnTitle) warnTitle.innerText = 'This contract plan is fully paid off!';
                if (warnDesc) warnDesc.innerText = 'No further payments are required for this installment.';
                submitBtn.disabled = true;
                if (saveAndPrintBtn) saveAndPrintBtn.disabled = true;
                return;
            }

            if (usd > currentRemainingBalance + 0.009) {
                if (warnBox) warnBox.classList.remove('hidden');
                if (warnTitle) warnTitle.innerText = 'Payment exceeds remaining balance!';
                if (warnDesc) warnDesc.innerText = `Entered amount ($${usd.toFixed(2)}) is greater than remaining balance ($${currentRemainingBalance.toFixed(2)}).`;
                submitBtn.disabled = true;
                if (saveAndPrintBtn) saveAndPrintBtn.disabled = true;
                return;
            }

            if (usd <= 0) {
                if (warnBox) warnBox.classList.remove('hidden');
                if (warnTitle) warnTitle.innerText = 'Invalid Payment Amount!';
                if (warnDesc) warnDesc.innerText = 'Please enter an amount greater than 0.';
                submitBtn.disabled = true;
                if (saveAndPrintBtn) saveAndPrintBtn.disabled = true;
                return;
            }

            // Clean
            if (warnBox) warnBox.classList.add('hidden');
            submitBtn.disabled = false;
            if (saveAndPrintBtn) saveAndPrintBtn.disabled = false;
        }

        amountInput.addEventListener('input', function() {
            updateRiel(); updateDynamicQr(); updateCalculations(); validateAmount();
        });

        // Currency Toggle Logic
        const toggleUsd = id('toggleCurrencyUsd');
        const toggleKhr = id('toggleCurrencyKhr');

        function setCurrencyMode(mode) {
            const oldMode = qrCurrencyMode;
            qrCurrencyMode = mode;
            
            if (oldMode !== mode) {
                const currentVal = parseFloat(amountInput.value) || 0;
                const currentPen = penaltyInput ? (parseFloat(penaltyInput.value) || 0) : 0;
                if (currentVal > 0) {
                    amountInput.value = mode === 'KHR' ? Math.round(currentVal * exchangeRate) : (currentVal / exchangeRate).toFixed(2);
                }
                if (penaltyInput && currentPen > 0) {
                    penaltyInput.value = mode === 'KHR' ? Math.round(currentPen * exchangeRate) : (currentPen / exchangeRate).toFixed(2);
                }
            }

            if (mode === 'KHR') {
                toggleKhr.className = 'px-2.5 py-0.5 text-xs font-bold rounded bg-blue-600 text-white shadow-sm transition cursor-pointer';
                toggleUsd.className = 'px-2.5 py-0.5 text-xs font-bold rounded text-slate-600 hover:text-blue-600 transition cursor-pointer';
                id('amountLabel').innerText = '{{ __('app.amount') }} (KHR)';
                if (id('penaltyLabel')) id('penaltyLabel').innerText = '{{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }} (KHR)';
            } else {
                toggleUsd.className = 'px-2.5 py-0.5 text-xs font-bold rounded bg-blue-600 text-white shadow-sm transition cursor-pointer';
                toggleKhr.className = 'px-2.5 py-0.5 text-xs font-bold rounded text-slate-600 hover:text-blue-600 transition cursor-pointer';
                id('amountLabel').innerText = '{{ __('app.amount') }} (USD)';
                if (id('penaltyLabel')) id('penaltyLabel').innerText = '{{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }} (USD)';
            }
            updateRiel(); updatePenaltyRiel(); updateDynamicQr(); updateCalculations(); validateAmount();
        }

        if (toggleUsd && toggleKhr) {
            toggleUsd.addEventListener('click', () => setCurrencyMode('USD'));
            toggleKhr.addEventListener('click', () => setCurrencyMode('KHR'));
        }

        function updateRiel() {
            const val = parseFloat(amountInput.value) || 0;
            if (qrCurrencyMode === 'KHR') {
                amountRiel.innerText = '$' + (val / exchangeRate).toFixed(2);
            } else {
                amountRiel.innerText = Math.round(val * exchangeRate).toLocaleString('en-US') + ' ៛';
            }
        }

        function updatePenaltyRiel() {
            if (!penaltyInput || !penaltyRiel) return;
            const val = parseFloat(penaltyInput.value) || 0;
            if (qrCurrencyMode === 'KHR') {
                penaltyRiel.innerText = '$' + (val / exchangeRate).toFixed(2);
            } else {
                penaltyRiel.innerText = Math.round(val * exchangeRate).toLocaleString('en-US') + ' ៛';
            }
        }

        function updateCalculations() {
            const usd = qrCurrencyMode === 'KHR' ? ((parseFloat(amountInput.value) || 0) / exchangeRate) : (parseFloat(amountInput.value) || 0);
            const penalty = penaltyInput ? (qrCurrencyMode === 'KHR' ? ((parseFloat(penaltyInput.value) || 0) / exchangeRate) : (parseFloat(penaltyInput.value) || 0)) : 0;
            const subtotal = usd + penalty;
            const feePercent = parseFloat("{{ $cardProcessingFeePercent }}");
            const fee = (getSelectedMethodType() === 'credit_card') ? subtotal * (feePercent / 100) : 0;
            const totalUSD = subtotal + fee;
            const totalKHR = Math.round(totalUSD * exchangeRate);

            if (id('calcPrincipal')) id('calcPrincipal').innerText = '$' + subtotal.toFixed(2);
            if (id('calcFee')) id('calcFee').innerText = '+$' + fee.toFixed(2);
            if (id('calcTotalUSD')) id('calcTotalUSD').innerText = '$' + totalUSD.toFixed(2);
            if (id('calcTotalKHR')) id('calcTotalKHR').innerText = totalKHR.toLocaleString('en-US') + ' ៛';
            if (id('otpTotalAmount')) id('otpTotalAmount').innerHTML = `$${totalUSD.toFixed(2)} (${totalKHR.toLocaleString('en-US')} ៛)`;
        }

        function updateDynamicQr() {
            const img = id('bankQrImage');
            if (!img) return;
            const staticSrc = img.getAttribute('data-static-src');
            if (!baseQrPayload || !baseQrPayload.trim()) { img.src = staticSrc; return; }
            const usd = qrCurrencyMode === 'KHR' ? ((parseFloat(amountInput.value) || 0) / exchangeRate) : (parseFloat(amountInput.value) || 0);
            if (usd <= 0) { img.src = staticSrc; return; }
            img.src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(baseQrPayload.trim())}`;
        }

        // Drag & Drop File Upload Preview
        const dropZone = id('dropZone');
        const fileInput = id('fileInput');
        const dropPrompt = id('dropZonePrompt');
        const dropPreview = id('dropZonePreview');
        const previewThumbnail = id('previewThumbnail');
        const previewFileName = id('previewFileName');
        const previewFileSize = id('previewFileSize');
        const btnRemoveImage = id('btnRemoveImage');

        if (fileInput) {
            fileInput.addEventListener('change', handleFileSelect);
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => { e.preventDefault(); dropZone.classList.add('border-blue-500', 'bg-blue-50/50'); }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => { e.preventDefault(); dropZone.classList.remove('border-blue-500', 'bg-blue-50/50'); }, false);
            });
            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelect();
                }
            });
        }

        function handleFileSelect() {
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                previewFileName.innerText = file.name;
                previewFileSize.innerText = (file.size / 1024).toFixed(1) + ' KB';
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewThumbnail.src = e.target.result;
                    dropPrompt.classList.add('hidden');
                    dropPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        if (btnRemoveImage) {
            btnRemoveImage.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                dropPreview.classList.add('hidden');
                dropPrompt.classList.remove('hidden');
            });
        }

        // Run initializers
        if (installmentSelect.value) {
            handleInstallmentChange();
        } else {
            updateRiel(); updatePenaltyRiel(); updateCalculations();
        }
    });
</script>
@endsection