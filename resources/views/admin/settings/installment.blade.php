@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    @php
        $isKm = app()->getLocale() === 'km';
    @endphp

    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6 transition-all duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 ring-4 ring-emerald-50">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        {{ $isKm ? 'ការកំណត់បង់រំលស់' : 'Installment Settings' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        {{ $isKm ? 'គ្រប់គ្រងការគណនាការប្រាក់បង់ផ្តាច់ និងគោលការណ៍ពិន័យពេលបង់យឺត' : 'Manage payoff interest rules and late payment fee policies' }}
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition">
                <i class="fas fa-arrow-left"></i>
                <span>{{ $isKm ? 'ត្រឡប់ទៅការកំណត់' : 'Back to Settings' }}</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl p-4 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <span class="flex-shrink-0 text-emerald-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.installment.update') }}" class="space-y-6">
        @csrf

        <!-- Card 1: Payoff Settings (ការកំណត់ពេលបង់ផ្តាច់) -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-black">
                        <i class="fas fa-check-double"></i>
                    </span>
                    <span>{{ $isKm ? 'ការគណនាពេលបង់ផ្តាច់' : 'Pay Off Calculation' }}</span>
                </h2>
                <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg">
                    {{ $isKm ? 'បង់ផ្តាច់' : 'Pay Off' }}
                </span>
            </div>

            <p class="text-xs text-slate-500">
                {{ $isKm 
                    ? 'កំណត់ជម្រើសប្រភេទបង់ផ្តាច់ និងរូបមន្តគណនាការប្រាក់ នៅពេលអតិថិជនស្នើសុំបង់បញ្ចប់កិច្ចសន្យាមុនកាលកំណត់។' 
                    : 'Configure default payoff type and interest calculation when a customer settles an installment early.' }}
            </p>

            @php
                $defaultPayoffType = $settings['default_payoff_type'] ?? 'principal_only';
                $currentPayoffMode = $settings['payoff_interest_mode'] ?? 'one_month';
            @endphp

            <!-- Default Payoff Type Selection -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    {{ $isKm ? 'ប្រភេទបង់ផ្តាច់' : 'Payoff Type' }}
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Option 1: Principal Only -->
                    <label id="label-payoff-principal"
                           class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition {{ $defaultPayoffType === 'principal_only' ? 'border-blue-500 bg-blue-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="default_payoff_type" value="principal_only" {{ $defaultPayoffType === 'principal_only' ? 'checked' : '' }}
                                   onchange="updatePayoffTypeCards()"
                                   class="mt-1 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-bold text-slate-800 block">
                                    {{ $isKm ? 'បង់តែប្រាក់ដើមនៅសល់' : 'Principal Only' }}
                                </span>
                                <span class="text-xs text-slate-500 mt-0.5 block">
                                    {{ $isKm ? 'មិនគិតការប្រាក់បន្ថែម គិតតែប្រាក់ដើមសុទ្ធដែលនៅសល់' : 'No extra interest, only outstanding principal balance' }}
                                </span>
                            </div>
                        </div>
                    </label>

                    <!-- Option 2: Principal + Interest -->
                    <label id="label-payoff-interest"
                           class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition {{ $defaultPayoffType === 'principal_interest' ? 'border-emerald-500 bg-emerald-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="default_payoff_type" value="principal_interest" {{ $defaultPayoffType === 'principal_interest' ? 'checked' : '' }}
                                   onchange="updatePayoffTypeCards()"
                                   class="mt-1 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-bold text-slate-800 block">
                                    {{ $isKm ? 'ប្រាក់ដើម + ការប្រាក់' : 'Principal + Interest' }}
                                </span>
                                <span class="text-xs text-slate-500 mt-0.5 block">
                                    {{ $isKm ? 'គិតប្រាក់ដើម បូកបន្ថែមការប្រាក់តាមរូបមន្តខាងក្រោម' : 'Outstanding principal plus accrued interest below' }}
                                </span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Interest calculation method sub-section -->
            <div id="section-interest-formula" class="border-t border-slate-100 pt-4 space-y-3 {{ $defaultPayoffType === 'principal_interest' ? '' : 'opacity-60' }}">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-0.5">
                        {{ $isKm ? 'រូបមន្តគណនាការប្រាក់' : 'Interest Calculation Formula' }}
                    </label>
                    <p class="text-xs text-slate-400">
                        {{ $isKm ? '(អនុវត្តតែពេលជ្រើសរើស «ប្រាក់ដើម + ការប្រាក់»)' : '(Only applies when Principal + Interest is selected)' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Option 1: One Month -->
                    <label id="label-mode-one-month"
                           class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition {{ $currentPayoffMode === 'one_month' ? 'border-emerald-500 bg-emerald-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="payoff_interest_mode" value="one_month" {{ $currentPayoffMode === 'one_month' ? 'checked' : '' }}
                                   onchange="updateInterestModeCards()"
                                   class="mt-1 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-bold text-slate-800 block">
                                    {{ $isKm ? 'គិតការប្រាក់តែ ១ ខែ' : '1 Month Interest Only' }}
                                </span>
                                <span class="text-xs text-slate-500 mt-0.5 block">
                                    {{ $isKm ? 'គិតការប្រាក់ត្រឹមតែ ១ ខែនៃខែបច្ចុប្បន្ន' : 'Accrue only current month interest' }}
                                </span>
                            </div>
                        </div>
                    </label>

                    <!-- Option 2: All Remaining Months -->
                    <label id="label-mode-all-months"
                           class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition {{ $currentPayoffMode === 'all_remaining_months' ? 'border-emerald-500 bg-emerald-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="payoff_interest_mode" value="all_remaining_months" {{ $currentPayoffMode === 'all_remaining_months' ? 'checked' : '' }}
                                   onchange="updateInterestModeCards()"
                                   class="mt-1 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-bold text-slate-800 block">
                                    {{ $isKm ? 'គិតការប្រាក់ខែទាំងអស់ដែលនៅសល់' : 'All Remaining Months Interest' }}
                                </span>
                                <span class="text-xs text-slate-500 mt-0.5 block">
                                    {{ $isKm ? 'គិតការប្រាក់សរុបនៃរាល់ខែដែលមិនទាន់បានបង់' : 'Accrue total unpaid interest across all remaining months' }}
                                </span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Minimum Month Eligible for Payoff -->
            @php
                $minPayoffMonth = $settings['payoff_min_month'] ?? '0';
            @endphp
            <div class="border-t border-slate-100 pt-4">
                <div class="max-w-md">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ $isKm ? 'លក្ខខណ្ឌខែអប្បបរមាដែលអាចបង់ផ្តាច់បាន' : 'Minimum Eligible Months for Payoff' }}
                    </label>
                    <div class="relative">
                        <input type="number" step="1" min="0" name="payoff_min_month" value="{{ $minPayoffMonth }}"
                               placeholder="0"
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">
                            {{ $isKm ? 'ខែ' : 'months' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        {{ $isKm ? 'ដាក់លេខ ០ ប្រសិនបើអនុញ្ញាតឱ្យបង់ផ្តាច់បានគ្រប់ពេល' : 'Set to 0 to allow early payoff anytime without restriction' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Late Payment Settings (ការកំណត់ការបង់យឺត) -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-black">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <span>{{ $isKm ? 'គោលការណ៍ពិន័យការបង់យឺត' : 'Late Payment Penalty Policy' }}</span>
                </h2>
                <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg">
                    {{ $isKm ? 'ពិន័យយឺត' : 'Late Policy' }}
                </span>
            </div>

            <p class="text-xs text-slate-500">
                {{ $isKm 
                    ? 'កំណត់គោលការណ៍ពិន័យម្តង ហើយប្រព័ន្ធនឹងគណនាស្វ័យប្រវត្តិតាមរូបមន្ត។' 
                    : 'Configure late payment policy once, and system will calculate penalties automatically.' }}
            </p>

            @php
                $lateFeeMethod = $settings['late_fee_method'] ?? (($settings['late_fee_type'] ?? '') === 'percentage' ? 'percentage' : 'fixed');
                $lateFeeFrequency = $settings['late_fee_frequency'] ?? 'daily';
                $lateFeeRate = $settings['late_fee_rate'] ?? ($settings['late_fee_amount'] ?? '1.00');
                $gracePeriodDays = $settings['late_grace_period_days'] ?? '3';
                $autoCalculate = ($settings['auto_calculate_late_fee'] ?? '1') === '1';
                $telegramReminder = ($settings['telegram_late_reminder'] ?? '1') === '1';
                $adminApproval = ($settings['admin_approval_late_fee'] ?? '0') === '1';
            @endphp

            <!-- Method Selection -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    {{ $isKm ? 'វិធីគិតប្រាក់ពិន័យ' : 'Late Fee Method' }}
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label id="label-fee-fixed"
                           class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition {{ $lateFeeMethod === 'fixed' ? 'border-amber-500 bg-amber-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" name="late_fee_method" value="fixed" {{ $lateFeeMethod === 'fixed' ? 'checked' : '' }}
                               onchange="updateLateFeeCards()"
                               class="mt-1 w-4 h-4 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">
                                {{ $isKm ? 'គិតជាទឹកប្រាក់ដុល្លារ ($)' : 'Fixed Amount ($)' }}
                            </span>
                            <span class="text-xs text-slate-500 mt-0.5 block">
                                {{ $isKm ? 'ចំនួនទឹកប្រាក់ថេរកំណត់ (ឧ. $1.00 ក្នុងមួយថ្ងៃ ឬម្តង)' : 'Fixed dollar amount (e.g. $1.00/day or one-time)' }}
                            </span>
                        </div>
                    </label>

                    <label id="label-fee-percentage"
                           class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition {{ $lateFeeMethod === 'percentage' ? 'border-amber-500 bg-amber-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" name="late_fee_method" value="percentage" {{ $lateFeeMethod === 'percentage' ? 'checked' : '' }}
                               onchange="updateLateFeeCards()"
                               class="mt-1 w-4 h-4 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">
                                {{ $isKm ? 'គិតជាភាគរយ (%)' : 'Percentage (%)' }}
                            </span>
                            <span class="text-xs text-slate-500 mt-0.5 block">
                                {{ $isKm ? 'គិតភាគរយលើទឹកប្រាក់ត្រូវបង់ក្នុងខែដែលយឺត' : 'Calculated on monthly payment amount due' }}
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                <!-- Calculation Frequency -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        {{ $isKm ? 'ភាពញឹកញាប់នៃការគិត' : 'Calculation Frequency' }}
                    </label>
                    <select name="late_fee_frequency" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                        <option value="daily" {{ $lateFeeFrequency === 'daily' ? 'selected' : '' }}>
                            {{ $isKm ? 'គិតរៀងរាល់ថ្ងៃយឺត' : 'Daily' }}
                        </option>
                        <option value="one_time" {{ $lateFeeFrequency === 'one_time' ? 'selected' : '' }}>
                            {{ $isKm ? 'គិតតែម្តងប៉ុណ្ណោះ' : 'One-time' }}
                        </option>
                    </select>
                </div>

                <!-- Fee Rate / Amount -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        {{ $isKm ? 'តម្លៃពិន័យ' : 'Fee Rate / Amount' }}
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" name="late_fee_rate" id="input_late_fee_rate" value="{{ $lateFeeRate }}"
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="1.00">
                        <span id="rate_unit_badge" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">
                            {{ $lateFeeMethod === 'percentage' ? '%' : '$' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1" id="rate_hint_text">
                        {{ $isKm 
                            ? ($lateFeeMethod === 'percentage' ? 'ឧទាហរណ៍៖ 1.00%' : 'ឧទាហរណ៍៖ $1.00') 
                            : ($lateFeeMethod === 'percentage' ? 'e.g. 1.00%' : 'e.g. $1.00') }}
                    </p>
                </div>

                <!-- Grace Period (Days) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        {{ $isKm ? 'ថ្ងៃអនុគ្រោះមិនគិតពិន័យ' : 'Grace Period (Days)' }}
                    </label>
                    <div class="relative">
                        <input type="number" step="1" min="0" name="late_grace_period_days" value="{{ $gracePeriodDays }}"
                               placeholder="3"
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">
                            {{ $isKm ? 'ថ្ងៃ' : 'days' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        {{ $isKm ? 'លើកលែងមិនគិតពិន័យក្នុងរយៈពេលថ្ងៃនេះ' : 'No penalty during these initial days' }}
                    </p>
                </div>
            </div>

            <!-- Toggles / Checkboxes -->
            <div class="border-t border-slate-100 pt-4">
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="auto_calculate_late_fee" value="1" {{ $autoCalculate ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                    <div class="text-xs">
                        <span class="font-bold text-slate-800 block">
                            {{ $isKm ? 'គណនាប្រាក់ពិន័យដោយស្វ័យប្រវត្តិ' : 'Auto Calculate Late Fee' }}
                        </span>
                        <span class="text-slate-500">
                            {{ $isKm 
                                ? 'ប្រព័ន្ធគណនាប្រាក់ពិន័យស្វ័យប្រវត្តិតាមរូបមន្ត៖ (ថ្ងៃយឺត - ថ្ងៃអនុគ្រោះ) × តម្លៃពិន័យ' 
                                : 'Automatically calculates late penalty fee based on configured policy' }}
                        </span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md transition text-sm flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>{{ $isKm ? 'រក្សាទុកការផ្លាស់ប្តូរ' : 'Save Changes' }}</span>
            </button>
        </div>
    </form>
</div>

<script>
    function updatePayoffTypeCards() {
        const radios = document.getElementsByName('default_payoff_type');
        let selected = 'principal_only';
        for (const r of radios) {
            if (r.checked) {
                selected = r.value;
                break;
            }
        }

        const cardPrincipal = document.getElementById('label-payoff-principal');
        const cardInterest = document.getElementById('label-payoff-interest');
        const formulaSection = document.getElementById('section-interest-formula');

        if (selected === 'principal_only') {
            cardPrincipal.className = 'relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-blue-500 bg-blue-50/50 shadow-xs';
            cardInterest.className = 'relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-slate-200 hover:border-slate-300 bg-white';
            if (formulaSection) formulaSection.classList.add('opacity-60');
        } else {
            cardPrincipal.className = 'relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-slate-200 hover:border-slate-300 bg-white';
            cardInterest.className = 'relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-emerald-500 bg-emerald-50/50 shadow-xs';
            if (formulaSection) formulaSection.classList.remove('opacity-60');
        }
    }

    function updateInterestModeCards() {
        const radios = document.getElementsByName('payoff_interest_mode');
        let selected = 'one_month';
        for (const r of radios) {
            if (r.checked) {
                selected = r.value;
                break;
            }
        }

        const cardOne = document.getElementById('label-mode-one-month');
        const cardAll = document.getElementById('label-mode-all-months');

        if (selected === 'one_month') {
            cardOne.className = 'relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-emerald-500 bg-emerald-50/50 shadow-xs';
            cardAll.className = 'relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-slate-200 hover:border-slate-300 bg-white';
        } else {
            cardOne.className = 'relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-slate-200 hover:border-slate-300 bg-white';
            cardAll.className = 'relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-emerald-500 bg-emerald-50/50 shadow-xs';
        }
    }

    function updateLateFeeCards() {
        const radios = document.getElementsByName('late_fee_method');
        let selected = 'fixed';
        for (const r of radios) {
            if (r.checked) {
                selected = r.value;
                break;
            }
        }

        const cardFixed = document.getElementById('label-fee-fixed');
        const cardPerc = document.getElementById('label-fee-percentage');
        const badge = document.getElementById('rate_unit_badge');
        const hint = document.getElementById('rate_hint_text');
        const isKm = {{ $isKm ? 'true' : 'false' }};

        if (selected === 'fixed') {
            cardFixed.className = 'flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition border-amber-500 bg-amber-50/50 shadow-xs';
            cardPerc.className = 'flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition border-slate-200 hover:border-slate-300 bg-white';
            if (badge) badge.innerText = '$';
            if (hint) hint.innerText = isKm ? 'ឧទាហរណ៍៖ $1.00' : 'e.g. $1.00';
        } else {
            cardFixed.className = 'flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition border-slate-200 hover:border-slate-300 bg-white';
            cardPerc.className = 'flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition border-amber-500 bg-amber-50/50 shadow-xs';
            if (badge) badge.innerText = '%';
            if (hint) hint.innerText = isKm ? 'ឧទាហរណ៍៖ 1.00%' : 'e.g. 1.00%';
        }
    }
</script>
@endsection
