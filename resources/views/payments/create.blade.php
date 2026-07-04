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
                    <option value="">{{ app()->getLocale() === 'km' ? '--- ជ្រើសរើសគម្រោងបង់រំលស់ ---' : '--- Select Installment Plan ---' }}</option>
                    @foreach($installments as $installment)
                        @php
                            $schedule = $installment->getPaymentSchedule();
                            $unpaidRow = collect($schedule)->first(fn($row) => $row['status'] !== 'paid');
                            $dueAmount = $unpaidRow ? $unpaidRow['amount'] : 0;
                        @endphp
                        <option value="{{ $installment->id }}" data-due-amount="{{ $dueAmount }}" {{ old('installment_id') == $installment->id ? 'selected' : '' }}>
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
            $bankQrPayload = \App\Models\Setting::where('key', 'company_bank_qr_payload')->value('value');
        @endphp
        @if($bankQr)
        <div id="bankQrContainer" class="hidden rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-5 flex flex-col items-center justify-center text-center">
            <h4 class="text-sm font-bold text-blue-800 mb-3">
                <i class="fas fa-qrcode mr-1"></i>
                {{ app()->getLocale() === 'km' ? 'សូមស្កេន QR Code នេះដើម្បីទូទាត់ប្រាក់' : 'Please scan this QR Code to make payment' }}
            </h4>
            <img id="bankQrImage" src="{{ asset('storage/' . $bankQr) }}" data-static-src="{{ asset('storage/' . $bankQr) }}" alt="Shop Bank QR Code" class="w-48 h-48 rounded-lg border border-gray-200 shadow-sm object-contain bg-white p-2">
            <p id="qrInstructionMessage" class="mt-3 text-xs font-bold text-blue-700"></p>
            
            <p class="mt-2 text-xs text-gray-500 font-medium">
                {{ app()->getLocale() === 'km' 
                    ? 'បន្ទាប់ពីស្កេនរួច សូមបញ្ចូលរូបភាពបង្កាន់ដៃ (ស្លីបផ្ទេរប្រាក់) នៅខាងក្រោម'
                    : 'After scanning, please upload your transfer slip/receipt image below.' }}
            </p>
        </div>
        @endif

        <!-- Credit Card Fields Display Card (Hidden by default) -->
        <div id="creditCardContainer" class="hidden rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-5 space-y-4">
            <h4 class="text-sm font-bold text-blue-800 mb-1">
                <i class="fas fa-credit-card mr-1"></i>
                {{ app()->getLocale() === 'km' ? 'ព័ត៌មានកាតឥណទាន' : 'Credit Card Information' }}
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'km' ? 'ឈ្មោះម្ចាស់កាត' : 'Cardholder Name' }}</label>
                    <input type="text" name="card_holder_name" placeholder="e.g. SOK DARA" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'km' ? 'លេខកាត (៤ខ្ទង់ចុងក្រោយ)' : 'Card Number (Last 4 digits)' }}</label>
                    <input type="text" name="card_number" maxlength="4" placeholder="e.g. 1234" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'km' ? 'ប្រភេទកាត' : 'Card Brand' }}</label>
                    <select name="card_brand" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                        <option value="Visa">Visa</option>
                        <option value="MasterCard">MasterCard</option>
                        <option value="UnionPay">UnionPay</option>
                        <option value="JCB">JCB</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
        </div>

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
        const baseQrPayload = @json($bankQrPayload);
        const bankQrImage = document.getElementById('bankQrImage');

        function updateRiel() {
            const usd = parseFloat(amountInput.value) || 0;
            const riel = Math.round(usd * exchangeRate);
            amountRiel.innerText = riel.toLocaleString('en-US') + ' ៛';
        }

        function calculateCRC16_JS(payload) {
            let crc = 0xFFFF;
            for (let c = 0; c < payload.length; c++) {
                crc ^= payload.charCodeAt(c) << 8;
                for (let i = 0; i < 8; i++) {
                    if (crc & 0x8000) {
                        crc = ((crc << 1) ^ 0x1021) & 0xFFFF;
                    } else {
                        crc = (crc << 1) & 0xFFFF;
                    }
                }
            }
            return crc.toString(16).toUpperCase().padStart(4, '0');
        }

        function parseEMVCo(payload) {
            let tags = {};
            let i = 0;
            while (i < payload.length) {
                if (i + 4 > payload.length) break;
                let tag = payload.substring(i, i + 2);
                let length = parseInt(payload.substring(i + 2, i + 4));
                let value = payload.substring(i + 4, i + 4 + length);
                tags[tag] = value;
                i += 4 + length;
                if (tag === '63') break;
            }
            return tags;
        }

        function updateDynamicQr() {
            if (!baseQrPayload || !bankQrImage) return;

            const usd = parseFloat(amountInput.value) || 0;
            if (usd <= 0) {
                bankQrImage.src = bankQrImage.getAttribute('data-static-src');
                return;
            }

            try {
                let tags = parseEMVCo(baseQrPayload.trim());
                

                const msgEl = document.getElementById('qrInstructionMessage');

                // If it is a Personal P2P QR Code (Tag 30), it does not support dynamic amount pre-filling.
                // We return the original base payload exactly as it is to guarantee 100% scanning success.
                /*
                if (tags['30']) {
                    bankQrImage.src = bankQrImage.getAttribute('data-static-src');
                    if (msgEl) {
                        const isKh = '{{ app()->getLocale() }}' === 'km';
                        // Convert amount to Riel if account is KHR
                        const baseCurrency = (tags['53'] === '116') ? 'KHR' : 'USD';
                        let displayAmountStr = '';
                        if (baseCurrency === 'KHR') {
                            const khrAmount = (qrCurrencyMode === 'USD') ? Math.round(usd * exchangeRate) : usd;
                            displayAmountStr = khrAmount.toLocaleString('en-US') + ' ៛';
                        } else {
                            if (qrCurrencyMode === 'KHR' || usd < 1.00) {
                                displayAmountStr = Math.round(usd * exchangeRate).toLocaleString('en-US') + ' ៛';
                            } else {
                                displayAmountStr = '$' + usd.toFixed(2);
                            }
                        }
                        msgEl.innerHTML = isKh 
                            ? `* គណនីបុគ្គល (P2P)៖ សូមវាយបញ្ចូលទឹកប្រាក់ <span class="text-red-600 font-bold">${displayAmountStr}</span> ពេលទូទាត់។` 
                            : `* Personal Account (P2P): Please manually input <span class="text-red-600 font-bold">${displayAmountStr}</span> when paying.`;
                    }
                    return;
                }
                */

                // If it is a Merchant KHQR (Tag 29)
                if (msgEl) {
                    const isKh = '{{ app()->getLocale() }}' === 'km';
                    msgEl.innerText = isKh 
                        ? '* ទឹកប្រាក់ត្រូវបានបំពេញស្វ័យប្រវត្តិតាមវិក្កយបត្រ។' 
                        : '* Amount is automatically pre-filled.';
                }

                // Force initiation method to '12' (Dynamic QR) to allow pre-filled amounts.
                tags['01'] = '12';

                // Detect base currency (e.g., KHR-only bank accounts must always remain KHR)
                let baseCurrency = (tags['53'] === '116') ? 'KHR' : 'USD';
                let finalCurrency = '840';
                let finalAmountStr = '0';

                if (baseCurrency === 'KHR') {
                    finalCurrency = '116';
                    let amountInKhr = (qrCurrencyMode === 'USD') ? Math.round(usd * exchangeRate) : usd;
                    finalAmountStr = amountInKhr.toString();
                } else {
                    // Base is USD
                    if (qrCurrencyMode === 'KHR') {
                        finalCurrency = '116';
                        finalAmountStr = Math.round(usd * exchangeRate).toString();
                    } else {
                        // Input is USD
                        if (usd < 1.00) {
                            // Auto-switch small USD to KHR
                            finalCurrency = '116';
                            finalAmountStr = Math.round(usd * exchangeRate).toString();
                        } else {
                            finalCurrency = '840';
                            finalAmountStr = usd.toFixed(2);
                        }
                    }
                }

                tags['53'] = finalCurrency;
                tags['54'] = finalAmountStr;

                // Dynamically add Wing-specific Tag 99 with current and future expiration timestamps (24h)
                if (baseQrPayload.includes('wing_khqr@wing')) {
                    const nowMs = Date.now();
                    const expireMs = nowMs + 24 * 60 * 60 * 1000; // 24 hours later
                    tags['99'] = `0013${nowMs}0113${expireMs}`;
                }

                // Sort tags (EMVCo standard)
                let sortedKeys = Object.keys(tags).sort();
                let reconstructed = '';
                for (let key of sortedKeys) {
                    if (key === '63') continue;
                    let val = tags[key];
                    let lenStr = val.length.toString().padStart(2, '0');
                    reconstructed += key + lenStr + val;
                }
                reconstructed += '6304';
                let crc = calculateCRC16_JS(reconstructed);
                let finalPayload = reconstructed + crc;

                bankQrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(finalPayload)}`;
            } catch (err) {
                console.error('Error generating dynamic QR code:', err);
                bankQrImage.src = bankQrImage.getAttribute('data-static-src');
            }
        }

        let qrCurrencyMode = '{{ session('display_currency', 'USD') }}';

        function checkAndAutoSwitchCurrency() {
            const usd = parseFloat(amountInput.value) || 0;
            // If display currency is USD but amount is < $1.00, auto-switch to KHR
            if (usd > 0 && usd < 1.00) {
                qrCurrencyMode = 'KHR';
            } else {
                qrCurrencyMode = '{{ session('display_currency', 'USD') }}';
            }
        }

        amountInput.addEventListener('input', function() {
            updateRiel();
            checkAndAutoSwitchCurrency();
            updateDynamicQr();
        });
        
        updateRiel();
        checkAndAutoSwitchCurrency();
        updateDynamicQr();

        // Toggle Payment Method Fields
        const methodSelect = document.querySelector('select[name="payment_method_id"]');
        const bankQrContainer = document.getElementById('bankQrContainer');
        const creditCardContainer = document.getElementById('creditCardContainer');

        function togglePaymentMethodFields() {
            const selectedOption = methodSelect.options[methodSelect.selectedIndex];
            const methodType = selectedOption ? selectedOption.getAttribute('data-type') : '';
            
            // Handle QR Code
            if (bankQrContainer) {
                if (methodType === 'qr_code') {
                    bankQrContainer.classList.remove('hidden');
                } else {
                    bankQrContainer.classList.add('hidden');
                }
            }

            // Handle Credit Card
            if (creditCardContainer) {
                if (methodType === 'credit_card') {
                    creditCardContainer.classList.remove('hidden');
                } else {
                    creditCardContainer.classList.add('hidden');
                }
            }
        }

        if (methodSelect) {
            methodSelect.addEventListener('change', togglePaymentMethodFields);
            togglePaymentMethodFields(); // Run once on load
        }

        // Auto-fill due amount when installment is selected
        const installmentSelect = document.querySelector('select[name="installment_id"]');
        
        function handleInstallmentChange() {
            const selectedOption = installmentSelect.options[installmentSelect.selectedIndex];
            if (selectedOption) {
                const dueAmount = selectedOption.getAttribute('data-due-amount');
                if (dueAmount && parseFloat(dueAmount) > 0) {
                    amountInput.value = parseFloat(dueAmount).toFixed(2);
                } else {
                    amountInput.value = '';
                }
                // Trigger updates for riel and dynamic QR code
                updateRiel();
                checkAndAutoSwitchCurrency();
                updateDynamicQr();
            }
        }

        if (installmentSelect) {
            installmentSelect.addEventListener('change', handleInstallmentChange);
            
            // If the amount is currently empty on load and there is a selected installment, populate it
            if (amountInput && amountInput.value === '' && installmentSelect.value !== '') {
                handleInstallmentChange();
            }
        }
    });
</script>
@endsection