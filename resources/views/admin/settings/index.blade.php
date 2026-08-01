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
        class="tab-button flex-1 rounded-md bg-white px-4 py-2 text-sm font-medium text-slate-900 shadow-sm"
        role="tab"
        aria-selected="true">
        🏢 {{ __('app.company_settings') }}
    </button>
    <button 
        onclick="switchTab('tax')"
        id="tab-tax"
        class="tab-button flex-1 rounded-md px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900"
        role="tab"
        aria-selected="false">
        💰 ពន្ធ / Tax
    </button>
    <button 
        onclick="switchTab('gdrive')"
        id="tab-gdrive"
        class="tab-button flex-1 rounded-md px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900"
        role="tab"
        aria-selected="false">
        💾 Google Drive
    </button>
    <button 
        onclick="switchTab('qr')"
        id="tab-qr"
        class="tab-button flex-1 rounded-md px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900"
        role="tab"
        aria-selected="false">
        📱 QR Code
    </button>
</div>

<!-- Company Settings Tab -->
<div id="content-company" class="tab-content">
    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('admin.settings.company.update') }}" enctype="multipart/form-data" class="rounded-xl bg-white p-6 shadow border border-slate-100 lg:col-span-2">
            @csrf
            
            <!-- Company Name Section -->
            <div class="mb-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    {{ __('app.company_information') }}
                </h3>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            {{ __('app.company_name_english') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="company_name" 
                            value="{{ old('company_name', $settings['company_name'] ?? 'CityTech Computer Shop') }}" 
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="CityTech Computer Shop">
                    </div>
                    
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700" lang="km">
                            {{ __('app.company_name_khmer') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="company_name_km" 
                            value="{{ old('company_name_km', $settings['company_name_km'] ?? 'ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច') }}" 
                            required
                            lang="km"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច">
                    </div>
                </div>
            </div>
            
            <!-- Contact Information Section -->
            <div class="mb-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    {{ __('app.contact_information') }}
                </h3>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            {{ __('app.phone') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', $settings['company_phone'] ?? '012-345-678') }}" 
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="012-345-678">
                    </div>
                    
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            {{ __('app.email') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $settings['company_email'] ?? 'info@citytech.com') }}" 
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="info@citytech.com">
                    </div>
                </div>
            </div>
            
            <!-- Address Section -->
            <div class="mb-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    {{ __('app.address') }}
                </h3>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            {{ __('app.address_english') }} <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            name="address" 
                            required
                            rows="2"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="Phnom Penh, Cambodia">{{ old('address', $settings['company_address'] ?? 'Phnom Penh, Cambodia') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700" lang="km">
                            {{ __('app.address_khmer') }} <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            name="address_km" 
                            required
                            rows="2"
                            lang="km"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="ភ្នំពេញ ប្រទេសកម្ពុជា">{{ old('address_km', $settings['company_address_km'] ?? 'ភ្នំពេញ ប្រទេសកម្ពុជា') }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Business License & Financials Section -->
            <div class="mb-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    អាជ្ញាប័ណ្ណ និងការកំណត់ហិរញ្ញវត្ថុ / License & Financials
                </h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            {{ __('app.business_license_number') }}
                        </label>
                        <input 
                            type="text" 
                            name="business_license" 
                            value="{{ old('business_license', $settings['company_business_license'] ?? '') }}" 
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="BL-2024-xxxxx">
                    </div>
                    
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            អត្រាប្តូរប្រាក់រៀល / KHR Exchange Rate ($1 = ៛) <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="exchange_rate" 
                            value="{{ old('exchange_rate', $settings['exchange_rate'] ?? '4100') }}" 
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            កម្រៃសេវាទូទាត់កាត / Card Processing Fee (%) <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01"
                            name="card_processing_fee" 
                            value="{{ old('card_processing_fee', $settings['card_processing_fee'] ?? '2') }}" 
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            
            <!-- Logo Section -->
            <div class="mb-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    {{ __('app.company_logo') }}
                </h3>
                
                @if(!empty($settings['company_logo']))
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Company Logo" class="h-20 rounded-lg border border-slate-300 object-contain">
                    <p class="mt-1.5 text-xs text-slate-600">{{ __('app.current_logo') }}</p>
                </div>
                @endif
                
                <div>
                    <label class="mb-1.5 block text-sm text-slate-700">
                        {{ __('app.upload_new_logo') }}
                    </label>
                    <input 
                        type="file" 
                        name="logo" 
                        accept="image/jpeg,image/jpg,image/png"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 file:mr-3 file:rounded file:border-0 file:bg-blue-600 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-white hover:file:bg-blue-700">
                    <p class="mt-1 text-xs text-slate-600">{{ __('app.logo_requirements') }}</p>
                </div>
            </div>



            
            <!-- Wing Pay Gateway Section -->
            <div class="mb-6 border-t border-slate-100 pt-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-lime-500"></span>
                    Wing Pay Gateway Configuration (សម្រាប់ទូទាត់កាតឥណទានពិតប្រាកដ)
                </h3>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            Wing Pay Merchant ID
                        </label>
                        <input 
                            type="text" 
                            name="wing_pay_merchant_id" 
                            value="{{ old('wing_pay_merchant_id', $settings['wing_pay_merchant_id'] ?? '') }}" 
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="Enter Wing Merchant ID">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            Wing Pay Secret Key / API Key
                        </label>
                        <input 
                            type="password" 
                            name="wing_pay_secret_key" 
                            value="{{ old('wing_pay_secret_key', $settings['wing_pay_secret_key'] ?? '') }}" 
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="••••••••••••••••">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="mb-1.5 block text-sm text-slate-700">
                        Wing Pay API Endpoint URL
                    </label>
                    <input 
                        type="text" 
                        name="wing_pay_api_url" 
                        value="{{ old('wing_pay_api_url', $settings['wing_pay_api_url'] ?? 'https://sandbox-api.wingmoney.com/v1/payments') }}" 
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 font-mono"
                        placeholder="https://sandbox-api.wingmoney.com/v1/payments">
                    <p class="mt-1 text-xs text-slate-500">Default Sandbox URL is configured. Replace with production endpoint when launching live.</p>
                </div>
            </div>

            <!-- Telegram Token Section -->
            <div class="mb-6 border-t border-slate-100 pt-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    Telegram Token
                </h3>
                <div>
                    <label class="mb-1.5 block text-sm text-slate-700">
                        Telegram Bot Token
                    </label>
                    <input 
                        type="text" 
                        name="telegram_token" 
                        value="{{ old('telegram_token', $settings['telegram_token'] ?? '') }}" 
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        placeholder="វាយបញ្ចូល Telegram Bot Token នៅទីនេះ...">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    {{ __('app.save_changes') }}
                </button>
            </div>
        </form>

        <!-- Telegram Setup Card -->
        <div class="rounded-xl bg-white p-6 shadow border border-slate-100 self-start">
            <h2 class="mb-3 text-base font-semibold text-slate-900">Telegram Setup</h2>
            <p class="mb-3 text-xs text-slate-600">Use this webhook URL in your Telegram bot configuration.</p>
            <div class="rounded-lg bg-slate-100 p-3 text-xs text-slate-800 break-all font-mono">
                {{ url('/api/v1/telegram/webhook') }}
            </div>
            <p class="mt-4 text-xs text-slate-600">
                Status:
                <span class="font-semibold {{ !empty($settings['telegram_token']) ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ !empty($settings['telegram_token']) ? 'Configured' : 'Not configured' }}
                </span>
            </p>
            <a href="{{ route('telegram-logs.index') }}" class="mt-4 inline-flex rounded-lg bg-cyan-600 px-4 py-2 text-xs font-semibold text-white hover:bg-cyan-700">Open Telegram Center</a>
        </div>
    </div>
</div>

<!-- Tax Settings Tab -->
<div id="content-tax" class="tab-content hidden">
    <div class="rounded-xl bg-white shadow border border-slate-100">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6">
            @csrf
            
            <div class="mb-6">
                <h3 class="mb-4 text-lg font-semibold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">💰</span>
                    <span lang="km">ការកំណត់ពន្ធ / Tax Configuration</span>
                </h3>
                
                <!-- Enable Tax -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="tax_enabled" value="1" 
                            {{ ($settings['tax_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-slate-900" lang="km">
                            បើកប្រើប្រាស់ពន្ធ / Enable Tax System
                        </span>
                    </label>
                    <p class="mt-2 ml-8 text-xs text-slate-600" lang="km">
                        បើកដើម្បីគណនាពន្ធលើផលិតផល និងការលក់
                    </p>
                </div>
                
                <!-- Tax Rate -->
                <div class="grid gap-4 md:grid-cols-2 mb-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700" lang="km">
                            អត្រាពន្ធលំនាំដើម (%) / Default Tax Rate
                            <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="default_tax_rate" 
                            value="{{ old('default_tax_rate', $settings['default_tax_rate'] ?? '10') }}" 
                            step="0.01" min="0" max="100" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-slate-500">ឧ. 10 សម្រាប់ 10%</p>
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700" lang="km">
                            ស្លាកពន្ធ / Tax Label
                        </label>
                        <input type="text" readonly value="VAT (អាករលើតម្លៃបន្ថែម)"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-500 cursor-not-allowed focus:outline-none">
                        <input type="hidden" name="tax_label" value="VAT">
                    </div>
                </div>
                
                <!-- Tax Number -->
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-slate-700" lang="km">
                        លេខពន្ធក្រុមហ៊ុន / Tax Registration Number
                    </label>
                    <input type="text" name="tax_number" 
                        value="{{ old('tax_number', $settings['tax_number'] ?? '') }}" 
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="K001-xxxxxxxxx">
                    <p class="mt-1 text-xs text-slate-500" lang="km">លេខចុះបញ្ជីពន្ធពីនាយកដ្ឋានពន្ធដារ (ប្រសិនបើមាន)</p>
                </div>
                
                <!-- Tax Information Box -->
                <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                    <div class="flex gap-3">
                        <div class="text-amber-600 text-xl">ℹ️</div>
                        <div class="text-sm text-slate-700">
                            <p class="font-medium mb-2" lang="km">ចំណាំសំខាន់៖</p>
                            <ul class="list-disc list-inside space-y-1 text-xs" lang="km">
                                <li>អត្រាពន្ធលំនាំដើមនេះនឹងត្រូវបានប្រើសម្រាប់ផលិតផលដែលមានពន្ធ</li>
                                <li>អ្នកអាចកំណត់អត្រាពន្ធផ្សេងគ្នាសម្រាប់ផលិតផលនីមួយៗ</li>
                                <li>ពន្ធនឹងត្រូវបានគណនាដោយស្វ័យប្រវត្តិនៅពេលលក់</li>
                                <li>ពន្ធ VAT នៅកម្ពុជាជាទូទៅគឺ 10%</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <span lang="km">រក្សាទុក / Save Changes</span>
                </button>
            </div>
        </form>
    </div>
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
                    <li>បង្កើត **Service Account** រួចចូលទៅកាន់ Tab **Keys** -> **Add Key** -> **Create new key** (ជ្រើសរើសប្រភេទ **JSON**)។</li>
                    <li>បើកមើលឯកសារ JSON នោះ រួចចម្លងមាតិកាទាំងអស់មកបិទភ្ជាប់ (Paste) ក្នុងប្រអប់ខាងលើ។</li>
                    <li>ចម្លងអាសយដ្ឋានអ៊ីមែលរបស់ Service Account (មានកន្ទុយ `@...iam.gserviceaccount.com`)។</li>
                    <li>ចូលទៅ **Google Drive** ផ្ទាល់ខ្លួន បង្កើត Folder មួយ រួចធ្វើការ Share ទៅកាន់អ៊ីមែលរបស់ Service Account នោះដោយផ្តល់សិទ្ធិជា **Editor**។</li>
                </ol>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <span>រក្សាទុក / Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- QR Code Settings Tab -->
<div id="content-qr" class="tab-content hidden">
    <div class="mb-6">
        <h2 class="text-base font-bold text-slate-900">📱 QR Code សម្រាប់ការទូទាត់</h2>
        <p class="text-sm text-slate-500 mt-1">Upload រូបភាព QR Code សម្រាប់វិធីទូទាត់នីមួយៗ។ រូបភាពទាំងនេះនឹងត្រូវបង្ហាញនៅពេលអតិថិជនជ្រើសរើសការទូទាត់។</p>
    </div>

    @php
        $defaultQrMethods = [
            'qr_aba'       => ['label' => 'ABA Bank',    'color' => 'blue',   'icon' => '🏦', 'is_custom' => false],
            'qr_acleda'    => ['label' => 'ACLEDA Bank', 'color' => 'green',  'icon' => '🏛️', 'is_custom' => false],
            'qr_wing'      => ['label' => 'Wing Bank',   'color' => 'lime',   'icon' => '💚', 'is_custom' => false],
            'qr_truemoney' => ['label' => 'TrueMoney',   'color' => 'orange', 'icon' => '📱', 'is_custom' => false],
        ];

        $customListSetting = $settings['custom_qr_list'] ?? '[]';
        $customList = json_decode($customListSetting, true) ?: [];

        $qrMethods = $defaultQrMethods;
        foreach ($customList as $cItem) {
            if (isset($cItem['key']) && isset($cItem['label'])) {
                $qrMethods[$cItem['key']] = [
                    'label' => $cItem['label'],
                    'icon' => $cItem['icon'] ?? '🏦',
                    'is_custom' => true,
                ];
            }
        }
    @endphp

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Card for Adding New Custom Bank QR -->
        <div class="rounded-xl bg-indigo-50/40 border-2 border-dashed border-indigo-200 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">➕</span>
                    <h3 class="text-sm font-bold text-slate-900">បន្ថែម QR Code ធនាគារថ្មី</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">បន្ថែមរូបភាព QR Code សម្រាប់ធនាគារផ្សេងទៀតដូចជា Canadia Bank, Sathapana...</p>

                <form method="POST" action="{{ route('admin.settings.qr.custom.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="mb-1 block text-xs font-semibold text-slate-700">ឈ្មោះធនាគារ (Bank Name)</label>
                        <input 
                            type="text" 
                            name="bank_name" 
                            required 
                            placeholder="ឧទាហរណ៍៖ Canadia Bank, Sathapana..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                    </div>
                    <div class="mb-3">
                        <label class="mb-1 block text-xs font-semibold text-slate-700">រូបភាព QR Code</label>
                        <input 
                            type="file" 
                            id="qr_file_new_custom"
                            name="qr_image" 
                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-900 file:mr-2 file:rounded file:border-0 file:bg-indigo-600 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700"
                        >
                        <div id="qr_status_new_custom" class="mt-1 text-xs font-semibold hidden"></div>
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 block text-xs font-semibold text-slate-700">អត្ថបទកូដធនាគារ (KHQR Payload Text)</label>
                        <textarea 
                            id="qr_payload_new_custom"
                            name="qr_payload" 
                            rows="2"
                            class="w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-900 font-mono"
                            placeholder="000201010211... (ប្រព័ន្ធនឹងទាញយកកូដនេះដោយស្វ័យប្រវត្តពេលជ្រើសរើសរូបភាព)"
                        ></textarea>
                    </div>
                    <button 
                        type="submit" 
                        class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition flex items-center justify-center gap-1 shadow-sm"
                    >
                        ➕ បន្ថែម QR Code ធនាគារនេះ
                    </button>
                </form>
            </div>
        </div>

        @foreach($qrMethods as $qrKey => $qrInfo)
        <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <!-- Card Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="text-2xl">{{ $qrInfo['icon'] }}</span>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ $qrInfo['label'] }}</h3>
                    <p class="text-xs text-slate-400">{{ $qrKey }}</p>
                </div>
                @if(!empty($settings[$qrKey]))
                    <span class="ml-auto inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">✓ មាន</span>
                @else
                    <span class="ml-auto inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">គ្មាន</span>
                @endif
            </div>

            <!-- QR Preview -->
            <div class="flex-1 flex items-center justify-center p-5 bg-slate-50 min-h-[180px]">
                @if(!empty($settings[$qrKey]))
                    <img 
                        src="{{ asset('storage/' . $settings[$qrKey]) }}" 
                        alt="{{ $qrInfo['label'] }} QR"
                        class="max-h-40 max-w-full object-contain rounded-lg border border-slate-200 bg-white p-2 shadow-sm"
                    >
                @else
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto rounded-xl bg-slate-200 flex items-center justify-center text-3xl mb-2">📷</div>
                        <p class="text-xs text-slate-400">មិនទាន់មានរូបភាព</p>
                    </div>
                @endif
            </div>

            <!-- Upload Form -->
            <div class="p-4 border-t border-slate-100">
                <form 
                    method="POST" 
                    action="{{ route('admin.settings.qr.update', $qrKey) }}" 
                    enctype="multipart/form-data"
                >
                    @csrf
                    <div class="mb-3">
                        <label class="mb-1.5 block text-xs font-medium text-slate-600">ជ្រើសរើសរូបភាព QR Code ថ្មី</label>
                        <input 
                            type="file" 
                            id="qr_file_{{ $qrKey }}"
                            name="qr_image" 
                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                            class="w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-900 file:mr-2 file:rounded file:border-0 file:bg-indigo-600 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700"
                        >
                        <div id="qr_status_{{ $qrKey }}" class="mt-1 text-xs font-semibold hidden"></div>
                    </div>

                    <!-- KHQR Payload Text Section -->
                    <div class="mb-3">
                        <label class="mb-1 block text-xs font-semibold text-slate-700">
                            អត្ថបទកូដធនាគារ {{ $qrInfo['label'] }} (Payload Text)
                        </label>
                        <textarea 
                            id="qr_payload_{{ $qrKey }}"
                            name="qr_payload" 
                            rows="2"
                            class="w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-900 font-mono"
                            placeholder="ឧទាហរណ៍៖ 0002010102112938... (ប្រព័ន្ធនឹងទាញយកកូដនេះដោយស្វ័យប្រវត្តពេលជ្រើសរើសរូបភាព)"
                        >{{ old('qr_payload', $settings[$qrKey . '_payload'] ?? '') }}</textarea>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition flex items-center justify-center gap-1 shadow-sm"
                    >
                        💾 រក្សាទុក {{ $qrInfo['label'] }} QR Code
                    </button>
                </form>

                @if(!empty($settings[$qrKey]) || !empty($settings[$qrKey . '_payload']))
                <form 
                    method="POST" 
                    action="{{ ($qrInfo['is_custom'] ?? false) ? route('admin.settings.qr.custom.delete', $qrKey) : route('admin.settings.qr.delete', $qrKey) }}" 
                    class="mt-2"
                    onsubmit="return confirm('តើអ្នកពិតជាចង់លុប QR Code {{ $qrInfo['label'] }} ចោលមែនទេ?')"
                >
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="w-full rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-100 transition flex items-center justify-center gap-1"
                    >
                        🗑️ លុប QR Code
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
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
