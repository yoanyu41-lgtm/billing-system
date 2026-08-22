@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការទូទាត់' : 'Payment Report' }}
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
            <a href="{{ route('admin.reports.payment', ['filter' => 'daily']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}
            </a>
            <a href="{{ route('admin.reports.payment', ['filter' => 'this_week']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ ($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}
            </a>
            <a href="{{ route('admin.reports.payment', ['filter' => 'monthly']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
            </a>
            <a href="{{ route('admin.reports.payment', ['filter' => 'yearly']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reports.payment') }}" class="flex flex-wrap items-center gap-2">
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

            <button type="button" onclick="printReportDirect('{{ route('admin.reports.print', ['type' => 'payment', 'start_date' => $startDate, 'end_date' => $endDate, 'filter' => $filter ?? '']) }}')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> {{ app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print' }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 text-lg">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ប្រាក់ទទួលបានសរុប' : 'TOTAL RECEIVED' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format($totalPaymentReceived, 2) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20 text-lg">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'សាច់ប្រាក់សុទ្ធ' : 'CASH' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format($byMethod['cash'], 2) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-purple-500/20 text-lg">
                <i class="fas fa-qrcode"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">KHQR / ABA</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format($byMethod['khqr'], 2) }}</div>
            </div>
        </div>

        <!-- Bank Transfer -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-teal-500/20 text-lg">
                <i class="fas fa-building-columns"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ផ្ទេរតាមធនាគារ' : 'BANK TRANSFER' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format($byMethod['bank'], 2) }}</div>
            </div>
        </div>

        <!-- Other -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20 text-lg">
                <i class="fas fa-ellipsis"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ផ្សេងៗ' : 'OTHER' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format($byMethod['other'], 2) }}</div>
            </div>
        </div>

    </div>

    <!-- Data Table Section -->
    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'តារាងកំណត់ត្រាការទូទាត់' : 'Payment Records' }}
            </h2>
            <span class="text-xs font-medium text-slate-500">
                {{ app()->getLocale() === 'km' ? 'សរុប:' : 'Total:' }} <strong class="text-slate-800 font-bold">{{ count($paymentList) }}</strong> {{ app()->getLocale() === 'km' ? 'ប្រតិបត្តិការ' : 'records' }}
            </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold text-xs tracking-wide">
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'លេខកូដទូទាត់' : 'Payment ID' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'លេខវិក្កយបត្រ' : 'Invoice #' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customer' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ចំនួនប្រាក់' : 'Amount' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'វិធីសាស្ត្រទូទាត់' : 'Payment Method' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ដំណាក់កាលទី' : 'Installment #' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'អ្នកទទួល' : 'Received By' }}</th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ស្ថានភាព' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($paymentList as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $p->payment_id }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $p->invoice_no }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $p->customer }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                ${{ number_format($p->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $mLower = strtolower($p->method ?? '');
                                    $methodLabel = match(true) {
                                        str_contains($mLower, 'cash') => (app()->getLocale() === 'km' ? 'សាច់ប្រាក់' : 'Cash'),
                                        str_contains($mLower, 'qr') || str_contains($mLower, 'khqr') || str_contains($mLower, 'aba') => (app()->getLocale() === 'km' ? 'ស្កេន QR' : 'QR Code'),
                                        str_contains($mLower, 'credit') || str_contains($mLower, 'card') => (app()->getLocale() === 'km' ? 'កាតឥណទាន' : 'Credit Card'),
                                        str_contains($mLower, 'bank') || str_contains($mLower, 'wing') || str_contains($mLower, 'acleda') || str_contains($mLower, 'transfer') => (app()->getLocale() === 'km' ? 'ផ្ទេរធនាគារ' : 'Bank Transfer'),
                                        default => $p->method
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">
                                    {{ $methodLabel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $p->date }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-800">
                                {{ $p->installment_no }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                {{ $p->received_by }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $sLower = strtolower($p->status ?? '');
                                    $statusLabel = match(true) {
                                        str_contains($sLower, 'paid') || str_contains($sLower, 'approved') || str_contains($sLower, 'success') => (app()->getLocale() === 'km' ? 'បានបង់' : 'Paid'),
                                        str_contains($sLower, 'pending') => (app()->getLocale() === 'km' ? 'រង់ចាំ' : 'Pending'),
                                        str_contains($sLower, 'reject') => (app()->getLocale() === 'km' ? 'បដិសេធ' : 'Rejected'),
                                        default => $p->status
                                    };
                                    $badgeClass = match(true) {
                                        str_contains($sLower, 'paid') || str_contains($sLower, 'approved') || str_contains($sLower, 'success') => 'bg-[#dcfce7] text-[#16a34a]',
                                        str_contains($sLower, 'pending') => 'bg-amber-50 text-amber-600',
                                        str_contains($sLower, 'reject') => 'bg-rose-50 text-rose-600',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                                {{ app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យការបង់ប្រាក់ក្នុងចន្លោះពេលនេះទេ' : 'No payment records found for this period.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($paymentList) > 0)
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3.5 px-4 text-right uppercase text-[11px] text-slate-500 font-bold">
                                {{ app()->getLocale() === 'km' ? 'សរុបរួម:' : 'Grand Total:' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                ${{ number_format($totalPaymentReceived, 2) }}
                            </td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
