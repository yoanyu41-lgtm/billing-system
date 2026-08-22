@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Reports Section Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1 flex items-center gap-2">
                <i class="fas fa-calendar-day"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការលក់ប្រចាំថ្ងៃ' : 'Daily Sales Report' }}
            </h1>
            <p class="text-sm font-semibold text-slate-500 mt-1 font-mono">
                @if($startDate === $endDate)
                    Date: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                @else
                    From {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                @endif
            </p>
        </div>

        @include('admin.reports._nav')
    </div>

    <!-- Filter & Action Controls Box -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-5 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Quick Filter Pills: Today, Week, Month, Year -->
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-2xl text-xs font-bold border border-slate-200/60">
                <a href="{{ route('admin.reports.daily', ['filter' => 'today']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'today' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Today</a>
                <a href="{{ route('admin.reports.daily', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">This Week</a>
                <a href="{{ route('admin.reports.daily', ['filter' => 'this_month']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_month' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">This Month</a>
                <a href="{{ route('admin.reports.daily', ['filter' => 'this_year']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_year' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">This Year</a>
            </div>

            <!-- Export Buttons: Print, Excel, CSV -->
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="printReportDirect('{{ route('admin.reports.print', ['type' => 'daily', 'start_date' => $startDate, 'end_date' => $endDate]) }}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border-0 cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-print text-slate-500"></i> {{ app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print' }}
                </button>
                <a href="{{ route('admin.reports.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition border border-emerald-200/60 no-underline flex items-center gap-1.5">
                    <i class="fas fa-file-excel text-emerald-600"></i> Excel
                </a>
                <a href="{{ route('admin.reports.excel', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'csv']) }}" class="px-4 py-2 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold rounded-xl text-xs transition border border-teal-200/60 no-underline flex items-center gap-1.5">
                    <i class="fas fa-file-csv text-teal-600"></i> CSV
                </a>
            </div>
        </div>

        <!-- Custom Date Inputs + Search -->
        <form method="GET" action="{{ route('admin.reports.daily') }}" class="flex flex-wrap items-center gap-3 pt-1 border-t border-slate-100">
            <input type="hidden" name="filter" value="custom">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                <span>Date From</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                <span>Date To</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-xs border-0 cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>

    <!-- 4 Summary KPI Cards Focused on Daily Sales & Collections -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 🟦 Total Daily Sales -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 border border-blue-100">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Total Sales</span>
                <span class="text-2xl font-black text-blue-600">{{ format_currency($totalSales, $exchangeRate ?? 4100) }}</span>
            </div>
        </div>

        <!-- 🟩 Transactions Count -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 border border-emerald-100">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Transactions</span>
                <span class="text-2xl font-black text-slate-900">{{ count($transactions) }} Items</span>
            </div>
        </div>

        <!-- 🟪 Installment Collection -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0 border border-purple-100">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Installments</span>
                <span class="text-2xl font-black text-purple-600">{{ format_currency($kpiInstallments, $exchangeRate ?? 4100) }}</span>
            </div>
        </div>

        <!-- 🟧 Detailed Profit & Income Report Link -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-5 rounded-2xl shadow-sm text-white flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Profit Breakdown</span>
                <i class="fas fa-chart-line text-emerald-400"></i>
            </div>
            <div class="mt-2">
                <div class="text-xs text-slate-300">Net Income: <strong class="text-white">{{ format_currency($netIncome, $exchangeRate ?? 4100) }}</strong></div>
                <a href="{{ route('admin.reports.profit') }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-400 hover:text-emerald-300 mt-1 no-underline">
                    View Profit Report <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fas fa-receipt text-blue-500"></i> Daily Transactions Table
            </h3>
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-600 rounded-full">
                {{ count($transactions) }} Items
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Invoice</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6 text-right">Total</th>
                        <th class="py-3.5 px-6 text-right">Discount</th>
                        <th class="py-3.5 px-6">Payment</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Cashier</th>
                        <th class="py-3.5 px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900 font-mono text-xs">
                            {{ $trx->invoice_no }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-700">
                            {{ $trx->customer }}
                        </td>
                        <td class="py-4 px-6 text-right font-black text-slate-900">
                            {{ format_currency($trx->amount, $exchangeRate ?? 4100) }}
                        </td>
                        <td class="py-4 px-6 text-right font-semibold text-slate-500 text-xs">
                            {{ format_currency($trx->discount ?? 0, $exchangeRate ?? 4100) }}
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-700">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-bold text-slate-700">
                                <i class="fas fa-wallet text-slate-400"></i> {{ $trx->method }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $trx->status }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 font-mono">
                            {{ $trx->created_at }}
                        </td>
                        <td class="py-4 px-6 text-xs font-medium text-slate-700">
                            <i class="fas fa-user-shield text-blue-500 text-xs mr-1"></i> {{ $trx->cashier ?? 'Admin' }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button onclick="alert('Invoice: {{ $trx->invoice_no }}\nCustomer: {{ $trx->customer }}\nTotal: {{ format_currency($trx->amount, $exchangeRate ?? 4100) }}\nCashier: {{ $trx->cashier ?? 'Admin' }}')" class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold rounded-lg text-xs transition border-0 cursor-pointer">
                                View Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                            No Data Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
