@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Reports Section Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-1 flex items-center gap-2">
                <i class="fas fa-chart-bar"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការលក់' : 'Sales Report' }}
            </h1>
            <p class="text-sm font-semibold text-slate-500 mt-1 font-mono">
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </p>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Filter & Action Controls Box -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-5 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Quick Filter Pills: Daily, Week, Monthly, Yearly -->
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-2xl text-xs font-bold border border-slate-200/60">
                <a href="{{ route('admin.reports.sales', ['filter' => 'daily']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}</a>
                <a href="{{ route('admin.reports.sales', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}</a>
                <a href="{{ route('admin.reports.sales', ['filter' => 'monthly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}</a>
                <a href="{{ route('admin.reports.sales', ['filter' => 'yearly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}</a>
            </div>

            <!-- Export Buttons: Print, Excel, CSV, PDF -->
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border-0 cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-print text-slate-500"></i> Print
                </button>
                <a href="{{ route('admin.reports.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition border border-emerald-200/60 no-underline flex items-center gap-1.5">
                    <i class="fas fa-file-excel text-emerald-600"></i> Excel
                </a>
                <a href="{{ route('admin.reports.excel', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'csv']) }}" class="px-4 py-2 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold rounded-xl text-xs transition border border-teal-200/60 no-underline flex items-center gap-1.5">
                    <i class="fas fa-file-csv text-teal-600"></i> CSV
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'daily', 'date' => $startDate]) }}" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition border border-rose-200/60 no-underline flex items-center gap-1.5">
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

    <!-- 5 Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Sales (Blue) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0 border border-blue-100">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Total Sales</span>
                <span class="text-xl font-black text-blue-600">${{ number_format($totalSales, 2) }}</span>
            </div>
        </div>

        <!-- Number of Invoices (Cyan) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg shrink-0 border border-cyan-100">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">No. of Invoices</span>
                <span class="text-xl font-black text-slate-900">{{ number_format($numberOfInvoices) }}</span>
            </div>
        </div>

        <!-- Total Discount (Purple) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0 border border-purple-100">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Total Discount</span>
                <span class="text-xl font-black text-purple-600">${{ number_format($totalDiscount, 2) }}</span>
            </div>
        </div>

        <!-- Direct Sales (Emerald) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0 border border-emerald-100">
                <i class="fas fa-cash-register"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Direct Sales</span>
                <span class="text-xl font-black text-emerald-600">${{ number_format($directSalesTotal, 2) }}</span>
            </div>
        </div>

        <!-- Installment Sales (Amber) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0 border border-amber-100">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Installments</span>
                <span class="text-xl font-black text-amber-600">${{ number_format($installmentSalesTotal, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Sales Table with exact required fields -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fas fa-list-check text-blue-500"></i> Sales Report Transactions
            </h3>
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-600 rounded-full">
                {{ number_format($numberOfInvoices) }} Items
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-4">Invoice No.</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Product</th>
                        <th class="py-3.5 px-4 text-center">Qty</th>
                        <th class="py-3.5 px-4 text-right">Unit Price</th>
                        <th class="py-3.5 px-4 text-right">Discount</th>
                        <th class="py-3.5 px-4 text-right">Total Sale</th>
                        <th class="py-3.5 px-4 text-center">Sale Type</th>
                        <th class="py-3.5 px-4">Cashier</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($salesList as $item)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-900 font-mono text-xs">
                            {{ $item->invoice_no }}
                        </td>
                        <td class="py-3.5 px-4 text-xs font-mono text-slate-500">
                            {{ $item->date }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-800">
                            {{ $item->customer }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            {{ $item->product }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                            {{ $item->quantity }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-semibold text-slate-700">
                            ${{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-semibold text-slate-500">
                            ${{ number_format($item->discount, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-blue-600">
                            ${{ number_format($item->total, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->sale_type === 'Direct')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Direct
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                    Installment
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-xs font-medium text-slate-600">
                            <i class="fas fa-user-shield text-blue-500 mr-1 text-[10px]"></i>{{ $item->cashier }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                            No Sales Data Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
