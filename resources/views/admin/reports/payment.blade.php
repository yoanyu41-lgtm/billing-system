@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 mb-1">
                <i class="fas fa-wallet"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការបង់ប្រាក់' : 'Payment Report' }}
            </h1>
            <p class="text-sm font-semibold text-slate-500 mt-1 font-mono">
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </p>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Header & Action Controls -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-5 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-2xl text-xs font-bold border border-slate-200/60">
                <a href="{{ route('admin.reports.payment', ['filter' => 'daily']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}</a>
                <a href="{{ route('admin.reports.payment', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}</a>
                <a href="{{ route('admin.reports.payment', ['filter' => 'monthly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}</a>
                <a href="{{ route('admin.reports.payment', ['filter' => 'yearly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}</a>
            </div>

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

        <form method="GET" action="{{ route('admin.reports.payment') }}" class="flex flex-wrap items-center gap-3 pt-1 border-t border-slate-100">
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

    <!-- Cards: Method Breakdown (5 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Received (Blue) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-blue-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-hand-holding-dollar"></i> Total Received
            </div>
            <h3 class="text-xl font-black text-blue-600">${{ number_format($totalPaymentReceived, 2) }}</h3>
        </div>

        <!-- Cash (Green) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-emerald-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-money-bill-wave"></i> Cash
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['cash'], 2) }}</h3>
        </div>

        <!-- KHQR / ABA (Sky Blue) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-sky-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-qrcode"></i> KHQR / ABA
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['khqr'], 2) }}</h3>
        </div>

        <!-- Bank Transfer (Indigo) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-indigo-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-building-columns"></i> Bank Transfer
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['bank'], 2) }}</h3>
        </div>

        <!-- Other (Purple) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-purple-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-credit-card"></i> Other
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['other'], 2) }}</h3>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fas fa-receipt text-blue-500"></i> Payment Report Records
            </h3>
            <span class="text-sm font-bold text-blue-600">
                Total: ${{ number_format($totalPaymentReceived, 2) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Payment ID</th>
                        <th class="py-3.5 px-6">Invoice No.</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6 text-right">Payment Amount</th>
                        <th class="py-3.5 px-6">Payment Method</th>
                        <th class="py-3.5 px-6">Payment Date</th>
                        <th class="py-3.5 px-6 text-center">Installment No.</th>
                        <th class="py-3.5 px-6">Received By</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($paymentList as $p)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900 font-mono text-xs">
                            {{ $p->payment_id }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-700 font-mono text-xs">
                            {{ $p->invoice_no }}
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-800">
                            {{ $p->customer }}
                        </td>
                        <td class="py-4 px-6 text-right font-black text-emerald-600">
                            ${{ number_format($p->amount, 2) }}
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-700">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-bold text-slate-800">
                                <i class="fas fa-credit-card text-slate-400"></i>
                                {{ $p->method }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 font-mono">
                            {{ $p->date }}
                        </td>
                        <td class="py-4 px-6 text-center font-bold text-slate-700 text-xs">
                            {{ $p->installment_no }}
                        </td>
                        <td class="py-4 px-6 text-xs font-medium text-slate-600">
                            <i class="fas fa-user-shield text-blue-500 mr-1 text-[10px]"></i>{{ $p->received_by }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $p->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                            No Payment Data Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
