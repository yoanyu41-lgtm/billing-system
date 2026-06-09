@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.invoice_detail') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.view') }} {{ __('app.invoice') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.back') }}
            </a>
            <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                {{ __('app.print') }}
            </a>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 md:p-12 relative overflow-hidden">
        <!-- Watermark / Header accent -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-indigo-600"></div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-8 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">CITYTECH COMPUTER</h2>
                <p class="text-xs text-gray-500 mt-1">Phnom Penh, Cambodia | Phone: 012 345 678</p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('app.invoice_number') }}</div>
                <div class="text-lg font-bold text-indigo-600">{{ $invoice->invoice_number }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('app.date') }}: {{ $invoice->created_at?->format('d M Y') ?? '—' }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Customer info -->
            <div class="p-5 rounded-xl bg-gray-50/70 border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('app.customer') }}</h3>
                <div class="space-y-1.5">
                    <div class="text-sm font-semibold text-gray-800">{{ $invoice->payment?->installment?->customer?->name ?? 'N/A' }}</div>
                    @if($invoice->payment?->installment?->customer?->phone)
                        <div class="text-xs text-gray-600">{{ __('app.phone') }}: {{ $invoice->payment->installment->customer->phone }}</div>
                    @endif
                </div>
            </div>

            <!-- Meta info -->
            <div class="p-5 rounded-xl bg-gray-50/70 border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('app.staff') }}</h3>
                <div class="space-y-1.5">
                    <div class="text-sm font-semibold text-gray-800">{{ $invoice->payment?->installment?->user?->name ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ __('app.approved_by') }}</div>
                </div>
            </div>
        </div>

        <!-- Details Table -->
        <div class="border border-gray-100 rounded-xl overflow-hidden mb-8">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('app.product') }}</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('app.amount') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr>
                        <td class="px-6 py-5">
                            <div class="text-sm font-semibold text-gray-900">{{ $invoice->payment?->installment?->product?->name ?? 'N/A' }}</div>
                            @if($invoice->payment?->installment?->product?->code)
                                <div class="text-xs text-indigo-600 font-medium mt-0.5">{{ __('app.code') }}: {{ $invoice->payment->installment->product->code }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right whitespace-nowrap text-sm font-bold text-gray-900">
                            ${{ number_format($invoice->payment?->amount ?? 0, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Invoice summary calculations -->
        <div class="flex justify-end">
            <div class="w-full sm:w-80 space-y-3">
                <div class="flex justify-between text-sm text-gray-600 px-2">
                    <span>{{ __('app.amount') }}</span>
                    <span class="font-bold text-gray-900">${{ number_format($invoice->payment?->amount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-amber-700 bg-amber-50 rounded-lg p-3 font-semibold border border-amber-100">
                    <span>{{ __('app.remaining_balance') }}</span>
                    <span>${{ number_format($invoice->payment?->installment?->remaining_balance ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-8 mt-12 text-center text-xs text-gray-400 leading-relaxed">
            {{ __('app.promo_text') }}
        </div>
    </div>
</div>
@endsection