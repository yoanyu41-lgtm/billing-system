@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 mb-1">
                <i class="fas fa-boxes-stacked"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ __('app.product_report') }}
            </h1>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Header Controls & Search Filter (Standard Component) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition border-0 cursor-pointer">
                    <i class="fas fa-print"></i> {{ __('app.print') }}
                </button>
            </div>
        </div>

        <!-- Filter Bar (Image 1 Standard Component) -->
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <div class="inline-flex items-center p-1 bg-slate-100/90 rounded-2xl text-sm font-semibold border border-slate-200/50">
                <a href="{{ route('admin.reports.product', ['filter' => 'today']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'today' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ __('app.today') }}</a>
                <a href="{{ route('admin.reports.product', ['filter' => 'this_week']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_week' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ __('app.this_week') }}</a>
                <a href="{{ route('admin.reports.product', ['filter' => 'this_month']) }}" class="px-4 py-2 rounded-xl transition no-underline {{ ($filter ?? '') === 'this_month' || empty($filter) ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">{{ __('app.this_month') }}</a>
            </div>

            <form method="GET" action="{{ route('admin.reports.product') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="category" value="{{ $category }}" placeholder="{{ __('app.category') ?? 'ប្រភេទ' }}..." class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 w-44 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <input type="hidden" name="filter" value="custom">
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="text-sm font-medium text-slate-400 px-1">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-sm border-0 cursor-pointer flex items-center justify-center">
                    {{ __('app.filter') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Cards (4 Standard Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 border border-blue-100">
                <i class="fas fa-box"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.total_catalog_items') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalProducts) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 border border-emerald-100">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.total_sold_units') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalSoldQty) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-100">
                <i class="fas fa-boxes-packing"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.available_stock') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($inStock) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-100">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">{{ __('app.low_stock_warning') }}</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($lowStock) }}</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">{{ __('app.product_name') }}</th>
                        <th class="py-3.5 px-6">{{ __('app.category') ?? 'ប្រភេទ' }}</th>
                        <th class="py-3.5 px-6 text-center">{{ __('app.total_sold_units') }}</th>
                        <th class="py-3.5 px-6 text-center">{{ __('app.available_stock') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('app.revenue_generated') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($products as $p)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900 flex items-center gap-3">
                            @if($p->image)
                                <img src="{{ asset('storage/' . $p->image) }}" class="w-9 h-9 rounded-lg object-cover border border-slate-100">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center text-xs">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            <div>
                                <span>{{ $p->name }}</span>
                                <span class="text-xs text-slate-400 block font-normal">Code: {{ $p->code ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-600">
                            {{ $p->category ?: (__('app.uncategorized') ?? 'គ្មានប្រភេទ') }}
                        </td>
                        <td class="py-4 px-6 text-center font-bold text-slate-800">
                            {{ $p->sold_qty }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($p->stock <= 5)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                    {{ $p->stock }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    {{ $p->stock }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right font-black text-slate-900">
                            ${{ number_format($p->total_revenue, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                            {{ __('app.no_data') ?? 'គ្មានទិន្នន័យ' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
