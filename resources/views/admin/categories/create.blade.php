@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.add_category') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Add a new product category to organize your inventory catalog.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('app.back') }}
        </a>
    </div>

    <!-- Form Card -->
    <form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        @csrf

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.category_name') }} <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}" placeholder="e.g., Laptop, Accessories">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.brand') }}</label>
            <input type="text" name="brand" value="{{ old('brand') }}" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('brand') ? 'border-red-500' : 'border-gray-300' }}" placeholder="e.g., Dell, HP, Logitech">
            @error('brand')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-sm text-gray-500 mt-1">Optional: Specify a brand for this category</p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150 shadow-sm">
                {{ __('app.cancel') }}
            </a>
            <button type="submit" class="px-6 py-2.5 font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition duration-150">
                {{ __('app.save') }}
            </button>
        </div>
    </form>
</div>
@endsection
