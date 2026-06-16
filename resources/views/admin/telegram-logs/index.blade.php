@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">📬 មជ្ឈមណ្ឌល Telegram (Telegram Center)</h1>
        <p class="text-xs text-slate-500 mt-1">ពិនិត្យមើលការកំណត់ Telegram Bot កំណត់ Webhook និងផ្ញើសារសាកល្បងទៅកាន់អតិថិជន។</p>
    </div>
    <a href="{{ route('admin.settings.index') }}" class="rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition duration-150 flex items-center gap-2">
        ⚙️ ការកំណត់ប្រព័ន្ធ (Settings)
    </a>
</div>

<!-- Stats Cards -->
<div class="mb-6 grid gap-4 md:grid-cols-3">
    <!-- Telegram Token -->
    <div class="rounded-xl bg-white p-5 shadow border border-slate-100 flex items-center justify-between">
        <div>
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Telegram Token</span>
            <span class="block text-sm font-bold text-slate-900 mt-2">
                @if($tokenConfigured)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        បានកំណត់រួចរាល់ (Configured)
                    </span>
                    <span class="block text-[10px] text-slate-400 mt-1">ប្រភព៖ {{ !empty($settings['telegram_token']) ? 'កម្រិត Setting' : 'កម្រិត .env' }}</span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        មិនទាន់កំណត់ (Not Configured)
                    </span>
                @endif
            </span>
        </div>
        <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
            <i class="fas fa-key text-xl"></i>
        </div>
    </div>

    <!-- Webhook URL -->
    <div class="rounded-xl bg-white p-5 shadow border border-slate-100 flex items-center justify-between">
        <div style="max-width: 80%;">
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Webhook URL (សកម្មលើ Telegram)</span>
            <span class="block text-xs font-mono font-bold mt-2 break-all {{ $actualWebhookUrl ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $actualWebhookUrl ?: 'មិនទាន់កំណត់ ឬក្រៅបណ្តាញ (Not Set)' }}
            </span>
        </div>
        <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
            <i class="fab fa-telegram-plane text-xl"></i>
        </div>
    </div>

    <!-- Messages Logged -->
    <div class="rounded-xl bg-white p-5 shadow border border-slate-100 flex items-center justify-between">
        <div>
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider font-sans">ប្រវត្តិផ្ញើសារសរុប (Logs)</span>
            <span class="block text-2xl font-bold text-slate-900 mt-1 font-mono">{{ $telegramLogs->total() }}</span>
        </div>
        <div class="rounded-lg bg-amber-50 p-3 text-amber-600">
            <i class="fas fa-history text-xl"></i>
        </div>
    </div>
</div>

<div class="mb-6 grid gap-6 lg:grid-cols-2">
    <!-- Set Webhook Card -->
    <div class="rounded-xl bg-white p-6 shadow border border-slate-100">
        <h2 class="mb-2 text-base font-semibold text-slate-950 flex items-center gap-2">🔌 កំណត់ Webhook</h2>
        <p class="mb-4 text-xs text-slate-500">សូមប្រើប្រាស់តំណភ្ជាប់ HTTPS សាធារណៈ (Localtunnel/Ngrok) ដើម្បីឱ្យ Telegram អាចហៅមកកាន់ប្រព័ន្ធរបស់លោកអ្នកបាន។</p>
        
        <form method="POST" action="{{ route('telegram-logs.set-webhook') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700">តំណភ្ជាប់ Webhook URL</label>
                <input 
                    type="url" 
                    name="webhook_url" 
                    value="{{ $actualWebhookUrl ?: url('/api/v1/telegram/webhook') }}" 
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" 
                    placeholder="https://your-domain.com/api/v1/telegram/webhook"
                    required>
                <p class="mt-1.5 text-[10px] text-slate-400">ប្រព័ន្ធបានបំពេញតំណភ្ជាប់សកម្មបច្ចុប្បន្នដោយស្វ័យប្រវត្ត។</p>
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-sm transition duration-150">
                💾 កំណត់ Webhook
            </button>
        </form>
    </div>

    <!-- Send Test Message Card -->
    <div class="rounded-xl bg-white p-6 shadow border border-slate-100">
        <h2 class="mb-2 text-base font-semibold text-slate-950 flex items-center gap-2">📨 ផ្ញើសារសាកល្បង (Test Message)</h2>
        <p class="mb-4 text-xs text-slate-500">ផ្ញើសារទៅកាន់ Telegram អតិថិជនដើម្បីធ្វើតេស្តសាកល្បងការតភ្ជាប់។</p>
        
        <form method="POST" action="{{ route('telegram-logs.send-test') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 font-sans">ជ្រើសរើសអតិថិជន (Customer)</label>
                <select name="customer_id" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">ជ្រើសរើសអតិថិជនដំបូងបង្អស់ (First Linked)</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} (ID: {{ $customer->telegram_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700">ខ្លឹមសារសារ (Message)</label>
                <textarea 
                    name="test_message" 
                    rows="3" 
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">✅ នេះជាសារសាកល្បងចេញពីប្រព័ន្ធបង់រំលស់ CityTech Billing System។</textarea>
            </div>
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 shadow-sm transition duration-150">
                🚀 ផ្ញើសារសាកល្បង
            </button>
        </form>
    </div>
</div>

<!-- Log List Card -->
<div class="rounded-xl bg-white p-6 shadow border border-slate-100">
    <h2 class="mb-4 text-base font-semibold text-slate-950 flex items-center gap-2">📜 ប្រវត្តិការផ្ញើសារថ្មីៗ (Recent Telegram Logs)</h2>
    <div class="overflow-x-auto rounded-lg border border-slate-100">
        <table class="min-w-full text-xs text-slate-700">
            <thead>
                <tr class="border-b bg-slate-50 text-left font-semibold text-slate-600">
                    <th class="px-4 py-3">ឈ្មោះអតិថិជន</th>
                    <th class="px-4 py-3">Telegram ID</th>
                    <th class="px-4 py-3" style="width: 50%;">ខ្លឹមសារសារ</th>
                    <th class="px-4 py-3">ថ្ងៃផ្ញើ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($telegramLogs as $log)
                    <tr class="hover:bg-slate-50 align-top">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $log->customer->name ?? 'មិនស្គាល់' }}</td>
                        <td class="px-4 py-3 text-slate-600 font-mono">{{ $log->customer->telegram_id ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-700 leading-relaxed">{!! nl2br(e($log->message)) !!}</td>
                        <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ optional($log->sent_at)->format('Y-m-d H:i') ?? $log->sent_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400">គ្មានប្រវត្តិនៃការផ្ញើសារត្រូវបានកត់ត្រានៅឡើយទេ។</td>
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
