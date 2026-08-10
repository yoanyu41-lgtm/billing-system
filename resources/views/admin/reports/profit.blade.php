@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-emerald-600 mb-1">
                <i class="fas fa-chart-line"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ប្រាក់ចំណេញ (Profit / Income Report)' : 'Profit / Income Report' }}
            </h1>
            <p class="text-sm font-semibold text-slate-500 mt-1 font-mono">
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </p>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Header Controls & Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 no-print">
        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-2xl text-xs font-bold border border-slate-200/60">
                <a href="{{ route('admin.reports.profit', ['filter' => 'daily']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}</a>
                <a href="{{ route('admin.reports.profit', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}</a>
                <a href="{{ route('admin.reports.profit', ['filter' => 'monthly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}</a>
                <a href="{{ route('admin.reports.profit', ['filter' => 'yearly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}</a>
            </div>

            <form method="GET" action="{{ route('admin.reports.profit') }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="filter" value="custom">
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="text-xs font-medium text-slate-400">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-xs border-0 cursor-pointer flex items-center justify-center">
                    Filter
                </button>
            </form>

            <button onclick="window.print()" class="ml-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border-0 cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> Print
            </button>
        </div>
    </div>

    <!-- Formula Information Note Box -->
    <div class="p-4 rounded-2xl bg-slate-900 text-white shadow-sm space-y-2">
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-400">
            <i class="fas fa-calculator"></i> Profit Calculation Standard Formula
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs font-mono text-slate-300">
            <div class="bg-slate-800/80 p-2.5 rounded-xl border border-slate-700">
                <span class="text-slate-400">Net Sales:</span> <strong class="text-white">Selling Price - Discount</strong>
            </div>
            <div class="bg-slate-800/80 p-2.5 rounded-xl border border-slate-700">
                <span class="text-slate-400">Gross Profit:</span> <strong class="text-emerald-400">Net Sales - Cost Price</strong>
            </div>
            <div class="bg-slate-800/80 p-2.5 rounded-xl border border-slate-700">
                <span class="text-slate-400">Net Income:</span> <strong class="text-amber-400">Gross Profit - Total Expense</strong>
            </div>
        </div>
    </div>

    <!-- 5 Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Net Sales (Blue) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-blue-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-shopping-bag"></i> Net Sales
            </div>
            <h3 class="text-xl font-black text-blue-600">${{ number_format($netSales, 2) }}</h3>
        </div>

        <!-- Total Cost Price (Amber) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-amber-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-boxes-packing"></i> Cost Price
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($totalCost, 2) }}</h3>
        </div>

        <!-- Gross Profit (Green) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-emerald-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-chart-line"></i> Gross Profit
            </div>
            <h3 class="text-xl font-black text-emerald-600">${{ number_format($grossProfit, 2) }}</h3>
        </div>

        <!-- Expenses (Red) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-rose-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-hand-holding-dollar"></i> Expenses
            </div>
            <h3 class="text-xl font-black text-rose-600">${{ number_format($totalExpenses, 2) }}</h3>
        </div>

        <!-- Net Income (Orange) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-amber-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-coins"></i> Net Income
            </div>
            <h3 class="text-xl font-black {{ $netIncome >= 0 ? 'text-amber-600' : 'text-rose-600' }}">
                ${{ number_format($netIncome, 2) }}
            </h3>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fas fa-receipt text-emerald-500"></i> Financial Profit Ledger
            </h3>
            <span class="text-sm font-black text-emerald-600">
                Net Income: ${{ number_format($netIncome, 2) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Ref No.</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4 text-right">Selling Price</th>
                        <th class="py-3.5 px-4 text-right">Cost Price</th>
                        <th class="py-3.5 px-4 text-right">Discount</th>
                        <th class="py-3.5 px-4 text-right">Net Sales</th>
                        <th class="py-3.5 px-4 text-right">Gross Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($ledger as $item)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3.5 px-4 text-xs font-mono text-slate-500">
                            {{ $item->date->format('d/m/Y') }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-900 font-mono text-xs">
                            {{ $item->ref_no }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            @if($item->type === 'Direct Sale')
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Direct</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">Installment</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-800">
                            {{ $item->customer }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-semibold text-slate-900">
                            ${{ number_format($item->selling_price, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-semibold text-amber-600">
                            ${{ number_format($item->cost_price, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-semibold text-purple-600">
                            ${{ number_format($item->discount, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-blue-600">
                            ${{ number_format($item->net_sales, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-emerald-600">
                            ${{ number_format($item->gross_profit, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                            No Financial Ledger Data Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900 text-xs">
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-right uppercase tracking-wider">Total Summary:</td>
                        <td class="py-4 px-4 text-right font-black">${{ number_format($totalSelling, 2) }}</td>
                        <td class="py-4 px-4 text-right font-black text-amber-600">${{ number_format($totalCost, 2) }}</td>
                        <td class="py-4 px-4 text-right font-black text-purple-600">${{ number_format($totalDiscount, 2) }}</td>
                        <td class="py-4 px-4 text-right font-black text-blue-600">${{ number_format($netSales, 2) }}</td>
                        <td class="py-4 px-4 text-right font-black text-emerald-600">${{ number_format($grossProfit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
