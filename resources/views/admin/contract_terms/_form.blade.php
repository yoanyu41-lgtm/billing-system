<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.term_title') }} (ខ្មែរ) <span class="text-red-500">*</span></label>
        <input type="text" name="title_km" value="{{ old('title_km', $term->title_km ?? '') }}" required lang="km" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition {{ $errors->has('title_km') ? 'border-red-500' : 'border-gray-300' }}" placeholder="ឧ. មាត្រា១ - កាតព្វកិច្ចអ្នកទិញ">
        @error('title_km')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.term_title') }} (English)</label>
        <input type="text" name="title_en" value="{{ old('title_en', $term->title_en ?? '') }}" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="e.g. BUYER'S OBLIGATIONS">
    </div>
</div>

<div class="mb-6">
    <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.term_content') }} (ខ្មែរ) <span class="text-red-500">*</span></label>
    <textarea name="content_km" rows="5" required lang="km" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition {{ $errors->has('content_km') ? 'border-red-500' : 'border-gray-300' }}" placeholder="មួយបន្ទាត់ក្នុងមួយចំណុច">{{ old('content_km', $term->content_km ?? '') }}</textarea>
    <p class="text-xs text-gray-500 mt-1" lang="km">បំបែកមួយចំណុចក្នុងមួយបន្ទាត់ (វានឹងបង្ហាញជាបញ្ជីលេខ)</p>
    @error('content_km')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-6">
    <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.term_content') }} (English)</label>
    <textarea name="content_en" rows="5" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="One point per line">{{ old('content_en', $term->content_en ?? '') }}</textarea>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div>
        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.sort_order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $term->sort_order ?? 0) }}" min="0" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
    </div>
    <div>
        <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.status') }}</label>
        <label class="inline-flex items-center gap-3 cursor-pointer mt-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $term->is_active ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">{{ __('app.active') }}</span>
        </label>
    </div>
</div>
