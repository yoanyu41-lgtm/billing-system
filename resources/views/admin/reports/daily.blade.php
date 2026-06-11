@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.daily_report') ?? 'Daily Report' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">{{ __('app.search') }}</button>
            <a href="{{ route('admin.reports.export', ['type' => 'daily', 'date' => $date]) }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </form>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.installment') }}</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">${{ number_format($total, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.direct_sale') }}</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($salesTotal, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 bg-blue-50 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.grand_total') }}</p>
            <p class="text-2xl font-extrabold text-blue-700 mt-1">${{ number_format($grandTotal, 2) }}</p>
        </div>
    </div>

    {{-- Installment payments --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">{{ __('app.payments') }}</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.customer') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.amount') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                <tr>
                    <td class="px-5 py-3 text-gray-900">{{ $payment->installment->customer->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-right font-semibold">${{ number_format($payment->amount, 2) }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">{{ __('app.'.$payment->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-400">{{ __('app.no_payments') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Direct sales --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">{{ __('app.direct_sales') }}</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.invoice_no') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.customer') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.total') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sales as $sale)
                <tr>
                    <td class="px-5 py-3 font-semibold text-blue-600">{{ $sale->invoice_no ?? ('#'.$sale->id) }}</td>
                    <td class="px-5 py-3 text-gray-900">{{ $sale->customer_name ?: __('app.walk_in_customer') }}</td>
                    <td class="px-5 py-3 text-right font-semibold">${{ number_format($sale->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-400">{{ __('app.no_sales_yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
