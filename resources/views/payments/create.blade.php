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
                            $penaltyAmount = $installment->calculatePenalty();
                        @endphp
                        <option value="{{ $installment->id }}" data-due-amount="{{ $dueAmount }}" data-penalty-amount="{{ $penaltyAmount }}" {{ old('installment_id') == $installment->id ? 'selected' : '' }}>
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
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-gray-700" id="amountLabel">{{ __('app.amount') }} (USD)</label>
                    <div class="inline-flex rounded-lg border border-gray-200 p-0.5 bg-gray-100" role="group">
                        <button type="button" id="toggleCurrencyUsd" class="px-3 py-1 text-xs font-bold rounded-md bg-white text-blue-600 shadow-sm transition">USD ($)</button>
                        <button type="button" id="toggleCurrencyKhr" class="px-3 py-1 text-xs font-bold rounded-md text-gray-600 hover:text-blue-600 transition">KHR (៛)</button>
                    </div>
                </div>
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

            <!-- Penalty Fee -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2" id="penaltyLabel">{{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }} (USD)</label>
                <input 
                    type="number" 
                    name="penalty_amount" 
                    id="penaltyInput" 
                    step="0.01" 
                    min="0" 
                    value="{{ old('penalty_amount', '0.00') }}" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                <span class="mt-1.5 block text-sm font-semibold text-indigo-600" id="penaltyRiel">0 ៛</span>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
        <div id="creditCardContainer" class="hidden rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50/10 p-6 space-y-6">
            <h4 class="text-sm font-bold text-indigo-900 mb-1 flex items-center gap-2">
                <i class="fas fa-credit-card text-indigo-600"></i>
                {{ app()->getLocale() === 'km' ? 'ព័ត៌មានកាតឥណទានស្តង់ដារ' : 'Standard Credit Card Gateway' }}
                <span class="text-[10px] bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded font-medium">PCI-DSS Compliant Simulation</span>
            </h4>

            <!-- Interactive 3D Card Preview -->
            <div class="card-container">
                <div class="credit-card-preview" id="cardPreview">
                    <!-- Front Side -->
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
                    <!-- Back Side -->
                    <div class="card-back">
                        <div class="w-full h-10 bg-black mt-2"></div>
                        <div class="px-6 mt-4">
                            <div class="text-[9px] text-right uppercase tracking-wider text-gray-300 mb-1">CVC/CVV</div>
                            <div class="bg-white text-gray-900 h-9 px-3 flex items-center justify-end font-mono font-bold rounded shadow-inner" id="cardCvvPreview">•••</div>
                        </div>
                        <div class="px-6 mt-2 text-right">
                            <div class="text-[9px] text-gray-400 italic">Simulated PCI secure tokenized payment.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Inputs Form -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ app()->getLocale() === 'km' ? 'លេខកាតពេញ (១៦ ខ្ទង់)' : 'Card Number (16-digit)' }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="card_number_full" placeholder="4000 1234 5678 9010" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                        <div class="absolute left-3.5 top-2.5 text-gray-400 text-sm">
                            <i class="fas fa-credit-card" id="cardInputIcon"></i>
                        </div>
                    </div>
                    <span id="cardNumberError" class="text-xs text-red-500 mt-1 hidden">{{ app()->getLocale() === 'km' ? 'លេខកាតមិនត្រឹមត្រូវ (ពិនិត្យក្បួន Luhn មិនជោគជ័យ)' : 'Invalid card number (Luhn check failed)' }}</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ app()->getLocale() === 'km' ? 'ឈ្មោះម្ចាស់កាត' : 'Cardholder Name' }} <span class="text-red-500">*</span></label>
                    <input type="text" id="card_holder_name_input" placeholder="SOK DARA" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 uppercase transition duration-150">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ app()->getLocale() === 'km' ? 'ថ្ងៃផុតកំណត់ (ខែ/ឆ្នាំ)' : 'Expiration Date (MM/YY)' }} <span class="text-red-500">*</span></label>
                    <input type="text" id="card_expiry" placeholder="MM/YY" maxlength="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-center focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    <span id="cardExpiryError" class="text-xs text-red-500 mt-1 hidden">{{ app()->getLocale() === 'km' ? 'កាតផុតកំណត់ ឬកាលបរិច្ឆេទខុស' : 'Expired card or invalid date' }}</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">CVV/CVC <span class="text-red-500">*</span></label>
                    <input type="password" id="card_cvv" placeholder="•••" maxlength="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-center focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ app()->getLocale() === 'km' ? 'ប្រភេទកាតទូទាត់' : 'Detected Brand' }}</label>
                    <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold text-center select-none" id="detectedBrandBadge">
                        Other
                    </div>
                </div>
            </div>

            <!-- Hidden Inputs to Map back to DB structure -->
            <input type="hidden" name="card_holder_name" id="card_holder_name_hidden">
            <input type="hidden" name="card_number" id="card_number_hidden">
            <input type="hidden" name="card_brand" id="card_brand_hidden">

            <!-- Card Transaction Fee Calculations (ដែលអាចគណនា) -->
            <div class="border-t border-indigo-100 pt-4 mt-2 bg-indigo-50/50 rounded-xl p-4">
                <div class="text-xs font-bold text-indigo-900 mb-2 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ព័ត៌មានលម្អិតអំពីការគណនា' : 'Fee Calculation Breakdown' }}</div>
                <div class="space-y-1.5 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>{{ app()->getLocale() === 'km' ? 'ទឹកប្រាក់ត្រូវបង់ (Principal Amount):' : 'Principal Amount:' }}</span>
                        <span class="font-semibold text-gray-900" id="calcPrincipal">$0.00</span>
                    </div>
                    @php
                        $cardProcessingFeePercent = \App\Models\Setting::where('key', 'card_processing_fee')->value('value') ?? '2';
                    @endphp
                    <div class="flex justify-between">
                        <span class="flex items-center gap-1">
                            {{ app()->getLocale() === 'km' ? "កម្រៃសេវាទូទាត់កាត (Processing Fee {$cardProcessingFeePercent}%):" : "Card Processing Fee ({$cardProcessingFeePercent}%):" }}
                            <i class="fas fa-info-circle text-xs text-indigo-500 cursor-help" title="Standard merchant fee for Visa/Mastercard processing."></i>
                        </span>
                        <span class="font-semibold text-indigo-600" id="calcFee">+$0.00</span>
                    </div>
                    <div class="border-t border-gray-200 my-2 pt-2 flex justify-between text-base font-bold text-indigo-900">
                        <span>{{ app()->getLocale() === 'km' ? 'ទឹកប្រាក់ត្រូវទូទាត់សរុប (Total Charge):' : 'Total Amount to Charge:' }}</span>
                        <div class="text-right">
                            <span id="calcTotalUSD">$0.00</span>
                            <span class="block text-xs font-medium text-indigo-600 mt-0.5" id="calcTotalKHR">0 ៛</span>
                        </div>
                    </div>
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
                id="submitBtn"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 text-sm shadow-sm border-0 cursor-pointer"
            >
                <span id="btnText">{{ __('app.submit_payment') }}</span>
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

