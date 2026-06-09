@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-gray-400 mb-1 flex items-center gap-1.5">
                <a href="{{ route('admin.products.stock') }}" class="hover:text-indigo-600 transition">{{ __('app.manage_stock') }}</a>
                <span>/</span>
                <span class="text-gray-600">{{ $product->name }}</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.product_detail') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.product_detail_subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if(request('from') === 'stock')
            <a href="{{ route('admin.products.stock') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.back_to_manage_stock') }}
            </a>
            @else
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.back_to_product_list') }}
            </a>
            @endif
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.products.edit', [$product, 'from' => request('from')]) }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                {{ __('app.edit_product') }}
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- ── Left column: Image & quick facts ── -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Image card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover rounded-xl shadow-md border border-gray-100 mb-5">
                @else
                    <div class="w-full aspect-square bg-gray-50 rounded-xl flex flex-col items-center justify-center text-gray-300 mb-5 border border-dashed border-gray-200">
                        <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-sm font-medium">{{ __('app.no_image') }}</span>
                    </div>
                @endif
                <h2 class="text-xl font-bold text-gray-900 text-center">{{ $product->name }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $product->code }}</p>
                <div class="flex flex-wrap justify-center gap-2 mt-4">
                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $product->category ?? __('app.uncategorized') }}
                    </span>
                    @if($product->brand)
                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                        {{ $product->brand }}
                    </span>
                    @endif
                    @if($product->is_active)
                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ __('app.active') }}
                    </span>
                    @else
                    <span class="px-3 py-1 inline-flex items-center gap-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ __('app.inactive') }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Stock status card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('app.stock_status') }}</div>
                @if($product->stock <= 0)
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-lg font-bold text-red-600">{{ __('app.out_of_stock') }}</span>
                    </div>
                @elseif($product->stock <= ($product->low_stock_threshold ?? 5))
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                        <span class="text-lg font-bold text-yellow-600">{{ $product->stock }} ({{ __('app.low_stock') }})</span>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-lg font-bold text-green-600">{{ $product->stock }} {{ __('app.in_stock') }}</span>
                    </div>
                @endif
            </div>

            @if($product->cpu || $product->ram || $product->storage || $product->graphics_card || $product->color || $product->warranty)
            <!-- Specs card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">{{ __('app.computer_specifications') }}</div>
                <div class="grid grid-cols-1 gap-4">
                    @if($product->cpu)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">{{ __('app.cpu') }}</div>
                            <div class="text-sm text-gray-900 font-semibold">{{ $product->cpu }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->ram)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 12a2 2 0 00-2 2v2a2 2 0 002 2h14a2 2 0 002-2v-2a2 2 0 00-2-2"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">{{ __('app.ram') }}</div>
                            <div class="text-sm text-gray-900 font-semibold">{{ $product->ram }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->storage)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">{{ __('app.storage') }}</div>
                            <div class="text-sm text-gray-900 font-semibold">{{ $product->storage }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->graphics_card)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">{{ __('app.graphics_card') }}</div>
                            <div class="text-sm text-gray-900 font-semibold">{{ $product->graphics_card }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->color)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">{{ __('app.color') }}</div>
                            <div class="text-sm text-gray-900 font-semibold">{{ $product->color }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->warranty)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="w-9 h-9 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">{{ __('app.warranty') }}</div>
                            <div class="text-sm text-gray-900 font-semibold">{{ $product->warranty }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- ── Right column: Details ── -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Pricing card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-indigo-50 to-white rounded-xl p-5 border border-indigo-100/60">
                        <div class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-1">{{ __('app.selling_price') }}</div>
                        <div class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ __('app.cost_price') }}</div>
                        <div class="text-2xl font-bold text-gray-700">${{ $product->cost_price ? number_format($product->cost_price, 2) : '—' }}</div>
                    </div>
                </div>
            </div>

            <!-- Supplier card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('app.supplier') }}</div>
                @if($product->supplier)
                    <span class="px-3 py-1.5 inline-flex items-center text-sm font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path></svg>
                        {{ $product->supplier->name }}
                    </span>
                @elseif($suppliers->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach($suppliers as $supplier)
                            <span class="px-3 py-1.5 inline-flex items-center text-sm font-medium rounded-full bg-green-50 text-green-700 border border-green-200">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path></svg>
                                {{ $supplier->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <span class="text-gray-400 italic text-sm">{{ __('app.no_supplier_recorded') }}</span>
                @endif
            </div>

            @if($purchaseHistory->count())
            <!-- Purchase history card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">{{ __('app.purchase_history') }}</div>
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-500">{{ __('app.supplier') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-500">{{ __('app.purchase_date') }}</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-500">{{ __('app.qty') }}</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-500">{{ __('app.cost_price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($purchaseHistory as $item)
                            <tr class="hover:bg-gray-50/70 transition duration-150">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ optional(optional($item->purchase)->supplier)->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ optional(optional($item->purchase)->purchase_date)->format('Y-m-d') ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 font-semibold">${{ number_format($item->cost_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
