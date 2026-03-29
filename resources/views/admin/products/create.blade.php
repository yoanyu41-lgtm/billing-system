@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Add Product</h1>

<form method="POST" action="{{ route('admin.products.store') }}" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="mb-4">
        <label class="block font-medium">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Price</label>
        <input type="number" name="price" step="0.01" value="{{ old('price') }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('price') ? 'border-red-500' : 'border-gray-300' }}">
        @error('price')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Stock</label>
        <input type="number" name="stock" value="{{ old('stock') }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('stock') ? 'border-red-500' : 'border-gray-300' }}">
        @error('stock')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Description</label>
        <textarea name="description" class="w-full border px-2 py-1 rounded {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }}">{{ old('description') }}</textarea>
        @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
</form>
@endsection