@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ប្រាក់ចំណេញ' : 'Profit / Income Report' }}
            </h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                {{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទបង្ហាញ:' : 'Reporting Period:' }} 
                <span class="font-semibold text-slate-700">
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </span>
            </p>
        </div>
        
        @include('admin.reports._nav')
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.profit', ['filter' => 'daily']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}
            </a>
            <a href="{{ route('admin.reports.profit', ['filter' => 'this_week']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ ($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}
            </a>
            <a href="{{ route('admin.reports.profit', ['filter' => 'monthly']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
            </a>
            <a href="{{ route('admin.reports.profit', ['filter' => 'yearly']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reports.profit') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="filter" value="custom">
            
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span>{{ app()->getLocale() === 'km' ? 'ចាប់ពី:' : 'From' }}</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span>{{ app()->getLocale() === 'km' ? 'ដល់:' : 'To' }}</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <button type="submit" class="px-4 py-2 bg-[#0b1f3a] hover:bg-[#07162b] text-white font-bold rounded-xl text-xs transition shadow-sm border-0 cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-search text-[11px]"></i> {{ app()->getLocale() === 'km' ? 'ស្វែងរក' : 'Search' }}
            </button>

            <button type="button" onclick="printReportDirect('{{ route('admin.reports.print', ['type' => 'profit', 'start_date' => $startDate, 'end_date' => $endDate, 'filter' => $filter ?? '']) }}')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> {{ app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print' }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 text-lg">
                <i class="fas fa-cart-shopping"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'តម្លៃលក់សរុប' : 'TOTAL SALES' }}</span>
                <div class="text-base font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format($totalSelling, 2) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-purple-500/20 text-lg">
                <i class="fas fa-boxes-packing"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ថ្លៃដើមទំនិញ' : 'COST OF GOODS' }}</span>
                <div class="text-base font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format($totalCost, 2) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20 text-lg">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'បញ្ចុះតម្លៃ' : 'DISCOUNT' }}</span>
                <div class="text-base font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format($totalDiscount, 2) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-teal-500/20 text-lg">
                <i class="fas fa-money-bill-trend-up"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ចំណេញដុល' : 'GROSS PROFIT' }}</span>
                <div class="text-base font-black text-emerald-600 mt-0.5 tracking-tight">${{ number_format($grossProfit, 2) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-500/20 text-lg">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ចំណាយសរុប' : 'TOTAL EXPENSE' }}</span>
                <div class="text-base font-black text-rose-600 mt-0.5 tracking-tight">${{ number_format($totalExpenses, 2) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/20 text-lg">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ចំណេញសុទ្ធ' : 'NET INCOME' }}</span>
                <div class="text-base font-black {{ $netIncome >= 0 ? 'text-slate-900' : 'text-rose-600' }} mt-0.5 tracking-tight">
                    ${{ number_format($netIncome, 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'តារាងសៀវភៅគណនេយ្យប្រាក់ចំណេញ' : 'Profit Ledger' }}
            </h2>
            <span class="text-xs font-medium text-slate-500">
                {{ app()->getLocale() === 'km' ? 'សរុប:' : 'Total:' }} <strong class="text-slate-800 font-bold">{{ count($ledger) }}</strong> {{ app()->getLocale() === 'km' ? 'ប្រតិបត្តិការ' : 'records' }}
            </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold text-xs tracking-wide">
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'លេខយោង' : 'Reference Number' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ប្រភេទ' : 'Type' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customer' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'តម្លៃលក់' : 'Selling Price' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ថ្លៃដើម' : 'Cost Price' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'បញ្ចុះតម្លៃ' : 'Discount' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ការលក់សុទ្ធ' : 'Net Sales' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ចំណេញដុល' : 'Gross Profit' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($ledger as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-slate-600 font-mono font-medium">
                                {{ $item->date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 font-mono">
                                {{ $item->ref_no }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->type === 'Direct Sale')
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#dcfce7] text-[#16a34a]">
                                        {{ app()->getLocale() === 'km' ? 'លក់ដាច់' : 'Direct' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#e0f2fe] text-[#0284c7]">
                                        {{ app()->getLocale() === 'km' ? 'បង់រំលស់' : 'Installment' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $item->customer }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                ${{ number_format($item->selling_price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-600">
                                ${{ number_format($item->cost_price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-500">
                                ${{ number_format($item->discount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
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
                                {{ app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យគណនេយ្យក្នុងចន្លោះពេលនេះទេ' : 'No financial ledger records found.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($ledger) > 0)
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900">
                        <tr>
                            <td colspan="4" class="py-3.5 px-4 text-right uppercase text-[11px] text-slate-500 font-bold">
                                {{ app()->getLocale() === 'km' ? 'សរុបរួម:' : 'Grand Total:' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">${{ number_format($totalSelling, 2) }}</td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-600">${{ number_format($totalCost, 2) }}</td>
                            <td class="py-3.5 px-4 text-semibold text-slate-500">${{ number_format($totalDiscount, 2) }}</td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">${{ number_format($netSales, 2) }}</td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-600 text-sm">${{ number_format($grossProfit, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
