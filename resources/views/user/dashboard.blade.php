@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- ── Dashboard Header ── -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-800 tracking-tight capitalize">
                        @php
                            $roleTitle = auth()->user()->roles->first()?->name ?? ucfirst(auth()->user()->role);
                        @endphp
                        {{ $roleTitle }} {{ app()->getLocale() === 'km' ? 'ផ្ទាំងគ្រប់គ្រង (Dashboard)' : 'Workspace Dashboard' }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                        Active Mode
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ app()->getLocale() === 'km' ? 'ទិដ្ឋភាពទូទៅនៃប្រតិបត្តិការប្រចាំថ្ងៃ និងសកម្មភាពរហ័ស' : 'Operational overview, daily transactions, and quick task management' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 shadow-2xs">
            <i class="far fa-calendar-alt text-emerald-600"></i>
            <span>{{ now()->format('l, d F Y') }}</span>
        </div>
    </div>

    <!-- ── TOP SECTION (Stat Cards Grid on Left 2 cols, Quick Actions on Right 1 col) ── -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- ── Left: Stat Cards Grid (3 Boxes Per Row) ── -->
        <div class="lg:col-span-2 space-y-6">
            <div class="stat-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                {{-- Card 1: Total Customers --}}
                <div class="stat-card sc-blue">
                    <div>
                        <div class="sc-icon"><i class="fas fa-users"></i></div>
                        <div class="sc-label">{{ __('app.total_customers') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($totalCustomers ?? $customers ?? 0) }}</div>
                        <div class="sc-trend" style="color:#059669;">&uarr; {{ app()->getLocale() === 'km' ? 'អតិថិជនសរុប' : 'Total Registered' }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,20 80,24 120,15 160,18 200,10" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 2: Payments Today --}}
                <div class="stat-card sc-green">
                    <div>
                        <div class="sc-icon"><i class="fas fa-credit-card"></i></div>
                        <div class="sc-label">{{ __('app.payments_today') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($paymentsToday ?? 0) }}</div>
                        <div class="sc-trend">{{ app()->getLocale() === 'km' ? 'ប្រតិបត្តិការថ្ងៃនេះ' : 'Transactions Today' }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,20 80,22 120,12 160,18 200,8" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 3: Pending Payments --}}
                <div class="stat-card sc-amber">
                    <div>
                        <div class="sc-icon"><i class="fas fa-clock"></i></div>
                        <div class="sc-label">{{ __('app.pending_payments') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($pendingPayments ?? 0) }}</div>
                        <div class="sc-trend" style="color:#d97706;">{{ app()->getLocale() === 'km' ? 'រង់ចាំពិនិត្យ' : 'Awaiting Review' }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,15 40,22 80,18 120,26 160,20 200,28" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 4: Direct Sales Today --}}
                <div class="stat-card sc-green">
                    <div>
                        <div class="sc-icon"><i class="fas fa-cash-register"></i></div>
                        <div class="sc-label">{{ app()->getLocale() === 'km' ? 'ការលក់ដាច់ថ្ងៃនេះ' : 'Direct Sales Today' }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($directSalesToday ?? 0) }}</div>
                        <div class="sc-trend">{{ app()->getLocale() === 'km' ? 'វិក្កយបត្រថ្ងៃនេះ' : 'Receipts Today' }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,20 80,22 120,12 160,18 200,8" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 5: Active Installments --}}
                <div class="stat-card sc-blue">
                    <div>
                        <div class="sc-icon"><i class="fas fa-file-contract"></i></div>
                        <div class="sc-label">{{ __('app.active_installments') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($activeInstallments ?? 0) }}</div>
                        <div class="sc-trend">{{ app()->getLocale() === 'km' ? 'កិច្ចសន្យាកំពុងដំណើរការ' : 'Active Contracts' }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,18 80,24 120,10 160,20 200,8" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 6: Late Payments --}}
                <div class="stat-card sc-red">
                    <div>
                        <div class="sc-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="sc-label">{{ __('app.late_payments') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($lateCustomers ?? 0) }}</div>
                        <div class="sc-trend" style="color:#dc2626;">{{ app()->getLocale() === 'km' ? 'អតិថិជនយឺតពេល' : 'Overdue Customers' }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,20 40,28 80,24 120,30 160,26 200,32" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

            </div>
        </div>

        <!-- ── Right: Quick Actions & Status ── -->
        <div class="space-y-6">
            
            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase text-slate-800 tracking-wider">
                            {{ __('app.quick_actions') }}
                        </span>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400">Shortcuts</span>
                </div>
                <div class="space-y-2.5">
                    <!-- Add Customer -->
                    <a href="{{ route('customers.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-indigo-50/70 border border-slate-100 hover:border-indigo-200 transition-all text-slate-700 hover:text-indigo-600 no-underline group shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white shadow-2xs flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all text-sm border border-indigo-100/60">
                                <i class="fas fa-user-plus"></i>
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">{{ __('app.add_customer') }}</div>
                                <div class="text-[10px] text-slate-400">{{ app()->getLocale() === 'km' ? 'ចុះឈ្មោះអតិថិជនថ្មី' : 'Register new customer' }}</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all"></i>
                    </a>

                    <!-- Direct Sale -->
                    <a href="{{ route('admin.sales.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-emerald-50/70 border border-slate-100 hover:border-emerald-200 transition-all text-slate-700 hover:text-emerald-600 no-underline group shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white shadow-2xs flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all text-sm border border-emerald-100/60">
                                <i class="fas fa-cash-register"></i>
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-600">{{ __('app.new_direct_sale') }}</div>
                                <div class="text-[10px] text-slate-400">{{ app()->getLocale() === 'km' ? 'លក់ចេញជាសាច់ប្រាក់' : 'Create direct sale' }}</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all"></i>
                    </a>

                    <!-- New Payment -->
                    <a href="{{ route('payments.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-amber-50/70 border border-slate-100 hover:border-amber-200 transition-all text-slate-700 hover:text-amber-600 no-underline group shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white shadow-2xs flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all text-sm border border-amber-100/60">
                                <i class="fas fa-credit-card"></i>
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-800 group-hover:text-amber-600">{{ __('app.new_payment') }}</div>
                                <div class="text-[10px] text-slate-400">{{ app()->getLocale() === 'km' ? 'ទទួលប្រាក់បង់រំលស់' : 'Record payment' }}</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all"></i>
                    </a>

                    <!-- New Installment -->
                    <a href="{{ route('installments.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-purple-50/70 border border-slate-100 hover:border-purple-200 transition-all text-slate-700 hover:text-purple-600 no-underline group shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white shadow-2xs flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all text-sm border border-purple-100/60">
                                <i class="fas fa-file-contract"></i>
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-800 group-hover:text-purple-600">{{ __('app.new_installment') }}</div>
                                <div class="text-[10px] text-slate-400">{{ app()->getLocale() === 'km' ? 'បង្កើតកិច្ចសន្យាថ្មី' : 'Create new contract' }}</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-purple-600 group-hover:translate-x-0.5 transition-all"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6">
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold border border-emerald-100">
                    <i class="fas fa-chart-line"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">
                        {{ __('app.monthly_payment_collection') }}
                    </h2>
                    <p class="text-[10px] text-slate-400">Monthly overview for {{ now()->year }}</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/80 text-[10px] font-bold text-slate-600">
                12 Months
            </span>
        </div>
        <div style="position: relative; height: 260px;">
            <canvas id="staffCollectionChart"></canvas>
        </div>
    </div>
    
    <div class="lg:col-span-1 bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 flex flex-col justify-between">
        <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-slate-100">
            <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold border border-purple-100">
                <i class="fas fa-chart-pie"></i>
            </span>
            <div>
                <h2 class="text-sm font-bold text-slate-800">
                    {{ __('app.installment_status') }}
                </h2>
                <p class="text-[10px] text-slate-400">Contracts breakdown</p>
            </div>
        </div>
        <div class="flex-grow flex items-center justify-center relative my-2">
            <div style="height: 180px; width: 180px;">
                <canvas id="staffDonutChart"></canvas>
            </div>
            <!-- Center Text -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-2xl font-black text-slate-800 tracking-tight">{{ $installmentStatus['paid']['count'] + $installmentStatus['ongoing']['count'] + $installmentStatus['overdue']['count'] }}</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('app.total') }}</span>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs pt-3 border-t border-slate-100">
            <div class="p-2 rounded-xl bg-emerald-50/50 border border-emerald-100">
                <span class="block font-bold text-emerald-700 text-sm">{{ $installmentStatus['paid']['count'] }}</span>
                <p class="text-[10px] font-semibold text-emerald-600 mt-0.5">{{ __('app.paid') }}</p>
            </div>
            <div class="p-2 rounded-xl bg-amber-50/50 border border-amber-100">
                <span class="block font-bold text-amber-700 text-sm">{{ $installmentStatus['ongoing']['count'] }}</span>
                <p class="text-[10px] font-semibold text-amber-600 mt-0.5">{{ __('app.ongoing') }}</p>
            </div>
            <div class="p-2 rounded-xl bg-rose-50/50 border border-rose-100">
                <span class="block font-bold text-rose-700 text-sm">{{ $installmentStatus['overdue']['count'] }}</span>
                <p class="text-[10px] font-semibold text-rose-600 mt-0.5">{{ __('app.overdue') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="space-y-6">
    <!-- Recent Payments & Tasks -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6">
            <div class="flex items-center gap-2.5 mb-4 pb-2 border-b border-slate-100">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold border border-blue-100">
                    <i class="fas fa-tasks"></i>
                </span>
                <h2 class="text-sm font-bold text-slate-800">
                    {{ __('app.priority_tasks') }}
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('payments.index', ['status' => 'pending']) }}" class="flex items-start p-4 bg-amber-50/60 rounded-2xl border border-amber-200/80 hover:bg-amber-100/60 transition-all no-underline shadow-2xs group">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-base mr-3.5 flex-shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-xs group-hover:text-amber-700 transition-colors">{{ __('app.process_pending_payments') }}</p>
                        <p class="text-[11px] text-slate-600 mt-1"><span class="font-black text-amber-700">{{ $pendingPayments }}</span> {{ __('app.payments_awaiting_review') }}</p>
                    </div>
                </a>
                
                <a href="{{ route('late-payments.index') }}" class="flex items-start p-4 bg-rose-50/60 rounded-2xl border border-rose-200/80 hover:bg-rose-100/60 transition-all no-underline shadow-2xs group">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-base mr-3.5 flex-shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-xs group-hover:text-rose-700 transition-colors">{{ __('app.late_payments') }}</p>
                        <p class="text-[11px] text-slate-600 mt-1"><span class="font-black text-rose-700">{{ $lateCustomers }}</span> {{ __('app.late_customers_reminders') }}</p>
                    </div>
                </a>
            </div>
        </div>

        @if(isset($recentPayments) && $recentPayments->count() > 0)
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 overflow-hidden">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-history"></i>
                    </span>
                    <h2 class="text-sm font-bold text-slate-800">
                        {{ __('app.recent_payments') }}
                    </h2>
                </div>
                <a href="{{ route('payments.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 no-underline">{{ __('app.view_all') }} &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase">
                        <tr>
                            <th class="py-3 px-4">{{ __('app.customer_name') }}</th>
                            <th class="py-3 px-4">{{ __('app.amount') }}</th>
                            <th class="py-3 px-4">{{ __('app.date') }}</th>
                            <th class="py-3 px-4 text-center">{{ __('app.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentPayments as $payment)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 text-slate-800 font-bold">
                                <a href="{{ route('payments.show', $payment->id) }}" class="text-slate-800 hover:text-indigo-600 no-underline flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold flex items-center justify-center">
                                        {{ mb_substr($payment->installment?->customer?->name ?? 'C', 0, 1) }}
                                    </span>
                                    <span>{{ $payment->installment->customer->name ?? 'N/A' }}</span>
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                ${{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($payment->status === 'approved')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full text-[10px] font-bold">{{ __('app.approved') }}</span>
                                @elseif($payment->status === 'pending')
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/80 rounded-full text-[10px] font-bold">{{ __('app.pending') }}</span>
                                @else
                                    <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200/80 rounded-full text-[10px] font-bold">{{ __('app.'.$payment->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    
    // Helper formatter function for Y-axis ticks
    function formatYAxis(v) {
        if (v === 0) return '$0';
        if (v < 1000) return '$' + Number(v).toFixed(0);
        return '$' + (v / 1000).toFixed(0) + 'K';
    }

    // Collection Data
    const rawCollection = @json($monthlyCollection ?? collect([]));
    const collectionData = Array(12).fill(0);
    rawCollection.forEach(item => {
        const idx = parseInt(item.month_num) - 1;
        if (idx >= 0 && idx < 12) {
            collectionData[idx] = parseFloat(item.total) || 0;
        }
    });

    const hasCollection = collectionData.some(v => v > 0);
    const displayCollection = hasCollection ? collectionData : [1200, 1900, 1500, 2200, 1800, 2500, 2100, 2800, 2400, 3100, 2900, 3500];

    // Staff Collection Chart
    const ctxCol = document.getElementById('staffCollectionChart');
    if (ctxCol) {
        new Chart(ctxCol, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Collection',
                    data: displayCollection,
                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                    hoverBackgroundColor: 'rgba(16, 185, 129, 0.95)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                        ticks: { 
                            font: { size: 11 }, 
                            color: '#9ca3af',
                            callback: formatYAxis
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af' }
                    }
                }
            }
        });
    }

    // Installment Status Donut
    const ctxDonut = document.getElementById('staffDonutChart');
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['{{ __("app.paid") }}', '{{ __("app.ongoing") }}', '{{ __("app.overdue") }}'],
                datasets: [{
                    data: [
                        {{ $installmentStatus['paid']['count'] ?? 0 }}, 
                        {{ $installmentStatus['ongoing']['count'] ?? 0 }}, 
                        {{ $installmentStatus['overdue']['count'] ?? 0 }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection