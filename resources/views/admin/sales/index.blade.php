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
        @if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.create'))
        <a href="{{ route('admin.sales.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> {{ __('app.new_direct_sale') }}
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <form method="GET" class="mb-4">
        <div class="flex items-center gap-2 max-w-md">
            <div class="relative flex-1">
                <input type="text" name="q" id="search-input" value="{{ request('q') }}" autocomplete="off"
                       placeholder="{{ __('app.invoice_no') }} / {{ __('app.customer_name') }} / {{ __('app.customer_phone') }}"
                       class="w-full pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                @if(request('q'))
                <button type="button" onclick="clearSearchInput(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" title="Clear">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif

                <div id="suggestions-box" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"></div>
            </div>
            <button type="submit" class="px-4 py-2.5 text-sm bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                <i class="fas fa-search"></i>
            </button>
            @if(request('q'))
            <a href="{{ route('admin.sales.index') }}" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors text-center shrink-0">
                {{ __('app.clear') }}
            </a>
            @endif
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
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <span class="font-bold text-gray-900">{{ format_currency($sale->total, $exchangeRate) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                                    {{ \Illuminate\Support\Facades\Lang::has('app.'.$sale->payment_method) ? __('app.'.$sale->payment_method) : $sale->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                <a href="{{ route('admin.sales.show', [$sale, 'from' => request('from')]) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 hover:text-blue-800 transition" 
                                   title="{{ __('app.view_receipt') }}">
                                    <i class="fas fa-eye text-base"></i>
                                </a>
                                @if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.delete'))
                                <form action="{{ route('admin.sales.destroy', $sale) }}" method="POST" class="inline"
                                      onsubmit="return confirm('{{ __('app.confirm_delete_sale') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-800 transition"
                                            title="{{ __('app.delete') ?? 'Delete' }}">
                                        <i class="fas fa-trash-alt text-base"></i>
                                    </button>
                                </form>
                                @endif
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

<script>
    function clearSearchInput(btn) {
        const input = document.getElementById('search-input');
        if (input) {
            input.value = '';
            input.closest('form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const suggestions = @json($suggestions ?? []);
        const input = document.getElementById('search-input');
        const box = document.getElementById('suggestions-box');

        if (!input || !box) return;

        function filterSuggestions(val) {
            if (!val || val.trim().length < 1) {
                box.innerHTML = '';
                box.classList.add('hidden');
                return;
            }

            const query = val.toLowerCase();
            const matches = suggestions.filter(item => 
                item.label.toLowerCase().includes(query) || 
                item.value.toLowerCase().includes(query)
            ).slice(0, 8);

            if (matches.length === 0) {
                box.innerHTML = '';
                box.classList.add('hidden');
                return;
            }

            box.innerHTML = matches.map(match => {
                return `
                    <div class="suggestion-item px-4 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700 transition duration-150 font-medium border-b border-gray-50 last:border-0" data-value="${escapeHtml(match.value)}">
                        ${escapeHtml(match.label)}
                    </div>
                `;
            }).join('');

            box.classList.remove('hidden');
        }

        function escapeHtml(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        input.addEventListener('input', function() {
            filterSuggestions(this.value);
            const urlParams = new URLSearchParams(window.location.search);
            if (this.value.trim() === '' && urlParams.has('q') && urlParams.get('q') !== '') {
                this.closest('form').submit();
            }
        });

        input.addEventListener('focus', function() {
            filterSuggestions(this.value);
        });

        box.addEventListener('click', function(e) {
            const item = e.target.closest('.suggestion-item');
            if (item) {
                input.value = item.getAttribute('data-value');
                box.classList.add('hidden');
                input.closest('form').submit();
            }
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !box.contains(e.target)) {
                box.classList.add('hidden');
            }
        });
    });
</script>
@endsection
