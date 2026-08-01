@extends('layouts.app')

@section('content')
<style>
.form-section {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8eaf0;
    box-shadow: 0 1px 4px rgba(99,102,241,0.04), 0 2px 12px rgba(0,0,0,0.04);
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.form-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 24px;
    border-bottom: 1px solid #f0f0f5;
    background: linear-gradient(90deg, #f8f7ff 0%, #fff 100%);
}
.form-section-header .section-icon {
    width: 34px; height: 34px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.form-section-body { padding: 20px 24px 24px; }
.field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
}
.field-input {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 14px;
    color: #1f2937;
    background: #fafafa;
    transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    outline: none;
}
.field-input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.field-input.has-error { border-color: #ef4444; }
.field-input-prefix {
    position: relative;
}
.field-input-prefix .prefix-symbol {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    color: #9ca3af; font-size: 13px; font-weight: 600; pointer-events: none;
}
.field-input-prefix .field-input { padding-left: 28px; }
</style>

<div class="container mx-auto px-4 py-6 max-w-5xl">

    {{-- Hero Header --}}
    <div class="rounded-2xl mb-6 overflow-hidden" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 60%, #7c3aed 100%); box-shadow: 0 4px 24px rgba(99,102,241,0.25);">
        <div class="px-6 py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.15);">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white leading-tight">{{ __('app.edit_product') }}</h1>
                    <p class="text-indigo-200 text-sm mt-0.5 flex items-center gap-1.5">
                        <i class="fas fa-box text-xs"></i>
                        <span>{{ $product->name }}</span>
                    </p>
                </div>
            </div>
            @if(request('from') === 'stock')
            <a href="{{ route('admin.products.stock') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-sm transition" style="background: rgba(255,255,255,0.18); color: #fff; border: 1px solid rgba(255,255,255,0.25); backdrop-filter: blur(4px);">
                <i class="fas fa-arrow-left text-xs"></i> {{ __('app.back_to_manage_stock') }}
            </a>
            @else
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-sm transition" style="background: rgba(255,255,255,0.18); color: #fff; border: 1px solid rgba(255,255,255,0.25); backdrop-filter: blur(4px);">
                <i class="fas fa-arrow-left text-xs"></i> {{ __('app.back_to_product_list') }}
            </a>
            @endif
        </div>
    </div>

    @if ($errors->any())
    <div class="mb-5 p-4 rounded-xl border border-red-200 bg-red-50 flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
        <div>
            <p class="text-sm font-bold text-red-700 mb-1">{{ app()->getLocale() === 'km' ? 'សូមកែប្រែកំហុសខាងក្រោម:' : 'Please fix the following errors:' }}</p>
            <ul class="text-sm text-red-600 space-y-0.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.products.update', [$product, 'from' => request('from')]) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Section 1: Basic Info --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="section-icon" style="background: #eef2ff;">
                    <i class="fas fa-tag text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ app()->getLocale() === 'km' ? 'ព័ត៌មានមូលដ្ឋាន' : 'Basic Information' }}</p>
                    <p class="text-xs text-gray-400">{{ app()->getLocale() === 'km' ? 'លេខកូដ បារខូដ ឈ្មោះ ប្រភេទ' : 'Code, barcode, name, category' }}</p>
                </div>
            </div>
            <div class="form-section-body">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="field-label">{{ __('app.item_code') }} <span class="text-red-500 normal-case">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $product->code) }}" required
                               class="field-input {{ $errors->has('code') ? 'has-error' : '' }}" placeholder="e.g., PROD-001">
                        @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.barcode') }}</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}"
                               class="field-input" placeholder="e.g. 880123456789">
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.name') }} <span class="text-red-500 normal-case">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="field-input {{ $errors->has('name') ? 'has-error' : '' }}" placeholder="{{ __('app.product_name') }}">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.category') }}</label>
                        <input type="text" name="category" value="{{ old('category', $product->category) }}" list="categories-list"
                               class="field-input {{ $errors->has('category') ? 'has-error' : '' }}" placeholder="e.g. Laptop, Monitor...">
                        <datalist id="categories-list">
                            @foreach($categories as $cat)<option value="{{ $cat }}">@endforeach
                        </datalist>
                        @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.supplier') }}</label>
                        <select name="supplier_id" class="field-input {{ $errors->has('supplier_id') ? 'has-error' : '' }}">
                            <option value="">{{ __('app.select_supplier') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ (string)old('supplier_id', $product->supplier_id) === (string)$supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.condition') }}</label>
                        <select name="condition" class="field-input">
                            @php $cond = old('condition', $product->condition ?? 'new'); @endphp
                            <option value="new" {{ $cond === 'new' ? 'selected' : '' }}>{{ __('app.condition_new') }}</option>
                            <option value="demo" {{ $cond === 'demo' ? 'selected' : '' }}>{{ __('app.condition_demo') }}</option>
                            <option value="used" {{ $cond === 'used' ? 'selected' : '' }}>{{ __('app.condition_used') }}</option>
                            <option value="refurbished" {{ $cond === 'refurbished' ? 'selected' : '' }}>{{ __('app.condition_refurbished') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Pricing & Stock --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="section-icon" style="background: #f0fdf4;">
                    <i class="fas fa-dollar-sign text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ app()->getLocale() === 'km' ? 'តម្លៃ & ស្តុក' : 'Pricing & Stock' }}</p>
                    <p class="text-xs text-gray-400">{{ app()->getLocale() === 'km' ? 'តម្លៃ ថ្លៃដើម ចំនួន ស្តុក' : 'Selling price, cost, quantity' }}</p>
                </div>
            </div>
            <div class="form-section-body">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="field-label">{{ __('app.stock_quantity') }} <span class="text-red-500 normal-case">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required
                               class="field-input {{ $errors->has('stock') ? 'has-error' : '' }}" placeholder="0">
                        @error('stock')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.selling_price') }} <span class="text-red-500 normal-case">*</span></label>
                        <div class="field-input-prefix">
                            <span class="prefix-symbol">$</span>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" required
                                   class="field-input {{ $errors->has('price') ? 'has-error' : '' }}" placeholder="0.00">
                        </div>
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.cost_price') }}</label>
                        <div class="field-input-prefix">
                            <span class="prefix-symbol">$</span>
                            <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01"
                                   class="field-input {{ $errors->has('cost_price') ? 'has-error' : '' }}" placeholder="0.00">
                        </div>
                        @error('cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.low_stock_threshold') }}</label>
                        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}" min="0"
                               class="field-input" placeholder="5">
                    </div>
                </div>
            </div>
        </div>

        {{-- Tax Section (conditional) --}}
        @php
            $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') ?? '0';
            $defaultTaxRate = \App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? '10';
        @endphp
        @if($taxEnabled == '1')
        <div class="form-section">
            <div class="form-section-header">
                <div class="section-icon" style="background: #fffbeb;">
                    <i class="fas fa-percent text-amber-500"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ __('app.tax') }} / VAT</p>
                    <p class="text-xs text-gray-400">{{ app()->getLocale() === 'km' ? 'ការកំណត់ VAT' : 'Tax configuration' }}</p>
                </div>
            </div>
            <div class="form-section-body">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-3">
                        <label class="flex items-center gap-3 cursor-pointer w-fit">
                            <input type="checkbox" name="is_taxable" value="1" {{ old('is_taxable', $product->is_taxable ?? 1) == '1' ? 'checked' : '' }}
                                   class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="text-sm font-semibold text-gray-700">{{ __('app.taxable') }} (មាន VAT)</span>
                        </label>
                        <p class="text-xs text-gray-400 mt-1 ml-8">{{ app()->getLocale() === 'km' ? 'ធីកប្រសិនបើផលិតផលនេះមាន VAT' : 'Check if this product is taxable' }}</p>
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.tax_rate') }} (%)</label>
                        <input type="number" name="tax_rate" step="0.01" min="0" max="100"
                               value="{{ old('tax_rate', $product->tax_rate ?? $defaultTaxRate) }}"
                               class="field-input" placeholder="10.00">
                        <p class="text-xs text-gray-400 mt-1">ឧ. 10 សម្រាប់ 10%</p>
                    </div>
                    <div>
                        @php
                            $currentTaxType = old('tax_type', $product->tax_type ?? 'exclusive');
                            if ($currentTaxType === 'មិនរួមពន្ធ') $currentTaxType = 'exclusive';
                            elseif ($currentTaxType === 'រួមពន្ធហើយ') $currentTaxType = 'inclusive';
                        @endphp
                        <label class="field-label">{{ __('app.tax_type') }}</label>
                        <select name="tax_type" class="field-input">
                            <option value="exclusive" {{ $currentTaxType === 'exclusive' ? 'selected' : '' }}>{{ __('app.tax_exclusive') }}</option>
                            <option value="inclusive" {{ $currentTaxType === 'inclusive' ? 'selected' : '' }}>{{ __('app.tax_inclusive') }}</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">មិនរួម VAT = តម្លៃ + VAT | រួម VAT ហើយ = តម្លៃបូក VAT</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Section 3: Computer Specs --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="section-icon" style="background: #eff6ff;">
                    <i class="fas fa-microchip text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ __('app.computer_specs') }}</p>
                    <p class="text-xs text-gray-400">CPU, RAM, {{ __('app.storage') }}, {{ __('app.graphics_card') }}, {{ __('app.color') }}, {{ __('app.warranty') }}</p>
                </div>
            </div>
            <div class="form-section-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="field-label">CPU</label>
                        <input type="text" name="cpu" value="{{ old('cpu', $product->cpu) }}" class="field-input" placeholder="e.g., Intel Core i5">
                    </div>
                    <div>
                        <label class="field-label">RAM</label>
                        <input type="text" name="ram" value="{{ old('ram', $product->ram) }}" class="field-input" placeholder="e.g., 16GB DDR4">
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.storage') }}</label>
                        <input type="text" name="storage" value="{{ old('storage', $product->storage) }}" class="field-input" placeholder="e.g., 512GB NVMe SSD">
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.graphics_card') }}</label>
                        <input type="text" name="graphics_card" value="{{ old('graphics_card', $product->graphics_card) }}" class="field-input" placeholder="e.g., NVIDIA RTX 3050">
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.color') }}</label>
                        <input type="text" name="color" value="{{ old('color', $product->color) }}" class="field-input" placeholder="e.g., Space Gray, Black">
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.warranty') }}</label>
                        <input type="text" name="warranty" value="{{ old('warranty', $product->warranty) }}" class="field-input" placeholder="{{ __('app.warranty_placeholder') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Extended Info --}}
        <div class="form-section">
            <div class="form-section-header">
                <div class="section-icon" style="background: #fdf4ff;">
                    <i class="fas fa-boxes text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ app()->getLocale() === 'km' ? 'ព័ត៌មានបន្ថែម' : 'Extended Fields' }}</p>
                    <p class="text-xs text-gray-400">{{ app()->getLocale() === 'km' ? 'ឈ្មោះ២ ឯកតា IMEI ទីតាំង កំណត់ចំណាំ' : 'Name2, unit, IMEI, location, notes' }}</p>
                </div>
            </div>
            <div class="form-section-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'ឈ្មោះ២ (ឈ្មោះជំនួស)' : 'Name2 (Secondary Name)' }}</label>
                        <input type="text" name="name2" value="{{ old('name2', $product->name2 ?? '') }}" class="field-input" placeholder="Alternative / Local Name">
                    </div>
                    <div>
                        <label class="field-label">{{ __('app.unit') }}</label>
                        <input type="text" name="unit" value="{{ old('unit', $product->unit ?? '') }}" class="field-input" placeholder="e.g. Pcs, Set, Box">
                    </div>
                    <div>
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'ឯកតាប្ដូរ' : 'Exchange Unit' }}</label>
                        <input type="text" name="exchange_unit" value="{{ old('exchange_unit', $product->exchange_unit ?? '') }}" class="field-input" placeholder="e.g. 1">
                    </div>
                    <div>
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'ចំនួនស្តុកអតិបរមា' : 'Max Stock Qty' }}</label>
                        <input type="number" name="max_stock_qty" value="{{ old('max_stock_qty', $product->max_stock_qty ?? '') }}" min="0" class="field-input" placeholder="e.g. 100">
                    </div>
                    <div>
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'ទីតាំងស្តុក' : 'Stock Location' }}</label>
                        <input type="text" name="location" value="{{ old('location', $product->location ?? '') }}" class="field-input" placeholder="e.g. Shelf A-01">
                    </div>
                    <div>
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'IMEI / លេខស៊េរី' : 'IMEI / Serial No.' }}</label>
                        <input type="text" name="imei" value="{{ old('imei', $product->imei ?? '') }}" class="field-input" placeholder="Enter IMEI or Serial">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទស្តុកចូលចុងក្រោយ' : 'Last Stock In Date' }}</label>
                        <input type="datetime-local" name="last_stock_in_at"
                               value="{{ old('last_stock_in_at', isset($product->last_stock_in_at) ? \Carbon\Carbon::parse($product->last_stock_in_at)->format('Y-m-d\TH:i') : '') }}"
                               class="field-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'សង្ខេប' : 'Summary' }}</label>
                        <textarea name="summary" rows="3" class="field-input" style="resize: vertical;" placeholder="Short summary...">{{ old('summary', $product->summary ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'កំណត់ចំណាំស្តុក' : 'Stock Note' }}</label>
                        <textarea name="stock_note" rows="3" class="field-input" style="resize: vertical;" placeholder="Notes about stock...">{{ old('stock_note', $product->stock_note ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="field-label">{{ app()->getLocale() === 'km' ? 'ព័ត៌មាន SEO' : 'SEO Info' }}</label>
                        <textarea name="seo" rows="3" class="field-input" style="resize: vertical;" placeholder="SEO keywords / description...">{{ old('seo', $product->seo ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Image --}}
        @include('partials.product-image-picker')

        {{-- Status & Submit --}}
        <div class="form-section">
            <div class="form-section-body">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active_toggle"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-800">{{ __('app.status') }}</span>
                            <p class="text-xs text-gray-400">{{ app()->getLocale() === 'km' ? 'ធ្វើឱ្យផលិតផលនេះសកម្ម' : 'Make this product active' }}</p>
                        </div>
                    </label>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        @if(request('from') === 'stock')
                        <a href="{{ route('admin.products.stock') }}" class="flex-1 sm:flex-none text-center px-5 py-2.5 font-semibold text-sm text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">
                            {{ __('app.cancel') }}
                        </a>
                        @else
                        <a href="{{ route('admin.products.index') }}" class="flex-1 sm:flex-none text-center px-5 py-2.5 font-semibold text-sm text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">
                            {{ __('app.cancel') }}
                        </a>
                        @endif
                        <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 font-bold text-sm text-white rounded-xl transition shadow-md" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                            <i class="fas fa-save"></i>
                            {{ __('app.update_product') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection