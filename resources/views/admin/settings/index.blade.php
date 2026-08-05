@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">{{ __('app.settings') }}</h1>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-emerald-100 p-4 text-emerald-800">
    ✓ {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 rounded-lg bg-rose-100 p-4 text-rose-800">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Tabs Navigation -->
<div class="mb-4 flex space-x-1 rounded-lg bg-slate-200 p-1">
    <button 
        onclick="switchTab('company')"
        id="tab-company"
        class="tab-button flex-1 rounded-md bg-white px-4 py-2 text-sm font-medium text-slate-900 shadow-sm transition"
        role="tab"
        aria-selected="true">
        🏢 {{ __('app.company_settings') }}
    </button>
    <button 
        onclick="switchTab('gdrive')"
        id="tab-gdrive"
        class="tab-button flex-1 rounded-md px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition"
        role="tab"
        aria-selected="false">
        💾 Google Drive
    </button>
    <button 
        onclick="switchTab('qr')"
        id="tab-qr"
        class="tab-button flex-1 rounded-md px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition"
        role="tab"
        aria-selected="false">
        📱 QR Code
    </button>
</div>

<!-- Company Settings Tab -->
<div id="content-company" class="tab-content">
    <form method="POST" action="{{ route('admin.settings.company.update') }}" enctype="multipart/form-data" class="space-y-6 w-full">
        @csrf
        
        <!-- Section 1: Company Identity Section -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200/80 space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-xl font-bold">🏢</div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        {{ __('app.company_information') }}
                    </h3>
                    <p class="text-xs text-slate-500">{{ __('app.company_info_subtitle') }}</p>
                </div>
            </div>
            
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.company_name_english') }} <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="company_name" 
                        value="{{ old('company_name', $settings['company_name'] ?? 'CityTech Computer Shop') }}" 
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs"
                        placeholder="CityTech Computer Shop">
                </div>
                
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.company_name_khmer') }} <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="company_name_km" 
                        value="{{ old('company_name_km', $settings['company_name_km'] ?? 'ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច') }}" 
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs"
                        placeholder="ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        📞 {{ __('app.phone') }} <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        value="{{ old('phone', $settings['company_phone'] ?? '012-345-678') }}" 
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs"
                        placeholder="012-345-678">
                </div>
                
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        ✉️ {{ __('app.email') }} <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $settings['company_email'] ?? 'info@citytech.com') }}" 
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs"
                        placeholder="info@citytech.com">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        📍 {{ __('app.address_english') }} <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="address" 
                        required
                        rows="2"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs"
                        placeholder="Phnom Penh, Cambodia">{{ old('address', $settings['company_address'] ?? 'Phnom Penh, Cambodia') }}</textarea>
                </div>
                
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        📍 {{ __('app.address_khmer') }} <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="address_km" 
                        required
                        rows="2"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs"
                        placeholder="ភ្នំពេញ ប្រទេសកម្ពុជា">{{ old('address_km', $settings['company_address_km'] ?? 'ភ្នំពេញ ប្រទេសកម្ពុជា') }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Section 2: Financials & License Section -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200/80 space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl font-bold">💹</div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        {{ __('app.license_financials') }}
                    </h3>
                    <p class="text-xs text-slate-500">{{ __('app.license_financials_subtitle') }}</p>
                </div>
            </div>
            
            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.business_license_number') }}
                    </label>
                    <input 
                        type="text" 
                        name="business_license" 
                        value="{{ old('business_license', $settings['company_business_license'] ?? '') }}" 
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs"
                        placeholder="BL-2024-xxxxx">
                </div>
                
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.exchange_rate') }} ($1 = ៛) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="exchange_rate" 
                            value="{{ old('exchange_rate', $settings['exchange_rate'] ?? '4100') }}" 
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white pl-4 pr-10 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs font-semibold">
                        <span class="absolute right-3.5 top-3 text-xs font-bold text-slate-400">៛</span>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.card_processing_fee') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            step="0.01"
                            name="card_processing_fee" 
                            value="{{ old('card_processing_fee', $settings['card_processing_fee'] ?? '2') }}" 
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white pl-4 pr-8 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs font-semibold">
                        <span class="absolute right-3.5 top-3 text-xs font-bold text-slate-400">%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Tax Configuration Card (Integrated into Company Settings) -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200/80 space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-xl font-bold">💰</div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            {{ __('app.tax_configuration') }}
                        </h3>
                        <p class="text-xs text-slate-500">{{ __('app.tax_configuration_subtitle') }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 border border-blue-200/70">
                    {{ ($settings['tax_enabled'] ?? '0') == '1' ? '🟢 ' . __('app.tax_status_enabled') : '⚪ ' . __('app.tax_status_disabled') }}
                </span>
            </div>
            
            <!-- Enable Tax Toggle Banner -->
            <div class="p-4 bg-gradient-to-r from-blue-50/80 to-indigo-50/80 rounded-2xl border border-blue-100">
                <label class="flex items-center cursor-pointer gap-3.5">
                    <input type="checkbox" name="tax_enabled" value="1" 
                        {{ ($settings['tax_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                        class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    <div>
                        <span class="text-sm font-bold text-slate-900">
                            {{ __('app.enable_tax_system') }}
                        </span>
                        <p class="text-xs text-slate-600 mt-0.5">
                            {{ __('app.enable_tax_system_desc') }}
                        </p>
                    </div>
                </label>
            </div>
            
            <!-- Tax Rate & Registration Info -->
            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.default_tax_rate') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="default_tax_rate" 
                            value="{{ old('default_tax_rate', $settings['default_tax_rate'] ?? '10') }}" 
                            step="0.01" min="0" max="100" required
                            class="w-full rounded-xl border border-slate-300 bg-white pl-4 pr-8 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs font-semibold"
                            placeholder="10">
                        <span class="absolute right-3.5 top-3 text-xs font-bold text-slate-400">%</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ __('app.default_tax_rate_hint') }}</p>
                </div>
                
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.tax_label') }}
                    </label>
                    <input type="text" readonly value="{{ __('app.tax_label_vat') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-100/90 px-4 py-2.5 text-sm text-slate-600 cursor-not-allowed focus:outline-none font-semibold">
                    <input type="hidden" name="tax_label" value="VAT">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.tax_registration_number') }}
                    </label>
                    <input type="text" name="tax_number" 
                        value="{{ old('tax_number', $settings['tax_number'] ?? '') }}" 
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs"
                        placeholder="K001-xxxxxxxxx">
                    <p class="mt-1 text-xs text-slate-500">{{ __('app.tax_registration_number_hint') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Section 4: Logo Section -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200/80 space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 border border-violet-100 flex items-center justify-center text-violet-600 text-xl font-bold">🖼️</div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        {{ __('app.company_logo') }}
                    </h3>
                    <p class="text-xs text-slate-500">{{ __('app.company_logo_subtitle') }}</p>
                </div>
            </div>
            
            <div class="grid gap-6 md:grid-cols-2 items-center">
                @if(!empty($settings['company_logo']))
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
                    <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Company Logo" class="h-20 w-auto max-w-[140px] rounded-xl border border-slate-200 object-contain p-2 bg-white shadow-2xs">
                    <div>
                        <p class="text-xs font-bold text-slate-800">{{ __('app.current_logo') }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ __('app.current_logo_desc') }}</p>
                    </div>
                </div>
                @else
                <div class="p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-300 text-center">
                    <p class="text-xs font-semibold text-slate-400">{{ __('app.no_logo_uploaded') }}</p>
                </div>
                @endif
                
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        {{ __('app.upload_new_logo') }}
                    </label>
                    <input 
                        type="file" 
                        name="logo" 
                        accept="image/jpeg,image/jpg,image/png"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-1.5 file:text-xs file:font-bold file:text-white hover:file:bg-blue-700 cursor-pointer transition shadow-2xs">
                    <p class="mt-2 text-xs text-slate-500">{{ __('app.logo_requirements') }}</p>
                </div>
            </div>
        </div>

        <!-- Section 5: Integrations & Gateways (Payment & Telegram) -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Payment Gateway Card -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200/80 space-y-5">
                <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-xl font-bold">💳</div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            {{ __('app.payment_gateway_config') }}
                        </h3>
                        <p class="text-xs text-slate-500">{{ __('app.payment_gateway_subtitle') }}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Merchant ID</label>
                        <input 
                            type="text" 
                            name="wing_pay_merchant_id" 
                            value="{{ old('wing_pay_merchant_id', $settings['wing_pay_merchant_id'] ?? '') }}" 
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            placeholder="Enter Merchant ID">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Secret Key / API Key</label>
                        <input 
                            type="password" 
                            name="wing_pay_secret_key" 
                            value="{{ old('wing_pay_secret_key', $settings['wing_pay_secret_key'] ?? '') }}" 
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            placeholder="••••••••••••••••">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Payment API Endpoint URL</label>
                        <input 
                            type="text" 
                            name="wing_pay_api_url" 
                            value="{{ old('wing_pay_api_url', $settings['wing_pay_api_url'] ?? '') }}" 
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs text-slate-900 font-mono focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            placeholder="https://api.paymentgateway.com/v1/payments">
                        <p class="mt-1 text-xs text-slate-500">{{ __('app.payment_gateway_desc') }}</p>
                    </div>
                </div>
            </div>

            <!-- Telegram Integration Card -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200/80 space-y-5 flex flex-col justify-between">
                <div>
                    <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 text-xl font-bold">✈️</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">{{ __('app.telegram_bot_setup') }}</h3>
                            <p class="text-xs text-slate-500">{{ __('app.telegram_bot_subtitle') }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4 pt-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-700">Telegram Bot Token</label>
                            <input 
                                type="text" 
                                name="telegram_token" 
                                value="{{ old('telegram_token', $settings['telegram_token'] ?? '') }}" 
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                                placeholder="123456789:ABCdefGHIjklmno...">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">{{ __('app.webhook_endpoint_url') }}</label>
                            <div class="rounded-xl bg-slate-100 p-2.5 text-xs text-slate-800 break-all font-mono border border-slate-200/70">
                                {{ url('/api/v1/telegram/webhook') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-600">
                        {{ __('app.status_label') }} 
                        <span class="{{ !empty($settings['telegram_token']) ? 'text-emerald-600 font-bold' : 'text-rose-600 font-bold' }}">
                            {{ !empty($settings['telegram_token']) ? '🟢 ' . __('app.status_connected') : '🔴 ' . __('app.status_disconnected') }}
                        </span>
                    </span>
                    <a href="{{ route('telegram-logs.index') }}" class="rounded-xl bg-sky-600 px-4 py-2 text-xs font-bold text-white hover:bg-sky-700 transition shadow-2xs">
                        {{ __('app.manage_telegram') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Save Action Bar -->
        <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
            <span class="text-xs text-slate-500 font-semibold">{{ __('app.check_info_before_save') }}</span>
            <button type="submit" class="rounded-xl bg-blue-600 hover:bg-blue-700 px-8 py-3 text-sm font-bold text-white shadow-md hover:shadow-lg transition cursor-pointer flex items-center gap-2">
                <span>✓ {{ __('app.save_changes') }}</span>
            </button>
        </div>
    </form>
</div>

<!-- Google Drive Backup Tab -->
<div id="content-gdrive" class="tab-content hidden">
    <div class="rounded-xl bg-white shadow border border-slate-100">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6">
            @csrf
            
            <h2 class="mb-4 text-base font-bold text-slate-900 flex items-center gap-2">
                <span>💾</span> {{ __('app.google_drive_backup') ?? 'Google Drive Backup Settings' }}
            </h2>
            
            <!-- Enable Checkbox -->
            <div class="mb-5">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="google_drive_backup_enabled" value="1" 
                        {{ ($settings['google_drive_backup_enabled'] ?? '0') == '1' ? 'checked' : '' }} 
                        class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                    <span class="ml-3 text-sm font-medium text-slate-700">{{ __('app.enable_google_drive_backup') ?? 'Enable Google Drive Auto Backups' }}</span>
                </label>
                <p class="text-xs text-slate-500 mt-1 ml-8">បើកដំណើរការមុខងារបម្រុងទុកមូលដ្ឋានទិន្នន័យទៅកាន់ Google Drive ដោយស្វ័យប្រវត្តិនារៀងរាល់យប់។</p>
            </div>

            <!-- Folder ID -->
            <div class="mb-5">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Google Drive Folder ID
                </label>
                <input type="text" name="google_drive_folder_id" 
                    value="{{ old('google_drive_folder_id', $settings['google_drive_folder_id'] ?? '') }}" 
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="បញ្ចូល Folder ID នៅទីនេះ (ឧ. 1a2b3c4d5e...)">
                <p class="mt-1 text-xs text-slate-500">ទុកទំនេរ ប្រសិនបើចង់រក្សាទុកក្នុង Root (ទំព័រដើម) នៃ Google Drive។</p>
            </div>

            <!-- Service Account JSON Key -->
            <div class="mb-5">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Google Service Account JSON Key
                </label>
                <textarea name="google_drive_service_account_json" rows="6"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-mono text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="ចម្លង (Copy) រួចបិទភ្ជាប់ (Paste) មាតិកានៅក្នុងឯកសារ JSON Key របស់ Google Service Account នៅទីនេះ...">{{ old('google_drive_service_account_json', $settings['google_drive_service_account_json'] ?? '') }}</textarea>
                <p class="mt-1 text-xs text-slate-500">ចម្លងមាតិកាទាំងអស់ចេញពីឯកសារ JSON Key ដែលទាញយកពី Google Cloud Console។</p>
            </div>

            <!-- Setup Instructions Box -->
            <div class="mb-6 p-5 bg-blue-50 rounded-xl border border-blue-200">
                <h4 class="font-bold text-sm text-blue-900 mb-3 flex items-center gap-1.5">
                    <span>💡</span> របៀបដំឡើង និងប្រើប្រាស់ (Setup Instructions)
                </h4>
                <ol class="list-decimal list-inside space-y-2 text-xs text-slate-700">
                    <li>ចូលទៅកាន់ **Google Cloud Console** រួចបង្កើត Project ថ្មីមួយ។</li>
                    <li>បើកដំណើរការ **Google Drive API** សម្រាប់ Project នោះ។</li>
                    <li>បង្កើត **Service Account** រួចចូលទៅក�<!-- QR Code Settings Tab -->
<div id="content-qr" class="tab-content hidden space-y-6">
    @php
        $generalMethods = [
            'pm_cash' => ['label' => 'Cash (ប្រាក់សុទ្ធ)',        'icon' => '💵', 'desc' => 'ទូទាត់ជាសាច់ប្រាក់ផ្ទាល់ (Cash Payment)', 'is_qr' => false],
            'pm_card' => ['label' => 'Credit Card (កាតឥណទាន)', 'icon' => '💳', 'desc' => 'ទូទាត់តាមម៉ាស៊ីន POS ឬកាត (Card)',     'is_qr' => false],
        ];
        $defaultQrMethods = [
            'qr_aba'       => ['label' => 'ABA Bank',    'color' => 'blue',   'icon' => '🏦', 'is_custom' => false, 'is_qr' => true],
            'qr_acleda'    => ['label' => 'ACLEDA Bank', 'color' => 'green',  'icon' => '🏛️', 'is_custom' => false, 'is_qr' => true],
            'qr_wing'      => ['label' => 'Wing Bank',   'color' => 'lime',   'icon' => '💚', 'is_custom' => false, 'is_qr' => true],
            'qr_truemoney' => ['label' => 'TrueMoney',   'color' => 'orange', 'icon' => '📱', 'is_custom' => false, 'is_qr' => true],
        ];
        $customListSetting = $settings['custom_qr_list'] ?? '[]';
        $customList = json_decode($customListSetting, true) ?: [];
        $qrMethods = array_merge($generalMethods, $defaultQrMethods);
        foreach ($customList as $cItem) {
            if (isset($cItem['key']) && isset($cItem['label'])) {
                $qrMethods[$cItem['key']] = ['label' => $cItem['label'], 'icon' => $cItem['icon'] ?? '🏦', 'is_custom' => true, 'is_qr' => true];
            }
        }
        $defaultPaymentMethod = $settings['default_payment_method'] ?? 'pm_cash';
        $hiddenMethods = json_decode($settings['hidden_payment_methods'] ?? '[]', true) ?: [];
    @endphp

    <!-- Executive Header Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Left Header Title -->
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-xl text-white shadow-sm flex-shrink-0">
                    📱
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 leading-snug">{{ __('app.payment_method_qr_settings') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('app.qr_header_subtitle') }}</p>
                </div>
            </div>

            <!-- Right Header Actions & Filters -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Search Box -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-xs pointer-events-none">🔍</span>
                    <input type="text" id="qr_search_input" onkeyup="filterQrCards()"
                        placeholder="{{ __('app.search') }}..."
                        class="pl-8 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 w-44 transition">
                </div>

                <!-- Status Filter Pills -->
                <div class="inline-flex items-center rounded-xl bg-slate-100 p-1 text-xs font-semibold">
                    <button type="button" onclick="setQrFilter('all')" id="filter-btn-all"
                        class="qr-filter-btn px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-2xs transition-all cursor-pointer">{{ __('app.filter_all') }}</button>
                    <button type="button" onclick="setQrFilter('enabled')" id="filter-btn-enabled"
                        class="qr-filter-btn px-3 py-1.5 rounded-lg text-slate-500 hover:text-emerald-700 transition-all cursor-pointer">🟢 {{ __('app.filter_enabled') }}</button>
                    <button type="button" onclick="setQrFilter('disabled')" id="filter-btn-disabled"
                        class="qr-filter-btn px-3 py-1.5 rounded-lg text-slate-500 hover:text-rose-600 transition-all cursor-pointer">🔴 {{ __('app.filter_disabled') }}</button>
                </div>

                <!-- Add New Bank QR Button -->
                <button type="button" onclick="openAddBankModal()"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white px-4 py-2 text-xs font-bold shadow-sm transition hover:shadow cursor-pointer">
                    ➕ {{ __('app.add_new_bank_qr') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Clean Uniform Payment Cards Grid -->
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" id="qr_cards_container">

        @foreach($qrMethods as $qrKey => $qrInfo)
        @php
            $isQr = $qrInfo['is_qr'] ?? true;
            $isEnabled = !in_array($qrKey, $hiddenMethods) && !in_array(str_replace('qr_', '', $qrKey) . '_qr', $hiddenMethods);
            $isDefault = ($defaultPaymentMethod === $qrKey);
            $hasQrImage = !empty($settings[$qrKey]);
        @endphp

        <!-- ── Payment Method Card ── -->
        <div class="qr-card-item rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col transition-all duration-200 hover:shadow-md hover:-translate-y-0.5"
            data-search-term="{{ strtolower($qrInfo['label']) }} {{ strtolower($qrKey) }}"
            data-status="{{ $isEnabled ? 'enabled' : 'disabled' }}">

            <!-- Card Header Bar -->
            <div class="px-4 py-3.5 flex items-center justify-between border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-xl flex-shrink-0 leading-none">{{ $qrInfo['icon'] }}</span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <h3 class="text-xs font-bold text-slate-900 truncate">{{ $qrInfo['label'] }}</h3>
                            @if($isDefault)
                                <span class="flex-shrink-0 inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[9px] font-bold text-emerald-700 tracking-tight">⭐ DEFAULT</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-slate-400 font-mono truncate mt-0.5">{{ $qrKey }}</p>
                    </div>
                </div>

                <!-- Toggle ON/OFF Switch -->
                <form method="POST" action="{{ route('admin.settings.payment-method.toggle', $qrKey) }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit"
                        title="{{ $isEnabled ? 'Disable method' : 'Enable method' }}"
                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full border transition-all cursor-pointer {{ $isEnabled ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                        {{ $isEnabled ? '🟢 ON' : '🔴 OFF' }}
                    </button>
                </form>
            </div>

            <!-- Card Center Content (QR Image or Method Icon) -->
            <div class="flex flex-col items-center justify-center py-6 px-4 bg-white border-b border-slate-100 min-h-[165px]">
                @if(!$isQr)
                    <div class="flex flex-col items-center gap-2 text-center">
                        <div class="w-20 h-20 rounded-2xl bg-indigo-50/80 border border-indigo-100 flex items-center justify-center text-4xl shadow-2xs">
                            {{ $qrInfo['icon'] }}
                        </div>
                        <span class="text-xs font-bold text-slate-800 mt-1">{{ $qrInfo['label'] }}</span>
                        <span class="text-[11px] text-slate-500 max-w-[210px] leading-tight">{{ $settings[$qrKey . '_description'] ?? ($qrInfo['desc'] ?? '') }}</span>
                    </div>
                @elseif($hasQrImage)
                    <div class="group relative">
                        <img src="{{ asset('storage/' . $settings[$qrKey]) }}"
                            alt="{{ $qrInfo['label'] }} QR"
                            class="max-h-36 max-w-full object-contain rounded-xl border border-slate-200/80 bg-white p-2 shadow-xs transition-transform duration-200 group-hover:scale-105">
                    </div>
                @else
                    <div class="flex flex-col items-center gap-2 text-slate-400">
                        <div class="w-20 h-20 rounded-2xl bg-slate-100 border border-dashed border-slate-200 flex flex-col items-center justify-center text-3xl">📷</div>
                        <span class="text-[11px] font-medium text-slate-500">{{ __('app.no_logo_uploaded') }}</span>
                    </div>
                @endif
            </div>

            <!-- Card Action Footer -->
            <div class="px-4 py-3 bg-slate-50/50 flex items-center justify-between gap-2 border-b border-slate-100">
                <!-- Make Default Button -->
                @if(!$isDefault)
                    <form method="POST" action="{{ route('admin.settings.payment-method.set-default') }}">
                        @csrf
                        <input type="hidden" name="key" value="{{ $qrKey }}">
                        <button type="submit" class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 hover:text-emerald-700 border border-slate-200 bg-white rounded-lg px-2.5 py-1 transition hover:border-emerald-300 shadow-2xs cursor-pointer">
                            ⭐ {{ __('app.make_default') }}
                        </button>
                    </form>
                @else
                    <span class="text-[11px] font-bold text-emerald-700 flex items-center gap-1">✓ {{ __('app.is_default') }}</span>
                @endif

                <!-- Test & Edit Buttons -->
                <div class="flex items-center gap-1.5">
                    @if($isQr)
                    <button type="button"
                        onclick="openTestPaymentModal('{{ addslashes($qrInfo['label']) }}','{{ $hasQrImage ? asset('storage/'.$settings[$qrKey]) : '' }}','{{ addslashes($settings[$qrKey.'_payload'] ?? '') }}')"
                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 border border-indigo-200 bg-indigo-50/80 rounded-lg px-2.5 py-1 transition hover:bg-indigo-100 cursor-pointer">
                        🧪 Test
                    </button>
                    @endif
                    <button type="button" onclick="toggleEditForm('edit_{{ $qrKey }}')"
                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 hover:text-indigo-600 border border-slate-200 bg-white rounded-lg px-2.5 py-1 transition hover:border-indigo-300 cursor-pointer">
                        ✏️ {{ __('app.edit') }}
                    </button>
                </div>
            </div>

            <!-- Collapsible Edit Panel (Toggled smoothly) -->
            <div id="edit_{{ $qrKey }}" class="hidden px-4 py-4 border-t border-slate-100 bg-slate-50/80 space-y-3">
                <form method="POST" action="{{ route('admin.settings.qr.update', $qrKey) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    @if($isQr)
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">{{ __('app.qr_image_label') }}</label>
                        <input type="file" id="qr_file_{{ $qrKey }}" name="qr_image"
                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                            onchange="previewImage(this,'preview_img_{{ $qrKey }}')"
                            class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-[11px] text-slate-900 file:mr-2 file:rounded-lg file:border-0 file:bg-slate-800 file:px-2.5 file:py-1 file:text-[10px] file:font-bold file:text-white hover:file:bg-slate-900 cursor-pointer transition">
                        <div id="qr_status_{{ $qrKey }}" class="mt-1.5 text-[11px] font-semibold hidden"></div>
                        <img id="preview_img_{{ $qrKey }}" class="hidden mt-2 max-h-20 rounded-xl border border-indigo-200 bg-white p-1 shadow-xs mx-auto" alt="Preview">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">KHQR Payload</label>
                        <textarea id="qr_payload_{{ $qrKey }}" name="qr_payload" rows="2"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] text-slate-900 font-mono focus:border-indigo-400 focus:outline-none resize-none transition"
                            placeholder="000201... (ស្វ័យប្រវត្ត)">{{ old('qr_payload', $settings[$qrKey . '_payload'] ?? '') }}</textarea>
                    </div>
                    @endif
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">{{ __('app.qr_notes_label') }}</label>
                        <input type="text" name="qr_description"
                            value="{{ old('qr_description', $settings[$qrKey . '_description'] ?? '') }}"
                            placeholder="{{ __('app.qr_notes_placeholder') }}"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] text-slate-900 placeholder-slate-400 focus:border-indigo-400 focus:outline-none transition">
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 px-4 py-2 text-xs font-bold text-white shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        💾 {{ __('app.save') }}
                    </button>
                </form>

                <!-- Footer Audit Meta & Delete Button -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 text-[10px] text-slate-400">
                    <span>{{ __('app.updated_by') }}: <strong class="text-slate-600">{{ $settings[$qrKey.'_updated_by'] ?? auth()->user()->name }}</strong></span>
                    <span>{{ __('app.last_update') }}: <strong class="text-slate-600">{{ $settings[$qrKey.'_updated_at'] ?? now()->format('d M Y') }}</strong></span>
                </div>
                @if($isQr && ($hasQrImage || !empty($settings[$qrKey . '_payload'])))
                <button type="button"
                    onclick="openDeleteModal('{{ addslashes($qrInfo['label']) }}', '{{ ($qrInfo['is_custom'] ?? false) ? route('admin.settings.qr.custom.delete', $qrKey) : route('admin.settings.qr.delete', $qrKey) }}')"
                    class="w-full rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 text-[11px] font-bold text-rose-600 transition flex items-center justify-center gap-1 cursor-pointer">
                    🗑️ {{ __('app.delete_qr') }}
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>ite shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                            💾 {{ __('app.save') }}
                        </button>
                    </form>
                    <!-- Last Updated -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[10px] text-slate-400">
                        <span>{{ __('app.updated_by') }}: <strong class="text-slate-600">{{ $settings[$qrKey.'_updated_by'] ?? auth()->user()->name }}</strong></span>
                        <span>{{ __('app.last_update') }}: <strong class="text-slate-600">{{ $settings[$qrKey.'_updated_at'] ?? now()->format('d M Y') }}</strong></span>
                    </div>
                    <!-- Delete -->
                    @if($isQr && ($hasQrImage || !empty($settings[$qrKey . '_payload'])))
                    <button type="button"
                        onclick="openDeleteModal('{{ addslashes($qrInfo['label']) }}', '{{ ($qrInfo['is_custom'] ?? false) ? route('admin.settings.qr.custom.delete', $qrKey) : route('admin.settings.qr.delete', $qrKey) }}')"
                        class="w-full rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 text-[11px] font-bold text-rose-600 transition flex items-center justify-center gap-1 cursor-pointer">
                        🗑️ {{ __('app.delete_qr') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>





<!-- 5. Custom Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-md w-full p-6 text-center space-y-4">
        <div class="w-14 h-14 rounded-full bg-rose-50 border border-rose-100 text-rose-600 text-2xl flex items-center justify-center mx-auto font-bold">🗑️</div>
        <div>
            <h3 class="text-lg font-bold text-slate-900" id="deleteModalTitle">Delete QR Code?</h3>
            <p class="text-xs text-slate-500 mt-1">This action cannot be undone. រូបភាព និងទិន្នន័យ QR នេះនឹងត្រូវលុបចេញពីប្រព័ន្ធ។</p>
        </div>
        <form id="deleteModalForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" onclick="closeDeleteModal()" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                    Cancel (បោះបង់)
                </button>
                <button type="submit" class="rounded-xl bg-rose-600 hover:bg-rose-700 px-5 py-2.5 text-xs font-bold text-white shadow-md transition cursor-pointer">
                    Delete (លុបចោល)
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 6. Test Payment Preview Modal -->
<div id="testPaymentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full p-6 text-center space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span>🧪</span> សាកល្បងទូទាត់ប្រាក់ (Test Payment)
            </h3>
            <button type="button" onclick="closeTestPaymentModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer">✕</button>
        </div>

        <div class="space-y-3">
            <h4 class="text-base font-bold text-slate-900" id="testModalBankName">ABA Bank</h4>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-center min-h-[160px]" id="testModalQrContainer">
                <img id="testModalQrImg" class="max-h-48 object-contain rounded-xl border border-slate-200 bg-white p-2 shadow-2xs" alt="QR Code">
            </div>
            <div class="p-3 bg-slate-100 rounded-xl text-left">
                <label class="text-[11px] font-bold text-slate-600 block mb-1">KHQR String Payload:</label>
                <p id="testModalPayload" class="text-[10px] font-mono text-slate-800 break-all select-all"></p>
            </div>
        </div>

        <div class="pt-2">
            <button type="button" onclick="closeTestPaymentModal()" class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-xs font-bold text-white shadow-md transition cursor-pointer">
                ✓ យល់ព្រម (Close Test)
            </button>
        </div>
    </div>
</div>

<!-- jsQR Library for instant client-side QR decoding -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active styles from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
        button.classList.add('text-slate-600');
        button.setAttribute('aria-selected', 'false');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active styles to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('text-slate-600');
    activeButton.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
    activeButton.setAttribute('aria-selected', 'true');
}

// Switch to correct tab on load
@if(session('qr_tab'))
    switchTab('qr');
@else
    switchTab('company');
@endif

// Instant Browser-Side Image Preview
function previewImage(input, previewImgId) {
    const previewImg = document.getElementById(previewImgId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Interactive Live Search
function filterQrCards() {
    const query = document.getElementById('qr_search_input').value.toLowerCase().trim();
    document.querySelectorAll('.qr-card-item').forEach(card => {
        const term = card.getAttribute('data-search-term') || '';
        if (term.includes(query)) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

// Toggle collapsible edit form per card
function toggleEditForm(formId) {
    const panel = document.getElementById(formId);
    const key = formId.replace('edit_', '');
    const chevron = document.getElementById('chevron_' + key);
    if (panel) {
        const isHidden = panel.classList.contains('hidden');
        panel.classList.toggle('hidden', !isHidden);
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    }
}

// Status Filter Pills (All / Enabled / Disabled)
function setQrFilter(filterType) {
    document.querySelectorAll('.qr-filter-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'text-slate-900', 'shadow-2xs');
        btn.classList.add('text-slate-600');
    });

    const activeBtn = document.getElementById('filter-btn-' + filterType);
    if (activeBtn) {
        activeBtn.classList.remove('text-slate-600');
        activeBtn.classList.add('bg-white', 'text-slate-900', 'shadow-2xs');
    }

    document.querySelectorAll('.qr-card-item').forEach(card => {
        const status = card.getAttribute('data-status');
        if (filterType === 'all' || status === filterType) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

// Modal Handlers
function openDeleteModal(bankName, actionUrl) {
    document.getElementById('deleteModalTitle').innerText = 'Delete ' + bankName + ' QR?';
    document.getElementById('deleteModalForm').action = actionUrl;
    document.getElementById('deleteConfirmModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').classList.add('hidden');
}

function openTestPaymentModal(bankName, qrImgUrl, payloadText) {
    document.getElementById('testModalBankName').innerText = bankName;
    const imgEl = document.getElementById('testModalQrImg');
    const container = document.getElementById('testModalQrContainer');
    
    if (qrImgUrl) {
        imgEl.src = qrImgUrl;
        imgEl.classList.remove('hidden');
    } else {
        imgEl.classList.add('hidden');
    }
    
    document.getElementById('testModalPayload').innerText = payloadText || '00020101021129380009khqr@aclb0111855...';
    document.getElementById('testPaymentModal').classList.remove('hidden');
}

function closeTestPaymentModal() {
    document.getElementById('testPaymentModal').classList.add('hidden');
}

// Instant Browser-Side QR Code Decoder function
function bindQrDecoder(fileInputId, textareaId, badgeId) {
    const fileInput = document.getElementById(fileInputId);
    const textarea = document.getElementById(textareaId);
    const badge = document.getElementById(badgeId);

    if (fileInput && textarea) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            textarea.value = '';

            if (badge) {
                badge.classList.remove('hidden', 'text-emerald-600', 'text-amber-600');
                badge.classList.add('text-blue-600');
                badge.innerText = '⏳ កំពុងអានអត្ថបទ KHQR ពីរូបភាព...';
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0, img.width, img.height);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    let code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert",
                    });

                    if (!code) {
                        code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "attemptBoth",
                        });
                    }

                    if (code && code.data) {
                        textarea.value = code.data;
                        if (badge) {
                            badge.className = 'mt-1 text-xs font-semibold text-emerald-600 flex items-center gap-1';
                            badge.innerHTML = '✓ បានអានកូដ KHQR ជោគជ័យ!';
                        }
                    } else {
                        if (badge) {
                            badge.className = 'mt-1 text-xs font-semibold text-amber-600 flex items-center gap-1';
                            badge.innerHTML = '⚠️ មិនអាចអានកូដស្វ័យប្រវត្តិបានទេ។ សូមចម្លង (Paste) អត្ថបទ KHQR ចូលដោយដៃ។';
                        }
                    }
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Bind custom new bank form input
    bindQrDecoder('qr_file_new_custom', 'qr_payload_new_custom', 'qr_status_new_custom');

    // Bind all cards in QR Code tab
    @json(array_keys($qrMethods)).forEach(function(key) {
        bindQrDecoder('qr_file_' + key, 'qr_payload_' + key, 'qr_status_' + key);
    });
});
</script>

@endsection
