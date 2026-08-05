@extends('layouts.app')

@section('content')
@php
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">⚙️ {{ $L('គ្រប់គ្រងប្រព័ន្ធ Telegram', 'Telegram Management') }}</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $L('ផ្សព្វផ្សាយសារទៅគ្រប់គ្នា ផ្ញើសារទៅកាន់អតិថិជនម្នាក់ៗ និងពិនិត្យប្រវត្តិផ្ញើសារ។', 'Broadcast messages, send messages to individual customers, and view log history.') }}</p>
        </div>
    </div>

    {{-- Session Flash Alerts --}}
    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-sm font-semibold flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm font-semibold flex items-center gap-2">
        <i class="fas fa-times-circle text-rose-600"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Tabs Controller --}}
    <div class="border-b border-slate-200">
        <nav class="flex space-x-6" aria-label="Tabs">
            {{-- Tab 1 Button --}}
            <button onclick="switchTab('broadcast-tab')" id="btn-broadcast-tab" class="tab-btn pb-4 px-1 border-b-2 border-blue-600 font-bold text-sm text-blue-600 flex items-center gap-2 border-0 bg-transparent cursor-pointer transition">
                <i class="fas fa-paper-plane text-base"></i>
                <span>{{ $L('ផ្ញើសារ (Send Message)', 'Send Message') }}</span>
            </button>
            
            {{-- Tab 2 Button --}}
            <button onclick="switchTab('logs-tab')" id="btn-logs-tab" class="tab-btn pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-slate-500 hover:text-slate-800 flex items-center gap-2 border-0 bg-transparent cursor-pointer transition">
                <i class="fas fa-history text-base"></i>
                <span>{{ $L('ប្រវត្តិផ្ញើសារ (Message Logs)', 'Message Logs') }}</span>
            </button>

            {{-- Tab 3 Button --}}
            <button onclick="switchTab('settings-tab')" id="btn-settings-tab" class="tab-btn pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-slate-500 hover:text-slate-800 flex items-center gap-2 border-0 bg-transparent cursor-pointer transition">
                <i class="fas fa-cog text-base"></i>
                <span>{{ $L('ការកំណត់ Bot & Webhook', 'Bot Settings & Webhook') }}</span>
            </button>
        </nav>
    </div>

    {{-- TAB CONTENT AREA --}}
    
    {{-- Tab 1: Send Message Content (Broadcast + Individual) --}}
    <div id="content-broadcast-tab" class="tab-content space-y-6">
        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $L('អតិថិជនបានភ្ជាប់ Telegram', 'Linked Telegram Customers') }}</span>
                    <span class="block text-2xl font-bold text-slate-950 mt-1">{{ $totalLinked }} / {{ $totalCustomers }} នាក់</span>
                </div>
                <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                    <i class="fab fa-telegram-plane text-2xl"></i>
                </div>
            </div>
            
            <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $L('អត្រាការភ្ជាប់គណនី (Linked Rate)', 'Linked Rate') }}</span>
                    <span class="block text-2xl font-bold text-slate-950 mt-1">
                        {{ $totalCustomers > 0 ? round(($totalLinked / $totalCustomers) * 100, 1) : 0 }}%
                    </span>
                </div>
                <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3 items-start">
            <!-- Composer Form (Broadcast) -->
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 lg:col-span-2 space-y-4">
                <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-bullhorn text-blue-500"></i>
                    <span>{{ $L('ផ្សព្វផ្សាយសារទៅកាន់អតិថិជនទាំងអស់', 'Broadcast Message to All Customers') }}</span>
                </h2>
                
                <form method="POST" action="{{ route('admin.broadcast.send') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="message" class="mb-2 block text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $L('ខ្លឹមសារសារផ្សព្វផ្សាយ (Message Content)', 'Message Content') }}</label>
                        <textarea 
                            name="message" 
                            id="message" 
                            rows="9" 
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" 
                            placeholder="{{ $L('វាយខ្លឹមសារសារផ្សព្វផ្សាយនៅទីនេះ...', 'Type your broadcast message content here...') }}"
                            required></textarea>
                        <p class="mt-2 text-xs text-slate-400">{{ $L('សារផ្សព្វផ្សាយនេះនឹងត្រូវផ្ញើទៅកាន់អតិថិជនចំនួន', 'This broadcast will be sent directly to') }} <b>{{ $totalLinked }}</b> {{ $L('នាក់ភ្លាមៗ។', 'linked customers instantly.') }}</p>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button 
                            type="reset" 
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 border-0 cursor-pointer">
                            {{ $L('សម្អាត (Clear)', 'Clear') }}
                        </button>
                        <button 
                            type="submit" 
                            class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition duration-150 flex items-center gap-2 border-0 cursor-pointer"
                            onclick="return confirm('{{ $L('តើលោកអ្នកពិតជាចង់ផ្ញើសារផ្សព្វផ្សាយនេះទៅកាន់អតិថិជនទាំងអស់មែនទេ?', 'Are you sure you want to send this broadcast to all linked customers?') }}')">
                            <i class="fas fa-paper-plane"></i>
                            <span>{{ $L('ផ្ញើសារផ្សព្វផ្សាយ (Broadcast Now)', 'Broadcast Now') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column (Individual Message + Formatting Guide) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Send Individual Message -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
                    <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-paper-plane text-emerald-500"></i>
                        <span>{{ $L('ផ្ញើសារទៅកាន់អតិថិជនម្នាក់ៗ', 'Send Message to Individual') }}</span>
                    </h2>
                    
                    <form method="POST" action="{{ route('telegram-logs.send-test') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $L('ជ្រើសរើសអតិថិជន', 'Select Customer') }}</label>
                            <select name="customer_id" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">{{ $L('ជ្រើសរើសអតិថិជនដំបូងបង្អស់', 'First Linked Customer') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} (ID: {{ $customer->telegram_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $L('ខ្លឹមសារសារ (Message)', 'Message') }}</label>
                            <textarea 
                                name="test_message" 
                                rows="3" 
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="{{ $L('វាយសារនៅទីនេះ...', 'Type your message here...') }}"
                                required>✅ នេះជាសារចេញពីប្រព័ន្ធបង់រំលស់ CityTech Billing System។</textarea>
                        </div>

                        {{-- QR Code Selector for Individual Message --}}
                        @if(!empty($allQrList))
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-600 uppercase tracking-wider">
                                <i class="fas fa-qrcode text-purple-500"></i>
                                {{ $L('ជ្រើស QR Code ភ្ជាប់ (ស្រេចចិត្ត)', 'Attach QR Code (Optional)') }}
                            </label>
                            <input type="hidden" name="qr_key" id="individual_qr_key" value="">
                            <div class="grid grid-cols-3 gap-1.5">
                                <div onclick="selectIndividualQr('', this)"
                                    class="qr-option-card individual-qr-card selected-qr cursor-pointer rounded-lg border-2 border-blue-400 bg-blue-50 p-1.5 flex flex-col items-center gap-1 transition-all duration-150"
                                    data-qr-key="">
                                    <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center">
                                        <i class="fas fa-ban text-slate-400"></i>
                                    </div>
                                    <span class="text-xs font-medium text-slate-500 text-center leading-tight">{{ $L('គ្មាន', 'None') }}</span>
                                </div>
                                @foreach($allQrList as $qrItem)
                                <div onclick="selectIndividualQr('{{ $qrItem['key'] }}', this)"
                                    class="qr-option-card individual-qr-card cursor-pointer rounded-lg border-2 border-slate-200 bg-white p-1.5 flex flex-col items-center gap-1 transition-all duration-150 hover:border-purple-400 hover:bg-purple-50"
                                    data-qr-key="{{ $qrItem['key'] }}">
                                    <img src="{{ asset('storage/' . $qrItem['img']) }}"
                                        class="w-10 h-10 object-contain rounded border border-slate-200"
                                        onerror="this.style.display='none'">
                                    <span class="text-xs font-medium text-slate-700 text-center leading-tight">{{ $qrItem['label'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm border-0 cursor-pointer transition">
                            🚀 {{ $L('ផ្ញើសារផ្ទាល់ខ្លួន (Send Message)', 'Send Message') }}
                        </button>
                    </form>
                </div>

                <!-- Formatting Guide -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
                    <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-500"></i>
                        <span>{{ $L('ការប្រើប្រាស់ Markdown', 'Markdown Formatting') }}</span>
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="rounded-lg bg-slate-50 p-2.5">
                            <span class="block text-xs font-bold text-slate-700">{{ $L('អក្សរដិត (Bold)', 'Bold') }}</span>
                            <code class="text-xs text-blue-600">*{{ $L('ខ្លឹមសារដិត', 'bold text') }}*</code>
                        </div>
                        
                        <div class="rounded-lg bg-slate-50 p-2.5">
                            <span class="block text-xs font-bold text-slate-700">{{ $L('អក្សរទ្រេត (Italic)', 'Italic') }}</span>
                            <code class="text-xs text-blue-600">_{{ $L('ខ្លឹមសារទ្រេត', 'italic text') }}_</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab 2: Logs Content --}}
    <div id="content-logs-tab" class="tab-content hidden space-y-4">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-950 flex items-center gap-2">
                    <i class="fas fa-history text-slate-400"></i>
                    <span>{{ $L('ប្រវត្តិការផ្ញើសារថ្មីៗ (Recent Telegram Logs)', 'Recent Telegram Logs') }}</span>
                </h2>
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">{{ $telegramLogs->total() }} {{ $L('សារសរុប', 'Total Logs') }}</span>
            </div>
            
            <div class="overflow-x-auto rounded-lg border border-slate-100">
                <table class="min-w-full text-xs text-slate-700">
                    <thead>
                        <tr class="border-b bg-slate-50 text-left font-semibold text-slate-600">
                            <th class="px-4 py-3">{{ $L('ឈ្មោះអតិថិជន', 'Customer Name') }}</th>
                            <th class="px-4 py-3">Telegram ID</th>
                            <th class="px-4 py-3" style="width: 50%;">{{ $L('ខ្លឹមសារសារ', 'Message Content') }}</th>
                            <th class="px-4 py-3">{{ $L('ថ្ងៃផ្ញើ', 'Sent Date') }}</th>
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
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400">{{ $L('គ្មានប្រវត្តិនៃការផ្ញើសារត្រូវបានកត់ត្រានៅឡើយទេ។', 'No telegram messages logged yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $telegramLogs->links() }}
            </div>
        </div>
    </div>

    {{-- Tab 3: Bot Settings & Webhook Content --}}
    <div id="content-settings-tab" class="tab-content hidden space-y-6">
        <div class="grid gap-6 md:grid-cols-2 items-start">
            <!-- Webhook Configuration Card -->
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
                <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-link text-indigo-500"></i>
                    <span>{{ $L('កំណត់ Telegram Webhook (Set Webhook)', 'Set Telegram Webhook') }}</span>
                </h2>
                
                <p class="text-xs text-slate-500 leading-relaxed">
                    {{ $L('បញ្ចូល Webhook URL ដើម្បីទទួលដំណឹងទូទាត់ប្រាក់ពីអតិថិជនតាម Telegram Bot ដោយស្វ័យប្រវត្តិ។', 'Set the Webhook URL to automatically receive customer payment receipts via Telegram Bot.') }}
                </p>

                <form method="POST" action="{{ route('telegram-logs.set-webhook') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-600 uppercase tracking-wider">Webhook Endpoint URL</label>
                        <input type="url" name="webhook_url" value="{{ $actualWebhookUrl ?? url('/api/telegram/webhook') }}" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div class="rounded-lg bg-slate-50 p-3 text-xs space-y-1">
                        <span class="font-bold text-slate-700 block">{{ $L('ស្ថានភាព Webhook បច្ចុប្បន្ន (Current Webhook Status):', 'Current Webhook Status:') }}</span>
                        @if($actualWebhookUrl)
                            <span class="text-emerald-600 font-mono font-bold flex items-center gap-1 break-all">
                                <i class="fas fa-check-circle"></i> {{ $actualWebhookUrl }}
                            </span>
                        @else
                            <span class="text-rose-500 font-bold flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i> {{ $L('មិនទាន់បានកំណត់ Webhook ទេ (Not Configured)', 'Webhook Not Set') }}
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-indigo-700 shadow-sm border-0 cursor-pointer transition flex items-center justify-center gap-2">
                        <i class="fas fa-plug"></i>
                        <span>{{ $L('រក្សាទុក & កំណត់ Webhook (Set Webhook)', 'Set Webhook URL') }}</span>
                    </button>
                </form>
            </div>

            <!-- Bot Token & Instructions Card -->
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
                <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-robot text-purple-500"></i>
                    <span>{{ $L('ព័ត៌មាន Bot API Token (Bot Token Info)', 'Bot Token Information') }}</span>
                </h2>

                <div class="rounded-lg bg-slate-50 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600">Telegram Bot Token:</span>
                        @if($tokenConfigured)
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold border border-emerald-300">
                                🟢 {{ $L('បានភ្ជាប់', 'Configured') }}
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-bold border border-rose-300">
                                🔴 {{ $L('មិនទាន់ភ្ជាប់', 'Not Configured') }}
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        {{ $L('លោកអ្នកអាចកំណត់ ឬប្តូរ Telegram Bot API Token នៅក្នុងទំព័រ ការកំណត់ (General Settings)។', 'You can configure or change the Telegram Bot API Token in General Settings.') }}
                    </p>

                    <div class="pt-1">
                        <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition">
                            <i class="fas fa-cog"></i>
                            <span>{{ $L('ទៅកាន់ទំព័រ Settings (Go to Settings)', 'Go to Settings') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Selector JS & CSS --}}
<style>
.qr-option-card { user-select: none; }
.qr-option-card.selected-qr {
    border-color: #7c3aed !important;
    background-color: #f5f3ff !important;
    box-shadow: 0 0 0 2px #c4b5fd;
}
</style>

{{-- Vanilla JS Tab Controller --}}
<script>
function switchTab(tabId) {
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Show target tab content
    document.getElementById('content-' + tabId).classList.remove('hidden');
    
    // Reset all tab button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600', 'font-bold');
        btn.classList.add('border-transparent', 'text-slate-500', 'font-medium');
    });
    
    // Set target tab button style
    const activeBtn = document.getElementById('btn-' + tabId);
    activeBtn.classList.remove('border-transparent', 'text-slate-500', 'font-medium');
    activeBtn.classList.add('border-blue-600', 'text-blue-600', 'font-bold');
    
    // Store active tab in localStorage
    localStorage.setItem('activeTelegramTab', tabId);
}

// QR Selector for Broadcast
function selectBroadcastQr(key, el) {
    document.querySelectorAll('.broadcast-qr-card').forEach(c => c.classList.remove('selected-qr'));
    el.classList.add('selected-qr');
    document.getElementById('broadcast_qr_key').value = key;
}

// QR Selector for Individual Message
function selectIndividualQr(key, el) {
    document.querySelectorAll('.individual-qr-card').forEach(c => c.classList.remove('selected-qr'));
    el.classList.add('selected-qr');
    document.getElementById('individual_qr_key').value = key;
}

// Restore active tab on load (e.g. after form submit or pagination click)
document.addEventListener('DOMContentLoaded', function() {
    // Check url search params first (if user is paginating logs, they should stay on logs tab)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page')) {
        switchTab('logs-tab');
        return;
    }

    const savedTab = localStorage.getItem('activeTelegramTab');
    if (savedTab && document.getElementById('btn-' + savedTab)) {
        switchTab(savedTab);
    }
});
</script>
@endsection
