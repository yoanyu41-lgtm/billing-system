@extends('layouts.app')

@section('content')
<h1 class="mb-4 text-2xl font-bold text-slate-900">Settings</h1>

<div class="grid gap-6 lg:grid-cols-3">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="rounded-xl bg-white p-6 shadow lg:col-span-2">
        @csrf
        <div class="mb-4">
            <label class="block">Shop Name</label>
            <input type="text" name="shop_name" value="{{ $settings['shop_name'] ?? 'City Tech Computer' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block">Shop Address</label>
            <input type="text" name="shop_address" value="{{ $settings['shop_address'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block">Shop Phone</label>
            <input type="text" name="shop_phone" value="{{ $settings['shop_phone'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block">Currency</label>
            <input type="text" name="currency" value="{{ $settings['currency'] ?? 'USD' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block">Default Interest Rate (%)</label>
            <input type="number" name="default_interest_rate" value="{{ $settings['default_interest_rate'] ?? 0 }}" step="0.01" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block">Telegram Token</label>
            <input type="text" name="telegram_token" value="{{ $settings['telegram_token'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Paste your bot token here">
        </div>
        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Update</button>
    </form>

    <div class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Telegram Setup</h2>
        <p class="mb-3 text-sm text-slate-600">Use this webhook URL in your Telegram bot configuration.</p>
        <div class="rounded-lg bg-slate-100 p-3 text-sm text-slate-800 break-all">
            {{ url('/api/telegram/webhook') }}
        </div>
        <p class="mt-4 text-sm text-slate-600">
            Status:
            <span class="font-semibold {{ !empty($settings['telegram_token']) ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ !empty($settings['telegram_token']) ? 'Configured' : 'Not configured' }}
            </span>
        </p>
        <a href="{{ route('admin.telegram-logs.index') }}" class="mt-4 inline-flex rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Open Telegram Center</a>
    </div>
</div>
@endsection