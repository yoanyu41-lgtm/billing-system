@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.customer_report') ?? 'Customer Report' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.customer_report_subtitle') }}</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកអតិថិជន...' : 'Search customer...' }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition duration-150 border-0 cursor-pointer">
                <i class="fas fa-search"></i> {{ __('app.search') }}
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('admin.reports.customer') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition duration-150" style="text-decoration: none;">
                    {{ app()->getLocale() === 'km' ? 'ជម្រះ' : 'Clear' }}
                </a>
            @endif
        </form>
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
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.total_customers') }}</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.portfolio_value') }}</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">${{ number_format($portfolioValue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.total_collected') }}</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 bg-orange-50 p-5">
            <p class="text-xs text-orange-600 uppercase tracking-wide">{{ __('app.outstanding_balance') }}</p>
            <p class="text-2xl font-extrabold text-orange-700 mt-1">${{ number_format($outstanding, 2) }}</p>
        </div>
    </div>

    {{-- Customers Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">{{ __('app.customers_portfolio_summary') }}</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.customer') }}</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.active_plans') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.total_value') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.total_paid') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.outstanding') }}</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.progress') }}</th>
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
                                <div class="text-xs text-gray-500">{{ $customer->phone ?? __('app.no_phone') }}</div>
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
                                <div class="flex items-center justify-center gap-2.5">
                                    @if(round($progress) >= 100)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-check-circle text-[10px]"></i> 100%
                                        </span>
                                    @else
                                        <div class="w-20 bg-gray-100 rounded-full h-2 overflow-hidden border border-gray-200/30">
                                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-600">{{ round($progress) }}%</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-400">{{ __('app.no_customers_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            let timeout = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const query = this.value.trim();
                
                // Trigger form submit only when length is 0 (cleared) or >= 2
                if (query.length >= 2 || query.length === 0) {
                    timeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 400); // 400ms debounce delay
                }
            });
            
            // Keep focus and put cursor at the end of the text on reload
            if (searchInput.value.length > 0) {
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.focus();
                searchInput.value = val;
            }
        }
    });
</script>
@endsection
