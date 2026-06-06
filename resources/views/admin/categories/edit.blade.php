@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Category</h1>

<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="bg-white p-6 rounded shadow">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="block font-medium">Category Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Category</button>
</form>
@endsection
