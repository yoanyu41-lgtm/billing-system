@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 mb-1">
                <i class="fas fa-wallet"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ __('app.payment_report') }}
            </h1>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Header & Action Controls (Matching Image 1) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition border-0 cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-print"></i> {{ __('app.print') }}
                </button>
            </div>
        </div>

        <!-- Filter Bar (Image 1 Style) -->
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <form method="GET" action="{{ route('admin.reports.payment') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="text-sm font-medium text-slate-400 px-1">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-sm border-0 cursor-pointer flex items-center justify-center">
                    {{ __('app.filter') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Cards: Method Breakdown (5 Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Cash (Green) -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-emerald-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-money-bill-wave"></i> Cash
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['cash'], 2) }}</h3>
        </div>

        <!-- ABA (Sky Blue) -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-sky-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-university"></i> ABA Bank
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['aba'], 2) }}</h3>
        </div>

        <!-- ACLEDA (Indigo) -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-indigo-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-building-columns"></i> ACLEDA Bank
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['acleda'], 2) }}</h3>
        </div>

        <!-- Wing (Lime Green) -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-lime-700 text-xs font-bold uppercase mb-1">
                <i class="fas fa-wallet"></i> Wing Bank
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['wing'], 2) }}</h3>
        </div>

        <!-- Other (Purple) -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 text-purple-600 text-xs font-bold uppercase mb-1">
                <i class="fas fa-qrcode"></i> Other / Cards
            </div>
            <h3 class="text-xl font-black text-slate-900">${{ number_format($byMethod['other'], 2) }}</h3>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fas fa-receipt text-blue-500"></i> {{ __('app.payment_history') }}
            </h3>
            <span class="text-sm font-bold text-blue-600">
                {{ __('app.total') }}: ${{ number_format($totalAmount, 2) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">{{ __('app.receipt_no') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.customer') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.payment_date') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.payment_method') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('app.amount') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.received_by') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($payments as $p)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900">
                            #PAY-{{ $p->id }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-700">
                            {{ $p->installment->customer->name ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-600 font-mono">
                            {{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-700">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-bold text-slate-800">
                                <i class="fas fa-credit-card text-slate-400"></i>
                                {{ $p->paymentMethod->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right font-black text-slate-900">
                            ${{ number_format($p->amount + $p->penalty_amount, 2) }}
                        </td>
                        <td class="py-4 px-6 text-xs font-medium text-slate-600">
                            {{ $p->user->name ?? 'System' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                            {{ __('app.no_data') ?? 'គ្មានទិន្នន័យ' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