<style>
    /* Card Flip & 3D CSS Animation */
    .card-container {
        perspective: 1000px;
        width: 100%;
        max-width: 320px;
        height: 190px;
        margin: 0 auto 1.5rem auto;
    }
    .credit-card-preview {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .credit-card-preview.flipped {
        transform: rotateY(180deg);
    }
    .card-front, .card-back {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        backface-visibility: hidden;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.25);
        color: white;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .card-back {
        transform: rotateY(180deg);
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        padding: 20px 0;
    }
    .card-chip {
        width: 38px;
        height: 28px;
        background: linear-gradient(135deg, #ffe082 0%, #ffb300 100%);
        border-radius: 5px;
        position: relative;
        box-shadow: inset 0 1px 1px rgba(255,255,255,0.4);
    }
    .card-chip::after {
        content: '';
        position: absolute;
        top: 4px;
        left: 7px;
        width: 24px;
        height: 20px;
        border: 1px solid rgba(0,0,0,0.15);
        border-radius: 3px;
    }
</style>

<!-- 3D Secure Simulated OTP Modal -->
<div id="otpModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all duration-300">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="otpModalContent">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
            <span class="text-base font-bold text-indigo-900 flex items-center gap-1.5">
                <i class="fas fa-shield-alt text-emerald-500"></i>
                {{ app()->getLocale() === 'km' ? 'ការផ្ទៀងផ្ទាត់សុវត្ថិភាព 3D Secure' : '3D Secure Bank Verification' }}
            </span>
            <div class="flex gap-1.5">
                <span class="text-[9px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded shadow-sm">Visa Secure</span>
                <span class="text-[9px] font-bold text-red-700 bg-red-50 border border-red-200 px-1.5 py-0.5 rounded shadow-sm">Mastercard ID Check</span>
            </div>
        </div>

        <!-- Body Content -->
        <div class="space-y-4" id="otpModalMainBody">
            <p class="text-xs text-gray-600 leading-relaxed">
                {{ app()->getLocale() === 'km' 
                    ? 'ធនាគាររបស់អ្នកតម្រូវឱ្យផ្ទៀងផ្ទាត់កូដសម្ងាត់បណ្តោះអាសន្ន (OTP) ដើម្បីបញ្ចប់ការផ្ទេរប្រាក់ចេញពីគណនីរបស់អ្នក។ កូដ OTP ត្រូវបានផ្ញើទៅកាន់លេខទូរស័ព្ទរបស់អ្នកដែលបានភ្ជាប់ជាមួយកាត។'
                    : 'Your card issuer requires One-Time Password (OTP) verification to authorize transferring funds out of your account. A 6-digit OTP code has been sent to your registered phone number.' }}
            </p>

            <!-- Transaction Details Card -->
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ app()->getLocale() === 'km' ? 'ហាងទទួលប្រាក់:' : 'Merchant:' }}</span>
                    <span class="font-bold text-slate-800">CITYTECH COMPUTER SHOP</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ app()->getLocale() === 'km' ? 'កាតទូទាត់:' : 'Card Type:' }}</span>
                    <span class="font-semibold text-slate-800" id="otpCardType">Visa ****1234</span>
                </div>
                <div class="flex justify-between text-sm pt-1.5 border-t border-slate-200/60">
                    <span class="font-bold text-slate-700">{{ app()->getLocale() === 'km' ? 'ទឹកប្រាក់ត្រូវទូទាត់:' : 'Total Charge Amount:' }}</span>
                    <span class="font-black text-indigo-700" id="otpTotalAmount">$0.00 (0 ៛)</span>
                </div>
            </div>

            <!-- Simulation Helper Alert -->
            <div class="rounded-lg bg-amber-50 border border-amber-200 p-3 flex items-start gap-2.5 text-xs text-amber-800">
                <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                <div>
                    <span class="font-bold">{{ app()->getLocale() === 'km' ? 'កូដ OTP សម្រាប់សាកល្បង៖' : 'Simulated Sandbox OTP:' }}</span>
                    <code class="ml-1 px-2 py-0.5 bg-white border border-amber-300 font-mono font-black text-sm rounded shadow-sm" id="simulatedOtpCode">123456</code>
                    <p class="mt-1 text-[11px] text-amber-700">{{ app()->getLocale() === 'km' ? 'បញ្ចូលកូដខាងលើដើម្បីជោគជ័យ។' : 'Please input the code above to authorize real-like transaction.' }}</p>
                </div>
            </div>

            <!-- OTP Input Form -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">{{ app()->getLocale() === 'km' ? 'បញ្ចូលកូដ OTP ៦ ខ្ទង់' : 'Enter 6-Digit OTP Code' }}</label>
                <input type="text" id="otpInput" placeholder="••••••" maxlength="6" class="w-full tracking-[0.5em] text-center font-mono font-black text-lg px-4 py-2 border-2 border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition">
                <span id="otpErrorMsg" class="text-xs text-red-500 mt-1.5 text-center block hidden">Incorrect OTP. Please check the code and try again.</span>
            </div>
        </div>

        <!-- Processing Screen Overlay (within modal) -->
        <div id="otpProcessingOverlay" class="hidden flex flex-col items-center justify-center py-8 text-center space-y-4">
            <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-indigo-600"></div>
            <div class="space-y-1">
                <h5 class="text-sm font-bold text-indigo-900" id="otpProcessingTitle">Contacting Issuing Bank...</h5>
                <p class="text-xs text-gray-500" id="otpProcessingDesc">Securing transaction session...</p>
            </div>
        </div>

        <!-- Footer Buttons -->
        <div class="mt-6 flex items-center justify-end gap-2 border-t border-gray-100 pt-4 animate-fade-in" id="otpModalButtons">
            <button type="button" id="cancelOtpBtn" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-xs transition duration-150">
                {{ app()->getLocale() === 'km' ? 'បោះបង់' : 'Cancel' }}
            </button>
            <button type="button" id="verifyOtpBtn" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-xs shadow transition duration-150">
                {{ app()->getLocale() === 'km' ? 'ផ្ទៀងផ្ទាត់ & ទូទាត់' : 'Verify & Authorize' }}
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const methodSelect = document.querySelector('select[name="payment_method_id"]');
        const bankQrContainer = document.getElementById('bankQrContainer');
        const creditCardContainer = document.getElementById('creditCardContainer');

        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const amountInput = document.getElementById('amountInput');
        const amountRiel = document.getElementById('amountRiel');
        const penaltyInput = document.getElementById('penaltyInput');
        const penaltyRiel = document.getElementById('penaltyRiel');
        const exchangeRate = {{ $exchangeRate }};
        const baseQrPayload = @json($bankQrPayload);
        const bankQrImage = document.getElementById('bankQrImage');

        // CC Inputs
        const ccContainer = document.getElementById('creditCardContainer');
        const cardNumberInput = document.getElementById('card_number_full');
        const cardHolderInput = document.getElementById('card_holder_name_input');
        const cardExpiryInput = document.getElementById('card_expiry');
        const cardCvvInput = document.getElementById('card_cvv');

        // Hidden fields for submission
        const hiddenHolder = document.getElementById('card_holder_name_hidden');
        const hiddenNumber = document.getElementById('card_number_hidden');
        const hiddenBrand = document.getElementById('card_brand_hidden');

        // OTP simulation elements
        const otpModal = document.getElementById('otpModal');
        const otpModalContent = document.getElementById('otpModalContent');
        const otpInput = document.getElementById('otpInput');
        const otpErrorMsg = document.getElementById('otpErrorMsg');
        const verifyOtpBtn = document.getElementById('verifyOtpBtn');
        const cancelOtpBtn = document.getElementById('cancelOtpBtn');
        const otpCardType = document.getElementById('otpCardType');
        const otpTotalAmount = document.getElementById('otpTotalAmount');
        const simulatedOtpCode = document.getElementById('simulatedOtpCode');
        const otpModalMainBody = document.getElementById('otpModalMainBody');
        const otpProcessingOverlay = document.getElementById('otpProcessingOverlay');
        const otpProcessingTitle = document.getElementById('otpProcessingTitle');
        const otpProcessingDesc = document.getElementById('otpProcessingDesc');
        const otpModalButtons = document.getElementById('otpModalButtons');

        let currentActiveOtp = "123456";

        // Card Flip click trigger
        const previewEl = document.getElementById('cardPreview');
        if (previewEl) {
            previewEl.addEventListener('click', function() {
                this.classList.toggle('flipped');
            });
        }

        // Expiry Expiration Date auto format and validate
        if (cardExpiryInput) {
            cardExpiryInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 2) {
                    e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
                } else {
                    e.target.value = value;
                }
                document.getElementById('cardExpiryPreview').innerText = e.target.value || 'MM/YY';

                if (e.target.value.length === 5) {
                    if (validateExpiry(e.target.value)) {
                        cardExpiryInput.classList.remove('border-red-500');
                        document.getElementById('cardExpiryError').classList.add('hidden');
                    } else {
                        cardExpiryInput.classList.add('border-red-500');
                        document.getElementById('cardExpiryError').classList.remove('hidden');
                    }
                } else {
                    cardExpiryInput.classList.remove('border-red-500');
                    document.getElementById('cardExpiryError').classList.add('hidden');
                }
            });
        }

        // CVV auto flip preview
        if (cardCvvInput) {
            cardCvvInput.addEventListener('focus', function() {
                if (previewEl) previewEl.classList.add('flipped');
            });
            cardCvvInput.addEventListener('blur', function() {
                if (previewEl) previewEl.classList.remove('flipped');
            });
            cardCvvInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value;
                document.getElementById('cardCvvPreview').innerText = '•'.repeat(value.length) || '•••';
            });
        }

        // Cardholder name formatting
        if (cardHolderInput) {
            cardHolderInput.addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase();
                e.target.value = value;
                document.getElementById('cardHolderPreview').innerText = value || 'SOK DARA';
            });
        }

        // Full Card Number input mask, brand detection, Luhn validation
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                let formatted = value.match(/.{1,4}/g)?.join(' ') || '';
                e.target.value = formatted.substring(0, 19); // 16 digits + 3 spaces

                document.getElementById('cardNumberPreview').innerText = e.target.value || '•••• •••• •••• ••••';

                const brand = detectCardBrand(value);
                updateCardBrandUI(brand);

                if (value.length >= 15) {
                    if (luhnCheck(value)) {
                        cardNumberInput.classList.remove('border-red-500');
                        document.getElementById('cardNumberError').classList.add('hidden');
                    } else {
                        cardNumberInput.classList.add('border-red-500');
                        document.getElementById('cardNumberError').classList.remove('hidden');
                    }
                } else {
                    cardNumberInput.classList.remove('border-red-500');
                    document.getElementById('cardNumberError').classList.add('hidden');
                }
            });
        }

        function detectCardBrand(number) {
            number = number.replace(/\s+/g, '');
            if (number.startsWith('4')) return 'Visa';
            if (/^(5[1-5]|2[2-7])/.test(number)) return 'MasterCard';
            if (/^3[47]/.test(number)) return 'Amex';
            if (/^35/.test(number)) return 'JCB';
            if (/^(62|81)/.test(number)) return 'UnionPay';
            return 'Other';
        }

        function luhnCheck(val) {
            let sum = 0;
            for (let i = 0; i < val.length; i++) {
                let intVal = parseInt(val.substr(i, 1));
                if (val.length % 2 == i % 2) {
                    intVal *= 2;
                    if (intVal > 9) {
                        intVal = (intVal % 10) + 1;
                    }
                }
                sum += intVal;
            }
            return (sum % 10) == 0;
        }

        function validateExpiry(value) {
            if (!/^\d{2}\/\d{2}$/.test(value)) return false;
            const parts = value.split('/');
            const month = parseInt(parts[0], 10);
            const year = parseInt('20' + parts[1], 10);
            if (month < 1 || month > 12) return false;
            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth() + 1;
            if (year < currentYear) return false;
            if (year === currentYear && month < currentMonth) return false;
            return true;
        }

        function updateCardBrandUI(brand) {
            const badge = document.getElementById('detectedBrandBadge');
            if (badge) badge.innerText = brand;

            const preview = document.getElementById('cardPreview');
            if (!preview) return;
            const front = preview.querySelector('.card-front');
            const logo = document.getElementById('cardBrandLogoPreview');
            const icon = document.getElementById('cardInputIcon');

            if (logo) logo.innerText = brand === 'Other' ? 'Card' : brand;

            if (front) {
                front.className = 'card-front';
                if (brand === 'Visa') {
                    front.style.background = 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%)';
                } else if (brand === 'MasterCard') {
                    front.style.background = 'linear-gradient(135deg, #7c2d12 0%, #ea580c 50%, #f97316 100%)';
                } else if (brand === 'Amex') {
                    front.style.background = 'linear-gradient(135deg, #065f46 0%, #10b981 50%, #34d399 100%)';
                } else if (brand === 'JCB') {
                    front.style.background = 'linear-gradient(135deg, #881337 0%, #e11d48 50%, #fb7185 100%)';
                } else if (brand === 'UnionPay') {
                    front.style.background = 'linear-gradient(135deg, #134e5e 0%, #2f80ed 50%, #00c6ff 100%)';
                } else {
                    front.style.background = 'linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%)';
                }
            }

            if (icon) {
                icon.className = 'fas';
                if (brand === 'Visa') icon.classList.add('fa-cc-visa', 'text-blue-600');
                else if (brand === 'MasterCard') icon.classList.add('fa-cc-mastercard', 'text-orange-500');
                else if (brand === 'Amex') icon.classList.add('fa-cc-amex', 'text-emerald-600');
                else if (brand === 'JCB') icon.classList.add('fa-cc-jcb', 'text-rose-600');
                else icon.classList.add('fa-credit-card', 'text-gray-400');
            }
        }

        // Calculations & Breakdown (ដែលអាចគណនា)
        function updateCalculations() {
            const usd = parseFloat(amountInput.value) || 0;
            const penalty = penaltyInput ? (parseFloat(penaltyInput.value) || 0) : 0;
            const subtotal = usd + penalty;
            const feePercent = parseFloat("{{ \App\Models\Setting::where('key', 'card_processing_fee')->value('value') ?? '2' }}");
            const fee = (methodSelect && methodSelect.options[methodSelect.selectedIndex]?.getAttribute('data-type') === 'credit_card')
                ? subtotal * (feePercent / 100)
                : 0;

            const totalUSD = subtotal + fee;
            const totalKHR = Math.round(totalUSD * exchangeRate);

            const principalEl = document.getElementById('calcPrincipal');
            const feeEl = document.getElementById('calcFee');
            const totalUSDEl = document.getElementById('calcTotalUSD');
            const totalKHREl = document.getElementById('calcTotalKHR');

            if (principalEl) principalEl.innerText = '$' + subtotal.toFixed(2);
            if (feeEl) feeEl.innerText = '+$' + fee.toFixed(2);
            if (totalUSDEl) totalUSDEl.innerText = '$' + totalUSD.toFixed(2);
            if (totalKHREl) totalKHREl.innerText = totalKHR.toLocaleString('en-US') + ' ៛';

            // Pre-fill OTP Modal amounts
            if (otpTotalAmount) {
                otpTotalAmount.innerHTML = `<span class="text-indigo-700 font-bold">$${totalUSD.toFixed(2)}</span> (${totalKHR.toLocaleString('en-US')} ៛)`;
            }
        }

        function updateRiel() {
            const val = parseFloat(amountInput.value) || 0;
            if (qrCurrencyMode === 'KHR') {
                const usd = val / exchangeRate;
                amountRiel.innerText = '$' + usd.toFixed(2);
            } else {
                const riel = Math.round(val * exchangeRate);
                amountRiel.innerText = riel.toLocaleString('en-US') + ' ៛';
            }
        }

        function updatePenaltyRiel() {
            if (!penaltyInput || !penaltyRiel) return;
            const val = parseFloat(penaltyInput.value) || 0;
            if (qrCurrencyMode === 'KHR') {
                const usd = val / exchangeRate;
                penaltyRiel.innerText = '$' + usd.toFixed(2);
            } else {
                const riel = Math.round(val * exchangeRate);
                penaltyRiel.innerText = riel.toLocaleString('en-US') + ' ៛';
            }
        }


        function calculateCRC16_JS(payload) {
            const bytes = new TextEncoder().encode(payload);
            let crc = 0xFFFF;
            for (let c = 0; c < bytes.length; c++) {
                crc ^= bytes[c] << 8;
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


        function getByteLength(str) {
            return new TextEncoder().encode(str).length;
        }

        function parseEMVCo(payload) {
            let tags = [];
            let i = 0;
            while (i < payload.length) {
                if (i + 4 > payload.length) break;
                let tag = payload.substring(i, i + 2);
                let lenStr = payload.substring(i + 2, i + 4);
                let length = parseInt(lenStr);
                if (isNaN(length)) break;
                let value = payload.substring(i + 4, i + 4 + length);
                tags.push({ tag: tag, lenStr: lenStr, val: value });
                i += 4 + length;
                if (tag === '63') break;
            }
            return tags;
        }



        function updateDynamicQr() {
            if (!bankQrImage) return;

            const staticSrc = bankQrImage.getAttribute('data-static-src');

            // No payload saved → always show uploaded static image
            if (!baseQrPayload || baseQrPayload.trim() === '') {
                bankQrImage.src = staticSrc;
                return;
            }

            const usd = parseFloat(amountInput.value) || 0;
            const msgEl = document.getElementById('qrInstructionMessage');

            // No amount yet → show static uploaded QR image
            if (usd <= 0) {
                bankQrImage.src = staticSrc;
                if (msgEl) msgEl.innerText = '';
                return;
            }

            try {
                let tags = parseEMVCo(baseQrPayload.trim());

                // Helper to update or insert a tag
                function setTag(tag, lenStr, val, insertAfterTag = null) {
                    let index = tags.findIndex(item => item.tag === tag);
                    if (index !== -1) {
                        tags[index].lenStr = lenStr;
                        tags[index].val = val;
                    } else {
                        let newItem = { tag: tag, lenStr: lenStr, val: val };
                        if (insertAfterTag) {
                            let afterIdx = tags.findIndex(item => item.tag === insertAfterTag);
                            if (afterIdx !== -1) {
                                tags.splice(afterIdx + 1, 0, newItem);
                                return;
                            }
                        }
                        // Default to push before Tag 63 (which is always last)
                        let idx63 = tags.findIndex(item => item.tag === '63');
                        if (idx63 !== -1) {
                            tags.splice(idx63, 0, newItem);
                        } else {
                            tags.push(newItem);
                        }
                    }
                }

                // Set dynamic mode
                setTag('01', '02', '12');

                let inputCurrency = qrCurrencyMode;
                let finalCurrency = (qrCurrencyMode === 'KHR') ? '116' : '840';
                
                // Force KHR for USD amounts less than $1.00 (since Bakong minimum for USD is $1.00)
                if (inputCurrency === 'USD' && usd > 0 && usd < 1.00) {
                    finalCurrency = '116';
                }

                let finalAmountStr = '0';

                if (finalCurrency === '116') {
                    let amountInKhr = (inputCurrency === 'USD') ? Math.round(usd * exchangeRate) : usd;
                    finalAmountStr = Math.round(amountInKhr).toString();
                } else {
                    let amountInUsd = (inputCurrency === 'KHR') ? (usd / exchangeRate) : usd;
                    finalAmountStr = amountInUsd.toFixed(2);
                }




                setTag('53', '03', finalCurrency);
                setTag('54', finalAmountStr.length.toString().padStart(2, '0'), finalAmountStr, '53');

                // Wing timestamp tag
                if (baseQrPayload.includes('wing_khqr@wing')) {
                    const nowMs = Date.now();
                    const expireMs = nowMs + 24 * 60 * 60 * 1000;
                    let wingVal = `0013${nowMs}0113${expireMs}`;
                    setTag('99', wingVal.length.toString().padStart(2, '0'), wingVal);
                }

                let reconstructed = '';
                for (let item of tags) {
                    if (item.tag === '63') continue;
                    reconstructed += item.tag + item.lenStr + item.val;
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

        const toggleUsd = document.getElementById('toggleCurrencyUsd');
        const toggleKhr = document.getElementById('toggleCurrencyKhr');
        const amountLabel = document.getElementById('amountLabel');
        const penaltyLabel = document.getElementById('penaltyLabel');

        function setCurrencyMode(mode) {
            const oldMode = qrCurrencyMode;
            qrCurrencyMode = mode;
            
            // Convert current input values if mode actually changed
            if (oldMode !== mode) {
                const currentAmount = parseFloat(amountInput.value) || 0;
                const currentPenalty = penaltyInput ? (parseFloat(penaltyInput.value) || 0) : 0;
                
                if (currentAmount > 0) {
                    if (mode === 'KHR') {
                        amountInput.value = Math.round(currentAmount * exchangeRate);
                    } else {
                        amountInput.value = (currentAmount / exchangeRate).toFixed(2);
                    }
                }
                
                if (penaltyInput && currentPenalty > 0) {
                    if (mode === 'KHR') {
                        penaltyInput.value = Math.round(currentPenalty * exchangeRate);
                    } else {
                        penaltyInput.value = (currentPenalty / exchangeRate).toFixed(2);
                    }
                }
            }

            if (mode === 'KHR') {
                toggleKhr.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                toggleKhr.classList.remove('text-gray-600');
                toggleUsd.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                toggleUsd.classList.add('text-gray-600');
                
                if (amountLabel) amountLabel.innerText = '{{ __('app.amount') }} (KHR)';
                if (penaltyLabel) penaltyLabel.innerText = '{{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }} (KHR)';
                
                amountInput.step = '100';
                amountInput.min = '100';
                if (penaltyInput) {
                    penaltyInput.step = '100';
                    penaltyInput.min = '0';
                }
            } else {
                toggleUsd.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                toggleUsd.classList.remove('text-gray-600');
                toggleKhr.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                toggleKhr.classList.add('text-gray-600');
                
                if (amountLabel) amountLabel.innerText = '{{ __('app.amount') }} (USD)';
                if (penaltyLabel) penaltyLabel.innerText = '{{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }} (USD)';
                
                amountInput.step = '0.01';
                amountInput.min = '0.01';
                if (penaltyInput) {
                    penaltyInput.step = '0.01';
                    penaltyInput.min = '0';
                }
            }
            
            updateRiel();
            updatePenaltyRiel();
            updateDynamicQr();
            updateCalculations();
        }

        if (toggleUsd && toggleKhr) {
            toggleUsd.addEventListener('click', () => setCurrencyMode('USD'));
            toggleKhr.addEventListener('click', () => setCurrencyMode('KHR'));
        }

        // Set initial state matching session currency
        setCurrencyMode(qrCurrencyMode);

        amountInput.addEventListener('input', function() {
            updateRiel();
            updateDynamicQr();
            updateCalculations();
        });

        
        // Toggle Payment Method Fields

        function togglePaymentMethodFields() {
            const selectedOption = methodSelect.options[methodSelect.selectedIndex];
            const methodType = selectedOption ? selectedOption.getAttribute('data-type') : '';
            
            if (bankQrContainer) {
                if (methodType === 'qr_code') {
                    bankQrContainer.classList.remove('hidden');
                } else {
                    bankQrContainer.classList.add('hidden');
                }
            }

            if (creditCardContainer) {
                if (methodType === 'credit_card') {
                    creditCardContainer.classList.remove('hidden');
                    // Enable standard requirements
                    cardNumberInput.setAttribute('required', 'true');
                    cardHolderInput.setAttribute('required', 'true');
                    cardExpiryInput.setAttribute('required', 'true');
                    cardCvvInput.setAttribute('required', 'true');
                } else {
                    creditCardContainer.classList.add('hidden');
                    // Disable standard requirements
                    cardNumberInput.removeAttribute('required');
                    cardHolderInput.removeAttribute('required');
                    cardExpiryInput.removeAttribute('required');
                    cardCvvInput.removeAttribute('required');
                }
            }
            updateCalculations();
        }

        if (methodSelect) {
            methodSelect.addEventListener('change', togglePaymentMethodFields);
        }

        // Auto-fill due amount when installment is selected
        const installmentSelect = document.querySelector('select[name="installment_id"]');
        
        function handleInstallmentChange() {
            const selectedOption = installmentSelect.options[installmentSelect.selectedIndex];
            if (selectedOption) {
                const dueAmount = parseFloat(selectedOption.getAttribute('data-due-amount')) || 0;
                const penaltyAmount = parseFloat(selectedOption.getAttribute('data-penalty-amount')) || 0;
                
                if (dueAmount > 0) {
                    if (qrCurrencyMode === 'KHR') {
                        amountInput.value = Math.round(dueAmount * exchangeRate);
                    } else {
                        amountInput.value = dueAmount.toFixed(2);
                    }
                } else {
                    amountInput.value = '';
                }

                // Add penalty auto-fill
                if (penaltyInput) {
                    if (penaltyAmount > 0) {
                        if (qrCurrencyMode === 'KHR') {
                            penaltyInput.value = Math.round(penaltyAmount * exchangeRate);
                        } else {
                            penaltyInput.value = penaltyAmount.toFixed(2);
                        }
                    } else {
                        penaltyInput.value = qrCurrencyMode === 'KHR' ? '0' : '0.00';
                    }
                    updatePenaltyRiel();
                }

                updateRiel();
                updateDynamicQr();
                updateCalculations();
            }
        }

        if (installmentSelect) {
            installmentSelect.addEventListener('change', handleInstallmentChange);
        }

        if (penaltyInput) {
            penaltyInput.addEventListener('input', function() {
                updatePenaltyRiel();
                updateCalculations();
            });
        }

        // Run initializers
        updateRiel();
        updatePenaltyRiel();
        updateDynamicQr();
        if (methodSelect) {
            togglePaymentMethodFields();
        }
        if (installmentSelect && amountInput && amountInput.value === '' && installmentSelect.value !== '') {
            handleInstallmentChange();
        }

        // Intercept form submit and trigger 3DS OTP modal
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const amount = parseFloat(amountInput.value) || 0;
            const riel = Math.round(amount * exchangeRate);
            const isKhmer = '{{ app()->getLocale() }}' === 'km';
            
            const selectedOption = methodSelect.options[methodSelect.selectedIndex];
            const methodType = selectedOption ? selectedOption.getAttribute('data-type') : '';

            if (methodType === 'credit_card') {
                // Validate card fields
                const cardNumber = cardNumberInput.value.replace(/\s+/g, '');
                const expiry = cardExpiryInput.value;
                const cvv = cardCvvInput.value;
                const holder = cardHolderInput.value.trim();

                if (!holder) {
                    alert(isKhmer ? 'សូមបញ្ចូលឈ្មោះម្ចាស់កាត!' : 'Please enter cardholder name!');
                    cardHolderInput.focus();
                    return;
                }
                if (cardNumber.length < 15 || !luhnCheck(cardNumber)) {
                    alert(isKhmer ? 'លេខកាតឥណទានមិនត្រឹមត្រូវទេ!' : 'Invalid card number!');
                    cardNumberInput.focus();
                    return;
                }
                if (!validateExpiry(expiry)) {
                    alert(isKhmer ? 'កាលបរិច្ឆេទផុតកំណត់កាតមិនត្រឹមត្រូវទេ ឬកាតហួសកំណត់!' : 'Invalid expiration date or card expired!');
                    cardExpiryInput.focus();
                    return;
                }
                if (cvv.length < 3) {
                    alert(isKhmer ? 'សូមបញ្ចូលកូដ CVV សុវត្ថិភាព ៣ ឬ ៤ ខ្ទង់!' : 'Please enter 3 or 4 digit CVV!');
                    cardCvvInput.focus();
                    return;
                }

                // Setup OTP modal details
                const brand = detectCardBrand(cardNumber);
                otpCardType.innerText = `${brand} ****${cardNumber.slice(-4)}`;
                
                // Randomize OTP Code
                currentActiveOtp = Math.floor(100000 + Math.random() * 900000).toString();
                simulatedOtpCode.innerText = currentActiveOtp;
                otpInput.value = '';
                otpErrorMsg.classList.add('hidden');
                
                // Open OTP Modal with slide in/fade in animations
                otpModal.classList.remove('hidden');
                setTimeout(() => {
                    otpModalContent.classList.remove('scale-95', 'opacity-0');
                }, 50);

                return; // Wait for OTP
            }

            // Normal Flow (Cash / QR Code)
            const msg = isKhmer 
                ? `តើអ្នកប្រាកដថាចង់បង់ប្រាក់មែនទេ?\n\nចំនួន: $${amount.toFixed(2)} (${riel.toLocaleString()} ៛)`
                : `Are you sure you want to submit this payment?\n\nAmount: $${amount.toFixed(2)} (${riel.toLocaleString()} ៛)`;
            
            if (confirm(msg)) {
                submitBtn.disabled = true;
                btnText.textContent = isKhmer ? 'កំពុងរក្សាទុក...' : 'Submitting...';
                form.submit();
            }
        });

        // OTP modal actions
        cancelOtpBtn.addEventListener('click', function() {
            otpModalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                otpModal.classList.add('hidden');
            }, 300);
        });

        verifyOtpBtn.addEventListener('click', function() {
            const code = otpInput.value.trim();
            const isKhmer = '{{ app()->getLocale() }}' === 'km';

            if (code !== currentActiveOtp) {
                otpErrorMsg.classList.remove('hidden');
                otpInput.focus();
                return;
            }

            otpErrorMsg.classList.add('hidden');
            
            // Start Simulated Processing Animation Stages
            otpModalMainBody.classList.add('hidden');
            otpModalButtons.classList.add('hidden');
            otpProcessingOverlay.classList.remove('hidden');

            const processingStages = [
                { title: isKhmer ? 'កំពុងភ្ជាប់ទៅកាន់ធនាគារម្ចាស់កាត...' : 'Establishing secure channel...', desc: 'Securing transaction session...' },
                { title: isKhmer ? 'កំពុងផ្ទៀងផ្ទាត់ព័ត៌មានកាត...' : 'Verifying card credentials...', desc: 'Authorizing through VISA/Mastercard network...' },
                { title: isKhmer ? 'កំពុងផ្ទេរប្រាក់ចេញពីគណនី...' : 'Transferring funds from account...', desc: 'Finalizing ledger settlement...' },
                { title: isKhmer ? 'ទូទាត់ជោគជ័យ!' : 'Transaction Approved!', desc: 'Redirecting back to application...' }
            ];

            let stage = 0;
            function runStage() {
                if (stage < processingStages.length) {
                    otpProcessingTitle.innerText = processingStages[stage].title;
                    otpProcessingDesc.innerText = processingStages[stage].desc;
                    stage++;
                    setTimeout(runStage, 1000);
                } else {
                    // Populate hidden fields to database format
                    const cardNum = cardNumberInput.value.replace(/\s+/g, '');
                    hiddenHolder.value = cardHolderInput.value.trim();
                    hiddenNumber.value = cardNum.slice(-4);
                    hiddenBrand.value = detectCardBrand(cardNum);

                    // Submit form
                    submitBtn.disabled = true;
                    btnText.textContent = isKhmer ? 'កំពុងរក្សាទុក...' : 'Submitting...';
                    form.submit();
                }
            }
            runStage();
        });

        // Trigger verify button on enter key
        otpInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                verifyOtpBtn.click();
            }
        });
    });
</script>
@endsection