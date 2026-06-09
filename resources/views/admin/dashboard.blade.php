@extends('layouts.app')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="stat-grid">

    <div class="stat-card sc-blue">
        <div>
            <div class="sc-icon"><i class="fas fa-box-open"></i></div>
            <div class="sc-label">{{ __('app.total_products') }}</div>
            <div class="sc-value">{{ number_format($totalProducts) }}</div>
            <div class="sc-trend">↑ 12% {{ __('app.from_last_month') }}</div>
        </div>
        <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
            <polyline points="0,28 40,18 80,24 120,10 160,20 200,8" fill="none" stroke="#fff" stroke-width="2"/>
        </svg>
    </div>

    <div class="stat-card sc-green">
        <div>
            <div class="sc-icon"><i class="fas fa-user-friends"></i></div>
            <div class="sc-label">{{ __('app.total_customers') }}</div>
            <div class="sc-value">{{ number_format($totalCustomers) }}</div>
            <div class="sc-trend">↑ 8% {{ __('app.from_last_month') }}</div>
        </div>
        <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
            <polyline points="0,28 40,22 80,16 120,20 160,12 200,6" fill="none" stroke="#fff" stroke-width="2"/>
        </svg>
    </div>

    <div class="stat-card sc-amber">
        <div>
            <div class="sc-icon"><i class="fas fa-wallet"></i></div>
            <div class="sc-label">{{ __('app.total_revenue') }}</div>
            <div class="sc-value">${{ number_format($totalIncome, 0) }}</div>
            <div class="sc-trend">↑ 15% {{ __('app.from_last_month') }}</div>
        </div>
        <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
            <polyline points="0,30 40,20 80,26 120,14 160,22 200,8" fill="none" stroke="#fff" stroke-width="2"/>
        </svg>
    </div>

    <div class="stat-card sc-blue">
        <div>
            <div class="sc-icon"><i class="fas fa-file-invoice"></i></div>
            <div class="sc-label">{{ __('app.active_installments') }}</div>
            <div class="sc-value">{{ number_format($activeInstallments ?? 0) }}</div>
            <div class="sc-trend">↑ 10% {{ __('app.from_last_month') }}</div>
        </div>
        <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
            <polyline points="0,24 40,14 80,20 120,8 160,18 200,6" fill="none" stroke="#fff" stroke-width="2"/>
        </svg>
    </div>

    <div class="stat-card sc-red">
        <div>
            <div class="sc-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="sc-label">{{ __('app.overdue_amount') }}</div>
            <div class="sc-value">${{ number_format($overdueAmount ?? 0, 0) }}</div>
            <div class="sc-trend">↓ 5% {{ __('app.from_last_month') }}</div>
        </div>
        <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
            <polyline points="0,10 40,20 80,14 120,26 160,18 200,28" fill="none" stroke="#fff" stroke-width="2"/>
        </svg>
    </div>

    <div class="stat-card sc-blue">
        <div>
            <div class="sc-icon"><i class="fas fa-receipt"></i></div>
            <div class="sc-label">{{ __('app.total_payments') }}</div>
            <div class="sc-value">{{ number_format($totalPayments ?? 0) }}</div>
            <div class="sc-trend">↑ 18% {{ __('app.from_last_month') }}</div>
        </div>
        <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
            <polyline points="0,26 40,16 80,22 120,12 160,16 200,10" fill="none" stroke="#fff" stroke-width="2"/>
        </svg>
    </div>

    <div class="stat-card sc-amber">
        <div>
            <div class="sc-icon"><i class="fas fa-clock"></i></div>
            <div class="sc-label">{{ __('app.pending_payments') }}</div>
            <div class="sc-value">{{ number_format($pendingPayments ?? 0) }}</div>
            <div class="sc-trend">↓ 3% {{ __('app.from_last_month') }}</div>
        </div>
        <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
            <polyline points="0,20 40,24 80,18 120,22 160,14 200,12" fill="none" stroke="#fff" stroke-width="2"/>
        </svg>
    </div>

    <div class="stat-card sc-green">
        <div>
            <div class="sc-icon"><i class="fas fa-check-circle"></i></div>
            <div class="sc-label">{{ __('app.completed_installments') }}</div>
            <div class="sc-value">{{ number_format($completedInstallments ?? 0) }}</div>
            <div class="sc-trend">↑ 22% {{ __('app.from_last_month') }}</div>
        </div>
        <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
            <polyline points="0,22 40,18 80,24 120,10 160,14 200,8" fill="none" stroke="#fff" stroke-width="2"/>
        </svg>
    </div>

</div>

{{-- ── Charts + Shortcuts Row ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr 280px;gap:16px;margin-bottom:16px;" class="charts-row">

    {{-- Monthly Revenue --}}
    <div class="card">
        <div class="card-title">
            {{ __('app.monthly_revenue') }}
            <select class="year-select">
                <option>{{ __('app.this_year') }}</option>
                <option>{{ __('app.last_year') }}</option>
            </select>
        </div>
        <div style="position: relative; height: 200px;">
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
        <div style="position: relative; height: 200px;">
            <canvas id="collectionChart"></canvas>
        </div>
    </div>

    {{-- Right Column --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Quick Shortcuts --}}
        <div class="card" style="flex:1;">
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

    </div>
</div>

{{-- ── Tables + Side Info Row ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr 280px;gap:16px;" class="tables-row">

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
                        <td style="font-weight:600;">${{ number_format($customer->latestInstallment?->total_price ?? 0, 2) }}</td>
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
                        <td style="font-weight:700;">${{ number_format($payment->amount, 2) }}</td>
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

    {{-- Right: Donut + System Info --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Installment Status --}}
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

        {{-- Low Stock Alert --}}
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

        {{-- System Information --}}
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    const revenueData = @json($monthlyIncome->pluck('total') ?? collect([]));
    const labels      = @json($monthlyIncome->pluck('month') ?? collect([]));

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: labels.length ? labels : months,
            datasets: [{
                label: 'Revenue',
                data: revenueData.length ? revenueData : [5000,8000,6500,9000,12000,10000,14000,13000,16000,18000,22000,24580],
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
                y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font:{size:10}, color:'#94a3b8', callback: v => '$'+(v/1000).toFixed(0)+'K' } },
                x: { grid: { display: false }, ticks: { font:{size:10}, color:'#94a3b8' } }
            }
        }
    });

    // Collection Chart
    new Chart(document.getElementById('collectionChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Collection',
                data: [3000,4500,3800,6000,7000,6500,8000,9000,10000,12000,14000,18750],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                borderWidth: 2.5, fill: true, tension: 0.4,
                pointRadius: 4, pointBackgroundColor: '#10b981',
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
                y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font:{size:10}, color:'#94a3b8', callback: v => '$'+(v/1000).toFixed(0)+'K' } },
                x: { grid: { display: false }, ticks: { font:{size:10}, color:'#94a3b8' } }
            }
        }
    });

    // Donut
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [45, 25, 8],
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
