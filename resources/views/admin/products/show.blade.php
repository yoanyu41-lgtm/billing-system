@extends('layouts.app')

@section('content')
@php
    $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') ?? '0';
    $hasValidImage = $product->image && !\Illuminate\Support\Str::contains(strtolower($product->image), 'undefined');
    $imgSrc = $hasValidImage ? (\Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : asset('storage/' . $product->image)) : null;
    $unitCost = $product->cost_price ?? $product->price;
    $stockValue = (float)$unitCost * (int)$product->stock;
    $profit = $product->cost_price ? ($product->price - $product->cost_price) : null;
    $isKm = app()->getLocale() === 'km';
@endphp

<div class="container mx-auto px-4 py-8 max-w-6xl">

    {{-- ── Top Nav Bar ── --}}
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <nav class="flex items-center gap-2 text-sm text-gray-400">
            @if(request('from') === 'stock')
                <a href="{{ route('admin.products.stock') }}" class="hover:text-indigo-600 transition font-medium">{{ __('app.manage_stock') }}</a>
            @else
                <a href="{{ route('admin.products.index') }}" class="hover:text-indigo-600 transition font-medium">{{ __('app.products') }}</a>
            @endif
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-600 font-medium truncate max-w-xs">{{ $product->name }}</span>
        </nav>
        <div class="flex items-center gap-2">
            @if(request('from') === 'stock')
                <a href="{{ route('admin.products.stock') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('app.back_to_manage_stock') }}
                </a>
            @else
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('app.back_to_product_list') }}
                </a>
            @endif
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.products.edit', [$product, 'from' => request('from')]) }}" class="inline-flex items-center gap-1.5 text-sm bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                {{ __('app.edit_product') }}
            </a>
            @endif
        </div>
    </div>

    {{-- ── Hero Card ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
        <div class="flex flex-col md:flex-row">
            {{-- Image --}}
            <div class="md:w-64 lg:w-72 shrink-0 bg-gradient-to-br from-indigo-50 to-slate-50 flex items-center justify-center p-6 border-b md:border-b-0 md:border-r border-gray-100">
                @if($imgSrc)
                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}"
                         class="w-52 h-52 object-cover rounded-xl shadow-md border border-gray-100"
                         onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&color=4F46E5&background=EEF2FF&bold=true&size=200'">
                @else
                    <div class="w-52 h-52 bg-indigo-50 rounded-xl border border-dashed border-indigo-200 flex flex-col items-center justify-center text-indigo-300">
                        <svg class="w-14 h-14 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-medium text-indigo-300">{{ __('app.no_image') }}</span>
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="flex-1 p-6 lg:p-8">
                {{-- Badges --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if($product->category)
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $product->category }}
                    </span>
                    @endif
                    @if($product->condition)
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-100">
                        {{ $product->condition === 'new' ? ($isKm ? 'ថ្មី 100%' : 'Brand New') : ucfirst($product->condition) }}
                    </span>
                    @endif
                    @if($product->is_active)
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-100 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ __('app.active') }}
                    </span>
                    @else
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ __('app.inactive') }}
                    </span>
                    @endif
                </div>

                {{-- Name --}}
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-1">{{ $product->name }}</h1>
                @if($product->name2)
                <p class="text-sm text-gray-500 mb-3">{{ $product->name2 }}</p>
                @endif

                {{-- Codes --}}
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        {{ $isKm ? 'កូដ' : 'Item Code' }}: {{ $product->code }}
                    </span>
                    @if($product->barcode)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-mono border border-gray-200">
                        🏷️ {{ $isKm ? 'បារកូដ' : 'Barcode' }}: {{ $product->barcode }}
                    </span>
                    @endif
                    @if($product->supplier)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-semibold border border-green-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $product->supplier->name }}
                    </span>
                    @endif
                </div>

                {{-- Spec Tags --}}
                @if($product->cpu || $product->ram || $product->storage)
                <div class="flex flex-wrap gap-1.5 mt-4">
                    @if($product->cpu)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">CPU: {{ $product->cpu }}</span>
                    @endif
                    @if($product->ram)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">RAM: {{ $product->ram }}</span>
                    @endif
                    @if($product->storage)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">{{ $isKm ? 'ឧបករណ៍ផ្ទុក' : 'Storage' }}: {{ $product->storage }}</span>
                    @endif
                    @if($product->graphics_card)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">GPU: {{ $product->graphics_card }}</span>
                    @endif
                    @if($product->warranty)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-teal-50 text-teal-700 border border-teal-100">🛡️ {{ $product->warranty }}</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Stats Cards Row ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Price --}}
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-5 text-white shadow-sm shadow-indigo-200">
            <div class="text-xs font-semibold text-indigo-200 uppercase tracking-wider mb-2">
                {{ $isKm ? 'តម្លៃលក់' : 'Price' }}
            </div>
            <div class="text-2xl font-bold">${{ number_format($product->price, 2) }}</div>
            <div class="text-xs text-indigo-200 mt-1">{{ $isKm ? 'ក្នុង ១ ឯកតា' : 'per unit' }}</div>
        </div>

        {{-- Supply Price --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                {{ $isKm ? 'តម្លៃដើម' : 'Supply Price' }}
            </div>
            <div class="text-2xl font-bold text-gray-800">
                {{ $product->cost_price ? '$' . number_format($product->cost_price, 2) : '—' }}
            </div>
            @if($profit !== null)
            <div class="text-xs mt-1 {{ $profit >= 0 ? 'text-green-500' : 'text-red-500' }} font-medium">
                {{ $profit >= 0 ? '▲' : '▼' }} ${{ number_format(abs($profit), 2) }} {{ $isKm ? 'ចំណេញ' : 'profit' }}
            </div>
            @else
            <div class="text-xs text-gray-400 mt-1">{{ $isKm ? 'មិនបានបញ្ចូល' : 'not set' }}</div>
            @endif
        </div>

        {{-- Stock Qty --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                {{ $isKm ? 'ចំនួនស្តុក' : 'Stock Qty.' }}
            </div>
            <div class="text-2xl font-bold
                @if($product->stock <= 0) text-red-600
                @elseif($product->stock <= ($product->low_stock_threshold ?? 5)) text-amber-600
                @else text-green-600 @endif">
                {{ $product->stock }}
            </div>
            <div class="text-xs mt-1
                @if($product->stock <= 0) text-red-400
                @elseif($product->stock <= ($product->low_stock_threshold ?? 5)) text-amber-400
                @else text-green-400 @endif font-medium">
                @if($product->stock <= 0) {{ __('app.out_of_stock') }}
                @elseif($product->stock <= ($product->low_stock_threshold ?? 5)) {{ __('app.low_stock') }}
                @else {{ __('app.in_stock') }} @endif
            </div>
        </div>

        {{-- Stock Value --}}
        <div class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl p-5 border border-emerald-100 shadow-sm">
            <div class="text-xs font-semibold text-emerald-500 uppercase tracking-wider mb-2">
                {{ $isKm ? 'តម្លៃស្តុកសរុប' : 'Stock Value' }}
            </div>
            <div class="text-2xl font-bold text-emerald-700">${{ number_format($stockValue, 2) }}</div>
            <div class="text-xs text-emerald-400 mt-1">
                @if($product->cost_price)
                    ${{ number_format($product->cost_price, 2) }} × {{ $product->stock }}
                @else
                    ${{ number_format($product->price, 2) }} × {{ $product->stock }}
                @endif
            </div>
        </div>
    </div>

    {{-- ── Main Grid ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left Column ── --}}
        <div class="space-y-6">

            {{-- Stock Detail --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ $isKm ? 'ព័ត៌មានស្តុក' : 'Stock Details' }}
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">{{ $isKm ? 'ចំនួនបច្ចុប្បន្ន' : 'Current Stock' }}</span>
                        <span class="text-sm font-bold text-gray-800">{{ $product->stock }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">{{ $isKm ? 'ស្តុកទាប' : 'Low Stock Alert' }}</span>
                        <span class="text-sm font-semibold text-amber-600">{{ $product->low_stock_threshold ?? 5 }}</span>
                    </div>
                    @if($product->max_stock_qty !== null)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">{{ $isKm ? 'ស្តុកអតិបរមា' : 'Max Stock' }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $product->max_stock_qty }}</span>
                    </div>
                    @endif
                    @if($product->unit)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">{{ $isKm ? 'ឯកតា' : 'Unit' }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $product->unit }}</span>
                    </div>
                    @endif
                    @if($product->exchange_unit)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">{{ $isKm ? 'ឯកតាប្ដូរ' : 'Exchange Unit' }}</span>
                        <span class="text-sm font-semibold text-indigo-600">{{ $product->exchange_unit }}</span>
                    </div>
                    @endif
                    @if($product->last_stock_in_at)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-500">{{ $isKm ? 'ស្តុកចូលចុងក្រោយ' : 'Last Stock In' }}</span>
                        <span class="text-xs font-semibold text-gray-700">{{ \Carbon\Carbon::parse($product->last_stock_in_at)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Supplier --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ __('app.supplier') }}
                </div>
                @if($product->supplier)
                    <div class="flex items-center gap-3 bg-green-50 rounded-xl px-4 py-3 border border-green-100">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($product->supplier->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-semibold text-green-800">{{ $product->supplier->name }}</span>
                    </div>
                @elseif($suppliers->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach($suppliers as $supplier)
                        <div class="flex items-center gap-2 bg-green-50 rounded-xl px-3 py-2 border border-green-100">
                            <div class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($supplier->name, 0, 1)) }}
                            </div>
                            <span class="text-xs font-medium text-green-800">{{ $supplier->name }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">{{ __('app.no_supplier_recorded') }}</p>
                @endif
            </div>

            {{-- VAT --}}
            @if($taxEnabled == '1')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                    VAT
                </div>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">{{ __('app.taxable') }}</span>
                        <span class="text-xs font-bold {{ $product->is_taxable ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $product->is_taxable ? __('app.yes') : __('app.no') }}
                        </span>
                    </div>
                    @if($product->is_taxable)
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">{{ __('app.tax_rate') }}</span>
                        <span class="text-xs font-bold text-indigo-600">{{ (float)$product->tax_rate }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">{{ __('app.tax_type') }}</span>
                        <span class="text-xs font-bold text-gray-700">
                            {{ $product->tax_type === 'inclusive' ? __('app.tax_inclusive') : __('app.tax_exclusive') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- ── Right Column ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Description --}}
            @if($product->description || $product->summary)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $isKm ? 'ការពិពណ៌នា' : 'Description' }}
                </div>
                @if($product->summary)
                <p class="text-sm text-gray-600 leading-relaxed mb-3">{{ $product->summary }}</p>
                @endif
                @if($product->description)
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                @endif
            </div>
            @endif

            {{-- Extended Info --}}
            @if($product->imei || $product->stock_note || $product->name2 || $product->seo)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $isKm ? 'ព័ត៌មានបន្ថែម' : 'Additional Info' }}
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if($product->name2)
                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                        <div class="text-xs text-gray-400 font-medium mb-1">{{ $isKm ? 'ឈ្មោះ២' : 'Secondary Name' }}</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $product->name2 }}</div>
                    </div>
                    @endif
                    @if($product->imei)
                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                        <div class="text-xs text-gray-400 font-medium mb-1">IMEI / Serial No.</div>
                        <div class="text-sm font-mono font-semibold text-gray-800">{{ $product->imei }}</div>
                    </div>
                    @endif
                </div>
                @if($product->stock_note)
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 mt-3">
                    <div class="text-xs text-amber-700 font-bold mb-1">📋 {{ $isKm ? 'កំណត់ចំណាំស្តុក' : 'Stock Note' }}</div>
                    <div class="text-sm text-amber-900 whitespace-pre-line">{{ $product->stock_note }}</div>
                </div>
                @endif
                @if($product->seo)
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mt-3">
                    <div class="text-xs text-gray-400 font-bold mb-1">SEO Info</div>
                    <div class="text-sm text-gray-600 whitespace-pre-line">{{ $product->seo }}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- Computer Specs --}}
            @if($product->cpu || $product->ram || $product->storage || $product->graphics_card || $product->color || $product->warranty)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    {{ __('app.computer_specifications') }}
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @if($product->cpu)
                    <div class="flex items-center gap-3 bg-indigo-50/60 rounded-xl p-3.5 border border-indigo-100/60">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-indigo-400 font-medium">CPU</div>
                            <div class="text-xs font-bold text-indigo-800">{{ $product->cpu }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->ram)
                    <div class="flex items-center gap-3 bg-emerald-50/60 rounded-xl p-3.5 border border-emerald-100/60">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 12a2 2 0 00-2 2v2a2 2 0 002 2h14a2 2 0 002-2v-2a2 2 0 00-2-2"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-emerald-400 font-medium">RAM</div>
                            <div class="text-xs font-bold text-emerald-800">{{ $product->ram }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->storage)
                    <div class="flex items-center gap-3 bg-amber-50/60 rounded-xl p-3.5 border border-amber-100/60">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-amber-400 font-medium">{{ $isKm ? 'ឧបករណ៍ផ្ទុក' : 'Storage' }}</div>
                            <div class="text-xs font-bold text-amber-800">{{ $product->storage }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->graphics_card)
                    <div class="flex items-center gap-3 bg-rose-50/60 rounded-xl p-3.5 border border-rose-100/60">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-rose-400 font-medium">GPU</div>
                            <div class="text-xs font-bold text-rose-800">{{ $product->graphics_card }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->color)
                    <div class="flex items-center gap-3 bg-sky-50/60 rounded-xl p-3.5 border border-sky-100/60">
                        <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-sky-400 font-medium">{{ $isKm ? 'ពណ៌' : 'Color' }}</div>
                            <div class="text-xs font-bold text-sky-800">{{ $product->color }}</div>
                        </div>
                    </div>
                    @endif
                    @if($product->warranty)
                    <div class="flex items-center gap-3 bg-teal-50/60 rounded-xl p-3.5 border border-teal-100/60">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-teal-400 font-medium">{{ $isKm ? 'ការធានា' : 'Warranty' }}</div>
                            <div class="text-xs font-bold text-teal-800">{{ $product->warranty }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Purchase History --}}
            @if($purchaseHistory->count())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    {{ __('app.purchase_history') }}
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">{{ __('app.supplier') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">{{ __('app.purchase_date') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">{{ __('app.qty') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">{{ $isKm ? 'តម្លៃដើម' : 'Supply Price' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($purchaseHistory as $item)
                            <tr class="hover:bg-gray-50/70 transition duration-150">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ optional(optional($item->purchase)->supplier)->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ optional(optional($item->purchase)->purchase_date)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 font-semibold">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 font-bold">${{ number_format($item->cost_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>{{-- end right col --}}
    </div>{{-- end main grid --}}
</div>
@endsection
