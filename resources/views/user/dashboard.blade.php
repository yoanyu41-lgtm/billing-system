@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ __('app.staff_dashboard') }}</h1>
        <p class="text-gray-500 mt-1">{{ __('app.overview_subtitle') }}</p>
    </div>
    <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg border border-gray-100 shadow-sm">
        <i class="fas fa-calendar-day mr-2 text-blue-500"></i> {{ now()->format('l, d F Y') }}
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Stat Card 1 -->
    <a href="{{ route('customers.index') }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md hover:border-blue-200 group">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4 group-hover:bg-blue-100 transition">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 group-hover:text-blue-600 transition">{{ __('app.total_customers') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $customers }}</p>
            </div>
        </div>
    </a>
    
    <!-- Stat Card 2 -->
    <a href="{{ route('payments.index') }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md hover:border-green-200 group">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4 group-hover:bg-green-100 transition">
                <i class="fas fa-credit-card text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 group-hover:text-green-600 transition">{{ __('app.payments_today') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $paymentsToday }}</p>
            </div>
        </div>
    </a>
    
    <!-- Stat Card 3 -->
    <a href="{{ route('payments.index') }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md hover:border-yellow-200 group">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4 group-hover:bg-yellow-100 transition">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 group-hover:text-yellow-600 transition">{{ __('app.pending_payments') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingPayments }}</p>
            </div>
        </div>
    </a>
    
    <!-- Stat Card 4 -->
    <a href="{{ route('late-payments.index') }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md hover:border-red-200 group">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-50 text-red-600 mr-4 group-hover:bg-red-100 transition">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 group-hover:text-red-600 transition">{{ __('app.late_payments') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $lateCustomers }}</p>
            </div>
        </div>
    </a>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Quick Actions -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i> {{ __('app.quick_actions') }}
        </h2>
        <div class="space-y-3">
            <a href="{{ route('customers.create') }}" class="group flex items-center p-3 bg-gray-50 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-100">
                <div class="bg-white p-2 rounded shadow-sm text-blue-600 group-hover:text-blue-700">
                    <i class="fas fa-user-plus w-5 text-center"></i>
                </div>
                <span class="ml-3 font-medium text-gray-700 group-hover:text-blue-700">{{ __('app.add_customer') }}</span>
                <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs"></i>
            </a>
            
            <a href="{{ route('payments.create') }}" class="group flex items-center p-3 bg-gray-50 hover:bg-green-50 rounded-lg transition border border-transparent hover:border-green-100">
                <div class="bg-white p-2 rounded shadow-sm text-green-600 group-hover:text-green-700">
                    <i class="fas fa-money-bill-wave w-5 text-center"></i>
                </div>
                <span class="ml-3 font-medium text-gray-700 group-hover:text-green-700">{{ __('app.new_payment') }}</span>
                <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs"></i>
            </a>
            
            <a href="{{ route('installments.create') }}" class="group flex items-center p-3 bg-gray-50 hover:bg-purple-50 rounded-lg transition border border-transparent hover:border-purple-100">
                <div class="bg-white p-2 rounded shadow-sm text-purple-600 group-hover:text-purple-700">
                    <i class="fas fa-file-contract w-5 text-center"></i>
                </div>
                <span class="ml-3 font-medium text-gray-700 group-hover:text-purple-700">{{ __('app.new_installment') }}</span>
                <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs"></i>
            </a>
            
            <a href="{{ route('late-payments.index') }}" class="group flex items-center p-3 bg-gray-50 hover:bg-red-50 rounded-lg transition border border-transparent hover:border-red-100">
                <div class="bg-white p-2 rounded shadow-sm text-red-600 group-hover:text-red-700">
                    <i class="fas fa-bell w-5 text-center"></i>
                </div>
                <span class="ml-3 font-medium text-gray-700 group-hover:text-red-700">{{ __('app.late_payments') }}</span>
                <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Recent Payments & Tasks -->
    <div class="lg:col-span-2 space-y-6">
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
                            <td class="py-3 font-semibold text-gray-800">${{ number_format($payment->amount, 2) }}</td>
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
    
    // Collection Data
    const collectionDataRaw = @json($monthlyCollection ?? collect([]));
    const collectionData = collectionDataRaw.length > 0 
        ? collectionDataRaw.map(item => item.total) 
        : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        
    const labels = collectionDataRaw.length > 0 
        ? collectionDataRaw.map(item => item.month) 
        : months;

    // Staff Collection Chart
    const ctxCol = document.getElementById('staffCollectionChart');
    if (ctxCol) {
        new Chart(ctxCol, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Collection',
                    data: collectionData.length > 0 && Math.max(...collectionData) > 0 ? collectionData : [1200, 1900, 1500, 2200, 1800, 2500, 2100, 2800, 2400, 3100, 2900, 3500],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointHoverRadius: 6
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
                        ticks: { font: { size: 11 }, color: '#9ca3af' }
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