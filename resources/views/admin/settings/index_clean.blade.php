@extends('layouts.app')

@section('content')
@php
    $defaultPmKey = $settings['default_payment_method'] ?? 'pm_cash';
    
    $hiddenPmList = json_decode($settings['hidden_payment_methods'] ?? '[]', true);
    if (!is_array($hiddenPmList)) {
        $hiddenPmList = [];
    }

    $deletedDefaultQr = json_decode($settings['deleted_default_qr'] ?? '[]', true);
    if (!is_array($deletedDefaultQr)) {
        $deletedDefaultQr = [];
    }

    // Core list of payment methods
    $allPaymentItems = [];

    // 1. Cash (ប្រាក់សុទ្ធ)
    $allPaymentItems[] = [
        'key' => 'pm_cash',
        'name' => __('app.cash'),
        'icon' => '💵',
        'is_qr' => false,
        'image' => null,
        'payload' => null,
        'is_custom' => false,
        'can_delete' => false,
        'bg_gradient' => 'from-emerald-500 to-teal-600',
        'accent' => 'emerald',
        'desc' => __('app.cash_desc'),
    ];

    // 2. Credit Card (កាតឥណទាន/POS)
    $allPaymentItems[] = [
        'key' => 'pm_card',
        'name' => __('app.credit_card'),
        'icon' => '💳',
        'is_qr' => false,
        'image' => null,
        'payload' => null,
        'is_custom' => false,
        'can_delete' => false,
        'bg_gradient' => 'from-indigo-500 to-blue-600',
        'accent' => 'indigo',
        'desc' => __('app.card_desc'),
    ];

    // 3. Built-in Bank QRs
    $defaultBanks = [
        'qr_aba' => ['name' => 'ABA Bank', 'icon' => '🏦', 'accent' => 'sky', 'bg' => 'from-sky-600 to-cyan-700'],
        'qr_acleda' => ['name' => 'ACLEDA Bank', 'icon' => '🏦', 'accent' => 'blue', 'bg' => 'from-blue-700 to-indigo-900'],
        'qr_wing' => ['name' => 'Wing Bank', 'icon' => '🟢', 'accent' => 'lime', 'bg' => 'from-lime-500 to-emerald-600'],
        'qr_truemoney' => ['name' => 'TrueMoney', 'icon' => '🔴', 'accent' => 'orange', 'bg' => 'from-orange-500 to-amber-600'],
        'qr_bakong' => ['name' => 'Bakong KHQR', 'icon' => '🏦', 'accent' => 'rose', 'bg' => 'from-rose-600 to-red-700'],
    ];

    foreach ($defaultBanks as $bKey => $bInfo) {
        if (in_array($bKey, $deletedDefaultQr)) continue;

        $img = $settings[$bKey] ?? null;
        if ($bKey === 'qr_bakong' && !$img) {
            $img = $settings['company_bank_qr'] ?? null;
        }

        $payload = $settings[$bKey . '_payload'] ?? null;
        if ($bKey === 'qr_bakong' && !$payload) {
            $payload = $settings['company_bank_qr_payload'] ?? null;
        }

        $allPaymentItems[] = [
            'key' => $bKey,
            'name' => $bInfo['name'],
            'icon' => $bInfo['icon'],
            'is_qr' => true,
            'image' => $img,
            'payload' => $payload,
            'is_custom' => false,
            'can_delete' => true,
            'bg_gradient' => $bInfo['bg'],
            'accent' => $bInfo['accent'],
            'desc' => 'QR Code Bank',
        ];
    }

    // 4. Custom Bank QRs
    $customQrList = json_decode($settings['custom_qr_list'] ?? '[]', true);
    if (is_array($customQrList)) {
        foreach ($customQrList as $cBank) {
            $cKey = $cBank['key'] ?? null;
            if (!$cKey) continue;
            $allPaymentItems[] = [
                'key' => $cKey,
                'name' => $cBank['label'] ?? 'Custom Bank',
                'icon' => $cBank['icon'] ?? '🏦',
                'is_qr' => true,
                'image' => $settings[$cKey] ?? null,
                'payload' => $settings[$cKey . '_payload'] ?? null,
                'is_custom' => true,
                'can_delete' => true,
                'bg_gradient' => 'from-purple-600 to-indigo-700',
                'accent' => 'purple',
                'desc' => 'Custom Bank QR',
            ];
        }
    }
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header & Tab Controls -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6 transition-all duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 ring-4 ring-indigo-50">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        {{ __('app.settings') }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        {{ __('app.settings_subtitle') }}
                    </p>
                </div>
            </div>

            <!-- Tab Switcher -->
            <div class="inline-flex p-1 bg-slate-100/80 rounded-xl border border-slate-200/60 self-start sm:self-center">
                <button id="btn-tab-general" onclick="switchTab('general')" class="px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 flex items-center space-x-2 bg-white text-indigo-600 shadow-xs">
                    <span>🏢</span>
                    <span>{{ __('app.general_settings') }}</span>
                </button>
                <button id="btn-tab-qr" onclick="switchTab('qr')" class="px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 flex items-center space-x-2 text-slate-600 hover:text-slate-900">
                    <span>💳</span>
                    <span>{{ __('app.payment_method') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl p-4 flex items-center justify-between shadow-xs animate-fade-in">
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

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl p-4 flex items-center justify-between shadow-xs animate-fade-in">
            <div class="flex items-center space-x-3">
                <span class="flex-shrink-0 text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl p-4 flex items-center justify-between shadow-xs animate-fade-in">
            <div class="flex items-center space-x-3">
                <span class="flex-shrink-0 text-amber-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </span>
                <p class="font-medium">{{ session('warning') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- TAB 1: GENERAL COMPANY SETTINGS -->
    <div id="content-general" class="space-y-6">
        <form method="POST" action="{{ route('admin.settings.company.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Company Information -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                            <span>🏢</span>
                            <span>{{ __('app.company_information') }}</span>
                        </h2>
                        <span class="text-xs text-slate-400">{{ __('app.company_info_invoice_desc') }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('app.company_name') }} (English) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="company_name" value="{{ $settings['company_name'] ?? 'My Store' }}" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('app.company_name') }} (ភាសាខ្មែរ) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="company_name_km" value="{{ $settings['company_name_km'] ?? 'ហាងកុំព្យូទ័រ' }}" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                ចំណងជើងរងរបស់ប្រព័ន្ធ (System Subtitle Sidebar)
                            </label>
                            <input type="text" name="company_subtitle" value="{{ $settings['company_subtitle'] ?? 'Installment System' }}" placeholder="ឧ. Installment System" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('app.phone_number') }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ $settings['company_phone'] ?? '' }}" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('app.email_address') }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ $settings['company_email'] ?? '' }}" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('app.business_address') }} (English) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="address" value="{{ $settings['company_address'] ?? '' }}" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('app.business_address') }} (ភាសាខ្មែរ) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="address_km" value="{{ $settings['company_address_km'] ?? '' }}" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('app.business_license') }}
                            </label>
                            <input type="text" name="business_license" value="{{ $settings['company_business_license'] ?? '' }}" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Right: Logo & Currency -->
                <div class="space-y-6">
                    <!-- Logo Card -->
                    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2 border-b border-slate-100 pb-3">
                            <span>🖼️</span>
                            <span>{{ __('app.company_logo') }}</span>
                        </h2>

                        <div class="text-center space-y-3">
                            <div class="w-32 h-32 mx-auto rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden p-2 group relative">
                                @if(isset($settings['company_logo']) && $settings['company_logo'])
                                    <img id="logo-preview-img" src="{{ asset('storage/' . $settings['company_logo']) }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <img id="logo-preview-img" class="max-w-full max-h-full object-contain hidden">
                                    <div id="logo-preview-empty" class="text-center">
                                        <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-xs text-slate-400 mt-1 block">{{ __('app.no_logo_uploaded') }}</span>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="inline-flex items-center px-4 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 cursor-pointer transition-all">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    {{ __('app.upload_photo') }}
                                    <input type="file" name="logo" accept="image/*" class="hidden" onchange="previewCompanyLogo(this)">
                                </label>
                                <p class="text-[11px] text-slate-400 mt-1">PNG, JPG or WEBP (Max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Exchange Rate & Tax Card -->
                    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2 border-b border-slate-100 pb-3">
                            <span>💱</span>
                            <span>{{ __('app.exchange_rate') }} & {{ __('app.tax') }}</span>
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    {{ __('app.exchange_rate') }} (1 USD = KHR)
                                </label>
                                <div class="relative">
                                    <input type="number" name="exchange_rate" value="{{ $settings['exchange_rate'] ?? '4100' }}" step="1" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 pl-3.5 pr-12 py-2.5 focus:ring-2 focus:ring-indigo-500">
                                    <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400">៛/USD</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100">
                                <label class="flex items-center space-x-2.5 cursor-pointer">
                                    <input type="checkbox" name="tax_enabled" value="1" {{ ($settings['tax_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <span class="text-xs font-semibold text-slate-800">{{ __('app.enable_tax_system') }}</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">{{ __('app.tax_rate') }} (%)</label>
                                    <input type="number" name="default_tax_rate" value="{{ $settings['default_tax_rate'] ?? '10' }}" step="0.1" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">{{ __('app.tax_number') }}</label>
                                    <input type="text" name="tax_number" value="{{ $settings['tax_number'] ?? '' }}" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3 py-2">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Telegram Bot Integration Card -->
                    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                                <span>🤖</span>
                                <span>{{ __('app.telegram_bot_setup') }}</span>
                            </h2>
                            @if(isset($settings['telegram_token']) && !empty($settings['telegram_token']))
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full border border-emerald-300">
                                    {{ __('app.status_connected') }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-800 rounded-full border border-rose-300">
                                    {{ __('app.status_disconnected') }}
                                </span>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Telegram Bot API Token
                                </label>
                                <input type="text" name="telegram_token" value="{{ $settings['telegram_token'] ?? '' }}" placeholder="123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ..." class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 font-mono">
                                <p class="text-[11px] text-slate-400 mt-1">
                                    {{ __('app.telegram_bot_subtitle') }}
                                </p>
                            </div>

                            <div class="pt-2 flex justify-between items-center">
                                <a href="{{ route('telegram-logs.index') }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800 space-x-1">
                                    <span>🔗</span>
                                    <span>{{ __('app.manage_telegram') }}</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-500/20 transition-all transform active:scale-95 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ __('app.save_settings') }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: PAYMENT METHODS & QR CODE SETTINGS -->
    <div id="content-qr" class="space-y-6 hidden">
        <!-- Toolbar Header Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                        <span>💳</span>
                        <span>{{ __('app.payment_method_qr_settings') }}</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ __('app.qr_header_subtitle') }}
                    </p>
                </div>

                <!-- Action Controls -->
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" onclick="openAddBankModal()" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-semibold text-xs rounded-xl shadow-xs transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>➕ {{ __('app.add_new_bank_qr') }}</span>
                    </button>
                </div>
            </div>

            <!-- Search & Filter Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <div class="relative w-full sm:w-72">
                    <input type="text" id="pmSearchInput" onkeyup="filterPaymentCards()" placeholder="{{ __('app.search_bank_placeholder') }}" class="w-full text-xs rounded-xl border border-slate-300 pl-9 pr-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <span class="absolute left-3 top-2.5 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 9 0 0114 0z"/>
                        </svg>
                    </span>
                </div>

                <!-- Status Filter Pills -->
                <div class="inline-flex p-1 bg-slate-100 rounded-xl space-x-1 w-full sm:w-auto justify-center">
                    <button type="button" onclick="setFilterStatus('all')" id="btn-filter-all" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-indigo-600 shadow-xs">
                        {{ __('app.filter_all') }}
                    </button>
                    <button type="button" onclick="setFilterStatus('enabled')" id="btn-filter-enabled" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-600 hover:text-slate-900">
                        🟢 {{ __('app.filter_enabled') }}
                    </button>
                    <button type="button" onclick="setFilterStatus('disabled')" id="btn-filter-disabled" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-600 hover:text-slate-900">
                        🔴 {{ __('app.filter_disabled') }}
                    </button>
                </div>
            </div>

            <!-- Last Updated Info -->
            @if(isset($settings['last_payment_method_updated_by']))
                <div class="text-[11px] text-slate-400 flex items-center space-x-2 pt-2">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ __('app.last_updated_by') }}: <strong>{{ $settings['last_payment_method_updated_by'] }}</strong> ({{ $settings['last_payment_method_updated_at'] ?? 'N/A' }})</span>
                </div>
            @endif
        </div>

        <!-- Payment Cards Grid -->
        <div id="payment-cards-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($allPaymentItems as $item)
                @php
                    $isDefault = ($defaultPmKey === $item['key']);
                    
                    // Check if disabled/hidden
                    $isHidden = in_array($item['key'], $hiddenPmList);
                    if (!$isHidden && str_starts_with($item['key'], 'qr_')) {
                        $altKey = str_replace('qr_', '', $item['key']) . '_qr';
                        if (in_array($altKey, $hiddenPmList)) {
                            $isHidden = true;
                        }
                    }
                    $isEnabled = !$isHidden;
                @endphp

                <div class="pm-card group bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between"
                     data-name="{{ strtolower($item['name']) }}"
                     data-status="{{ $isEnabled ? 'enabled' : 'disabled' }}">

                    <!-- Card Top Header -->
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center space-x-2.5">
                            <span class="text-xl">{{ $item['icon'] }}</span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center space-x-1.5">
                                    <span>{{ $item['name'] }}</span>
                                    @if($isDefault)
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-100 text-amber-800 rounded-full border border-amber-300 animate-pulse">
                                            ⭐ DEFAULT
                                        </span>
                                    @endif
                                </h3>
                                <p class="text-[11px] text-slate-400">
                                    {{ $item['is_qr'] ? 'QR Code Bank' : __('app.direct_payment') }}
                                </p>
                            </div>
                        </div>

                        <!-- Enable/Disable Toggle Switch -->
                        <form method="POST" action="{{ route('admin.settings.payment-method.toggle', $item['key']) }}">
                            @csrf
                            <button type="submit" title="{{ $isEnabled ? 'Disable' : 'Enable' }}" class="px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all flex items-center space-x-1.5 {{ $isEnabled ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                                <span class="w-2 h-2 rounded-full {{ $isEnabled ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                <span>{{ $isEnabled ? '🟢 ON' : '⚫ OFF' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col items-center justify-center text-center space-y-3">
                        @if($item['is_qr'])
                            @if($item['image'])
                                <div class="w-32 h-32 rounded-xl border border-slate-200 bg-white p-2 shadow-2xs group-hover:scale-105 transition-transform duration-200 flex items-center justify-center overflow-hidden">
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain rounded-lg">
                                </div>
                            @else
                                <div class="w-32 h-32 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-400 p-2">
                                    <svg class="w-8 h-8 text-slate-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="text-[11px]">{{ __('app.no_qr_code') }}</span>
                                </div>
                            @endif
                        @else
                            <!-- Cash / Card Special Visual Frame -->
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br {{ $item['bg_gradient'] }} flex items-center justify-center text-5xl shadow-md text-white shadow-{{ $item['accent'] }}-500/20 group-hover:scale-105 transition-transform duration-200">
                                {{ $item['icon'] }}
                            </div>
                            <p class="text-xs text-slate-500 font-medium">
                                {{ $item['desc'] }}
                                @if($item['key'] === 'pm_card' && !empty($settings['card_gateway_merchant_id']))
                                    <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            API Configured ({{ strtoupper($settings['card_gateway_mode'] ?? 'LIVE') }})
                                        </span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Fee: {{ $settings['card_gateway_fee_percent'] ?? '3.00' }}%
                                        </span>
                                    </div>
                                @endif
                            </p>
                        @endif

                        @if($item['is_qr'] && $item['payload'])
                            <div class="text-[10px] text-slate-400 bg-slate-50 border border-slate-100 rounded-lg px-2.5 py-1 w-full truncate max-w-[200px]" title="{{ $item['payload'] }}">
                                KHQR: {{ Str::limit($item['payload'], 25) }}
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="p-3.5 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between gap-1.5 text-xs">
                        <!-- Left: Make Default Button -->
                        @if($isDefault)
                            <span class="px-2.5 py-1.5 rounded-xl bg-amber-50 text-amber-700 font-bold text-[11px] border border-amber-200 flex items-center space-x-1">
                                <span>✓</span>
                                <span>{{ __('app.default') }}</span>
                            </span>
                        @else
                            <form method="POST" action="{{ route('admin.settings.payment-method.set-default') }}" class="inline">
                                @csrf
                                <input type="hidden" name="key" value="{{ $item['key'] }}">
                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-semibold text-[11px] border border-slate-200 transition-all flex items-center space-x-1 shadow-2xs">
                                    <span>⭐</span>
                                    <span>{{ __('app.make_default') }}</span>
                                </button>
                            </form>
                        @endif

                        <!-- Right: Action Buttons -->
                        <div class="flex items-center space-x-1">
                            @if($item['is_qr'])
                                <button type="button" onclick="openTestQrModal('{{ $item['name'] }}', '{{ $item['image'] ? asset('storage/'.$item['image']) : '' }}', '{{ addslashes($item['payload'] ?? '') }}')" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-medium transition-all" title="Test QR Scan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </button>
                                <button type="button" onclick="openEditModal('{{ $item['key'] }}', '{{ $item['name'] }}', '{{ $item['image'] ? asset('storage/'.$item['image']) : '' }}', '{{ addslashes($item['payload'] ?? '') }}')" class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-all" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            @elseif($item['key'] === 'pm_card')
                                <button type="button" onclick="openCreditCardApiModal()" class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs hover:scale-[1.02] active:scale-95 cursor-pointer" title="Configure Bank API">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>⚙️ កែប្រែ API</span>
                                </button>
                            @endif

                            @if($item['can_delete'])
                                @php
                                    $deleteUrl = $item['is_custom']
                                        ? route('admin.settings.qr.custom.delete', $item['key'])
                                        : route('admin.settings.qr.delete', $item['key']);
                                @endphp
                                <button type="button" onclick="openDeleteModal('{{ $item['name'] }}', '{{ $deleteUrl }}')" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-medium transition-all" title="Delete">
                                    🗑️
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- MODAL 1: ADD NEW BANK QR MODAL -->
<div id="addBankModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden transform transition-all">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 flex items-center space-x-2">
                <span>➕</span>
                <span>{{ __('app.add_new_bank_qr') }}</span>
            </h3>
            <button onclick="closeAddBankModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.settings.qr.custom.store') }}" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ __('app.bank_name') }} <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="bank_name" placeholder="{{ __('app.bank_name_placeholder') }}" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ __('app.qr_image_label') }} <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="qr_image" accept="image/*" required onchange="previewAddQrImage(this)" class="w-full text-xs rounded-xl border border-slate-300 p-2">
                <div id="add-qr-preview-container" class="mt-2 text-center hidden">
                    <img id="add-qr-preview-img" class="w-28 h-28 mx-auto object-contain border rounded-xl p-1 shadow-2xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ __('app.khqr_payload') }} ({{ __('app.optional') }})
                </label>
                <textarea id="addQrPayloadInput" name="qr_payload" rows="2" placeholder="000201010212385800..." class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 font-mono"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeAddBankModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs">
                    {{ __('app.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: EDIT QR MODAL -->
<div id="editPaymentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden transform transition-all">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 flex items-center space-x-2">
                <span>✏️</span>
                <span id="editModalTitle">{{ __('app.edit') }}</span>
            </h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="editPaymentForm" method="POST" action="" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ __('app.change_qr_image') }}
                </label>
                <input type="file" name="qr_image" accept="image/*" onchange="previewEditQrImage(this)" class="w-full text-xs rounded-xl border border-slate-300 p-2">
                <div class="mt-2 text-center">
                    <img id="editQrPreviewImg" class="w-28 h-28 mx-auto object-contain border rounded-xl p-1 shadow-2xs hidden">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    {{ __('app.khqr_payload') }}
                </label>
                <textarea id="editQrPayload" name="qr_payload" rows="3" placeholder="00020101021238..." class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 font-mono"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs">
                    {{ __('app.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: TEST QR MODAL -->
<div id="testQrModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full overflow-hidden transform transition-all text-center p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 id="testModalTitle" class="text-sm font-bold text-slate-900">🧪 {{ __('app.test_qr_title') }}</h3>
            <button onclick="closeTestQrModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <div id="testQrImgContainer" class="w-48 h-48 mx-auto border-2 border-indigo-100 rounded-2xl p-3 bg-slate-50 flex items-center justify-center">
            <img id="testQrImg" class="max-w-full max-h-full object-contain rounded-lg">
            <p id="testQrEmpty" class="text-xs text-slate-400 hidden">{{ __('app.no_qr_code') }}</p>
        </div>

        <div>
            <label class="block text-[11px] font-semibold text-slate-500 mb-1">KHQR Payload String</label>
            <div class="relative">
                <input type="text" id="testQrPayload" readonly class="w-full text-[11px] font-mono rounded-lg border border-slate-200 bg-slate-50 pl-2.5 pr-14 py-1.5 text-slate-700">
                <button type="button" onclick="copyTestPayload()" class="absolute right-1 top-1 px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded hover:bg-indigo-700">
                    Copy
                </button>
            </div>
        </div>

        <button type="button" onclick="closeTestQrModal()" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
            {{ __('app.close') }}
        </button>
    </div>
</div>

<!-- MODAL 4: DELETE CONFIRMATION MODAL -->
<div id="deleteConfirmModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full overflow-hidden p-6 text-center space-y-4">
        <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center text-xl">
            ⚠️
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900">{{ __('app.confirm_delete_title') }}</h3>
            <p id="deleteModalText" class="text-xs text-slate-500 mt-1">
                {{ __('app.action_cannot_be_undone') }}
            </p>
        </div>

        <form id="deleteConfirmForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="w-1/2 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit" class="w-1/2 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs">
                    {{ __('app.delete_confirm') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    // Tab Navigation Logic
    function switchTab(tab) {
        const genTab = document.getElementById('content-general');
        const qrTab = document.getElementById('content-qr');
        const btnGen = document.getElementById('btn-tab-general');
        const btnQr = document.getElementById('btn-tab-qr');

        if (tab === 'qr') {
            genTab.classList.add('hidden');
            qrTab.classList.remove('hidden');
            btnQr.className = 'px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 flex items-center space-x-2 bg-white text-indigo-600 shadow-xs';
            btnGen.className = 'px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 flex items-center space-x-2 text-slate-600 hover:text-slate-900';
            window.location.hash = 'qr';
        } else {
            qrTab.classList.add('hidden');
            genTab.classList.remove('hidden');
            btnGen.className = 'px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 flex items-center space-x-2 bg-white text-indigo-600 shadow-xs';
            btnQr.className = 'px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 flex items-center space-x-2 text-slate-600 hover:text-slate-900';
            window.location.hash = 'general';
        }
    }

    // Auto-select tab based on URL hash or Session flash
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#qr' || {{ session('qr_tab') ? 'true' : 'false' }}) {
            switchTab('qr');
        }
    });

    // Image Previews
    function previewCompanyLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('logo-preview-img');
                const empty = document.getElementById('logo-preview-empty');
                img.src = e.target.result;
                img.classList.remove('hidden');
                if (empty) empty.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewAddQrImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('add-qr-preview-img');
                const container = document.getElementById('add-qr-preview-container');
                img.src = e.target.result;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);

            autoScanQrPayload(input.files[0], 'addQrPayloadInput');
        }
    }

    function previewEditQrImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('editQrPreviewImg');
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);

            autoScanQrPayload(input.files[0], 'editQrPayload');
        }
    }

    // Dual-Engine Auto QR Scanner (Canvas jsQR + Backup API)
    function autoScanQrPayload(file, targetInputId) {
        const targetInput = document.getElementById(targetInputId);
        if (!targetInput || !file) return;

        const origPlaceholder = targetInput.placeholder;
        targetInput.placeholder = "⏳ កំពុងអាន KHQR Payload ស្វ័យប្រវត្តិ... / Auto-scanning QR...";

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                let decodedText = null;

                // Engine 1: Local HTML5 Canvas jsQR (Offline & Fast)
                try {
                    if (typeof jsQR !== 'undefined') {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0, img.width, img.height);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "attemptBoth"
                        });
                        if (code && code.data) {
                            decodedText = code.data;
                        }
                    }
                } catch (err) {}

                if (decodedText) {
                    targetInput.value = decodedText;
                    targetInput.placeholder = origPlaceholder;
                } else {
                    // Engine 2: External QR Reader API Backup
                    fetchExternalQrApi(file, targetInput, origPlaceholder);
                }
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function fetchExternalQrApi(file, targetInput, origPlaceholder) {
        const formData = new FormData();
        formData.append('file', file);

        fetch('https://api.qrserver.com/v1/read-qr-code/', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const qrText = data[0]?.symbol[0]?.data;
            const error = data[0]?.symbol[0]?.error;

            if (qrText && !error) {
                targetInput.value = qrText;
            }
            targetInput.placeholder = origPlaceholder;
        })
        .catch(() => {
            targetInput.placeholder = origPlaceholder;
        });
    }

    // Modals Handlers
    function openAddBankModal() {
        document.getElementById('addBankModal').classList.remove('hidden');
    }
    function closeAddBankModal() {
        document.getElementById('addBankModal').classList.add('hidden');
    }

    function openEditModal(key, name, imageSrc, payload) {
        document.getElementById('editModalTitle').textContent = '{{ __("app.edit") }}: ' + name;
        const form = document.getElementById('editPaymentForm');
        form.action = "{{ url('admin/settings/qr') }}/" + key;
        
        const previewImg = document.getElementById('editQrPreviewImg');
        if (imageSrc) {
            previewImg.src = imageSrc;
            previewImg.classList.remove('hidden');
        } else {
            previewImg.classList.add('hidden');
        }

        document.getElementById('editQrPayload').value = payload || '';
        document.getElementById('editPaymentModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editPaymentModal').classList.add('hidden');
    }

    function openTestQrModal(name, imageSrc, payload) {
        document.getElementById('testModalTitle').textContent = '🧪 {{ __("app.test_qr_title") }} - ' + name;
        const img = document.getElementById('testQrImg');
        const empty = document.getElementById('testQrEmpty');

        if (imageSrc) {
            img.src = imageSrc;
            img.classList.remove('hidden');
            empty.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            empty.classList.remove('hidden');
        }

        document.getElementById('testQrPayload').value = payload || '';
        document.getElementById('testQrModal').classList.remove('hidden');
    }
    function closeTestQrModal() {
        document.getElementById('testQrModal').classList.add('hidden');
    }

    function copyTestPayload() {
        const payloadInput = document.getElementById('testQrPayload');
        payloadInput.select();
        navigator.clipboard.writeText(payloadInput.value);
        alert('Copied KHQR Payload!');
    }

    function openDeleteModal(name, actionUrl) {
        document.getElementById('deleteModalText').textContent = '{{ __("app.confirm_delete_title") }} ' + name;
        document.getElementById('deleteConfirmForm').action = actionUrl;
        document.getElementById('deleteConfirmModal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').classList.add('hidden');
    }

    // Live Search & Filter Logic
    let activeFilter = 'all';

    function filterPaymentCards() {
        const searchVal = document.getElementById('pmSearchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.pm-card');

        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const status = card.getAttribute('data-status') || '';

            const matchesSearch = name.includes(searchVal);
            const matchesStatus = (activeFilter === 'all') || (status === activeFilter);

            if (matchesSearch && matchesStatus) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function setFilterStatus(status) {
        activeFilter = status;
        
        const btnAll = document.getElementById('btn-filter-all');
        const btnEnabled = document.getElementById('btn-filter-enabled');
        const btnDisabled = document.getElementById('btn-filter-disabled');

        const activeClass = 'px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-indigo-600 shadow-xs';
        const inactiveClass = 'px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-600 hover:text-slate-900';

        btnAll.className = (status === 'all') ? activeClass : inactiveClass;
        btnEnabled.className = (status === 'enabled') ? activeClass : inactiveClass;
        btnDisabled.className = (status === 'disabled') ? activeClass : inactiveClass;

        filterPaymentCards();
    }
    function openCreditCardApiModal() {
        document.getElementById('creditCardApiModal').classList.remove('hidden');
    }
    function closeCreditCardApiModal() {
        document.getElementById('creditCardApiModal').classList.add('hidden');
    }
    function toggleCustomProviderInput(val) {
        const container = document.getElementById('customProviderContainer');
        if (container) {
            if (val === 'Generic Bank API') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    }
</script>

<!-- Credit Card Bank API / Payment Gateway Modal -->
<div id="creditCardApiModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl transition-all border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold shadow-2xs">
                    💳
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">កំណត់ប្រព័ន្ធ Bank API / Gateway (កាតឥណទាន)</h3>
                    <p class="text-xs text-slate-500">កំណត់ Merchant ID, API Keys និង Processing Fee សម្រាប់កាត់លុយតាមកាត</p>
                </div>
            </div>
            <button type="button" onclick="closeCreditCardApiModal()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.settings.card-gateway.update') }}" class="space-y-4">
            @csrf
            
            <!-- Gateway Provider -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ធនាគារ / អ្នកផ្តល់សេវា Gateway</label>
                <select name="card_gateway_provider" onchange="toggleCustomProviderInput(this.value)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 font-medium">
                    <option value="ABA PayWay" {{ ($settings['card_gateway_provider'] ?? '') === 'ABA PayWay' ? 'selected' : '' }}>ABA Bank (PayWay API / Credit Card)</option>
                    <option value="Mastercard MPGS" {{ ($settings['card_gateway_provider'] ?? '') === 'Mastercard MPGS' ? 'selected' : '' }}>Mastercard Payment Gateway Services (MPGS)</option>
                    <option value="Visa CyberSource" {{ ($settings['card_gateway_provider'] ?? '') === 'Visa CyberSource' ? 'selected' : '' }}>Visa CyberSource Gateway</option>
                    <option value="Stripe" {{ ($settings['card_gateway_provider'] ?? '') === 'Stripe' ? 'selected' : '' }}>Stripe Gateway</option>
                    <option value="Generic Bank API" {{ ($settings['card_gateway_provider'] ?? '') === 'Generic Bank API' ? 'selected' : '' }}>Generic Bank API Gateway (ធនាគារផ្សេងៗ...)</option>
                </select>
            </div>

            <!-- Custom Provider Name (Shown when Generic Bank API is selected) -->
            <div id="customProviderContainer" class="{{ ($settings['card_gateway_provider'] ?? '') === 'Generic Bank API' ? '' : 'hidden' }}">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ឈ្មោះធនាគារផ្ទាល់ខ្លួន (Custom Bank / Gateway Name)</label>
                <input type="text" name="card_gateway_custom_name" value="{{ old('card_gateway_custom_name', $settings['card_gateway_custom_name'] ?? '') }}" placeholder="Canadia Bank, Prince Bank, Sathapana..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 font-medium">
            </div>

            <!-- Merchant / Account ID -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">លេខសម្គាល់អាជីវកម្ម (Merchant ID / Account ID)</label>
                <input type="text" name="card_gateway_merchant_id" value="{{ old('card_gateway_merchant_id', $settings['card_gateway_merchant_id'] ?? '') }}" placeholder="aba_merchant_98765" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            </div>

            <!-- API Public Key -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">កូដ API Key (Public Key)</label>
                <input type="text" name="card_gateway_api_key" value="{{ old('card_gateway_api_key', $settings['card_gateway_api_key'] ?? '') }}" placeholder="pk_live_xxxxxxxxxxxxxxxxxxxx" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            </div>

            <!-- Secret Key -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">កូដសម្ងាត់ (Secret Key / Hash Secret)</label>
                <input type="password" name="card_gateway_secret_key" value="{{ old('card_gateway_secret_key', $settings['card_gateway_secret_key'] ?? '') }}" placeholder="sk_live_xxxxxxxxxxxxxxxxxxxx" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            </div>

            <!-- Fee & Environment Row -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">កម្រៃសេវាកាត់ (%) Processing Fee</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" name="card_gateway_fee_percent" value="{{ old('card_gateway_fee_percent', $settings['card_gateway_fee_percent'] ?? '3.00') }}" class="w-full rounded-xl border border-slate-300 pl-3 pr-8 py-2 text-sm font-bold text-indigo-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <span class="absolute right-3 top-2 text-sm font-bold text-slate-400">%</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ម៉ូដដំណើរការ (Environment Mode)</label>
                    <select name="card_gateway_mode" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <option value="sandbox" {{ ($settings['card_gateway_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>🧪 Sandbox (សាកល្បង / Test Mode)</option>
                        <option value="live" {{ ($settings['card_gateway_mode'] ?? '') === 'live' ? 'selected' : '' }}>🚀 Live (ដំណើរការពិត / Production Mode)</option>
                    </select>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-5">
                <button type="button" onclick="closeCreditCardApiModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                    បោះបង់ (Cancel)
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>រក្សាទុក API Settings</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
