@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('payments.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1" style="text-decoration: none;">
            <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
        </a>
        <h1 class="mt-2 text-3xl font-bold text-gray-800">{{ __('app.payment_details') }}</h1>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Customer -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ __('app.customer') }}</label>
                <div class="text-base font-semibold text-gray-800">
                    {{ $payment->installment?->customer?->name ?? 'N/A' }}
                </div>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ __('app.payment_method') }}</label>
                <div class="text-base font-semibold text-gray-800">
                    @php
                        $methodKey = strtolower(str_replace(' ', '_', $payment->paymentMethod->name ?? ''));
                    @endphp
                    {{ __('app.' . $methodKey) ?: ($payment->paymentMethod->name ?? 'N/A') }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Amount -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ __('app.amount') }}</label>
                <div class="text-lg font-bold text-gray-900">
                    {{ format_currency($payment->amount, $exchangeRate) }}
                </div>
            </div>

            <!-- Payment Date -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ __('app.date') }}</label>
                <div class="text-base font-semibold text-gray-800">
                    {{ $payment->payment_date }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Status -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ __('app.status') }}</label>
                <div class="mt-1">
                    @php
                        $pstatus = $payment->status ?? 'pending';
                        $statusColors = [
                            'approved' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            'rejected' => 'bg-red-100 text-red-600'
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$pstatus] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ __('app.'.$pstatus) }}
                    </span>
                </div>
            </div>

            <!-- Approved By / Actor -->
            @if($payment->approved_by)
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                    {{ app()->getLocale() === 'km' ? 'អនុម័តដោយ' : 'Approved By' }}
                </label>
                <div class="text-base font-semibold text-gray-800">
                    {{ $payment->user?->name ?? 'N/A' }}
                </div>
            </div>
            @endif
        </div>

        <!-- Attachment -->
        @if($payment->qr_image)
        <div class="pt-4 border-t border-gray-100">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                {{ app()->getLocale() === 'km' ? 'រូបភាព QR / វិក្កយបត្រ' : 'QR / Receipt Image' }}
            </label>
            <div class="mt-2">
                <img 
                    src="{{ asset('storage/' . $payment->qr_image) }}" 
                    alt="Receipt / QR proof" 
                    class="max-w-xs h-auto rounded-lg border border-gray-200 shadow-sm"
                >
            </div>
        </div>
        @endif
    </div>
</div>
@endsection