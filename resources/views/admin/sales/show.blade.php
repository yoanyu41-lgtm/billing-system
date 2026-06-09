@extends('layouts.app')

@section('content')
<div class="content">
    {{-- Header (hidden on print) --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-receipt text-blue-600"></i>
                {{ __('app.receipt') }} — {{ $sale->invoice_no ?? ('#'.$sale->id) }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
            </a>
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-print"></i> {{ __('app.print_receipt') }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm no-print">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Receipt --}}
    <div class="card max-w-3xl mx-auto" id="receipt">
        <div class="flex items-start justify-between border-b border-dashed pb-4 mb-4">
            <div class="flex items-center gap-3">
                <img src="{{ $companyLogo }}" alt="logo" style="width:48px;height:48px;object-fit:contain;">
                <div>
                    <div class="text-lg font-extrabold text-gray-900">{{ \App\Models\Setting::where('key','company_name')->value('value') ?? 'CityTech' }}</div>
                    <div class="text-xs text-gray-500">{{ \App\Models\Setting::where('key','company_phone')->value('value') }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-gray-900">{{ __('app.receipt') }}</div>
                <div class="text-xs text-gray-500">{{ $sale->invoice_no ?? ('#'.$sale->id) }}</div>
                <div class="text-xs text-gray-500">{{ optional($sale->sale_date)->format('d M Y') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div>
                <div class="text-xs text-gray-400">{{ __('app.customer') }}</div>
                <div class="font-medium text-gray-900">{{ $sale->customer_name ?: __('app.walk_in_customer') }}</div>
                @if($sale->customer_phone)<div class="text-gray-500">{{ $sale->customer_phone }}</div>@endif
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400">{{ __('app.payment_method') }}</div>
                <div class="font-medium text-gray-900">{{ __('app.'.$sale->payment_method) }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ __('app.recorded_by') }}: {{ $sale->creator->name ?? '-' }}</div>
            </div>
        </div>

        <table class="min-w-full text-sm mb-4">
            <thead>
                <tr class="border-b">
                    <th class="py-2 text-left font-semibold text-gray-600">{{ __('app.product') }}</th>
                    <th class="py-2 text-center font-semibold text-gray-600">{{ __('app.quantity') }}</th>
                    <th class="py-2 text-right font-semibold text-gray-600">{{ __('app.unit_price') }}</th>
                    <th class="py-2 text-right font-semibold text-gray-600">{{ __('app.subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr class="border-b border-gray-100">
                        <td class="py-2 text-gray-900">{{ $item->product->name ?? '—' }}</td>
                        <td class="py-2 text-center text-gray-700">{{ $item->quantity }}</td>
                        <td class="py-2 text-right text-gray-700">${{ number_format($item->price, 2) }}</td>
                        <td class="py-2 text-right font-medium text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ml-auto w-full max-w-xs space-y-1.5 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-gray-600">{{ __('app.subtotal') }}</span>
                <span class="font-medium text-gray-900">${{ number_format($sale->subtotal, 2) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">{{ __('app.discount') }}</span>
                <span class="font-medium text-gray-900">- ${{ number_format($sale->discount, 2) }}</span>
            </div>
            <div class="flex items-center justify-between border-t pt-2 mt-1">
                <span class="font-semibold text-gray-900">{{ __('app.grand_total') }}</span>
                <span class="text-xl font-extrabold text-blue-600">${{ number_format($sale->total, 2) }}</span>
            </div>
        </div>

        @if($sale->note)
            <div class="mt-4 text-xs text-gray-500 border-t border-dashed pt-3">{{ $sale->note }}</div>
        @endif

        <div class="mt-6 text-center text-xs text-gray-400 border-t border-dashed pt-4">
            {{ __('app.thank_you_purchase') }}
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    #sidebar, .topbar, aside { display: none !important; }
    body, .content { margin: 0 !important; padding: 0 !important; background: #fff !important; }
    #receipt { box-shadow: none !important; border: none !important; max-width: 100% !important; }
}
</style>
@endsection
