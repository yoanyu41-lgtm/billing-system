@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900">Staff Dashboard</h1>
    <p class="text-gray-600 mt-2">Your daily overview and quick actions</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-blue-100 text-sm font-semibold">My Customers</p>
                <p class="text-4xl font-bold mt-2">{{ $customers }}</p>
            </div>
            <i class="fas fa-users text-4xl opacity-20"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-green-100 text-sm font-semibold">Payments Today</p>
                <p class="text-4xl font-bold mt-2">{{ $paymentsToday }}</p>
            </div>
            <i class="fas fa-credit-card text-4xl opacity-20"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-yellow-100 text-sm font-semibold">Pending Payments</p>
                <p class="text-4xl font-bold mt-2">{{ $pendingPayments }}</p>
            </div>
            <i class="fas fa-clock text-4xl opacity-20"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-red-100 text-sm font-semibold">Late Customers</p>
                <p class="text-4xl font-bold mt-2">{{ $lateCustomers }}</p>
            </div>
            <i class="fas fa-exclamation-triangle text-4xl opacity-20"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🚀 Quick Actions</h2>
        <div class="space-y-3">
            <a href="{{ route('customers.create') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-lg transition duration-200">
                <i class="fas fa-plus-circle text-xl"></i>
                <span class="font-semibold">Add New Customer</span>
                <i class="fas fa-arrow-right ml-auto"></i>
            </a>
            <a href="{{ route('payments.create') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:shadow-lg transition duration-200">
                <i class="fas fa-money-bill-wave text-xl"></i>
                <span class="font-semibold">Record Payment</span>
                <i class="fas fa-arrow-right ml-auto"></i>
            </a>
            <a href="{{ route('installments.create') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition duration-200">
                <i class="fas fa-file-contract text-xl"></i>
                <span class="font-semibold">Create Installment</span>
                <i class="fas fa-arrow-right ml-auto"></i>
            </a>
            <a href="{{ route('late-payments.index') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:shadow-lg transition duration-200">
                <i class="fas fa-bell text-xl"></i>
                <span class="font-semibold">Check Late Payments</span>
                <i class="fas fa-arrow-right ml-auto"></i>
            </a>
        </div>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 Tasks Today</h2>
        <div class="space-y-4">
            <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                <i class="fas fa-check-circle text-blue-600 text-xl mt-1"></i>
                <div>
                    <p class="font-semibold text-gray-900">Process Pending Payments</p>
                    <p class="text-sm text-gray-600">{{ $pendingPayments }} payments awaiting approval</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                <i class="fas fa-clock text-yellow-600 text-xl mt-1"></i>
                <div>
                    <p class="font-semibold text-gray-900">Follow up with Late Customers</p>
                    <p class="text-sm text-gray-600">{{ $lateCustomers }} customers need reminders</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                <i class="fas fa-users text-green-600 text-xl mt-1"></i>
                <div>
                    <p class="font-semibold text-gray-900">Manage Your Customers</p>
                    <p class="text-sm text-gray-600">You manage {{ $customers }} customers total</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection