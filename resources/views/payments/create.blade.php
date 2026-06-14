@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('payments.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1" style="text-decoration: none;">
            <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
        </a>
        <h1 class="mt-2 text-3xl font-bold text-gray-800">{{ __('app.new_payment') }}</h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ app()->getLocale() === 'km' 
                ? 'សូមជ្រើសរើសគម្រោងបង់រំលស់ វិធីទូទាត់ និងបញ្ចូលរូបភាពបង្កាន់ដៃ ឬ QR កូដ ប្រសិនបើមាន។'
                : 'Select the installment plan, choose the payment method, and upload a receipt or QR slip image if needed.' }}
        </p>
    </div>

    <!-- Form Card -->
    <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Installment Selector -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('app.installment_plans') }}</label>
                <select name="installment_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @foreach($installments as $installment)
                        <option value="{{ $installment->id }}" {{ old('installment_id') == $installment->id ? 'selected' : '' }}>
                            {{ $installment->customer->name }} - {{ $installment->product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Payment Method Selector -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('app.payment_method') }}</label>
                <select name="payment_method_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">{{ __('app.select_method') }}</option>
                    @foreach($paymentMethods as $method)
                        @php
                            $methodKey = strtolower(str_replace(' ', '_', $method->name));
                            $translatedName = __('app.' . $methodKey);
                            if ($translatedName === 'app.' . $methodKey) {
                                $translatedName = $method->name;
                            }
                        @endphp
                        <option value="{{ $method->id }}" data-type="{{ $methodKey }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                            {{ $translatedName }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Amount -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('app.amount') }} (USD)</label>
                <input 
                    type="number" 
                    name="amount" 
                    id="amountInput" 
                    step="0.01" 
                    min="0.01" 
                    value="{{ old('amount') }}" 
                    required 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                <span class="mt-1.5 block text-sm font-semibold text-indigo-600" id="amountRiel">0 ៛</span>
            </div>

            <!-- Payment Date -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('app.payment_date') }}</label>
                <input 
                    type="date" 
                    name="payment_date" 
                    value="{{ old('payment_date', now()->toDateString()) }}" 
                    required 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
            </div>
        </div>

        <!-- Bank QR Code Display Card (Hidden by default) -->
        @php
            $bankQr = \App\Models\Setting::where('key', 'company_bank_qr')->value('value');
        @endphp
        @if($bankQr)
        <div id="bankQrContainer" class="hidden rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-5 flex flex-col items-center justify-center text-center">
            <h4 class="text-sm font-bold text-blue-800 mb-3">
                <i class="fas fa-qrcode mr-1"></i>
                {{ app()->getLocale() === 'km' ? 'សូមស្កេន QR Code នេះដើម្បីទូទាត់ប្រាក់' : 'Please scan this QR Code to make payment' }}
            </h4>
            <img src="{{ asset('storage/' . $bankQr) }}" alt="Shop Bank QR Code" class="w-48 h-48 rounded-lg border border-gray-200 shadow-sm object-contain bg-white p-2">
            <p class="mt-2 text-xs text-gray-500 font-medium">
                {{ app()->getLocale() === 'km' 
                    ? 'បន្ទាប់ពីស្កេនរួច សូមបញ្ចូលរូបភាពបង្កាន់ដៃ (ស្លីបផ្ទេរប្រាក់) នៅខាងក្រោម'
                    : 'After scanning, please upload your transfer slip/receipt image below.' }}
            </p>
        </div>
        @endif

        <!-- Attachment -->
        <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/50 p-5">
            <label class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fas fa-image text-blue-600 mr-1"></i>
                {{ __('app.qr_receipt_image') }}
            </label>
            <input type="file" name="qr_image" class="w-full px-4 py-2.5 border border-gray-300 bg-white rounded-lg text-sm">
            <p class="mt-2 text-xs text-gray-500">
                {{ app()->getLocale() === 'km' 
                    ? 'ស្រេចចិត្តសម្រាប់ការទូទាត់តាម QR, ស្លីបកាត, ឬភស្តុតាងផ្ទេរប្រាក់ផ្សេងៗ។'
                    : 'Optional for QR codes, credit card slips, or bank transfer receipts.' }}
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 pt-2">
            <button 
                type="submit" 
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 text-sm shadow-sm border-0 cursor-pointer"
            >
                {{ __('app.submit_payment') }}
            </button>
            <a 
                href="{{ route('payments.index') }}" 
                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-150 text-sm"
                style="text-decoration: none;"
            >
                {{ __('app.cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amountInput');
        const amountRiel = document.getElementById('amountRiel');
        const exchangeRate = {{ $exchangeRate }};

        function updateRiel() {
            const usd = parseFloat(amountInput.value) || 0;
            const riel = Math.round(usd * exchangeRate);
            amountRiel.innerText = riel.toLocaleString('en-US') + ' ៛';
        }

        amountInput.addEventListener('input', updateRiel);
        updateRiel();

        // Toggle Bank QR Code Display
        const methodSelect = document.querySelector('select[name="payment_method_id"]');
        const bankQrContainer = document.getElementById('bankQrContainer');

        function toggleBankQr() {
            if (!bankQrContainer) return;
            const selectedOption = methodSelect.options[methodSelect.selectedIndex];
            const methodType = selectedOption ? selectedOption.getAttribute('data-type') : '';
            
            if (methodType === 'qr_code') {
                bankQrContainer.classList.remove('hidden');
            } else {
                bankQrContainer.classList.add('hidden');
            }
        }

        if (methodSelect) {
            methodSelect.addEventListener('change', toggleBankQr);
            toggleBankQr(); // Run once on load
        }
    });
</script>
@endsection