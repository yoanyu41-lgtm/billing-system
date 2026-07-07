@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.customer_report') ?? 'Customer Report' }}</h1>
            <p class="text-sm text-gray-500 mt-1">Overview of customer portfolios, payments, and outstanding balances.</p>
        </div>
    </div>

    {{-- Calculations --}}
    @php
        $totalCustomers = $customers->count();
        $portfolioValue = 0;
        $totalPaid = 0;
        foreach ($customers as $c) {
            foreach ($c->installments as $inst) {
                if ($inst->status === 'active' || $inst->status === 'completed') {
                    $portfolioValue += $inst->total_price;
                    $totalPaid += $inst->payments->where('status', 'approved')->sum('amount');
                }
            }
        }
        $outstanding = $portfolioValue - $totalPaid;
        if ($outstanding < 0) $outstanding = 0;
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Customers</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Portfolio Value</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">${{ number_format($portfolioValue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Collected</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 bg-orange-50 p-5">
            <p class="text-xs text-orange-600 uppercase tracking-wide">Outstanding Balance</p>
            <p class="text-2xl font-extrabold text-orange-700 mt-1">${{ number_format($outstanding, 2) }}</p>
        </div>
    </div>

    {{-- Customers Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Customers Portfolio Summary</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Active Plans</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Value</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Paid</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Outstanding</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                        @php
                            $activePlans = $customer->installments->where('status', 'active');
                            $plansCount = $activePlans->count();
                            
                            $val = $customer->installments->sum('total_price');
                            $paid = 0;
                            foreach ($customer->installments as $inst) {
                                $paid += $inst->payments->where('status', 'approved')->sum('amount');
                            }
                            $out = $val - $paid;
                            if ($out < 0) $out = 0;
                            
                            $progress = $val > 0 ? ($paid / $val) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $customer->phone ?? 'No Phone' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-center font-semibold text-gray-700">
                                {{ $plansCount }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-medium text-slate-800">
                                ${{ number_format($val, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-medium text-emerald-600">
                                ${{ number_format($paid, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-orange-600">
                                ${{ number_format($out, 2) }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600">{{ round($progress) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-400">No customers found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
