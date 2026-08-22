@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ផលិតផល' : 'Product Report' }}
            </h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                {{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទបង្ហាញ:' : 'Reporting Period:' }} 
                <span class="font-semibold text-slate-700">
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </span>
            </p>
        </div>
        
        @include('admin.reports._nav')
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.product', ['filter' => 'daily', 'category' => $category ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}
            </a>
            <a href="{{ route('admin.reports.product', ['filter' => 'this_week', 'category' => $category ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ ($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}
            </a>
            <a href="{{ route('admin.reports.product', ['filter' => 'monthly', 'category' => $category ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
            </a>
            <a href="{{ route('admin.reports.product', ['filter' => 'yearly', 'category' => $category ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reports.product') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="filter" value="custom">
            
            <div class="relative min-w-[200px]">
                <input type="text" name="search" id="product-search-input" value="{{ $search ?? $category ?? '' }}" autocomplete="off"
                       placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកឈ្មោះ / កូដ / ប្រភេទ...' : 'Search name / code / category...' }}" 
                       class="w-full px-3 py-2 pr-8 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
                
                @if(!empty($search) || !empty($category))
                <button type="button" onclick="clearProductSearch(this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition" title="Clear">
                    <i class="fas fa-times text-xs"></i>
                </button>
                @endif

                <div id="product-suggestions-box" class="hidden absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 max-h-56 overflow-y-auto divide-y divide-slate-100"></div>
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span>{{ app()->getLocale() === 'km' ? 'ចាប់ពី:' : 'From' }}</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span>{{ app()->getLocale() === 'km' ? 'ដល់:' : 'To' }}</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <button type="submit" class="px-4 py-2 bg-[#0b1f3a] hover:bg-[#07162b] text-white font-bold rounded-xl text-xs transition shadow-sm border-0 cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-search text-[11px]"></i> {{ app()->getLocale() === 'km' ? 'ស្វែងរក' : 'Search' }}
            </button>

            <button type="button" onclick="printReportDirect('{{ route('admin.reports.print', ['type' => 'product', 'start_date' => $startDate, 'end_date' => $endDate, 'category' => $category ?? '', 'search' => $search ?? '', 'filter' => $filter ?? '']) }}')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> {{ app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print' }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 text-lg">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ផលិតផលសរុប' : 'TOTAL PRODUCTS' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($totalProducts) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-purple-500/20 text-lg">
                <i class="fas fa-warehouse"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ស្តុកសរុប' : 'TOTAL STOCK' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($inStock) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20 text-lg">
                <i class="fas fa-cart-arrow-down"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ចំនួនលក់ចេញ' : 'SOLD QUANTITY' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($totalSoldQty) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-teal-500/20 text-lg">
                <i class="fas fa-money-bill-trend-up"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ចំណូលសរុប' : 'TOTAL REVENUE' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">${{ number_format(collect($products)->sum('total_revenue'), 2) }}</div>
            </div>
        </div>
    </div>

    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'តារាងរបាយការណ៍ផលិតផល' : 'Product Report List' }}
            </h2>
            <span class="text-xs font-medium text-slate-500">
                {{ app()->getLocale() === 'km' ? 'សរុប:' : 'Total:' }} <strong class="text-slate-800 font-bold">{{ count($products) }}</strong> {{ app()->getLocale() === 'km' ? 'មុខទំនិញ' : 'items' }}
            </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold text-xs tracking-wide">
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ឈ្មោះទំនិញ' : 'Product Name' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ប្រភេទ' : 'Category' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'តម្លៃលក់' : 'Price' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ថ្លៃដើម' : 'Cost Price' }}</th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'លក់ចេញ' : 'Sold Qty' }}</th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ក្នុងស្តុក' : 'Stock' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ចំណូលសរុប' : 'Revenue' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2.5">
                                @if($p->image)
                                    <img src="{{ asset('storage/' . $p->image) }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center text-xs">
                                        <i class="fas fa-box"></i>
                                    </div>
                                @endif
                                <div>
                                    <span class="block font-bold text-slate-900">{{ $p->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono font-medium">Code: {{ $p->code ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $p->category ?: 'General' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                ${{ number_format($p->price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-600">
                                ${{ number_format($p->cost_price ?? 0, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                {{ $p->sold_qty }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($p->stock <= 5)
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#fee2e2] text-[#dc2626]">
                                        {{ $p->stock }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#dcfce7] text-[#16a34a]">
                                        {{ $p->stock }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                ${{ number_format($p->total_revenue, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                                {{ app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យផលិតផលទេ' : 'No product records found.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($products) > 0)
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900">
                        <tr>
                            <td colspan="4" class="py-3.5 px-4 text-right uppercase text-[11px] text-slate-500 font-bold">
                                {{ app()->getLocale() === 'km' ? 'សរុបរួម:' : 'Grand Total:' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-black">
                                {{ number_format(collect($products)->sum('sold_qty')) }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-black">
                                {{ number_format(collect($products)->sum('stock')) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                ${{ number_format(collect($products)->sum('total_revenue'), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    function clearProductSearch(btn) {
        const input = document.getElementById('product-search-input');
        if (input) {
            input.value = '';
            input.closest('form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const suggestions = @json($suggestions ?? []);
        const input = document.getElementById('product-search-input');
        const box = document.getElementById('product-suggestions-box');

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
                    <div class="product-suggestion-item px-3.5 py-2 hover:bg-slate-50 cursor-pointer text-xs text-slate-700 transition font-medium" data-value="${escapeHtml(match.value)}">
                        <i class="fas fa-box text-slate-400 mr-1.5 text-[10px]"></i> ${escapeHtml(match.label)}
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
            if (this.value.trim() === '' && (urlParams.has('search') || urlParams.has('category'))) {
                this.closest('form').submit();
            }
        });

        input.addEventListener('focus', function() {
            filterSuggestions(this.value);
        });

        box.addEventListener('click', function(e) {
            const item = e.target.closest('.product-suggestion-item');
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
