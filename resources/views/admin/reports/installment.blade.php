@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 mb-1">
                <i class="fas fa-file-invoice-dollar"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ __('app.installment_report') }}
            </h1>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Header Controls & Search Filter (Standard Component) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition border-0 cursor-pointer">
                    <i class="fas fa-print"></i> {{ __('app.print') }}
                </button>
            </div>
        </div>

        <!-- Filter Bar (Image 1 Standard Component) -->
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <div class="inline-flex items-center p-1 bg-slate-100/90 rounded-2xl text-sm font-semibold border border-slate-200/50">
                <a href="{{ route('admin.reports.installment', ['filter' => 'today']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'today' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ __('app.today') }}</a>
                <a href="{{ route('admin.reports.installment', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ __('app.this_week') }}</a>
                <a href="{{ route('admin.reports.installment', ['filter' => 'this_month']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_month' || empty($filter) ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ __('app.this_month') }}</a>
            </div>

            <form method="GET" action="{{ route('admin.reports.installment') }}" class="flex flex-wrap items-center gap-2">
                <select name="status" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- {{ __('app.filter_all') }} --</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>{{ __('app.active_contracts') }}</option>
                    <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>{{ __('app.completed') }}</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>{{ __('app.pending_approval') }}</option>
                </select>
                <input type="hidden" name="filter" value="custom">
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="text-sm font-medium text-slate-400 px-1">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-sm border-0 cursor-pointer flex items-center justify-center">
                    {{ __('app.filter') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Cards (4 Standard Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 border border-blue-100">
                <i class="fas fa-file-contract"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.active_contracts') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($activeCount) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 border border-emerald-100">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.completed') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($completedCount) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-100">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.overdue_contracts') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($overdueCount) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-100">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.pending_approval') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($pendingCount) }}</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">{{ __('app.invoice_no') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.customer') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.product') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('app.total_price') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('app.remaining_balance') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.next_due_date') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($installments as $inst)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900">
                            #INS-{{ $inst->id }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-700">
                            {{ $inst->customer->name ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-600">
                            {{ $inst->product->name ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6 text-right font-semibold text-slate-800">
                            ${{ number_format($inst->total_price, 2) }}
                        </td>
                        <td class="py-4 px-6 text-right font-black text-rose-600">
                            ${{ number_format($inst->remaining_balance, 2) }}
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-600 font-mono">
                            {{ $inst->next_due_date ? \Carbon\Carbon::parse($inst->next_due_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-6">
                            @php
                                $badgeCls = match($inst->status) {
                                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'active' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    default => 'bg-amber-50 text-amber-700 border-amber-100'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeCls }}">
                                {{ ucfirst($inst->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
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
