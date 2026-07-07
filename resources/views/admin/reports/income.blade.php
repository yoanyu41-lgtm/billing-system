@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.income_report') ?? 'Income Report' }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                Income breakdown from 
                {{ \Carbon\Carbon::parse($start)->format('d M Y') }} to 
                {{ \Carbon\Carbon::parse($end)->format('d M Y') }}.
            </p>
        </div>
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <div class="flex items-center gap-2">
                <input type="date" name="start" value="{{ \Carbon\Carbon::parse($start)->toDateString() }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-400 text-sm">to</span>
                <input type="date" name="end" value="{{ \Carbon\Carbon::parse($end)->toDateString() }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg border-0 cursor-pointer">
                {{ __('app.filter') ?? 'Filter' }}
            </button>
            <a href="{{ route('admin.reports.income') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg text-center" style="text-decoration: none;">
                Reset
            </a>
        </form>
    </div>

    {{-- Calculations --}}
    @php
        $grandTotal = $payments->sum('total');
        $daysCount = $payments->count();
        $averageDaily = $daysCount > 0 ? $grandTotal / $daysCount : 0;
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 bg-blue-50 p-5">
            <p class="text-xs text-blue-600 uppercase tracking-wide">Total Income</p>
            <p class="text-3xl font-extrabold text-blue-700 mt-1">${{ number_format($grandTotal, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Active Collection Days</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $daysCount }} Days</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Average Daily Income</p>
            <p class="text-3xl font-bold text-emerald-600 mt-1">${{ number_format($averageDaily, 2) }}</p>
        </div>
    </div>

    {{-- Income Breakdown Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Daily Income Collection Breakdown</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Income Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-3.5 text-gray-900 font-medium">
                                {{ \Carbon\Carbon::parse($payment->date)->format('d M Y (D)') }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-bold text-emerald-600">
                                ${{ number_format($payment->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-gray-400">No income collections recorded in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
