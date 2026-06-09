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
        onclick="switchTab('general')"
        id="tab-general"
        class="tab-button flex-1 rounded-md bg-white px-4 py-2 text-sm font-medium text-slate-900 shadow-sm"
        role="tab"
        aria-selected="true">
        ⚙️ {{ __('app.general_settings') }}
    </button>
    <button 
        onclick="switchTab('company')"
        id="tab-company"
        class="tab-button flex-1 rounded-md px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900"
        role="tab"
        aria-selected="false">
        🏢 {{ __('app.company_settings') }}
    </button>
</div>

<!-- General Settings Tab -->
<div id="content-general" class="tab-content">
    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="rounded-xl bg-white p-6 shadow border border-slate-100 lg:col-span-2">
            @csrf
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-slate-700">Shop Name</label>
                <input type="text" name="shop_name" value="{{ $settings['shop_name'] ?? 'City Tech Computer' }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-slate-700">Shop Address</label>
                <input type="text" name="shop_address" value="{{ $settings['shop_address'] ?? '' }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-slate-700">Shop Phone</label>
                <input type="text" name="shop_phone" value="{{ $settings['shop_phone'] ?? '' }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-slate-700">Shop Email</label>
                <input type="email" name="shop_email" value="{{ $settings['shop_email'] ?? '' }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="info@example.com">
            </div>
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-slate-700">Currency</label>
                <input type="text" name="currency" value="{{ $settings['currency'] ?? 'USD' }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-slate-700">Default Interest Rate (%)</label>
                <input type="number" name="default_interest_rate" value="{{ $settings['default_interest_rate'] ?? 0 }}" step="0.01" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-slate-700">Telegram Token</label>
                <input type="text" name="telegram_token" value="{{ $settings['telegram_token'] ?? '' }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Paste your bot token here">
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Update</button>
        </form>

        <div class="rounded-xl bg-white p-6 shadow border border-slate-100">
            <h2 class="mb-3 text-base font-semibold text-slate-900">Telegram Setup</h2>
            <p class="mb-3 text-xs text-slate-600">Use this webhook URL in your Telegram bot configuration.</p>
            <div class="rounded-lg bg-slate-100 p-3 text-xs text-slate-800 break-all">
                {{ url('/api/telegram/webhook') }}
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

<!-- Company Settings Tab -->
<div id="content-company" class="tab-content hidden">
    <div class="rounded-xl bg-white shadow border border-slate-100">
        <form method="POST" action="{{ route('admin.settings.company.update') }}" enctype="multipart/form-data" class="p-6">
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
                            Email <span class="text-rose-500">*</span>
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
            
            <!-- Business License Section -->
            <div class="mb-6">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">
                    {{ __('app.business_license') }}
                </h3>
                
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
                    <p class="mt-1 text-xs text-slate-600">{{ __('app.optional_field') }}</p>
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
            
            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    {{ __('app.save_changes') }}
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
@if($errors->any() && old('company_name'))
    switchTab('company');
@endif
</script>
@endsection
