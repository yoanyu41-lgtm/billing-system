@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 mb-1">
                <i class="fas fa-users"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍អតិថិជន' : 'Customer Report' }}
            </h1>
            <p class="text-sm font-semibold text-slate-500 mt-1 font-mono">
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </p>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Header Controls & Search Filter -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 no-print">
        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-2xl text-xs font-bold border border-slate-200/60">
                <a href="{{ route('admin.reports.customer', ['filter' => 'daily']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}</a>
                <a href="{{ route('admin.reports.customer', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}</a>
                <a href="{{ route('admin.reports.customer', ['filter' => 'monthly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}</a>
                <a href="{{ route('admin.reports.customer', ['filter' => 'yearly']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}</a>
            </div>

            <form method="GET" action="{{ route('admin.reports.customer') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Customer Name / Phone / Invoice / Contract..." class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-700 w-72 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <input type="hidden" name="filter" value="custom">
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="text-xs font-medium text-slate-400">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-xs border-0 cursor-pointer flex items-center justify-center">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
            </form>

            <button onclick="window.print()" class="ml-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border-0 cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> Print
            </button>
        </div>
    </div>

    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 border border-blue-100">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Total Customers</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalCustomers) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 border border-emerald-100">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">New Customers</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($newCustomers) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-100">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Active Borrowers</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($activeCustomers) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl shrink-0 border border-slate-200">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Completed Customers</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($completedCustomers) }}</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Phone</th>
                        <th class="py-3.5 px-6 text-center">Contracts</th>
                        <th class="py-3.5 px-6 text-right">Total Purchase</th>
                        <th class="py-3.5 px-6 text-right">Paid</th>
                        <th class="py-3.5 px-6 text-right">Outstanding</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($customerList as $c)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900">
                            {{ $c->name }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-600 font-mono text-xs">
                            {{ $c->phone }}
                        </td>
                        <td class="py-4 px-6 text-center font-bold text-slate-800">
                            {{ $c->contracts }}
                        </td>
                        <td class="py-4 px-6 text-right font-bold text-slate-900">
                            ${{ number_format($c->total_purchase, 2) }}
                        </td>
                        <td class="py-4 px-6 text-right font-semibold text-emerald-600">
                            ${{ number_format($c->paid, 2) }}
                        </td>
                        <td class="py-4 px-6 text-right font-black text-rose-600">
                            ${{ number_format($c->outstanding, 2) }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($c->status === 'Active')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                    Active
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Completed
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                            No Customer Data Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
