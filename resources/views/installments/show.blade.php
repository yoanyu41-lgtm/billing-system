@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
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
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.total_price') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">${{ number_format($installment->total_price, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.down_payment') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">${{ number_format($installment->down_payment, 2) }}</td>
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
                                <td class="px-5 py-3.5 text-right font-extrabold text-indigo-700 text-base">${{ number_format($installment->monthly_payment, 2) }}</td>
                            </tr>
                            <tr class="bg-amber-50/60 hover:bg-amber-50">
                                <td class="px-5 py-3.5 text-amber-800 font-semibold">{{ __('app.remaining_balance') }}</td>
                                <td class="px-5 py-3.5 text-right font-extrabold text-amber-800 text-base">${{ number_format($installment->remaining_balance, 2) }}</td>
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
</div>
@endsection
