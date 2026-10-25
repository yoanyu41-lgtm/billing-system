<?php $__env->startSection('content'); ?>
<?php
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
?>

<div class="space-y-6">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">⚙️ <?php echo e($L('គ្រប់គ្រងប្រព័ន្ធ Telegram', 'Telegram Management')); ?></h1>
            <p class="text-xs text-slate-500 mt-1"><?php echo e($L('ផ្សព្វផ្សាយសារទៅគ្រប់គ្នា ផ្ញើសារទៅកាន់អតិថិជនម្នាក់ៗ និងពិនិត្យប្រវត្តិផ្ញើសារ។', 'Broadcast messages, send messages to individual customers, and view log history.')); ?></p>
        </div>
    </div>

    
    <?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-sm font-semibold flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span><?php echo e(session('success')); ?></span>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm font-semibold flex items-center gap-2">
        <i class="fas fa-times-circle text-rose-600"></i>
        <span><?php echo e(session('error')); ?></span>
    </div>
    <?php endif; ?>

    
    <div class="border-b border-slate-200">
        <nav class="flex space-x-6" aria-label="Tabs">
            
            <button onclick="switchTab('broadcast-tab')" id="btn-broadcast-tab" class="tab-btn pb-4 px-1 border-b-2 border-blue-600 font-bold text-sm text-blue-600 flex items-center gap-2 border-0 bg-transparent cursor-pointer transition">
                <i class="fas fa-paper-plane text-base"></i>
                <span><?php echo e($L('ផ្ញើសារ (Send Message)', 'Send Message')); ?></span>
            </button>
            
            
            <button onclick="switchTab('logs-tab')" id="btn-logs-tab" class="tab-btn pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-slate-500 hover:text-slate-800 flex items-center gap-2 border-0 bg-transparent cursor-pointer transition">
                <i class="fas fa-history text-base"></i>
                <span><?php echo e($L('ប្រវត្តិផ្ញើសារ (Message Logs)', 'Message Logs')); ?></span>
            </button>

            
            <?php if(auth()->user()->hasRole('Admin') || strtolower(auth()->user()->role ?? '') === 'admin'): ?>
            <button onclick="switchTab('settings-tab')" id="btn-settings-tab" class="tab-btn pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-slate-500 hover:text-slate-800 flex items-center gap-2 border-0 bg-transparent cursor-pointer transition">
                <i class="fas fa-cog text-base"></i>
                <span><?php echo e($L('ការកំណត់ Bot & Webhook', 'Bot Settings & Webhook')); ?></span>
            </button>
            <?php endif; ?>
        </nav>
    </div>

    
    
    
    <div id="content-broadcast-tab" class="tab-content space-y-6">
        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider"><?php echo e($L('អតិថិជនបានភ្ជាប់ Telegram', 'Linked Telegram Customers')); ?></span>
                    <span class="block text-2xl font-bold text-slate-950 mt-1"><?php echo e($totalLinked); ?> / <?php echo e($totalCustomers); ?> នាក់</span>
                </div>
                <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                    <i class="fab fa-telegram-plane text-2xl"></i>
                </div>
            </div>
            
            <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider"><?php echo e($L('អត្រាការភ្ជាប់គណនី (Linked Rate)', 'Linked Rate')); ?></span>
                    <span class="block text-2xl font-bold text-slate-950 mt-1">
                        <?php echo e($totalCustomers > 0 ? round(($totalLinked / $totalCustomers) * 100, 1) : 0); ?>%
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
                    <span><?php echo e($L('ផ្សព្វផ្សាយសារទៅកាន់អតិថិជនទាំងអស់', 'Broadcast Message to All Customers')); ?></span>
                </h2>
                
                <form method="POST" action="<?php echo e(route('admin.broadcast.send')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="message" class="mb-2 block text-xs font-bold text-slate-600 uppercase tracking-wider"><?php echo e($L('ខ្លឹមសារសារផ្សព្វផ្សាយ (Message Content)', 'Message Content')); ?></label>
                        <textarea 
                            name="message" 
                            id="message" 
                            rows="9" 
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" 
                            placeholder="<?php echo e($L('វាយខ្លឹមសារសារផ្សព្វផ្សាយនៅទីនេះ...', 'Type your broadcast message content here...')); ?>"
                            required></textarea>
                        <p class="mt-2 text-xs text-slate-400"><?php echo e($L('សារផ្សព្វផ្សាយនេះនឹងត្រូវផ្ញើទៅកាន់អតិថិជនចំនួន', 'This broadcast will be sent directly to')); ?> <b><?php echo e($totalLinked); ?></b> <?php echo e($L('នាក់ភ្លាមៗ។', 'linked customers instantly.')); ?></p>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button 
                            type="reset" 
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 border-0 cursor-pointer">
                            <?php echo e($L('សម្អាត (Clear)', 'Clear')); ?>

                        </button>
                        <button 
                            type="submit" 
                            class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition duration-150 flex items-center gap-2 border-0 cursor-pointer"
                            onclick="return confirm('<?php echo e($L('តើលោកអ្នកពិតជាចង់ផ្ញើសារផ្សព្វផ្សាយនេះទៅកាន់អតិថិជនទាំងអស់មែនទេ?', 'Are you sure you want to send this broadcast to all linked customers?')); ?>')">
                            <i class="fas fa-paper-plane"></i>
                            <span><?php echo e($L('ផ្ញើសារផ្សព្វផ្សាយ (Broadcast Now)', 'Broadcast Now')); ?></span>
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
                        <span><?php echo e($L('ផ្ញើសារទៅកាន់អតិថិជនម្នាក់ៗ', 'Send Message to Individual')); ?></span>
                    </h2>
                    
                    <form method="POST" action="<?php echo e(route('telegram-logs.send-test')); ?>" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-600 uppercase tracking-wider"><?php echo e($L('ជ្រើសរើសអតិថិជន', 'Select Customer')); ?></label>
                            <select name="customer_id" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value=""><?php echo e($L('ជ្រើសរើសអតិថិជនដំបូងបង្អស់', 'First Linked Customer')); ?></option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?> (ID: <?php echo e($customer->telegram_id); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-600 uppercase tracking-wider"><?php echo e($L('ខ្លឹមសារសារ (Message)', 'Message')); ?></label>
                            <textarea 
                                name="test_message" 
                                rows="3" 
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="<?php echo e($L('វាយសារនៅទីនេះ...', 'Type your message here...')); ?>"
                                required>✅ នេះជាសារចេញពីប្រព័ន្ធបង់រំលស់ CityTech Billing System។</textarea>
                        </div>

                        
                        <?php if(!empty($allQrList)): ?>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-600 uppercase tracking-wider">
                                <i class="fas fa-qrcode text-purple-500"></i>
                                <?php echo e($L('ជ្រើស QR Code ភ្ជាប់ (ស្រេចចិត្ត)', 'Attach QR Code (Optional)')); ?>

                            </label>
                            <input type="hidden" name="qr_key" id="individual_qr_key" value="">
                            <div class="grid grid-cols-3 gap-1.5">
                                <div onclick="selectIndividualQr('', this)"
                                    class="qr-option-card individual-qr-card selected-qr cursor-pointer rounded-lg border-2 border-blue-400 bg-blue-50 p-1.5 flex flex-col items-center gap-1 transition-all duration-150"
                                    data-qr-key="">
                                    <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center">
                                        <i class="fas fa-ban text-slate-400"></i>
                                    </div>
                                    <span class="text-xs font-medium text-slate-500 text-center leading-tight"><?php echo e($L('គ្មាន', 'None')); ?></span>
                                </div>
                                <?php $__currentLoopData = $allQrList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qrItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div onclick="selectIndividualQr('<?php echo e($qrItem['key']); ?>', this)"
                                    class="qr-option-card individual-qr-card cursor-pointer rounded-lg border-2 border-slate-200 bg-white p-1.5 flex flex-col items-center gap-1 transition-all duration-150 hover:border-purple-400 hover:bg-purple-50"
                                    data-qr-key="<?php echo e($qrItem['key']); ?>">
                                    <img src="<?php echo e(asset('storage/' . $qrItem['img'])); ?>"
                                        class="w-10 h-10 object-contain rounded border border-slate-200"
                                        onerror="this.style.display='none'">
                                    <span class="text-xs font-medium text-slate-700 text-center leading-tight"><?php echo e($qrItem['label']); ?></span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm border-0 cursor-pointer transition">
                            🚀 <?php echo e($L('ផ្ញើសារផ្ទាល់ខ្លួន (Send Message)', 'Send Message')); ?>

                        </button>
                    </form>
                </div>

                <!-- Formatting Guide -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
                    <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-500"></i>
                        <span><?php echo e($L('ការប្រើប្រាស់ Markdown', 'Markdown Formatting')); ?></span>
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="rounded-lg bg-slate-50 p-2.5">
                            <span class="block text-xs font-bold text-slate-700"><?php echo e($L('អក្សរដិត (Bold)', 'Bold')); ?></span>
                            <code class="text-xs text-blue-600">*<?php echo e($L('ខ្លឹមសារដិត', 'bold text')); ?>*</code>
                        </div>
                        
                        <div class="rounded-lg bg-slate-50 p-2.5">
                            <span class="block text-xs font-bold text-slate-700"><?php echo e($L('អក្សរទ្រេត (Italic)', 'Italic')); ?></span>
                            <code class="text-xs text-blue-600">_<?php echo e($L('ខ្លឹមសារទ្រេត', 'italic text')); ?>_</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div id="content-logs-tab" class="tab-content hidden space-y-4">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-950 flex items-center gap-2">
                    <i class="fas fa-history text-slate-400"></i>
                    <span><?php echo e($L('ប្រវត្តិការផ្ញើសារថ្មីៗ (Recent Telegram Logs)', 'Recent Telegram Logs')); ?></span>
                </h2>
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg"><?php echo e($telegramLogs->total()); ?> <?php echo e($L('សារសរុប', 'Total Logs')); ?></span>
            </div>
            
            <div class="overflow-x-auto rounded-lg border border-slate-100">
                <table class="min-w-full text-xs text-slate-700">
                    <thead>
                        <tr class="border-b bg-slate-50 text-left font-semibold text-slate-600">
                            <th class="px-4 py-3"><?php echo e($L('ឈ្មោះអតិថិជន', 'Customer Name')); ?></th>
                            <th class="px-4 py-3">Telegram ID</th>
                            <th class="px-4 py-3" style="width: 50%;"><?php echo e($L('ខ្លឹមសារសារ', 'Message Content')); ?></th>
                            <th class="px-4 py-3"><?php echo e($L('ថ្ងៃផ្ញើ', 'Sent Date')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $telegramLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50 align-top">
                                <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($log->customer->name ?? 'មិនស្គាល់'); ?></td>
                                <td class="px-4 py-3 text-slate-600 font-mono"><?php echo e($log->customer->telegram_id ?? '-'); ?></td>
                                <td class="px-4 py-3 text-slate-700 leading-relaxed"><?php echo nl2br(e($log->message)); ?></td>
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap"><?php echo e(optional($log->sent_at)->format('Y-m-d H:i') ?? $log->sent_at); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400"><?php echo e($L('គ្មានប្រវត្តិនៃការផ្ញើសារត្រូវបានកត់ត្រានៅឡើយទេ។', 'No telegram messages logged yet.')); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <?php echo e($telegramLogs->links()); ?>

            </div>
        </div>
    </div>

    
    <?php if(auth()->user()->hasRole('Admin') || strtolower(auth()->user()->role ?? '') === 'admin'): ?>
    <div id="content-settings-tab" class="tab-content hidden space-y-6">
        <div class="grid gap-6 md:grid-cols-2 items-start">
            <!-- Webhook Configuration Card -->
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
                <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-link text-indigo-500"></i>
                    <span><?php echo e($L('កំណត់ Telegram Webhook (Set Webhook)', 'Set Telegram Webhook')); ?></span>
                </h2>
                
                <p class="text-xs text-slate-500 leading-relaxed">
                    <?php echo e($L('បញ្ចូល Webhook URL ដើម្បីទទួលដំណឹងទូទាត់ប្រាក់ពីអតិថិជនតាម Telegram Bot ដោយស្វ័យប្រវត្តិ។', 'Set the Webhook URL to automatically receive customer payment receipts via Telegram Bot.')); ?>

                </p>

                <form method="POST" action="<?php echo e(route('telegram-logs.set-webhook')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-600 uppercase tracking-wider">Webhook Endpoint URL</label>
                        <input type="url" name="webhook_url" value="<?php echo e($actualWebhookUrl ?? url('/api/telegram/webhook')); ?>" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div class="rounded-lg bg-slate-50 p-3 text-xs space-y-1">
                        <span class="font-bold text-slate-700 block"><?php echo e($L('ស្ថានភាព Webhook បច្ចុប្បន្ន (Current Webhook Status):', 'Current Webhook Status:')); ?></span>
                        <?php if($actualWebhookUrl): ?>
                            <span class="text-emerald-600 font-mono font-bold flex items-center gap-1 break-all">
                                <i class="fas fa-check-circle"></i> <?php echo e($actualWebhookUrl); ?>

                            </span>
                        <?php else: ?>
                            <span class="text-rose-500 font-bold flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo e($L('មិនទាន់បានកំណត់ Webhook ទេ (Not Configured)', 'Webhook Not Set')); ?>

                            </span>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-indigo-700 shadow-sm border-0 cursor-pointer transition flex items-center justify-center gap-2">
                        <i class="fas fa-plug"></i>
                        <span><?php echo e($L('រក្សាទុក & កំណត់ Webhook (Set Webhook)', 'Set Webhook URL')); ?></span>
                    </button>
                </form>
            </div>

            <!-- Bot Token & Instructions Card -->
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-100 space-y-4">
                <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-robot text-purple-500"></i>
                    <span><?php echo e($L('ព័ត៌មាន Bot API Token (Bot Token Info)', 'Bot Token Information')); ?></span>
                </h2>

                <div class="rounded-lg bg-slate-50 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600">Telegram Bot Token:</span>
                        <?php if($tokenConfigured): ?>
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold border border-emerald-300">
                                🟢 <?php echo e($L('បានភ្ជាប់', 'Configured')); ?>

                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-bold border border-rose-300">
                                🔴 <?php echo e($L('មិនទាន់ភ្ជាប់', 'Not Configured')); ?>

                            </span>
                        <?php endif; ?>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        <?php echo e($L('លោកអ្នកអាចកំណត់ ឬប្តូរ Telegram Bot API Token នៅក្នុងទំព័រ ការកំណត់ (General Settings)។', 'You can configure or change the Telegram Bot API Token in General Settings.')); ?>

                    </p>

                    <div class="pt-1">
                        <a href="<?php echo e(route('admin.settings.index')); ?>" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition">
                            <i class="fas fa-cog"></i>
                            <span><?php echo e($L('ទៅកាន់ទំព័រ Settings (Go to Settings)', 'Go to Settings')); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>


<style>
.qr-option-card { user-select: none; }
.qr-option-card.selected-qr {
    border-color: #7c3aed !important;
    background-color: #f5f3ff !important;
    box-shadow: 0 0 0 2px #c4b5fd;
}
</style>


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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/telegram-logs/index.blade.php ENDPATH**/ ?>