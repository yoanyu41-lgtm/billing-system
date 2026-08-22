@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍អតិថិជន' : 'Customer Report' }}
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
            <a href="{{ route('admin.reports.customer', ['filter' => 'daily', 'search' => $search ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}
            </a>
            <a href="{{ route('admin.reports.customer', ['filter' => 'this_week', 'search' => $search ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ ($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}
            </a>
            <a href="{{ route('admin.reports.customer', ['filter' => 'monthly', 'search' => $search ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
            </a>
            <a href="{{ route('admin.reports.customer', ['filter' => 'yearly', 'search' => $search ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reports.customer') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="filter" value="custom">
            
            <div class="relative min-w-[200px]">
                <input type="text" name="search" id="customer-search-input" value="{{ $search ?? '' }}" autocomplete="off"
                       placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកឈ្មោះ / លេខ...' : 'Search name / phone...' }}" 
                       class="w-full px-3 py-2 pr-8 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
                
                @if(!empty($search))
                <button type="button" onclick="clearCustomerSearch(this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition" title="Clear">
                    <i class="fas fa-times text-xs"></i>
                </button>
                @endif

                <div id="customer-suggestions-box" class="hidden absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 max-h-56 overflow-y-auto divide-y divide-slate-100"></div>
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

            <button type="button" onclick="printReportDirect('{{ route('admin.reports.print', ['type' => 'customer', 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search ?? '', 'filter' => $filter ?? '']) }}')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> {{ app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print' }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 text-lg">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'អតិថិជនសរុប' : 'TOTAL CUSTOMERS' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($totalCustomers) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20 text-lg">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'អតិថិជនថ្មី' : 'NEW CUSTOMERS' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($newCustomers) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20 text-lg">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'កំពុងបង់រំលស់' : 'ACTIVE BORROWERS' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($activeCustomers) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-teal-500/20 text-lg">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'បានបញ្ចប់ការបង់' : 'COMPLETED' }}</span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($completedCustomers) }}</div>
            </div>
        </div>
    </div>

    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'តារាងរបាយការណ៍អតិថិជន' : 'Customer Report List' }}
            </h2>
            <span class="text-xs font-medium text-slate-500">
                {{ app()->getLocale() === 'km' ? 'សរុប:' : 'Total:' }} <strong class="text-slate-800 font-bold">{{ count($customerList) }}</strong> {{ app()->getLocale() === 'km' ? 'នាក់' : 'customers' }}
            </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold text-xs tracking-wide">
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customer' }}</th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'លេខទូរស័ព្ទ' : 'Phone' }}</th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ចំនួនកិច្ចសន្យា' : 'Contracts' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ការទិញសរុប' : 'Total Purchase' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'បានបង់' : 'Paid' }}</th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'នៅខ្វះ' : 'Outstanding' }}</th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap">{{ app()->getLocale() === 'km' ? 'ស្ថានភាព' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($customerList as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $c->name }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono font-medium">
                                {{ $c->phone }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                {{ $c->contracts }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                ${{ number_format($c->total_purchase, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-600">
                                ${{ number_format($c->paid, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-rose-600">
                                ${{ number_format($c->outstanding, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($c->status === 'Active')
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#e0f2fe] text-[#0284c7]">
                                        {{ app()->getLocale() === 'km' ? 'សកម្ម' : 'Active' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#dcfce7] text-[#16a34a]">
                                        {{ app()->getLocale() === 'km' ? 'បានបញ្ចប់' : 'Completed' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                                {{ app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យអតិថិជនទេ' : 'No customer records found.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($customerList) > 0)
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3.5 px-4 text-right uppercase text-[11px] text-slate-500 font-bold">
                                {{ app()->getLocale() === 'km' ? 'សរុបរួម:' : 'Grand Total:' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                ${{ number_format(collect($customerList)->sum('total_purchase'), 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-600">
                                ${{ number_format(collect($customerList)->sum('paid'), 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-rose-600">
                                ${{ number_format(collect($customerList)->sum('outstanding'), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    function clearCustomerSearch(btn) {
        const input = document.getElementById('customer-search-input');
        if (input) {
            input.value = '';
            input.closest('form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const suggestions = @json($suggestions ?? []);
        const input = document.getElementById('customer-search-input');
        const box = document.getElementById('customer-suggestions-box');

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
                    <div class="customer-suggestion-item px-3.5 py-2 hover:bg-slate-50 cursor-pointer text-xs text-slate-700 transition font-medium" data-value="${escapeHtml(match.value)}">
                        <i class="fas fa-user text-slate-400 mr-1.5 text-[10px]"></i> ${escapeHtml(match.label)}
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
            if (this.value.trim() === '' && urlParams.has('search') && urlParams.get('search') !== '') {
                this.closest('form').submit();
            }
        });

        input.addEventListener('focus', function() {
            filterSuggestions(this.value);
        });

        box.addEventListener('click', function(e) {
            const item = e.target.closest('.customer-suggestion-item');
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
