@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                {{ __('app.payments') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.manage_your_business_easily') }}</p>
        </div>
        <div>
            <a href="{{ route('payments.create') }}" 
               class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-sm"
               style="text-decoration: none;"
            >
                <i class="fas fa-plus"></i>
                <span>{{ __('app.add_payment') }}</span>
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('payments.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Status Filter -->
            <div class="w-full md:w-48">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('app.status') }}</label>
                <select 
                    name="status" 
                    class="px-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                    <option value="">{{ __('app.all') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('app.pending') }}</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('app.approved') }}</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('app.rejected') }}</option>
                </select>
            </div>

            <!-- Filter Button -->
            <button 
                type="submit" 
                class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-150 flex items-center justify-center gap-2 text-sm border-0"
            >
                <i class="fas fa-filter"></i>
                <span>{{ __('app.filter') }}</span>
            </button>

            <!-- Clear Button -->
            @if(request('status'))
            <a 
                href="{{ route('payments.index') }}" 
                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-150 flex items-center justify-center gap-2 text-sm"
                style="text-decoration: none;"
            >
                <i class="fas fa-times"></i>
                <span>{{ __('app.clear') }}</span>
            </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle mr-2 text-green-500 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Payments Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.customer') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.payment_method') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.amount') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.date') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $payment->installment?->customer?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $methodKey = strtolower(str_replace(' ', '_', $payment->paymentMethod->name ?? ''));
                                $badgeColor = 'bg-gray-50 text-gray-700 border-gray-100';
                                if ($methodKey === 'cash') {
                                    $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                } elseif ($methodKey === 'qr_code') {
                                    $badgeColor = 'bg-blue-50 text-blue-700 border-blue-100';
                                } elseif ($methodKey === 'credit_card') {
                                    $badgeColor = 'bg-purple-50 text-purple-700 border-purple-100';
                                }
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeColor }}">
                                {{ __('app.' . $methodKey) ?: ($payment->paymentMethod->name ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="font-bold text-gray-900">{{ format_currency($payment->amount, $exchangeRate) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $payment->payment_date }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $pstatus = $payment->status ?? 'pending';
                                $statusColors = [
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'rejected' => 'bg-red-100 text-red-600'
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$pstatus] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ __('app.'.$pstatus) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-1.5">
                                <a href="{{ route('payments.show', $payment) }}" 
                                   class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-900 rounded-lg transition duration-150" 
                                   title="{{ __('app.view') }}"
                                >
                                    <i class="fas fa-eye text-base"></i>
                                </a>
                                @if($payment->status === 'pending' && auth()->user()->role === 'admin')
                                <form method="POST" action="{{ route('payments.approve', $payment) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-900 rounded-lg transition duration-150 border-0 cursor-pointer" 
                                            title="{{ __('app.approve') }}"
                                    >
                                        <i class="fas fa-check text-base"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('payments.reject', $payment) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2 text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-900 rounded-lg transition duration-150 border-0 cursor-pointer" 
                                            title="{{ __('app.reject') }}"
                                    >
                                        <i class="fas fa-times text-base"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                            {{ __('app.no_payments') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection