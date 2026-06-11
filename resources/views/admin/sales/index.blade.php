@extends('layouts.app')

@section('content')
<div class="content">
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-cash-register text-blue-600"></i>
                {{ __('app.sales_list') }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ __('app.sales_list_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.sales.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> {{ __('app.new_direct_sale') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <form method="GET" class="mb-4">
        <div class="flex items-center gap-2 max-w-md">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="{{ __('app.invoice_no') }} / {{ __('app.customer_name') }} / {{ __('app.customer_phone') }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button class="px-4 py-2.5 text-sm bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    <div class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.invoice_no') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.customer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.sale_date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.product') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.payment_method') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">{{ $sale->invoice_no ?? ('#'.$sale->id) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $sale->customer_name ?: __('app.walk_in_customer') }}
                                @if($sale->customer_phone)<div class="text-xs text-gray-400">{{ $sale->customer_phone }}</div>@endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ optional($sale->sale_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @foreach($sale->items as $item)
                                    <div class="flex items-center gap-2 {{ !$loop->last ? 'mb-1' : '' }}">
                                        <span>{{ $item->product->name ?? '—' }}</span>
                                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">x{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">${{ number_format($sale->total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                                    {{ \Illuminate\Support\Facades\Lang::has('app.'.$sale->payment_method) ? __('app.'.$sale->payment_method) : $sale->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.sales.show', $sale) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-eye"></i> {{ __('app.view_receipt') }}
                                </a>
                                <form action="{{ route('admin.sales.destroy', $sale) }}" method="POST" class="inline"
                                      onsubmit="return confirm('{{ __('app.confirm_delete_sale') }}')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                {{ __('app.no_sales_yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>
</div>
@endsection
