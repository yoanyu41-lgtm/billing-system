@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-1 flex items-center gap-2">
                <i class="fas fa-chart-line"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការលក់ប្រចាំឆ្នាំ' : 'Yearly Sales Report' }}
            </h1>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Header Controls & Search Filter (Standard Component) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 mb-1">
                    <i class="fas fa-calendar-check"></i> {{ __('app.reports') }}
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                    {{ __('app.yearly_report') }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Year {{ $year }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition border-0 cursor-pointer">
                    <i class="fas fa-print"></i> {{ __('app.print') }}
                </button>
            </div>
        </div>

        <!-- Filter Bar (Image 1 Standard Component) -->
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <form method="GET" action="{{ route('admin.reports.yearly') }}" class="flex flex-wrap items-center gap-2">
                <select name="year" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-sm border-0 cursor-pointer flex items-center justify-center">
                    {{ __('app.filter') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 border border-blue-100">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.annual_revenue') }}</span>
                <span class="text-2xl font-black text-slate-900">${{ number_format($annualRevenue, 2) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-100">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.annual_expenses') }}</span>
                <span class="text-2xl font-black text-slate-900">${{ number_format($annualExpense, 2) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 border border-emerald-100">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.annual_net_profit') }}</span>
                <span class="text-2xl font-black text-slate-900">${{ number_format($annualProfit, 2) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-100">
                <i class="fas fa-arrow-trend-up"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.growth_rate') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ $growthRate }}%</span>
            </div>
        </div>
    </div>

    <!-- Chart: Annual Trend -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-chart-line text-blue-500"></i> {{ __('app.yearly_report') }}
        </h3>
        <div class="h-80 w-full">
            <canvas id="yearlyChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('yearlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: '{{ __("app.annual_revenue") }} ($)',
                    data: {!! json_encode($chartRevenue) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: '{{ __("app.annual_net_profit") }} ($)',
                    data: {!! json_encode($chartProfit) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'transparent',
                    tension: 0.3
                },
                {
                    label: '{{ __("app.annual_expenses") }} ($)',
                    data: {!! json_encode($chartExpenses) !!},
                    borderColor: '#f43f5e',
                    backgroundColor: 'transparent',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endsection
