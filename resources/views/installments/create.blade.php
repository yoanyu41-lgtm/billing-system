@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.create_installment') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.configure_new_installment_plan') }}</p>
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
                            <option value="">-- {{ __('app.select') }} {{ __('app.customer') }} --</option>
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
                            <option value="" data-price="0">-- {{ __('app.select') }} {{ __('app.product') }} --</option>
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
                    <button type="submit" class="px-6 py-2.5 font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition duration-150">
                        {{ __('app.save') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Premium Real-Time Calculator Preview Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white text-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 sticky top-24">
                <h3 class="text-lg font-bold mb-4 flex items-center text-indigo-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 11h.01M12 7h.01M15 11h.01M12 14h.01M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"></path></svg>
                    {{ __('app.payment_calculator') }}
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500 text-sm">{{ __('app.subtotal') }}</span>
                        <span class="font-semibold text-gray-900" id="previewSubtotal">$0.00</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2" id="taxRow" style="display: none;">
                        <span class="text-gray-500 text-sm">{{ __('app.tax') }} (<span id="taxRateLabel">0</span>%)</span>
                        <span class="font-semibold text-gray-900" id="previewTax">$0.00</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500 text-sm font-semibold">{{ __('app.total_price') }}</span>
                        <span class="font-bold text-gray-900" id="previewTotal">$0.00</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500 text-sm">{{ __('app.principal_balance') }}</span>
                        <span class="font-semibold text-gray-900" id="previewPrincipal">$0.00</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500 text-sm">{{ __('app.monthly_interest') }}</span>
                        <span class="font-semibold text-gray-900" id="previewInterest">$0.00</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500 text-sm">{{ __('app.total_remaining') }}</span>
                        <span class="font-semibold text-gray-900" id="previewRemaining">$0.00</span>
                    </div>
                    <div class="pt-4 text-center bg-indigo-50 rounded-lg py-4 border border-indigo-100">
                        <span class="block text-indigo-400 text-xs uppercase tracking-wider font-semibold mb-1">{{ __('app.estimated_monthly_payment') }}</span>
                        <span class="text-3xl font-extrabold text-indigo-600" id="previewMonthly">$0.00</span>
                    </div>
                </div>

                <div class="mt-6 p-4 rounded-lg bg-gray-50 text-xs text-gray-500 leading-relaxed border border-gray-100">
                    <strong class="text-gray-700">{{ __('app.formula') }}:</strong><br>
                    {{ __('app.formula_principal') }}<br>
                    {{ __('app.formula_interest') }}<br>
                    {{ __('app.formula_payment') }}
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
    const previewSubtotal = document.getElementById('previewSubtotal');
    const previewTax = document.getElementById('previewTax');
    const previewTotal = document.getElementById('previewTotal');
    const taxRow = document.getElementById('taxRow');
    const taxRateLabel = document.getElementById('taxRateLabel');
    const previewPrincipal = document.getElementById('previewPrincipal');
    const previewInterest = document.getElementById('previewInterest');
    const previewRemaining = document.getElementById('previewRemaining');
    const previewMonthly = document.getElementById('previewMonthly');

    // Product data with tax info
    const products = [
        @foreach($products as $p)
        {
            id: {{ $p->id }},
            price: {{ $p->price }},
            is_taxable: {{ $p->is_taxable ? 'true' : 'false' }},
            tax_rate: {{ $p->tax_rate ?? 0 }},
            tax_type: '{{ $p->tax_type ?? "exclusive" }}'
        },
        @endforeach
    ];

    // Tax settings from database
    const taxEnabled = {{ \App\Models\Setting::where('key', 'tax_enabled')->value('value') == '1' ? 'true' : 'false' }};
    const defaultTaxRate = {{ (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 0) }};

    console.log('Tax settings - enabled:', taxEnabled, 'default rate:', defaultTaxRate);
    console.log('Products loaded:', products);

    // Auto fill total price on product select
    productSelect.addEventListener('change', () => {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        console.log('Product changed:', selectedOption.text, 'Price:', price);
        if(price > 0) {
            totalPriceInput.value = price.toFixed(2);
            console.log('Total price set to:', totalPriceInput.value);
        }
        calculateInstallment();
    });

    // Event listeners for calculation
    [totalPriceInput, downPaymentInput, interestRateInput, durationInput].forEach(input => {
        input.addEventListener('input', calculateInstallment);
    });

    function calculateInstallment() {
        const subtotalInput = parseFloat(totalPriceInput.value) || 0;
        const downPayment = parseFloat(downPaymentInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;
        const duration = parseInt(durationInput.value) || 1;

        console.log('calculateInstallment called - subtotal:', subtotalInput, 'down:', downPayment, 'interest:', interestRate, 'duration:', duration);

        // Get selected product tax info
        const selectedProductId = parseInt(productSelect.value);
        const selectedProduct = products.find(p => p.id === selectedProductId);
        
        console.log('Selected product ID:', selectedProductId, 'Product:', selectedProduct);
        
        let taxAmount = 0;
        let taxRate = 0;
        let totalPrice = subtotalInput;

        // Calculate tax if enabled and product is taxable
        if (taxEnabled && selectedProduct && selectedProduct.is_taxable) {
            taxRate = selectedProduct.tax_rate > 0 ? selectedProduct.tax_rate : defaultTaxRate;
            
            if (selectedProduct.tax_type === 'inclusive') {
                // Tax included in price
                taxAmount = subtotalInput - (subtotalInput / (1 + taxRate / 100));
            } else {
                // Tax exclusive (add on top)
                taxAmount = subtotalInput * (taxRate / 100);
                totalPrice = subtotalInput + taxAmount;
            }
            
            console.log('Tax calculated - rate:', taxRate, 'amount:', taxAmount, 'total:', totalPrice);
            taxRow.style.display = 'flex';
            taxRateLabel.innerText = taxRate.toFixed(0);
        } else {
            taxRow.style.display = 'none';
            console.log('No tax applied');
        }

        const principal = totalPrice - downPayment;
        const monthlyInterest = duration > 0 ? ((principal * interestRate / 100) / 12) : 0;
        const monthlyPayment = duration > 0 ? ((principal / duration) + monthlyInterest) : 0;
        const remaining = principal + (monthlyInterest * duration);

        console.log('Final calculation - principal:', principal, 'monthly:', monthlyPayment);

        // Render preview with formatting
        previewSubtotal.innerText = '$' + (subtotalInput > 0 ? subtotalInput.toFixed(2) : '0.00');
        previewTax.innerText = '$' + (taxAmount > 0 ? taxAmount.toFixed(2) : '0.00');
        previewTotal.innerText = '$' + (totalPrice > 0 ? totalPrice.toFixed(2) : '0.00');
        previewPrincipal.innerText = '$' + (principal > 0 ? principal.toFixed(2) : '0.00');
        previewInterest.innerText = '$' + (monthlyInterest > 0 ? monthlyInterest.toFixed(2) : '0.00');
        previewRemaining.innerText = '$' + (remaining > 0 ? remaining.toFixed(2) : '0.00');
        previewMonthly.innerText = '$' + (monthlyPayment > 0 ? monthlyPayment.toFixed(2) : '0.00');
    }

    // Run calculation initially
    calculateInstallment();
</script>
@endsection