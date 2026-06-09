@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Installment Plan Details</h1>
            <p class="text-sm text-gray-500 mt-1">Review the customer contract, financial figures, and payment status.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('installments.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.back') }} to List
            </a>
            <a href="{{ route('installments.edit', $installment) }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                {{ __('app.edit') }} Plan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Customer & Product Info (1 Col) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Customer Card -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Customer Profile</h3>
                        <p class="text-xs text-gray-500">Contract Holder</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">Name</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->customer?->name ?? 'N/A' }}</div>
                    </div>
                    @if($installment->customer?->phone)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">Phone Number</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->customer?->phone }}</div>
                    </div>
                    @endif
                    @if($installment->customer?->email)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">Email</div>
                        <div class="text-sm text-gray-800">{{ $installment->customer?->email }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Card -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Product Details</h3>
                        <p class="text-xs text-gray-500">Financed Asset</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">Model Name</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->product?->name ?? 'N/A' }}</div>
                    </div>
                    @if($installment->product?->code)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">Item Code</div>
                        <div class="text-sm font-semibold text-indigo-600">{{ $installment->product?->code }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Financial Breakdown (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Metrics -->
            <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Financial Schedule</h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                    <div class="p-4 rounded-xl bg-gray-50/70 border border-gray-100">
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Total Price</div>
                        <div class="text-xl font-bold text-gray-900">${{ number_format($installment->total_price, 2) }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50/70 border border-gray-100">
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Down Payment</div>
                        <div class="text-xl font-bold text-gray-900">${{ number_format($installment->down_payment, 2) }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50/70 border border-gray-100">
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Interest Rate</div>
                        <div class="text-xl font-bold text-gray-900">{{ $installment->interest_rate }}%</div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50/70 border border-gray-100">
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Duration</div>
                        <div class="text-xl font-bold text-gray-900">{{ $installment->duration_months }} Months</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Monthly Payment highlight -->
                    <div class="p-6 rounded-xl bg-indigo-50 border border-indigo-100">
                        <div class="text-xs text-indigo-600 font-bold uppercase tracking-wider mb-1">Monthly Payment</div>
                        <div class="text-3xl font-extrabold text-indigo-900">${{ number_format($installment->monthly_payment, 2) }}</div>
                        <p class="text-xs text-indigo-500 mt-2 font-medium">To be collected monthly until fully paid.</p>
                    </div>

                    <!-- Remaining balance highlight -->
                    <div class="p-6 rounded-xl bg-amber-50 border border-amber-100">
                        <div class="text-xs text-amber-700 font-bold uppercase tracking-wider mb-1">Remaining Balance</div>
                        <div class="text-3xl font-extrabold text-amber-900">${{ number_format($installment->remaining_balance, 2) }}</div>
                        <p class="text-xs text-amber-600 mt-2 font-medium">Current outstanding principal & interest.</p>
                    </div>
                </div>
            </div>

            <!-- Metadata details -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Plan Status</span>
                        <div class="mt-1">
                            @if($installment->status === 'active' || $installment->status === 'ongoing')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    Active
                                </span>
                            @elseif($installment->status === 'completed' || $installment->status === 'paid')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                    Completed
                                </span>
                            @elseif($installment->status === 'cancelled')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                    Cancelled
                                </span>
                            @else
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ $installment->status }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($installment->next_due_date)
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Next Due Date</span>
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