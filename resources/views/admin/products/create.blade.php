@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Add Product</h1>
            <p class="text-sm text-gray-500 mt-1">Enter the details below to create a new product in the inventory.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Item Code -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Item Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('code') ? 'border-red-500' : 'border-gray-300' }}" placeholder="e.g., PROD-001">
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}" placeholder="Product Name">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-gray-700 text-sm font-medium">Category</label>
                    <a href="{{ route('admin.categories.create') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition duration-150">+ Add Category</a>
                </div>
                <select name="category" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('category') ? 'border-red-500' : 'border-gray-300' }}">
                    <option value="">-- Select category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Brand -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Brand</label>
                <select name="brand" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('brand') ? 'border-red-500' : 'border-gray-300' }}">
                    <option value="">-- Select brand --</option>
                    @foreach(config('products.brands', []) as $brand)
                        <option value="{{ $brand }}" {{ old('brand') === $brand ? 'selected' : '' }}>{{ $brand }}</option>
                    @endforeach
                </select>
                @error('brand')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Stock Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="stock" value="{{ old('stock') }}" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('stock') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0">
                @error('stock')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Supplier -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Supplier</label>
                <select name="supplier_id" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('supplier_id') ? 'border-red-500' : 'border-gray-300' }}">
                    <option value="">-- Select supplier --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Selling Price <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" name="price" step="0.01" value="{{ old('price') }}" required class="w-full border pl-8 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('price') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0.00">
                </div>
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cost Price -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Cost Price</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" name="cost_price" step="0.01" value="{{ old('cost_price') }}" class="w-full border pl-8 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('cost_price') ? 'border-red-500' : 'border-gray-300' }}" placeholder="0.00">
                </div>
                @error('cost_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Low Stock Threshold -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Low Stock Threshold</label>
                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" min="0" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('low_stock_threshold') ? 'border-red-500' : 'border-gray-300' }}" placeholder="5">
                @error('low_stock_threshold')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Computer Specifications Section -->
        <div class="mb-8 border-t border-gray-100 pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Computer Specifications (Optional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- CPU -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">CPU</label>
                    <input type="text" name="cpu" value="{{ old('cpu') }}" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., Intel Core i5">
                </div>

                <!-- RAM -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">RAM</label>
                    <input type="text" name="ram" value="{{ old('ram') }}" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., 16GB DDR4">
                </div>

                <!-- Storage -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Storage (SSD/HDD)</label>
                    <input type="text" name="storage" value="{{ old('storage') }}" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., 512GB NVMe SSD">
                </div>

                <!-- Graphics Card -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Graphics Card</label>
                    <input type="text" name="graphics_card" value="{{ old('graphics_card') }}" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., Intel Iris Xe / NVIDIA RTX 3050">
                </div>
            </div>
        </div>

        <!-- Product Image -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">Product Image</label>
            <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 bg-gray-50 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 {{ $errors->has('image') ? 'border-red-500' : '' }}">
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-8">
            <label class="block text-gray-700 text-sm font-medium mb-2">Description</label>
            <textarea name="description" rows="4" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }}" placeholder="Enter product description here...">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150 shadow-sm">Cancel</a>
            <button type="submit" class="px-6 py-2.5 font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition duration-150">Save Product</button>
        </div>
    </form>
</div>
@endsection