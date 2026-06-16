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
                            រូបិយប័ណ្ណ / Currency <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="currency" 
                            value="{{ old('currency', $settings['currency'] ?? 'USD') }}" 
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="USD">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm text-slate-700">
                            អត្រាការប្រាក់លំនាំដើម / Default Interest Rate (%) <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01"
                            name="default_interest_rate" 
                            value="{{ old('default_interest_rate', $settings['default_interest_rate'] ?? '0') }}" 
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

            <!-- Bank QR Code Section -->
            <div class="mb-6 border-t border-slate-100 pt-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    រូបភាព QR Code ធនាគាររបស់ហាង / Shop Bank QR Code
                </h3>
                
                @if(!empty($settings['company_bank_qr']))
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $settings['company_bank_qr']) }}" alt="Bank QR Code" class="h-44 rounded-lg border border-slate-300 object-contain bg-white p-2">
                    <p class="mt-1.5 text-xs text-slate-600">QR Code បច្ចុប្បន្ន / Current QR Code</p>
                </div>
                @endif
                
                <div>
                    <label class="mb-1.5 block text-sm text-slate-700">
                        បញ្ចូលរូបភាព QR Code ថ្មី / Upload New QR Code Image
                    </label>
                    <input 
                        type="file" 
                        name="bank_qr" 
                        accept="image/jpeg,image/jpg,image/png"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 file:mr-3 file:rounded file:border-0 file:bg-blue-600 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-white hover:file:bg-blue-700">
                    <p class="mt-1 text-xs text-slate-600">{{ __('app.logo_requirements') }}</p>
                </div>
            </div>

            <!-- ABA Pay Link Section -->
            <div class="mb-6 border-t border-slate-100 pt-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    តំណភ្ជាប់ទូទាត់ប្រាក់ ABA Pay / ABA Pay Link
                </h3>
                <div>
                    <label class="mb-1.5 block text-sm text-slate-700">
                        ABA Pay Link (Deep Link/Merchant Link)
                    </label>
                    <input 
                        type="url" 
                        name="company_aba_pay_link" 
                        value="{{ old('company_aba_pay_link', $settings['company_aba_pay_link'] ?? '') }}" 
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        placeholder="https://link.aba.com.kh/...">
                    <p class="mt-1 text-xs text-slate-600">ប្រសិនបើលោកអ្នកមិនដាក់ Link នេះទេ នោះប្រព័ន្ធនឹងផ្ញើត្រឹមរូបភាព QR Code ធម្មតាទៅកាន់អតិថិជន</p>
                </div>
            </div>
            
            <!-- Telegram Token Section -->
            <div class="mb-6 border-t border-slate-100 pt-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    កូនសោ Telegram / Telegram Token
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

// Check if there are validation errors and switch to company tab if needed
switchTab('company');
</script>
@endsection
