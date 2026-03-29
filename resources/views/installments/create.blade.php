@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Create Installment</h1>

<form method="POST" action="{{ route('installments.store') }}" class="bg-white p-6 rounded shadow">
    @csrf

    <div class="mb-4">
        <label class="block font-medium">Customer</label>
        <select name="customer_id" required class="w-full border px-2 py-1 rounded {{ $errors->has('customer_id') ? 'border-red-500' : 'border-gray-300' }}">
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
            @endforeach
        </select>
        @error('customer_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Product</label>
        <select name="product_id" required class="w-full border px-2 py-1 rounded {{ $errors->has('product_id') ? 'border-red-500' : 'border-gray-300' }}">
            @foreach($products as $product)
                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} - ${{ $product->price }}</option>
            @endforeach
        </select>
        @error('product_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Total Price</label>
        <input type="number" name="total_price" step="0.01" value="{{ old('total_price') }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('total_price') ? 'border-red-500' : 'border-gray-300' }}">
        @error('total_price')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Down Payment</label>
        <input type="number" name="down_payment" step="0.01" value="{{ old('down_payment') }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('down_payment') ? 'border-red-500' : 'border-gray-300' }}">
        @error('down_payment')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Interest Rate (%)</label>
        <input type="number" name="interest_rate" step="0.01" value="{{ old('interest_rate') }}" class="w-full border px-2 py-1 rounded {{ $errors->has('interest_rate') ? 'border-red-500' : 'border-gray-300' }}">
        @error('interest_rate')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Duration (Months)</label>
        <input type="number" name="duration_months" value="{{ old('duration_months') }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('duration_months') ? 'border-red-500' : 'border-gray-300' }}">
        @error('duration_months')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
</form>
@endsection