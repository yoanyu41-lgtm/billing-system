@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <!-- ── TOP SECTION (Stat Cards on Left 2 cols, Quick Actions & Installment Status on Right 1 col) ── -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- ── Left: Stat Cards Grid (3 Boxes Per Row) ── -->
        <div class="lg:col-span-2 space-y-6">
            <div class="stat-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                {{-- Card 1: Total Products --}}
                <div class="stat-card sc-blue">
                    <div>
                        <div class="sc-icon"><i class="fas fa-box"></i></div>
                        <div class="sc-label">{{ __('app.total_products') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($totalProducts ?? 0) }}</div>
                        <div class="sc-trend">&mdash; 0% {{ __('app.from_last_month') }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,24 80,26 120,22 160,25 200,20" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 2: Total Customers --}}
                <div class="stat-card sc-green">
                    <div>
                        <div class="sc-icon"><i class="fas fa-users"></i></div>
                        <div class="sc-label">{{ __('app.total_customers') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($totalCustomers ?? 0) }}</div>
                        <div class="sc-trend" style="color:#059669;">&uarr; 5% {{ __('app.from_last_month') }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,20 80,24 120,15 160,18 200,10" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 3: Total Revenue --}}
                <div class="stat-card sc-amber">
                    <div>
                        <div class="sc-icon"><i class="fas fa-wallet"></i></div>
                        <div class="sc-label">{{ __('app.total_revenue') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ format_currency($totalIncome ?? 0, $exchangeRate) }}</div>
                        <div class="sc-trend" style="color:#dc2626;">&darr; 100% {{ __('app.from_last_month') }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,15 40,22 80,18 120,26 160,20 200,28" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 4: Direct Sales --}}
                <div class="stat-card sc-green">
                    <div>
                        <div class="sc-icon"><i class="fas fa-cash-register"></i></div>
                        <div class="sc-label">{{ __('app.direct_sales') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ format_currency($directSalesTotal ?? 0, $exchangeRate) }}</div>
                        <div class="sc-trend">{{ __('app.this_month') }}: {{ format_currency($directSalesMonth ?? 0, $exchangeRate) }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,20 80,22 120,12 160,18 200,8" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 5: Combined Income --}}
                <div class="stat-card sc-blue">
                    <div>
                        <div class="sc-icon"><i class="fas fa-coins"></i></div>
                        <div class="sc-label">{{ __('app.combined_income') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ format_currency($combinedIncome ?? 0, $exchangeRate) }}</div>
                        <div class="sc-trend">{{ __('app.installment') }} + {{ __('app.direct_sale') }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,18 80,24 120,10 160,20 200,8" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 6: Active Installments --}}
                <div class="stat-card sc-blue">
                    <div>
                        <div class="sc-icon"><i class="fas fa-file-contract"></i></div>
                        <div class="sc-label">{{ __('app.active_installments') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($activeInstallments ?? 0) }}</div>
                        <div class="sc-trend">&mdash; 0% {{ __('app.from_last_month') }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,25 40,23 80,25 120,22 160,24 200,20" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 7: Overdue Amount --}}
                <div class="stat-card sc-red">
                    <div>
                        <div class="sc-icon"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="sc-label">{{ __('app.overdue_amount') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ format_currency($overdueAmount ?? 0, $exchangeRate) }}</div>
                        <div class="sc-trend">&mdash; 0% {{ __('app.from_last_month') }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,20 40,25 80,22 120,28 160,24 200,30" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 8: Pending Payments --}}
                <div class="stat-card sc-amber">
                    <div>
                        <div class="sc-icon"><i class="fas fa-clock"></i></div>
                        <div class="sc-label">{{ __('app.pending_payments') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($pendingPayments ?? 0) }}</div>
                        <div class="sc-trend">&mdash; 0% {{ __('app.from_last_month') }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,25 40,24 80,26 120,23 160,25 200,22" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Card 9: Completed Installments --}}
                <div class="stat-card sc-green">
                    <div>
                        <div class="sc-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="sc-label">{{ __('app.completed_installments') }}</div>
                        <div class="sc-value text-2xl font-bold">{{ number_format($completedInstallments ?? 0) }}</div>
                        <div class="sc-trend" style="color:#dc2626;">&darr; 100% {{ __('app.from_last_month') }}</div>
                    </div>
                    <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                        <polyline points="0,28 40,20 80,24 120,15 160,18 200,10" fill="none" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>

            </div>
        </div>

        <!-- ── Right Top: Quick Shortcuts & Installment Status ── -->
        <div class="space-y-4">
            {{-- Quick Shortcuts (សកម្មភាពរហ័ស) --}}
            <div class="card">
                <div class="card-title" style="margin-bottom:10px;">{{ __('app.quick_actions') }}</div>
                <a href="{{ route('customers.create') }}" class="shortcut-btn">
                    <div class="shortcut-icon si-blue"><i class="fas fa-user-plus"></i></div>
                    <span style="color:#0f172a;">{{ __('app.add_customer') }}</span>
                </a>
                <a href="{{ route('admin.products.create') }}" class="shortcut-btn">
                    <div class="shortcut-icon si-green"><i class="fas fa-plus-circle"></i></div>
                    <span style="color:#0f172a;">{{ __('app.add_new_product') }}</span>
                </a>
                <a href="{{ route('installments.create') }}" class="shortcut-btn">
                    <div class="shortcut-icon si-blue"><i class="fas fa-file-invoice-dollar"></i></div>
                    <span style="color:#2563eb;">{{ __('app.new_installment') }}</span>
                </a>
                <a href="{{ route('payments.create') }}" class="shortcut-btn">
                    <div class="shortcut-icon si-amber"><i class="fas fa-credit-card"></i></div>
                    <span style="color:#d97706;">{{ __('app.new_payment') }}</span>
                </a>
            </div>

            {{-- Installment Status (ស្ថានភាពការបង់រំលោះ) --}}
            <div class="card">
                <div class="card-title" style="margin-bottom:8px;">{{ __('app.installment_status') }}</div>
                <div class="donut-wrap">
                    <canvas id="donutChart" style="width:90px!important;height:90px!important;flex-shrink:0;"></canvas>
                    <div class="donut-legend">
                        <div class="donut-row">
                            <div class="donut-dot" style="background:#10b981;"></div>
                            <div class="donut-label">{{ __('app.paid') }}</div>
                            <div class="donut-val">45 (58%)</div>
                        </div>
                        <div class="donut-row">
                            <div class="donut-dot" style="background:#f59e0b;"></div>
                            <div class="donut-label">{{ __('app.ongoing') }}</div>
                            <div class="donut-val">25 (32%)</div>
                        </div>
                        <div class="donut-row">
                            <div class="donut-dot" style="background:#ef4444;"></div>
                            <div class="donut-label">{{ __('app.overdue') }}</div>
                            <div class="donut-val">8 (10%)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ── MIDDLE SECTION: CHARTS (Full Width across the page!) ── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Monthly Revenue --}}
        <div class="card">
            <div class="card-title">
                {{ __('app.monthly_revenue') }}
                <select class="year-select">
                    <option>{{ __('app.this_year') }}</option>
                    <option>{{ __('app.last_year') }}</option>
                </select>
            </div>
            <div style="position: relative; height: 220px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Monthly Payment Collection --}}
        <div class="card">
            <div class="card-title">
                {{ __('app.monthly_payment_collection') }}
                <select class="year-select">
                    <option>{{ __('app.this_year') }}</option>
                    <option>{{ __('app.last_year') }}</option>
                </select>
            </div>
            <div style="position: relative; height: 220px;">
                <canvas id="collectionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ── LOW STOCK ALERT & SYSTEM INFORMATION (Directly Below Charts) ── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Low Stock Alert (ស្តុកជិតអស់) --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:6px;">
                    <span style="color:#f59e0b;">&#9888;</span> {{ __('app.low_stock_alert') }}
                </span>
                @if(($lowStockCount ?? 0) > 0)
                <span class="pill" style="background:#fef3c7;color:#b45309;font-size:10px;">{{ $lowStockCount }}</span>
                @endif
            </div>
            @forelse($lowStockProducts ?? [] as $p)
            <a href="{{ route('admin.products.show', [$p, 'from' => 'stock']) }}" style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;text-decoration:none;">
                <div style="min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:170px;">{{ $p->name }}</div>
                    <div style="font-size:11px;color:#94a3b8;">{{ $p->code }}</div>
                </div>
                <span class="pill" style="font-size:10px;{{ $p->stock <= 0 ? 'background:#fee2e2;color:#b91c1c;' : 'background:#fef3c7;color:#b45309;' }}">
                    {{ $p->stock <= 0 ? __('app.out_of_stock') : $p->stock }}
                </span>
            </a>
            @empty
            <div style="text-align:center;color:#94a3b8;font-size:12px;padding:16px 0;">{{ __('app.no_low_stock') }}</div>
            @endforelse
            @if(($lowStockCount ?? 0) > 0)
            <a href="{{ route('admin.products.stock') }}" class="btn-viewall" style="display:inline-block;margin-top:10px;background:#f59e0b;">{{ __('app.view_all') }}</a>
            @endif
        </div>

        {{-- System Information (ព័ត៌មានប្រព័ន្ធ) --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:4px;">{{ __('app.system_information') }}</div>
            <div class="sysrow">
                <span class="sk">{{ __('app.system_version') }}</span>
                <span class="sv">v1.0.0</span>
            </div>
            <div class="sysrow">
                <span class="sk">{{ __('app.last_backup') }}</span>
                <span class="sv" style="font-size:11px;">{{ now()->format('d M Y h:i A') }}</span>
            </div>
            <div class="sysrow">
                <span class="sk">{{ __('app.status') }}</span>
                <span class="pill pill-paid" style="font-size:10px;">{{ __('app.system_running') }}</span>
            </div>
        </div>
    </div>

    <!-- ── TABLES SECTION (Recent Customers & Recent Payments) ── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Recent Customers --}}
        <div class="card">
            <div class="card-title">
                <span>{{ __('app.recent_customers') }}</span>
                <a href="{{ route('customers.index') }}" class="btn-viewall">{{ __('app.view_all') }}</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('app.customer_name') }}</th>
                            <th>{{ __('app.phone') }}</th>
                            <th>{{ __('app.product') }}</th>
                            <th>{{ __('app.amount') }}</th>
                            <th>{{ __('app.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCustomers ?? [] as $i => $customer)
                        <tr>
                            <td style="color:#94a3b8;">{{ $i+1 }}</td>
                            <td style="font-weight:600;">{{ $customer->name }}</td>
                            <td class="mono">{{ $customer->phone }}</td>
                            <td>{{ $customer->latestInstallment?->product?->name ?? '—' }}</td>
                            <td>
                                <span class="font-semibold text-gray-900">${{ number_format($customer->latestInstallment?->total_price ?? 0, 2) }}</span>
                            </td>
                            <td>
                                @php $st = $customer->latestInstallment?->status ?? 'ongoing'; @endphp
                                <span class="pill pill-{{ $st }}">{{ ucfirst($st) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:20px 0;">{{ __('app.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="card">
            <div class="card-title">
                <span style="color:#6366f1;">{{ __('app.recent_payments') }}</span>
                <a href="{{ route('payments.index') }}" class="btn-viewall" style="background:#8b5cf6;">{{ __('app.view_all') }}</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('app.invoice_no') }}</th>
                            <th>{{ __('app.customers') }}</th>
                            <th>{{ __('app.amount') }}</th>
                            <th>{{ __('app.date') }}</th>
                            <th>{{ __('app.payment_method') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments ?? [] as $i => $payment)
                        <tr>
                            <td style="color:#94a3b8;">{{ $i+1 }}</td>
                            <td class="mono" style="color:#6366f1;">{{ $payment->invoice_no ?? 'INV-'.str_pad($payment->id,5,'0',STR_PAD_LEFT) }}</td>
                            <td style="font-weight:600;">{{ $payment->installment?->customer?->name }}</td>
                            <td>
                                <span class="font-semibold text-gray-900">${{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td>
                                @php
                                    $m = strtolower($payment->paymentMethod?->name ?? 'other');
                                    $cls = match(true) {
                                        str_contains($m,'qr')     => 'qr',
                                        str_contains($m,'aba')    => 'aba',
                                        str_contains($m,'credit') => 'cc',
                                        str_contains($m,'wing')   => 'wing',
                                        default => 'other'
                                    };
                                @endphp
                                <span class="pill pill-{{ $cls }}">{{ $payment->paymentMethod?->name ?? 'Other' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:20px 0;">{{ __('app.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // Helper formatter function for Y-axis ticks
    function formatYAxis(v) {
        if (v === 0) return '$0';
        if (v < 1000) return '$' + Number(v).toFixed(0);
        return '$' + (v / 1000).toFixed(0) + 'K';
    }

    // 1. Process Revenue Data
    const rawRevenue = @json($monthlyIncome ?? collect([]));
    const revenueData = Array(12).fill(0);
    rawRevenue.forEach(item => {
        const idx = parseInt(item.month_num) - 1;
        if (idx >= 0 && idx < 12) {
            revenueData[idx] = parseFloat(item.total) || 0;
        }
    });

    const hasRevenue = revenueData.some(v => v > 0);
    const displayRevenue = hasRevenue ? revenueData : [5000,8000,6500,9000,12000,10000,14000,13000,16000,18000,22000,24580];

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue',
                data: displayRevenue,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderWidth: 2.5, fill: true, tension: 0.4,
                pointRadius: 4, pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff', pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => ' $' + c.parsed.y.toLocaleString() } }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' }, 
                    ticks: { 
                        font:{size:10}, 
                        color:'#94a3b8', 
                        callback: formatYAxis 
                    } 
                },
                x: { grid: { display: false }, ticks: { font:{size:10}, color:'#94a3b8' } }
            }
        }
    });

    // 2. Process Collection Data
    const rawCollection = @json($monthlyCollection ?? collect([]));
    const collectionData = Array(12).fill(0);
    rawCollection.forEach(item => {
        const idx = parseInt(item.month_num) - 1;
        if (idx >= 0 && idx < 12) {
            collectionData[idx] = parseFloat(item.total) || 0;
        }
    });

    const hasCollection = collectionData.some(v => v > 0);
    const displayCollection = hasCollection ? collectionData : [3000,4500,3800,6000,7000,6500,8000,9000,10000,12000,14000,18750];

    // Collection Chart
    new Chart(document.getElementById('collectionChart'), {
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
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => ' $' + c.parsed.y.toLocaleString() } }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' }, 
                    ticks: { 
                        font:{size:10}, 
                        color:'#94a3b8', 
                        callback: formatYAxis 
                    } 
                },
                x: { grid: { display: false }, ticks: { font:{size:10}, color:'#94a3b8' } }
            }
        }
    });

    // Donut
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [
                    {{ $installmentStatus['paid']['count'] ?? 0 }}, 
                    {{ $installmentStatus['ongoing']['count'] ?? 0 }}, 
                    {{ $installmentStatus['overdue']['count'] ?? 0 }}
                ],
                backgroundColor: ['#10b981','#f59e0b','#ef4444'],
                borderWidth: 0, hoverOffset: 4
            }]
        },
        options: {
            cutout: '70%', responsive: false,
            plugins: { legend: { display: false } }
        }
    });
});
</script>

@endsection
