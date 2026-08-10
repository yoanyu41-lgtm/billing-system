@extends('layouts.app')

@section('content')
@php
    $companyName    = \App\Models\Setting::where('key','company_name')->value('value') ?? 'CityTech';
    $companyNameKm  = \App\Models\Setting::where('key','company_name_km')->value('value') ?? $companyName;
    $companyPhone   = \App\Models\Setting::where('key','company_phone')->value('value');
    $companyAddress = \App\Models\Setting::where('key','company_address')->value('value');
    $companyLogoRaw = \App\Models\Setting::where('key','company_logo')->value('value');
    $companyLogo    = $companyLogoRaw ? asset('storage/' . $companyLogoRaw) : asset('logo-ct.svg');
    $totalRiel       = round($purchase->total * 4000);
@endphp

<div class="container mx-auto px-4 py-8 max-w-4xl print:p-0 print:max-w-full">
    
    <!-- Action Bar (Hidden when printing) -->
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden">
        <div>
            <nav class="text-xs text-slate-400 mb-1 flex items-center gap-1.5">
                <a href="{{ route('admin.purchases.index') }}" class="hover:text-blue-600 transition">{{ __('app.purchase_history') }}</a>
                <span>/</span>
                <span class="text-slate-600 font-mono font-bold">#PO-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'ព័ត៌មានលម្អិតការទិញស្តុកចូល' : 'Stock Purchase Order' }}
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 px-4 py-2.5 rounded-xl transition duration-150 shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                {{ __('app.back') }}
            </a>
            <a href="{{ route('admin.purchases.edit', $purchase) }}" class="inline-flex items-center text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 px-4 py-2.5 rounded-xl transition duration-150">
                <i class="fas fa-edit mr-2"></i>
                {{ __('app.edit') }}
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition duration-150 shadow-md">
                <i class="fas fa-print mr-2"></i>
                {{ app()->getLocale() === 'km' ? 'បោះពុម្ព / Save PDF' : 'Print / Save PDF' }}
            </button>
        </div>
    </div>

    <!-- Main Printable Voucher Card (Pure White Clean Design) -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-6 sm:p-10 print:shadow-none print:border-none print:p-0">
        
        <!-- Clean Header Section with Store Logo -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b-2 border-slate-200 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200/90 p-2 flex items-center justify-center shadow-sm flex-shrink-0">
                    <img src="{{ $companyLogo }}" alt="Store Logo" class="w-full h-full object-contain" onError="this.onerror=null; this.src='https://ui-avatars.com/api/?name=CityTech&background=0D8ABC&color=fff';">
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        {{ app()->getLocale() === 'km' ? ($companyNameKm ?? 'ស៊ីធីធិច និង កុំព្យូទ័រ') : $companyName }}
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">CityTech Installment & Stock Management System</p>
                    @if($companyPhone || $companyAddress)
                        <div class="text-[11px] text-slate-400 mt-1 flex flex-wrap items-center gap-3">
                            @if($companyPhone)<span><i class="fas fa-phone mr-1"></i>{{ $companyPhone }}</span>@endif
                            @if($companyAddress)<span><i class="fas fa-location-dot mr-1"></i>{{ $companyAddress }}</span>@endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-left sm:text-right border-l-4 sm:border-l-0 sm:border-r-4 border-blue-600 pl-3 sm:pl-0 sm:pr-3">
                <div class="text-xs uppercase font-black tracking-wider text-blue-700">
                    {{ app()->getLocale() === 'km' ? 'ប័ណ្ណទិញទំនិញចូលស្តុក' : 'Stock Purchase Voucher' }}
                </div>
                <div class="text-xl font-black font-mono tracking-wider text-slate-900 mt-0.5">
                    #PO-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div class="text-[11px] font-bold text-slate-500 mt-1">
                    {{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទ' : 'Date' }}: <span class="font-mono text-slate-800">{{ $purchase->purchase_date?->format('Y-m-d') ?? $purchase->created_at->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <!-- Supplier & Metadata Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6">
            
            <!-- Supplier Details -->
            <div class="bg-white rounded-xl p-4 border border-slate-200 text-xs space-y-1.5">
                <div class="font-bold text-slate-500 uppercase tracking-wider text-[11px] flex items-center gap-1.5 pb-1.5 border-b border-slate-200">
                    <i class="fas fa-truck text-blue-600"></i>
                    {{ app()->getLocale() === 'km' ? 'ព័ត៌មានអ្នកផ្គត់ផ្គង់' : 'Supplier Details' }}
                </div>
                @if($purchase->supplier)
                    <div class="text-sm font-bold text-slate-800 pt-1">{{ $purchase->supplier->name }}</div>
                    @if($purchase->supplier->phone)
                        <div class="text-slate-600 flex items-center gap-2">
                            <i class="fas fa-phone text-slate-400 text-[10px]"></i>
                            <span>{{ $purchase->supplier->phone }}</span>
                        </div>
                    @endif
                    @if($purchase->supplier->address)
                        <div class="text-slate-600 flex items-center gap-2">
                            <i class="fas fa-location-dot text-slate-400 text-[10px]"></i>
                            <span>{{ $purchase->supplier->address }}</span>
                        </div>
                    @endif
                @else
                    <div class="text-slate-400 font-medium py-2 italic">
                        មិនមានព័ត៌មានអ្នកផ្គត់ផ្គង់ទេ (No Supplier Record)
                    </div>
                @endif
            </div>

            <!-- Status Details -->
            <div class="bg-white rounded-xl p-4 border border-slate-200 text-xs space-y-2">
                <div class="font-bold text-slate-500 uppercase tracking-wider text-[11px] flex items-center justify-between pb-1.5 border-b border-slate-200">
                    <span class="flex items-center gap-1.5"><i class="fas fa-info-circle text-blue-600"></i> {{ app()->getLocale() === 'km' ? 'ព័ត៌មានប្រតិបត្តិការ' : 'Order Information' }}</span>
                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 font-bold text-[10px] rounded-full border border-emerald-200">
                        <i class="fas fa-check-circle text-emerald-600 mr-1"></i> {{ app()->getLocale() === 'km' ? 'ចូលស្តុករួចរាល់' : 'Received' }}
                    </span>
                </div>
                <div class="space-y-1.5 pt-0.5">
                    <div class="flex justify-between items-center text-slate-700">
                        <span class="text-slate-500">{{ app()->getLocale() === 'km' ? 'ថ្ងៃកត់ត្រាក្នុងប្រព័ន្ធ' : 'Recorded At' }}:</span>
                        <span class="font-mono text-slate-800 font-semibold">{{ $purchase->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-700">
                        <span class="text-slate-500">{{ app()->getLocale() === 'km' ? 'ចំនួនមុខទំនិញ' : 'Total Items' }}:</span>
                        <span class="font-mono text-slate-800 font-semibold">{{ count($purchase->items) }} {{ app()->getLocale() === 'km' ? 'មុខ' : 'Items' }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Purchased Products Table -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <i class="fas fa-boxes-stacked text-blue-600"></i>
                    {{ app()->getLocale() === 'km' ? 'បញ្ជីមុខទំនិញដែលបានទិញចូលស្តុក' : 'Itemized Purchased Products' }}
                </h3>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                <table class="w-full text-left text-xs divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold text-slate-700 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">#</th>
                            <th class="px-4 py-3.5">{{ __('app.product') }}</th>
                            <th class="px-4 py-3.5 text-center">{{ __('app.quantity') }}</th>
                            <th class="px-4 py-3.5 text-right">{{ __('app.cost_price') }} ($)</th>
                            <th class="px-4 py-3.5 text-right">{{ __('app.subtotal') }} ($)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($purchase->items as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition duration-150">
                            <td class="px-4 py-4 text-center font-mono text-slate-400 font-bold">{{ $index + 1 }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs flex-shrink-0 border border-slate-200">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $item->product->name ?? '—' }}</div>
                                        @if($item->product?->code)
                                            <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-mono font-bold rounded mt-0.5 border border-slate-200">
                                                Code: {{ $item->product->code }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-slate-100 border border-slate-200 text-slate-800 font-mono font-bold rounded-lg text-xs">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right font-mono font-bold text-slate-700">${{ number_format($item->cost_price ?? 0, 2) }}</td>
                            <td class="px-4 py-4 text-right font-mono font-black text-slate-900 text-sm">${{ number_format(($item->cost_price ?? 0) * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calculations Summary Box (Pure White & Clean Lines) -->
        @php
            $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
            $taxLabel = \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT';
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t-2 border-slate-200">
            <!-- Riel Summary Card -->
            <div class="bg-white rounded-xl p-4 border border-slate-200 text-xs space-y-1 flex flex-col justify-center">
                <div class="font-bold text-slate-700 flex items-center gap-1.5">
                    <i class="fas fa-calculator text-blue-600"></i>
                    {{ app()->getLocale() === 'km' ? 'សរុបទឹកប្រាក់ជាប្រាក់រៀល' : 'Equivalent Total in KHR' }}
                </div>
                <div class="text-xl font-black font-mono text-blue-700 tracking-wide">
                    ៛{{ number_format($totalRiel) }} KHR
                </div>
                <div class="text-[10px] text-slate-400">(អត្រាប្តូរប្រាក់: 1 USD = 4,000 KHR)</div>
            </div>

            <!-- Grand Total Box (Clean White) -->
            <div class="space-y-2 text-xs">
                @if($taxEnabled && $purchase->tax_amount > 0)
                <div class="flex justify-between items-center py-1.5 px-3 bg-white border border-slate-200 rounded-lg text-slate-600">
                    <span>{{ __('app.subtotal') }}:</span>
                    <span class="font-mono font-bold text-slate-800">${{ number_format($purchase->total - $purchase->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 px-3 bg-white border border-slate-200 rounded-lg text-slate-600">
                    <span>{{ __('app.tax') }} ({{ $taxLabel }}):</span>
                    <span class="font-mono font-bold text-slate-800">${{ number_format($purchase->tax_amount, 2) }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center p-4 bg-white border-2 border-slate-900 text-slate-900 rounded-xl shadow-sm">
                    <span class="font-black uppercase tracking-wider text-xs text-slate-800">{{ app()->getLocale() === 'km' ? 'សរុបទឹកប្រាក់' : 'Grand Total' }}:</span>
                    <span class="font-mono text-xl font-black text-slate-900">${{ number_format($purchase->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Official Signatures Footer -->
        <div class="pt-10 mt-10 border-t border-slate-200 grid grid-cols-3 gap-4 text-center text-xs">
            <div>
                <div class="h-16"></div>
                <div class="border-t border-slate-300 pt-2 font-bold text-slate-800">{{ app()->getLocale() === 'km' ? 'អ្នករៀបចំទិន្នន័យ' : 'Prepared By' }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">(ហត្ថលេខា និង ឈ្មោះ)</div>
            </div>
            <div>
                <div class="h-16"></div>
                <div class="border-t border-slate-300 pt-2 font-bold text-slate-800">{{ app()->getLocale() === 'km' ? 'អ្នកទទួលស្តុក' : 'Store Manager' }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">(ហត្ថលេខា និង ត្រា)</div>
            </div>
            <div>
                <div class="h-16"></div>
                <div class="border-t border-slate-300 pt-2 font-bold text-slate-800">{{ app()->getLocale() === 'km' ? 'អ្នកផ្គត់ផ្គង់' : 'Supplier Signature' }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">(ហត្ថលេខា និង កាលបរិច្ឆេទ)</div>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    body {
        background-color: white !important;
    }
    .print\:hidden {
        display: none !important;
    }
    header, sidebar, nav, aside {
        display: none !important;
    }
    .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endsection
