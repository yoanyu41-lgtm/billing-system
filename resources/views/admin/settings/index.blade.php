@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Settings</h1>

<form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white p-6 rounded shadow">
    @csrf
    <div class="mb-4">
        <label class="block">Shop Name</label>
        <input type="text" name="shop_name" value="{{ $settings['shop_name'] ?? 'City Tech Computer' }}" class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">Shop Address</label>
        <input type="text" name="shop_address" value="{{ $settings['shop_address'] ?? '' }}" class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">Shop Phone</label>
        <input type="text" name="shop_phone" value="{{ $settings['shop_phone'] ?? '' }}" class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">Currency</label>
        <input type="text" name="currency" value="{{ $settings['currency'] ?? 'USD' }}" class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">Default Interest Rate (%)</label>
        <input type="number" name="default_interest_rate" value="{{ $settings['default_interest_rate'] ?? 0 }}" step="0.01" class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">Telegram Token</label>
        <input type="text" name="telegram_token" value="{{ $settings['telegram_token'] ?? '' }}" class="w-full border px-2 py-1">
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
</form>
@endsection