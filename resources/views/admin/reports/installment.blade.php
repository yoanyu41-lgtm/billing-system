@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 mb-1">
                <i class="fas fa-file-invoice-dollar"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការបង់រំលស់' : 'Installment Report' }}
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
                <a href="{{ route('admin.reports.installment', ['filter' => 'daily']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}</a>
                <a href="{{ route('admin.reports.installment', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}</a>
                <a href="{{ route('admin.reports.installment', ['filter' => 'monthly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}</a>
                <a href="{{ route('admin.reports.installment', ['filter' => 'yearly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}</a>
            </div>

            <form method="GET" action="{{ route('admin.reports.installment') }}" class="flex flex-wrap items-center gap-2">
                <select name="status" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Filter All Status --</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
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

    <!-- 5 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Active Installments -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0 border border-blue-100">
                <i class="fas fa-file-contract"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Active Contracts</span>
                <span class="text-xl font-black text-slate-900">{{ number_format($activeCount) }}</span>
            </div>
        </div>

        <!-- Completed Installments -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0 border border-emerald-100">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Completed</span>
                <span class="text-xl font-black text-slate-900">{{ number_format($completedCount) }}</span>
            </div>
        </div>

        <!-- Overdue Installments -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0 border border-rose-100">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Overdue</span>
                <span class="text-xl font-black text-rose-600">{{ number_format($overdueCount) }}</span>
            </div>
        </div>

        <!-- Total Outstanding -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0 border border-amber-100">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Total Outstanding</span>
                <span class="text-xl font-black text-amber-600">${{ number_format($totalOutstanding, 2) }}</span>
            </div>
        </div>

        <!-- Total Collected -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0 border border-purple-100">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Total Collected</span>
                <span class="text-xl font-black text-purple-600">${{ number_format($totalCollected, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-4">Contract No.</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Product</th>
                        <th class="py-3.5 px-4 text-right">Total Amount</th>
                        <th class="py-3.5 px-4 text-right">Down Payment</th>
                        <th class="py-3.5 px-4 text-right">Remaining</th>
                        <th class="py-3.5 px-4 text-center">Duration</th>
                        <th class="py-3.5 px-4 text-right">Monthly Pay</th>
                        <th class="py-3.5 px-4 text-center">Paid Months</th>
                        <th class="py-3.5 px-4 text-center">Rem. Months</th>
                        <th class="py-3.5 px-4 text-center">Next Due Date</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($installmentList as $inst)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-900 font-mono text-xs">
                            {{ $inst->contract_no }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-800">
                            {{ $inst->customer }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            {{ $inst->product }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-semibold text-slate-900">
                            ${{ number_format($inst->total_amount, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-semibold text-slate-600">
                            ${{ number_format($inst->down_payment, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-rose-600">
                            ${{ number_format($inst->remaining, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-semibold text-slate-700 text-xs">
                            {{ $inst->duration }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-semibold text-slate-800">
                            ${{ number_format($inst->monthly_payment, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">
                            {{ $inst->paid_months }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-amber-600">
                            {{ $inst->remaining_months }}
                        </td>
                        <td class="py-3.5 px-4 text-center text-xs text-slate-500 font-mono">
                            {{ $inst->next_due_date }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $badgeCls = match($inst->status) {
                                    'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Overdue' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'Active' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    default => 'bg-amber-50 text-amber-700 border-amber-100'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeCls }}">
                                {{ $inst->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                            No Installment Data Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
