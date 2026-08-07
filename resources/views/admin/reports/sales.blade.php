@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Reports Section Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-1 flex items-center gap-2">
                <i class="fas fa-chart-line"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការលក់' : 'Sales Report' }}
            </h1>
            <p class="text-sm font-semibold text-slate-500 mt-1 font-mono">
                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </p>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Filter & Action Controls Box -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Quick Filter Pills: Today, Week, Month, Year -->
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-2xl text-xs font-bold border border-slate-200/60">
                <a href="{{ route('admin.reports.sales', ['filter' => 'today']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'today' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Today</a>
                <a href="{{ route('admin.reports.sales', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">This Week</a>
                <a href="{{ route('admin.reports.sales', ['filter' => 'this_month']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_month' || empty($filter) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">This Month</a>
                <a href="{{ route('admin.reports.sales', ['filter' => 'this_year']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_year' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">This Year</a>
            </div>

            <!-- Export Buttons: Print, Excel, PDF -->
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border-0 cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-print text-slate-500"></i> Print
                </button>
                <a href="{{ route('admin.reports.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition border border-emerald-200/60 no-underline flex items-center gap-1.5">
                    <i class="fas fa-file-excel text-emerald-600"></i> Excel
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'monthly', 'month' => now()->month, 'year' => now()->year]) }}" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition border border-rose-200/60 no-underline flex items-center gap-1.5">
                    <i class="fas fa-file-pdf text-rose-600"></i> PDF
                </a>
            </div>
        </div>

        <!-- Custom Date Inputs + Search -->
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="flex flex-wrap items-center gap-3 pt-1 border-t border-slate-100">
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

    <!-- Summary Cards (4 Colors: Blue, Green, Red, Orange) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 🟦 Sales (Blue) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 border border-blue-100">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Total Sales</span>
                <span class="text-2xl font-black text-blue-600">${{ number_format($grandTotal, 2) }}</span>
            </div>
        </div>

        <!-- 🟩 Profit (Green) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 border border-emerald-100">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Profit</span>
                <span class="text-2xl font-black text-emerald-600">${{ number_format($totalSubtotal * 0.25, 2) }}</span>
            </div>
        </div>

        <!-- 🟥 Expense (Red) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-100">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Expense</span>
                <span class="text-2xl font-black text-rose-600">$0.00</span>
            </div>
        </div>

        <!-- 🟧 Net Income (Orange) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-100">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Net Income</span>
                <span class="text-2xl font-black text-amber-600">${{ number_format($grandTotal, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Invoice Table (Standard Headers + Action Column) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fas fa-receipt text-blue-500"></i> Sales Transactions
            </h3>
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-600 rounded-full">
                {{ number_format($totalSalesCount) }} Invoices
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Invoice</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6 text-right">Total</th>
                        <th class="py-3.5 px-6">Payment</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900">
                            {{ $sale->invoice_no ?? ('#SALE-' . $sale->id) }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-700">
                            {{ $sale->customer_name ?: 'Walk-in Customer' }}
                        </td>
                        <td class="py-4 px-6 text-right font-black text-slate-900">
                            ${{ number_format($sale->total, 2) }}
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-700">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-bold text-slate-700">
                                <i class="fas fa-money-bill-wave text-slate-400"></i> Cash
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Completed
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 font-mono">
                            {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button onclick="alert('Invoice: {{ $sale->invoice_no ?? $sale->id }}\nCustomer: {{ $sale->customer_name }}\nTotal: ${{ number_format($sale->total, 2) }}')" class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold rounded-lg text-xs transition border-0 cursor-pointer">
                                View Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                            No Sales Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
