@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Add Payment</h1>

<form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
    @csrf
    <div class="mb-4">
        <label class="block">Installment</label>
        <select name="installment_id" required class="w-full border px-2 py-1">
            @foreach($installments as $installment)
                <option value="{{ $installment->id }}">{{ $installment->customer->name }} - {{ $installment->product->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block">Amount</label>
        <input type="number" name="amount" step="0.01" required class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">Payment Date</label>
        <input type="date" name="payment_date" required class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">QR Image</label>
        <input type="file" name="qr_image" class="w-full border px-2 py-1">
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
</form>
@endsection