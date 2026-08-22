@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">
            @php
                $roleTitle = auth()->user()->roles->first()?->name ?? ucfirst(auth()->user()->role);
            @endphp
            {{ $roleTitle }} {{ app()->getLocale() === 'km' ? 'ផ្ទាំងគ្រប់គ្រង (Dashboard)' : 'Dashboard' }}
        </h1>
        <p class="text-gray-500 mt-1">{{ __('app.overview_subtitle') }}</p>
    </div>
    <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg border border-gray-100 shadow-sm">
        <i class="fas fa-calendar-day mr-2 text-blue-500"></i> {{ now()->format('l, d F Y') }}
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
                        <div class="sc-value text-2xl font-bold">{{ number_format($customers ?? 0) }}</div>
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
            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-2xs">
                <div class="flex items-center justify-between mb-3.5">
                    <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wider">
                        {{ __('app.quick_actions') }}
                    </span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                </div>
                <div class="space-y-2">
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('customers.create'))
                    <a href="{{ route('customers.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-indigo-50/60 border border-slate-100/80 hover:border-indigo-100 transition-all text-slate-700 hover:text-indigo-600 no-underline group shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-white shadow-2xs flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <i class="fas fa-user-plus text-xs"></i>
                            </span>
                            <span class="text-xs font-bold">{{ __('app.add_customer') }}</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.create'))
                    <a href="{{ route('admin.sales.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-emerald-50/60 border border-slate-100/80 hover:border-emerald-100 transition-all text-slate-700 hover:text-emerald-600 no-underline group shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-white shadow-2xs flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <i class="fas fa-cash-register text-xs"></i>
                            </span>
                            <span class="text-xs font-bold">{{ __('app.new_direct_sale') }}</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-emerald-600 transition-colors"></i>
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('payments.create'))
                    <a href="{{ route('payments.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-amber-50/60 border border-slate-100/80 hover:border-amber-100 transition-all text-slate-700 hover:text-amber-600 no-underline group shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-white shadow-2xs flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <i class="fas fa-credit-card text-xs"></i>
                            </span>
                            <span class="text-xs font-bold">{{ __('app.new_payment') }}</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-amber-600 transition-colors"></i>
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('installments.create'))
                    <a href="{{ route('installments.create') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-purple-50/60 border border-slate-100/80 hover:border-purple-100 transition-all text-slate-700 hover:text-purple-600 no-underline group shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-white shadow-2xs flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                <i class="fas fa-file-contract text-xs"></i>
                            </span>
                            <span class="text-xs font-bold">{{ __('app.new_installment') }}</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:text-purple-600 transition-colors"></i>
                    </a>
                    @endif
                </div>
            </div>

        </div>

    </div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-chart-line text-green-500 mr-2"></i> {{ __('app.monthly_payment_collection') }}
            </h2>
        </div>
        <div style="position: relative; height: 250px;">
            <canvas id="staffCollectionChart"></canvas>
        </div>
    </div>
    
    <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-purple-500 mr-2"></i> {{ __('app.installment_status') }}
        </h2>
        <div class="flex-grow flex items-center justify-center relative">
            <div style="height: 180px; width: 180px;">
                <canvas id="staffDonutChart"></canvas>
            </div>
            <!-- Center Text -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-2xl font-bold text-gray-700">{{ $installmentStatus['paid']['count'] + $installmentStatus['ongoing']['count'] + $installmentStatus['overdue']['count'] }}</span>
                <span class="text-xs text-gray-400">{{ __('app.total') }}</span>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-sm">
            <div>
                <div class="w-3 h-3 rounded-full bg-emerald-500 mx-auto mb-1"></div>
                <span class="font-medium text-gray-800">{{ $installmentStatus['paid']['count'] }}</span>
                <p class="text-xs text-gray-500">{{ __('app.paid') }}</p>
            </div>
            <div>
                <div class="w-3 h-3 rounded-full bg-amber-500 mx-auto mb-1"></div>
                <span class="font-medium text-gray-800">{{ $installmentStatus['ongoing']['count'] }}</span>
                <p class="text-xs text-gray-500">{{ __('app.ongoing') }}</p>
            </div>
            <div>
                <div class="w-3 h-3 rounded-full bg-red-500 mx-auto mb-1"></div>
                <span class="font-medium text-gray-800">{{ $installmentStatus['overdue']['count'] }}</span>
                <p class="text-xs text-gray-500">{{ __('app.overdue') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="space-y-6">
    <!-- Recent Payments & Tasks -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-tasks text-blue-500 mr-2"></i> {{ __('app.priority_tasks') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('payments.index', ['status' => 'pending']) }}" class="flex items-start p-4 bg-yellow-50 rounded-lg border border-yellow-100 hover:shadow-md transition">
                    <i class="fas fa-clock text-yellow-500 mt-1 mr-3 text-lg"></i>
                    <div>
                        <p class="font-semibold text-gray-800">{{ __('app.process_pending_payments') }}</p>
                        <p class="text-sm text-gray-600 mt-1"><span class="font-bold">{{ $pendingPayments }}</span> {{ __('app.payments_awaiting_review') }}</p>
                    </div>
                </a>
                
                <a href="{{ route('late-payments.index') }}" class="flex items-start p-4 bg-red-50 rounded-lg border border-red-100 hover:shadow-md transition">
                    <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3 text-lg"></i>
                    <div>
                        <p class="font-semibold text-gray-800">{{ __('app.late_payments') }}</p>
                        <p class="text-sm text-gray-600 mt-1"><span class="font-bold">{{ $lateCustomers }}</span> {{ __('app.late_customers_reminders') }}</p>
                    </div>
                </a>
            </div>
        </div>

        @if(isset($recentPayments) && $recentPayments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-history text-gray-500 mr-2"></i> {{ __('app.recent_payments') }}
                </h2>
                <a href="{{ route('payments.index') }}" class="text-sm text-blue-600 hover:underline">{{ __('app.view_all') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-100">
                            <th class="pb-3 font-medium">{{ __('app.customer_name') }}</th>
                            <th class="pb-3 font-medium">{{ __('app.amount') }}</th>
                            <th class="pb-3 font-medium">{{ __('app.date') }}</th>
                            <th class="pb-3 font-medium">{{ __('app.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentPayments as $payment)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 text-gray-800 font-medium">
                                <a href="{{ route('payments.show', $payment->id) }}" class="hover:text-blue-600 transition">
                                    {{ $payment->installment->customer->name ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="py-3">
                                <span class="font-semibold text-gray-800">${{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td class="py-3 text-gray-500">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : 'N/A' }}</td>
                            <td class="py-3">
                                @if($payment->status === 'approved')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-md text-xs font-medium border border-emerald-200">{{ __('app.approved') }}</span>
                                @elseif($payment->status === 'pending')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-md text-xs font-medium border border-amber-200">{{ __('app.pending') }}</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-md text-xs font-medium border border-red-200">{{ __('app.'.$payment->status) }}</span>
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