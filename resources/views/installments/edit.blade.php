@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Installment</h1>

<form method="POST" action="{{ route('installments.update', $installment) }}" class="bg-white p-6 rounded shadow">
    @csrf @method('PUT')

    <div class="mb-4">
        <label class="block font-medium">Total Price</label>
        <input type="number" name="total_price" value="{{ old('total_price', $installment->total_price) }}" step="0.01" required class="w-full border px-2 py-1 rounded {{ $errors->has('total_price') ? 'border-red-500' : 'border-gray-300' }}">
        @error('total_price')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Down Payment</label>
        <input type="number" name="down_payment" value="{{ old('down_payment', $installment->down_payment) }}" step="0.01" required class="w-full border px-2 py-1 rounded {{ $errors->has('down_payment') ? 'border-red-500' : 'border-gray-300' }}">
        @error('down_payment')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Interest Rate (%)</label>
        <input type="number" name="interest_rate" value="{{ old('interest_rate', $installment->interest_rate) }}" step="0.01" class="w-full border px-2 py-1 rounded {{ $errors->has('interest_rate') ? 'border-red-500' : 'border-gray-300' }}">
        @error('interest_rate')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Duration (Months)</label>
        <input type="number" name="duration_months" value="{{ old('duration_months', $installment->duration_months) }}" required class="w-full border px-2 py-1 rounded {{ $errors->has('duration_months') ? 'border-red-500' : 'border-gray-300' }}">
        @error('duration_months')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block font-medium">Status</label>
        <select name="status" class="w-full border px-2 py-1 rounded {{ $errors->has('status') ? 'border-red-500' : 'border-gray-300' }}">
            <option value="active" {{ old('status', $installment->status) == 'active' ? 'selected' : '' }}>Active</option>
            <option value="cancelled" {{ old('status', $installment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        @error('status')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
</form>
@endsection