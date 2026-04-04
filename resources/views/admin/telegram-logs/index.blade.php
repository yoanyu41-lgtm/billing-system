@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Telegram Center</h1>
        <p class="text-sm text-slate-600">Review Telegram settings, set the webhook, and send a test message.</p>
    </div>
    <a href="{{ route('admin.settings.index') }}" class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Open Settings</a>
</div>

<div class="mb-6 grid gap-4 md:grid-cols-3">
    <div class="rounded-xl bg-white p-5 shadow">
        <p class="text-sm font-semibold text-slate-500">Telegram Token</p>
        <p class="mt-2 text-sm text-slate-900">{{ !empty($settings['telegram_token']) ? 'Configured' : 'Not configured yet' }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow">
        <p class="text-sm font-semibold text-slate-500">Webhook URL</p>
        <p class="mt-2 break-all text-sm text-slate-900">{{ url('/api/telegram/webhook') }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow">
        <p class="text-sm font-semibold text-slate-500">Messages Logged</p>
        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $telegramLogs->total() }}</p>
    </div>
</div>

<div class="mb-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Set Webhook</h2>
        <p class="mb-3 text-sm text-slate-600">Use a public <strong>HTTPS</strong> URL so Telegram can call your system.</p>
        <form method="POST" action="{{ route('admin.telegram-logs.set-webhook') }}" class="space-y-3">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Webhook URL</label>
                <input type="url" name="webhook_url" value="{{ url('/api/telegram/webhook') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Set Webhook</button>
        </form>
    </div>

    <div class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Send Test Message</h2>
        <form method="POST" action="{{ route('admin.telegram-logs.send-test') }}" class="space-y-3">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Customer</label>
                <select name="customer_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">First linked customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->telegram_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Message</label>
                <textarea name="test_message" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2">✅ Test message from CityTech Billing System.</textarea>
            </div>
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Send Test</button>
        </form>
    </div>
</div>

<div class="rounded-xl bg-white p-6 shadow">
    <h2 class="mb-4 text-lg font-semibold text-slate-900">Recent Telegram Logs</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b bg-slate-50 text-left text-slate-700">
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Telegram ID</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">Sent At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($telegramLogs as $log)
                    <tr class="border-b border-slate-100 align-top">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $log->customer->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $log->customer->telegram_id ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $log->message }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ optional($log->sent_at)->format('Y-m-d H:i') ?? $log->sent_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">No Telegram logs available yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $telegramLogs->links() }}
    </div>
</div>
@endsection
