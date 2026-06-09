@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-gray-400 mb-1 flex items-center gap-1.5">
                <a href="{{ route('admin.purchases.index') }}" class="hover:text-indigo-600 transition">{{ __('app.purchase_history') }}</a>
                <span>/</span>
                <span class="text-gray-600">#{{ $purchase->id }}</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.purchase_detail') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.purchase_detail_subtitle') }} #{{ $purchase->id }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.back_to_purchase_history') }}
            </a>
            <a href="{{ route('admin.purchases.edit', $purchase) }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                {{ __('app.edit') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ __('app.supplier') }}</div>
            <div class="text-lg font-bold text-gray-900">{{ $purchase->supplier?->name ?? '—' }}</div>
            @if($purchase->supplier?->phone)
                <div class="text-xs text-gray-500 mt-0.5">{{ $purchase->supplier->phone }}</div>
            @endif
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ __('app.purchase_date') }}</div>
            <div class="text-lg font-bold text-gray-900">{{ $purchase->purchase_date?->format('Y-m-d') ?? $purchase->created_at->format('Y-m-d') }}</div>
        </div>
        <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl shadow-sm border border-indigo-100/60 p-5">
            <div class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-1">{{ __('app.total') }}</div>
            <div class="text-2xl font-bold text-gray-900">${{ number_format($purchase->total, 2) }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">{{ __('app.purchase_items') }}</div>
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">{{ __('app.product') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-500">{{ __('app.quantity') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-500">{{ __('app.cost_price') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-500">{{ __('app.subtotal') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($purchase->items as $item)
                    <tr class="hover:bg-gray-50/70 transition duration-150">
                        <td class="px-4 py-3">
                            <div class="text-gray-900 font-medium">{{ $item->product->name ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $item->product->code ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">${{ number_format($item->cost_price ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-900 font-semibold">${{ number_format(($item->cost_price ?? 0) * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-600">{{ __('app.total') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($purchase->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
