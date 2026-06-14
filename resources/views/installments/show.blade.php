@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm">{{ session('error') }}</div>
    @endif
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-gray-400 mb-1 flex items-center gap-1.5">
                <a href="{{ route('installments.index') }}" class="hover:text-indigo-600 transition">{{ __('app.installment_plans') }}</a>
                <span>/</span>
                <span class="text-gray-600">#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.installment_plan_details') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.installment_plan_details_sub') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('installments.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.back') }}
            </a>
            <a href="{{ route('installments.contract', $installment) }}" target="_blank" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                {{ __('app.print_contract') }}
            </a>
            <a href="{{ route('installments.edit', $installment) }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                {{ __('app.edit_plan') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Customer & Product Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Customer Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">{{ __('app.customer_profile') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('app.contract_holder') }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.name') }}</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->customer?->name ?? 'N/A' }}</div>
                    </div>
                    @if($installment->customer?->phone)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.phone') }}</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->customer?->phone }}</div>
                    </div>
                    @endif
                    @if($installment->customer?->email)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.email') }}</div>
                        <div class="text-sm text-gray-800">{{ $installment->customer?->email }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">{{ __('app.product_details') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('app.financed_asset') }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.model_name') }}</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->product?->name ?? 'N/A' }}</div>
                    </div>
                    @if($installment->product?->code)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.item_code') }}</div>
                        <div class="text-sm font-semibold text-indigo-600">{{ $installment->product?->code }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contract Document Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">{{ __('app.contract') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('app.contract_document') }}</p>
                    </div>
                </div>

                @if($installment->signed_contract)
                    <div class="space-y-3">
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-emerald-800">{{ __('app.signed') }}</p>
                                    <p class="text-xs text-emerald-600 mt-1">{{ $installment->contract_signed_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-xs text-emerald-600">{{ $installment->contract_signed_by }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('installments.downloadContract', $installment) }}" 
                               class="flex-1 inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>{{ __('app.download') }}</span>
                            </a>
                            <a href="{{ asset('storage/' . $installment->signed_contract) }}" target="_blank"
                               class="flex-1 inline-flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>{{ __('app.view') }}</span>
                            </a>
                        </div>

                        <form method="POST" action="{{ route('installments.deleteContract', $installment) }}" 
                              onsubmit="return confirm('{{ __('app.confirm_delete') }}')" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium px-4 py-2 rounded-lg transition-colors border border-red-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>{{ __('app.delete') }}</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-3">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-amber-800">{{ __('app.not_signed') }}</p>
                                    <p class="text-xs text-amber-600 mt-1" lang="km">សូម upload ឯកសារកិច្ចសន្យាដែលបានចុះហត្ថលេខារួចហើយ</p>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('installments.uploadContract', $installment) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" lang="km">ជ្រើសរើសឯកសារ</label>
                                    <input type="file" name="contract_file" accept=".pdf,.jpg,.jpeg,.png" required
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                    <p class="text-xs text-gray-500 mt-1" lang="km">PDF, JPG, PNG (អតិបរមា 5MB)</p>
                                    @error('contract_file')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span lang="km">Upload ឯកសារ</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Financial Breakdown -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Metrics -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">{{ __('app.financial_schedule') }}</h3>

                <div class="overflow-x-auto rounded-xl border border-gray-100 mb-6">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @if($installment->tax_amount > 0)
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.subtotal') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-bold text-gray-900 block">${{ number_format($installment->subtotal_before_tax ?? $installment->total_price, 2) }}</span>
                                    <span class="text-xs text-gray-400 block font-semibold">{{ number_format(round(($installment->subtotal_before_tax ?? $installment->total_price) * $exchangeRate)) }} ៛</span>
                                </td>
                            </tr>
                            @php
                                $taxLabel = \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT';
                            @endphp
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.tax') }} {{ $taxLabel }} ({{ $installment->tax_rate }}%)</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-bold text-gray-900 block">${{ number_format($installment->tax_amount, 2) }}</span>
                                    <span class="text-xs text-gray-400 block font-semibold">{{ number_format(round($installment->tax_amount * $exchangeRate)) }} ៛</span>
                                </td>
                            </tr>
                            @endif
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.total_price') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-bold text-gray-900 block">${{ number_format($installment->total_price, 2) }}</span>
                                    <span class="text-xs text-gray-400 block font-semibold">{{ number_format(round($installment->total_price * $exchangeRate)) }} ៛</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.down_payment') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-bold text-gray-900 block">${{ number_format($installment->down_payment, 2) }}</span>
                                    <span class="text-xs text-gray-400 block font-semibold">{{ number_format(round($installment->down_payment * $exchangeRate)) }} ៛</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.interest_rate') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">{{ $installment->interest_rate }}%</td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.duration') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">{{ $installment->duration_months }} {{ __('app.duration_unit') }}</td>
                            </tr>
                            <tr class="bg-indigo-50/60 hover:bg-indigo-50">
                                <td class="px-5 py-3.5 text-indigo-700 font-semibold">{{ __('app.monthly_payment') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-extrabold text-indigo-700 text-base block">${{ number_format($installment->monthly_payment, 2) }}</span>
                                    <span class="text-xs text-indigo-500 block font-semibold">{{ number_format(round($installment->monthly_payment * $exchangeRate)) }} ៛</span>
                                </td>
                            </tr>
                            <tr class="bg-amber-50/60 hover:bg-amber-50">
                                <td class="px-5 py-3.5 text-amber-800 font-semibold">{{ __('app.remaining_balance') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-extrabold text-amber-800 text-base block">${{ number_format($installment->remaining_balance, 2) }}</span>
                                    <span class="text-xs text-amber-600 block font-semibold">{{ number_format(round($installment->remaining_balance * $exchangeRate)) }} ៛</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Metadata details -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('app.plan_status') }}</span>
                        <div class="mt-1">
                            @if($installment->status === 'active' || $installment->status === 'ongoing')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">{{ __('app.active') }}</span>
                            @elseif($installment->status === 'completed' || $installment->status === 'paid')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">{{ __('app.completed') }}</span>
                            @elseif($installment->status === 'cancelled')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200">{{ __('app.cancelled') }}</span>
                            @else
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200">{{ $installment->status }}</span>
                            @endif
                        </div>
                    </div>
                    @if($installment->next_due_date)
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('app.next_due_date') }}</span>
                        <div class="mt-1 text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($installment->next_due_date)->format('d M Y') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Schedule section -->
    <div class="mt-8 bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-indigo-600"></i>
                <span>{{ __('app.payment_schedule') }}</span>
            </h3>
            <a href="{{ route('installments.schedule', $installment) }}" class="px-4 py-2 border border-indigo-200 text-indigo-600 hover:bg-indigo-50 font-semibold rounded-lg text-xs transition" style="text-decoration: none;">
                <i class="fas fa-print mr-1"></i>
                {{ app()->getLocale() === 'km' ? 'មើលកាលវិភាគបោះពុម្ព' : 'View Printable Schedule' }}
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border-b border-gray-200 px-4 py-3 text-center font-bold text-gray-600 text-xs">ល.រ<br><span class="font-normal text-[10px]">No.</span></th>
                        <th class="border-b border-gray-200 px-4 py-3 text-center font-bold text-gray-600 text-xs">{{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទបង់ប្រាក់' : 'Payment Date' }}</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-right font-bold text-gray-600 text-xs">{{ app()->getLocale() === 'km' ? 'ទឹកប្រាក់ត្រូវបង់' : 'Total Payment' }}</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-right font-bold text-gray-600 text-xs">{{ app()->getLocale() === 'km' ? 'ការប្រាក់' : 'Interests' }}</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-right font-bold text-gray-600 text-xs">{{ app()->getLocale() === 'km' ? 'ប្រាក់ដើម' : 'Principals' }}</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-right font-bold text-gray-600 text-xs">{{ app()->getLocale() === 'km' ? 'សមតុល្យប្រាក់ដើម' : 'Outstanding Principals' }}</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-right font-bold text-gray-600 text-xs">{{ app()->getLocale() === 'km' ? 'សមតុល្យបំណុល' : 'Outstanding Debts' }}</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-center font-bold text-gray-600 text-xs">{{ __('app.status') }}</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-center font-bold text-gray-600 text-xs">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($schedule as $row)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-4 py-3 text-center text-gray-700 font-semibold">{{ $row['month'] }}</td>
                        <td class="px-4 py-3 text-center text-gray-700 whitespace-nowrap">
                            {{ $row['due_date']->format('d/m/Y') }} 
                            <span class="text-gray-400 text-xs">({{ $row['day'] }})</span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">{{ format_currency($row['amount'], $exchangeRate) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ format_currency($row['interest'], $exchangeRate) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ format_currency($row['principal'], $exchangeRate) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ format_currency($row['outstanding_principal'], $exchangeRate) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ format_currency($row['outstanding_debt'], $exchangeRate) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($row['status'] === 'paid')
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-green-50 text-green-700 border border-green-200">{{ __('app.paid') }}</span>
                            @elseif($row['status'] === 'overdue')
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-red-50 text-red-700 border border-red-200">{{ __('app.overdue') }}</span>
                            @else
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-gray-50 text-gray-600 border border-gray-200">{{ __('app.pending') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($row['status'] !== 'paid')
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Send QR via Telegram --}}
                                @if(empty($installment->customer?->telegram_id))
                                    <span class="px-2.5 py-1 text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded-lg inline-flex items-center gap-1 cursor-not-allowed" title="{{ __('app.telegram_id_missing') }}">
                                        <i class="fab fa-telegram-plane"></i>
                                        <span>{{ __('app.send_qr_telegram') }}</span>
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('installments.send-telegram-qr', [$installment, $row['month']]) }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-xs text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border-0 cursor-pointer flex items-center gap-1 transition font-bold" title="{{ __('app.send_qr_telegram') }}">
                                            <i class="fab fa-telegram-plane"></i>
                                            <span>{{ __('app.send_qr_telegram') }}</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- Record Payment --}}
                                <button type="button" onclick="openRecordPaymentModal({{ $row['month'] }}, {{ $row['amount'] }}, '{{ $row['due_date']->toDateString() }}')" class="px-2.5 py-1 text-xs text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg border-0 cursor-pointer flex items-center gap-1 transition font-bold" title="{{ __('app.record_payment') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span>{{ __('app.record_payment') }}</span>
                                </button>
                            </div>
                            @else
                                <span class="text-xs text-gray-400 font-medium">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="recordPaymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRecordPaymentModal()"></div>

        <!-- Modal Center spacer -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal content card -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="m-0">
                @csrf
                <input type="hidden" name="installment_id" value="{{ $installment->id }}">
                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                
                <div class="bg-white px-6 pt-6 pb-4 sm:p-6 sm:pb-4 space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                            <span>{{ __('app.record_payment') }}</span>
                        </h3>
                        <button type="button" onclick="closeRecordPaymentModal()" class="text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer text-xl">
                            &times;
                        </button>
                    </div>

                    <!-- Details Display -->
                    <div class="bg-indigo-50/50 rounded-xl p-4 text-sm text-indigo-950 border border-indigo-100 flex justify-between">
                        <div>
                            <span class="font-medium block text-xs text-indigo-500 uppercase tracking-wider">{{ __('app.customer') }}</span>
                            <span class="font-semibold">{{ $installment->customer?->name }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-medium block text-xs text-indigo-500 uppercase tracking-wider" id="modalMonthLabel">Month</span>
                            <span class="font-semibold" id="modalMonthVal">1</span>
                        </div>
                    </div>

                    <!-- Amount and Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('app.amount') }} (USD)</label>
                            <input 
                                type="number" 
                                name="amount" 
                                id="modalAmountInput" 
                                step="0.01" 
                                min="0.01" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('app.payment_date') }}</label>
                            <input 
                                type="date" 
                                name="payment_date" 
                                value="{{ now()->toDateString() }}" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                        </div>
                    </div>

                    <!-- Payment Method selector -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('app.payment_method') }}</label>
                        <select name="payment_method_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @foreach($paymentMethods as $method)
                                @php
                                    $methodKey = strtolower(str_replace(' ', '_', $method->name));
                                    $translatedName = __('app.' . $methodKey);
                                    if ($translatedName === 'app.' . $methodKey) {
                                        $translatedName = $method->name;
                                    }
                                @endphp
                                <option value="{{ $method->id }}" {{ $methodKey === 'qr_code' ? 'selected' : '' }}>
                                    {{ $translatedName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer Slip Attachment -->
                    <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-4">
                        <label class="block text-sm font-bold text-blue-900 mb-1.5">
                            <i class="fas fa-image mr-1"></i>
                            {{ __('app.upload_customer_slip') }}
                        </label>
                        <input type="file" name="qr_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="mt-1 text-[11px] text-gray-500">
                            {{ app()->getLocale() === 'km' ? 'សូមបញ្ចូលរូបភាពបង្កាន់ដៃទូទាត់ QR / Slip របស់អតិថិជន។' : 'Please upload the customer\'s QR payment receipt/slip image.' }}
                        </p>
                    </div>

                    <!-- Approve Immediately (for authorized users) -->
                    @can('approve-payment')
                    <div class="flex items-center gap-2 pt-1">
                        <input 
                            type="checkbox" 
                            name="approve_now" 
                            id="modalApproveCheckbox" 
                            value="1" 
                            checked 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="modalApproveCheckbox" class="text-sm font-semibold text-gray-700 cursor-pointer flex items-center gap-1 select-none">
                            <i class="fas fa-check-double text-emerald-600"></i>
                            <span>{{ __('app.approve_now') }}</span>
                        </label>
                    </div>
                    @endcan
                </div>

                <!-- Footer buttons -->
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition text-sm shadow-sm border-0 cursor-pointer"
                    >
                        {{ __('app.confirm_and_approve') }}
                    </button>
                    <button 
                        type="button" 
                        onclick="closeRecordPaymentModal()" 
                        class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition text-sm"
                    >
                        {{ __('app.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRecordPaymentModal(month, amount, dueDate) {
        document.getElementById('modalMonthVal').innerText = month;
        document.getElementById('modalMonthLabel').innerText = "{{ __('app.installment_month') }} " + month;
        document.getElementById('modalAmountInput').value = amount;
        
        const modal = document.getElementById('recordPaymentModal');
        modal.classList.remove('hidden');
    }

    function closeRecordPaymentModal() {
        const modal = document.getElementById('recordPaymentModal');
        modal.classList.add('hidden');
    }
</script>
@endsection
