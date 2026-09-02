@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                {{ __('app.late_payments') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('app.late_payments_desc') }}
            </p>
        </div>
        <div>
            <form method="POST" action="{{ route('late-payments.due-reminders') }}">
                @csrf
                <button type="submit" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-sm border-0 cursor-pointer"
                >
                    <i class="fas fa-paper-plane text-base"></i>
                    <span>{{ __('app.send_due_date_reminders_today') }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notices -->
    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle mr-2 text-green-500 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-circle mr-2 text-red-500 text-lg"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Payments Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('app.customer') }}
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('app.product') }}
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('app.remaining_balance') }}
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('app.days_late') }}
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }}
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('app.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($lateInstallments as $installment)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-semibold text-sm">
                                    {{ substr($installment->customer?->name ?? 'N/A', 0, 2) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $installment->customer?->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-phone-alt mr-1 text-slate-400"></i>{{ $installment->customer?->phone ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="max-w-[150px] lg:max-w-[170px] truncate" title="{{ $installment->product?->name ?? 'N/A' }}">
                                {{ $installment->product?->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-rose-600">
                                ${{ number_format($installment->remaining_balance, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-amber-600">
                                {{ $installment->daysLate() }} {{ app()->getLocale() === 'km' ? 'ថ្ងៃ' : 'days' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-red-600">
                                ${{ number_format($installment->calculatePenalty(), 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form method="POST" action="{{ route('late-payments.remind', $installment) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-xs transition duration-150 border-0 cursor-pointer"
                                >
                                    <i class="fas fa-bell"></i>
                                    <span>{{ __('app.send_reminder') }}</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400 gap-3">
                                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">
                                    {{ __('app.all_payments_up_to_date') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection