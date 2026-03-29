@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900">Admin Dashboard</h1>
    <p class="text-gray-600 mt-2">Welcome back! Here's your system overview.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-blue-100 text-sm font-semibold">Total Customers</p>
                <p class="text-4xl font-bold mt-2">{{ $totalCustomers }}</p>
            </div>
            <i class="fas fa-users text-4xl opacity-20"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-green-100 text-sm font-semibold">Total Products</p>
                <p class="text-4xl font-bold mt-2">{{ $totalProducts }}</p>
            </div>
            <i class="fas fa-box text-4xl opacity-20"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-purple-100 text-sm font-semibold">Total Income</p>
                <p class="text-4xl font-bold mt-2">${{ number_format($totalIncome, 0) }}</p>
            </div>
            <i class="fas fa-dollar-sign text-4xl opacity-20"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-orange-100 text-sm font-semibold">Remaining Balance</p>
                <p class="text-4xl font-bold mt-2">${{ number_format($remainingBalance, 0) }}</p>
            </div>
            <i class="fas fa-chart-line text-4xl opacity-20"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-lg lg:col-span-2">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Monthly Income Chart</h2>
        <canvas id="incomeChart" class="max-h-80"></canvas>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-4">⚠️ Late Customers</h2>
        <p class="text-5xl font-bold text-red-600">{{ $lateCustomers }}</p>
        <p class="text-gray-600 mt-2">customers need attention</p>
        <a href="{{ route('late-payments.index') }}" class="text-blue-600 font-semibold mt-4 inline-block hover:text-blue-800">
            View Details →
        </a>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-xl font-bold text-gray-900 mb-4">💳 Recent Payments</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-200">
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Customer</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Amount</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentPayments as $payment)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $payment->installment->customer->name }}</td>
                    <td class="py-3 px-4 font-semibold">${{ number_format($payment->amount, 2) }}</td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                            @if($payment->status == 'approved') bg-green-100 text-green-800
                            @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $payment->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    const ctx = document.getElementById('incomeChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthlyIncome->pluck('month')),
            datasets: [{
                label: 'Monthly Income',
                data: @json($monthlyIncome->pluck('total')),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    labels: { usePointStyle: true, padding: 20 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection