@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Add Customer</h1>

<form method="POST" action="{{ route('customers.store') }}" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="mb-4">
        <label class="block font-medium">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border px-2 py-1 rounded {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }}">
        @error('phone')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Address</label>
        <textarea name="address" class="w-full border px-2 py-1 rounded {{ $errors->has('address') ? 'border-red-500' : 'border-gray-300' }}">{{ old('address') }}</textarea>
        @error('address')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
</form>
@endsection