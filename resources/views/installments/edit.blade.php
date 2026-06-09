@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Installment</h1>
            <p class="text-sm text-gray-500 mt-1">Modify pricing parameters or update status for installment plan #{{ $installment->id }}.</p>
        </div>
        <a href="{{ route('installments.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('app.back') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Form Card -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('installments.update', $installment) }}" id="installmentForm" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Total Price -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.total_price') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" step="0.01" name="total_price" id="totalPrice" value="{{ old('total_price', $installment->total_price) }}" required class="w-full border pl-8 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('total_price') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0.00">
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
                            <input type="number" step="0.01" name="down_payment" id="downPayment" value="{{ old('down_payment', $installment->down_payment) }}" required class="w-full border pl-8 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('down_payment') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0.00">
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
                            <input type="number" step="0.01" name="interest_rate" id="interestRate" value="{{ old('interest_rate', $installment->interest_rate) }}" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('interest_rate') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0.00">
                        </div>
                        @error('interest_rate')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.duration_months') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_months" id="durationMonths" value="{{ old('duration_months', $installment->duration_months) }}" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('duration_months') ? 'border-red-500' : 'border-gray-300' }}" placeholder="e.g., 12">
                        @error('duration_months')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.status') }} <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('status') ? 'border-red-500' : 'border-gray-300' }}">
                            <option value="active" {{ old('status', $installment->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="cancelled" {{ old('status', $installment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('installments.index') }}" class="px-6 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150 shadow-sm">
                        {{ __('app.cancel') }}
                    </a>
                    <button type="submit" class="px-6 py-2.5 font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition duration-150">
                        {{ __('app.update') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Preview Calculator -->
        <div class="lg:col-span-1">
            <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-md border border-indigo-950 sticky top-24">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 11h.01M12 7h.01M15 11h.01M12 14h.01M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"></path></svg>
                    Updated Calculator
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
                    <strong class="text-white font-semibold">Active Details:</strong><br>
                    Customer: {{ $installment->customer?->name ?? 'N/A' }}<br>
                    Product: {{ $installment->product?->name ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const totalPriceInput = document.getElementById('totalPrice');
    const downPaymentInput = document.getElementById('downPayment');
    const interestRateInput = document.getElementById('interestRate');
    const durationInput = document.getElementById('durationMonths');

    // Live Preview elements
    const previewPrincipal = document.getElementById('previewPrincipal');
    const previewInterest = document.getElementById('previewInterest');
    const previewRemaining = document.getElementById('previewRemaining');
    const previewMonthly = document.getElementById('previewMonthly');

    // Event listeners
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

        previewPrincipal.innerText = '$' + (principal > 0 ? principal.toFixed(2) : '0.00');
        previewInterest.innerText = '$' + (monthlyInterest > 0 ? monthlyInterest.toFixed(2) : '0.00');
        previewRemaining.innerText = '$' + (remaining > 0 ? remaining.toFixed(2) : '0.00');
        previewMonthly.innerText = '$' + (monthlyPayment > 0 ? monthlyPayment.toFixed(2) : '0.00');
    }

    calculateInstallment();
</script>
@endsection