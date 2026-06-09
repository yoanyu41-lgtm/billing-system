@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.create_installment') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Configure a new installment payment plan for a customer purchasing a product.</p>
        </div>
        <a href="{{ route('installments.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('app.back') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Form Card -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('installments.store') }}" id="installmentForm" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Customer -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.customer') }} <span class="text-red-500">*</span></label>
                        <select name="customer_id" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('customer_id') ? 'border-red-500' : 'border-gray-300' }}">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} {{ $customer->phone ? '(' . $customer->phone . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Product -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.product') }} <span class="text-red-500">*</span></label>
                        <select name="product_id" id="productId" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('product_id') ? 'border-red-500' : 'border-gray-300' }}">
                            <option value="" data-price="0">-- Choose Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (${{ number_format($product->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Price -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.total_price') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" step="0.01" name="total_price" id="totalPrice" value="{{ old('total_price') }}" required class="w-full border pl-8 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('total_price') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0.00">
                        </div>
                        @error('total_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Down Payment -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.down_payment') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" step="0.01" name="down_payment" id="downPayment" value="{{ old('down_payment', 0) }}" required class="w-full border pl-8 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('down_payment') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0.00">
                        </div>
                        @error('down_payment')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Interest Rate -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.interest_rate') }} (%)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">%</span>
                            <input type="number" step="0.01" name="interest_rate" id="interestRate" value="{{ old('interest_rate', 0) }}" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('interest_rate') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0.00">
                        </div>
                        @error('interest_rate')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.duration_months') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_months" id="durationMonths" value="{{ old('duration_months', 12) }}" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('duration_months') ? 'border-red-500' : 'border-gray-300' }}" placeholder="e.g., 12">
                        @error('duration_months')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('installments.index') }}" class="px-6 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150 shadow-sm">
                        {{ __('app.cancel') }}
                    </a>
                    <button type="submit" class="px-6 py-2.5 font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition duration-150">
                        {{ __('app.save') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Premium Real-Time Calculator Preview Panel -->
        <div class="lg:col-span-1">
            <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-md border border-indigo-950 sticky top-24">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 11h.01M12 7h.01M15 11h.01M12 14h.01M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"></path></svg>
                    Payment Calculator
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between border-b border-indigo-800 pb-2">
                        <span class="text-indigo-200 text-sm">Principal Balance</span>
                        <span class="font-semibold" id="previewPrincipal">$0.00</span>
                    </div>
                    <div class="flex justify-between border-b border-indigo-800 pb-2">
                        <span class="text-indigo-200 text-sm">Monthly Interest</span>
                        <span class="font-semibold" id="previewInterest">$0.00</span>
                    </div>
                    <div class="flex justify-between border-b border-indigo-800 pb-2">
                        <span class="text-indigo-200 text-sm">Total Remaining</span>
                        <span class="font-semibold" id="previewRemaining">$0.00</span>
                    </div>
                    <div class="pt-4 text-center">
                        <span class="block text-indigo-200 text-xs uppercase tracking-wider font-semibold mb-1">Estimated Monthly Payment</span>
                        <span class="text-3xl font-extrabold text-amber-300" id="previewMonthly">$0.00</span>
                    </div>
                </div>

                <div class="mt-6 p-4 rounded-lg bg-indigo-950/40 text-xs text-indigo-200 leading-relaxed border border-indigo-950/60">
                    <strong class="text-white">Formula:</strong><br>
                    Principal = Price - Down Payment<br>
                    Interest/Month = (Principal * Rate / 100) / 12<br>
                    Payment/Month = (Principal / Months) + Interest/Month
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const productSelect = document.getElementById('productId');
    const totalPriceInput = document.getElementById('totalPrice');
    const downPaymentInput = document.getElementById('downPayment');
    const interestRateInput = document.getElementById('interestRate');
    const durationInput = document.getElementById('durationMonths');

    // Live Preview elements
    const previewPrincipal = document.getElementById('previewPrincipal');
    const previewInterest = document.getElementById('previewInterest');
    const previewRemaining = document.getElementById('previewRemaining');
    const previewMonthly = document.getElementById('previewMonthly');

    // Auto fill total price on product select
    productSelect.addEventListener('change', () => {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        if(price > 0) {
            totalPriceInput.value = price.toFixed(2);
        }
        calculateInstallment();
    });

    // Event listeners for calculation
    [totalPriceInput, downPaymentInput, interestRateInput, durationInput].forEach(input => {
        input.addEventListener('input', calculateInstallment);
    });

    function calculateInstallment() {
        const totalPrice = parseFloat(totalPriceInput.value) || 0;
        const downPayment = parseFloat(downPaymentInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;
        const duration = parseInt(durationInput.value) || 1;

        const principal = totalPrice - downPayment;
        const monthlyInterest = duration > 0 ? ((principal * interestRate / 100) / 12) : 0;
        const monthlyPayment = duration > 0 ? ((principal / duration) + monthlyInterest) : 0;
        const remaining = principal + (monthlyInterest * duration);

        // Render preview with formatting
        previewPrincipal.innerText = '$' + (principal > 0 ? principal.toFixed(2) : '0.00');
        previewInterest.innerText = '$' + (monthlyInterest > 0 ? monthlyInterest.toFixed(2) : '0.00');
        previewRemaining.innerText = '$' + (remaining > 0 ? remaining.toFixed(2) : '0.00');
        previewMonthly.innerText = '$' + (monthlyPayment > 0 ? monthlyPayment.toFixed(2) : '0.00');
    }

    // Run calculation initially
    calculateInstallment();
</script>
@endsection