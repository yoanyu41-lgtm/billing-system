@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <!-- Header Section -->
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ដកហូត / យកទំនិញមកវិញ' : 'Product Return Report' }}
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

    <!-- Filter Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.return', ['filter' => 'daily']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}
            </a>
            <a href="{{ route('admin.reports.return', ['filter' => 'this_week']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ ($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}
            </a>
            <a href="{{ route('admin.reports.return', ['filter' => 'monthly']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
            </a>
            <a href="{{ route('admin.reports.return', ['filter' => 'yearly']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reports.return') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="filter" value="custom">

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកឈ្មោះ, លេខទូរស័ព្ទ, ទំនិញ...' : 'Search...' }}"
                       class="px-3.5 py-2 pl-8 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs w-48 sm:w-60">
                <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

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

            <button type="button" onclick="printReportDirect('{{ route('admin.reports.print', ['type' => 'return', 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search ?? '', 'filter' => $filter ?? '']) }}')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> {{ app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print' }}
            </button>
        </form>
    </div>

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Returned -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-500/20 text-lg">
                <i class="fas fa-undo-alt"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ចំនួនដកហូតសរុប' : 'TOTAL RETURNED' }}</span>
                <div class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalReturned) }} <span class="text-xs font-semibold text-slate-400">គ្រឿង</span></div>
            </div>
        </div>

        <!-- Total Contract Value -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 text-lg">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'តម្លៃកិច្ចសន្យាសរុប' : 'CONTRACT VALUE' }}</span>
                <div class="text-xl font-black text-slate-900 mt-0.5">${{ number_format($totalContractValue, 2) }}</div>
            </div>
        </div>

        <!-- Total Paid Before Return -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20 text-lg">
                <i class="fas fa-sack-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ប្រាក់បានទទួលមុនយកវិញ' : 'PAID BEFORE RETURN' }}</span>
                <div class="text-xl font-black text-emerald-600 mt-0.5">${{ number_format($totalPaidBeforeReturn, 2) }}</div>
            </div>
        </div>

        <!-- Voided Balance -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20 text-lg">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'សមតុល្យកាត់ចោល (ខាត)' : 'VOIDED BALANCE' }}</span>
                <div class="text-xl font-black text-rose-600 mt-0.5">${{ number_format($totalVoidedBalance, 2) }}</div>
            </div>
        </div>

    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800">
                {{ app()->getLocale() === 'km' ? 'តារាងលម្អិតការដកហូតទំនិញ' : 'Product Return List' }}
            </h2>
            <span class="text-xs text-slate-500 font-semibold">
                {{ app()->getLocale() === 'km' ? 'សរុប:' : 'Total:' }} {{ $returns->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 font-semibold">#</th>
                        <th class="py-3 px-4 font-semibold">{{ __('app.customer') }}</th>
                        <th class="py-3 px-4 font-semibold">{{ __('app.product') }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ app()->getLocale() === 'km' ? 'តម្លៃកិច្ចសន្យា' : 'Contract Price' }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ app()->getLocale() === 'km' ? 'បានបង់មុនយកវិញ' : 'Paid Prior' }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ app()->getLocale() === 'km' ? 'កាត់ចោល' : 'Voided' }}</th>
                        <th class="py-3 px-4 font-semibold text-center">{{ app()->getLocale() === 'km' ? 'ថ្ងៃយកមកវិញ' : 'Return Date' }}</th>
                        <th class="py-3 px-4 font-semibold text-center">{{ app()->getLocale() === 'km' ? 'បុគ្គលិកទទួល' : 'Staff' }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $idx => $r)
                    @php
                        $paidPrior = (float)$r->down_payment + (float)$r->payments->sum('amount');
                        $voided = max((float)$r->total_price - $paidPrior, 0);
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-500">
                            #INS-{{ str_pad($r->id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900">{{ $r->customer?->name ?? 'N/A' }}</div>
                            <div class="text-[11px] text-slate-400">{{ $r->customer?->phone ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-900 max-w-[200px] truncate" title="{{ $r->product?->name }}">
                                {{ $r->product?->name ?? 'N/A' }}
                            </div>
                            @if($r->product?->code)
                                <span class="text-[10px] text-indigo-600 font-mono">{{ $r->product->code }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                            ${{ number_format($r->total_price, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-emerald-600">
                            ${{ number_format($paidPrior, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-rose-600">
                            ${{ number_format($voided, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-medium text-slate-700">
                            {{ $r->returned_at ? \Carbon\Carbon::parse($r->returned_at)->format('d/m/Y') : ($r->updated_at ? \Carbon\Carbon::parse($r->updated_at)->format('d/m/Y') : '-') }}
                        </td>
                        <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                            {{ $r->returnedByUser?->name ?? 'Admin' }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('installments.return-certificate', $r) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition">
                                <i class="fas fa-print"></i>
                                <span>{{ app()->getLocale() === 'km' ? 'លិខិត' : 'Cert' }}</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="fas fa-undo-alt text-2xl block mb-2 opacity-40"></i>
                            <span>{{ app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យដកហូតទំនិញក្នុងចន្លោះពេលនេះឡើយ' : 'No returned products found for this period' }}</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
