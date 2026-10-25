@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <!-- Header Section -->
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍បង់ផ្តាច់ (Pay-Off)' : 'Pay-Off & Settlement Report' }}
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
            <a href="{{ route('admin.reports.settlement', ['filter' => 'daily']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}
            </a>
            <a href="{{ route('admin.reports.settlement', ['filter' => 'this_week']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ ($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}
            </a>
            <a href="{{ route('admin.reports.settlement', ['filter' => 'monthly']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
            </a>
            <a href="{{ route('admin.reports.settlement', ['filter' => 'yearly']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reports.settlement') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="filter" value="custom">

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកឈ្មោះ, លេខទូរស័ព្ទ...' : 'Search customer...' }}"
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

            <button type="button" onclick="printReportDirect('{{ route('admin.reports.print', ['type' => 'settlement', 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search ?? '', 'filter' => $filter ?? '']) }}')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> {{ app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print' }}
            </button>
        </form>
    </div>

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <!-- Total Settlements -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20 text-lg">
                <i class="fas fa-certificate"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ចំនួនបង់ផ្តាច់សរុប' : 'SETTLED CONTRACTS' }}</span>
                <div class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalSettlements) }}</div>
            </div>
        </div>

        <!-- Total Collected via Payoff -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 text-lg">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ទឹកប្រាក់ប្រមូលបានពីបង់ផ្តាច់' : 'COLLECTED AMOUNT' }}</span>
                <div class="text-xl font-black text-emerald-600 mt-0.5">${{ number_format($totalSettledAmount, 2) }}</div>
            </div>
        </div>

        <!-- Late Fee Collected -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20 text-lg">
                <i class="fas fa-sack-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ប្រាក់ពិន័យប្រមូលបាន' : 'LATE FEES COLLECTED' }}</span>
                <div class="text-xl font-black text-amber-600 mt-0.5">${{ number_format($totalPenaltyCollected, 2) }}</div>
            </div>
        </div>

    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800">
                {{ app()->getLocale() === 'km' ? 'បញ្ជីប្រតិបត្តិការបង់ផ្តាច់' : 'Settlement Transaction List' }}
            </h2>
            <span class="text-xs text-slate-500 font-semibold">
                {{ app()->getLocale() === 'km' ? 'សរុប:' : 'Total:' }} {{ $settlements->count() ?: $completedInstallments->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 font-semibold">#</th>
                        <th class="py-3 px-4 font-semibold">{{ __('app.customer') }}</th>
                        <th class="py-3 px-4 font-semibold">{{ __('app.product') }}</th>
                        <th class="py-3 px-4 font-semibold text-center">{{ app()->getLocale() === 'km' ? 'ថ្ងៃបង់ផ្តាច់' : 'Settled Date' }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ app()->getLocale() === 'km' ? 'ប្រាក់ដើមបង់ផ្តាច់' : 'Principal Paid' }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ app()->getLocale() === 'km' ? 'ប្រាក់ពិន័យ' : 'Late Fee' }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ app()->getLocale() === 'km' ? 'សរុបបង់ផ្តាច់' : 'Total Paid' }}</th>
                        <th class="py-3 px-4 font-semibold text-center">{{ __('app.status') }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($settlements as $s)
                    @php
                        $customerName = $s->customer?->name ?? ($s->installment?->customer?->name ?? 'N/A');
                        $customerPhone = $s->customer?->phone ?? ($s->installment?->customer?->phone ?? '-');
                        $productName = $s->product?->name ?? ($s->installment?->product?->name ?? 'N/A');
                        $instModel = $s->installment ?? \App\Models\Installment::find($s->installment_id);
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-500">
                            #INS-{{ str_pad($s->installment_id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900">{{ $customerName }}</div>
                            <div class="text-[11px] text-slate-400">{{ $customerPhone }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-900 max-w-[200px] truncate" title="{{ $productName }}">
                                {{ $productName }}
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-medium text-slate-700">
                            {{ $s->payment_date }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                            ${{ number_format((float)($s->principal ?? 0), 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-amber-600">
                            ${{ number_format((float)($s->penalty_amount ?? 0), 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-emerald-600">
                            ${{ number_format((float)($s->total_amount ?? 0), 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                {{ app()->getLocale() === 'km' ? 'បានបង់ផ្តាច់' : 'Settled' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            @if($instModel)
                            <a href="{{ route('installments.clearance', $instModel) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-lg text-xs font-bold transition">
                                <i class="fas fa-print"></i>
                                <span>{{ app()->getLocale() === 'km' ? 'លិខិតបង់ផ្តាច់' : 'Clearance' }}</span>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="fas fa-certificate text-2xl block mb-2 opacity-40"></i>
                            <span>{{ app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យបង់ផ្តាច់ក្នុងចន្លោះពេលនេះឡើយ' : 'No settlement records found' }}</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
