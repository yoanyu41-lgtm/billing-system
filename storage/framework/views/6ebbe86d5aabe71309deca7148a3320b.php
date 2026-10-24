<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6 transition-all duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 ring-4 ring-emerald-50">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        <?php echo e(__('app.installment_settings')); ?>

                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        <?php echo e(app()->getLocale() === 'km' ? 'គ្រប់គ្រងការគណនាការប្រាក់បង់ផ្តាច់ និងច្បាប់ពិន័យយឺត' : 'Manage payoff interest rules and late payment fee configurations'); ?>

                    </p>
                </div>
            </div>

            <a href="<?php echo e(route('admin.settings.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition">
                <i class="fas fa-arrow-left"></i>
                <span><?php echo e(__('app.back_to_settings') ?? 'ត្រឡប់ទៅការកំណត់'); ?></span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl p-4 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <span class="flex-shrink-0 text-emerald-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <p class="font-medium"><?php echo e(session('success')); ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.settings.installment.update')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <!-- Card 1: Payoff Interest Settings (ការគណនាការប្រាក់បង់ផ្តាច់) -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-black">
                        <i class="fas fa-check-double"></i>
                    </span>
                    <span><?php echo e(app()->getLocale() === 'km' ? 'ការគណនាការប្រាក់ពេលបង់ផ្តាច់ (Pay Off)' : 'Pay Off Interest Calculation'); ?></span>
                </h2>
                <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg">
                    Pay Off Mode
                </span>
            </div>

            <p class="text-xs text-slate-500">
                <?php echo e(app()->getLocale() === 'km' 
                    ? 'ជ្រើសរើសរូបមន្តគណនាការប្រាក់លំនាំដើម ពេលអតិថិជនស្នើសុំបង់ផ្តាច់ (Principal + Interest)។' 
                    : 'Choose the default interest calculation method when a customer requests an early payoff with interest.'); ?>

            </p>

            <?php
                $currentPayoffMode = $settings['payoff_interest_mode'] ?? 'one_month';
            ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Option 1: One Month -->
                <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition <?php echo e($currentPayoffMode === 'one_month' ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'); ?>">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="payoff_interest_mode" value="one_month" <?php echo e($currentPayoffMode === 'one_month' ? 'checked' : ''); ?> class="text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-bold text-slate-800 block">
                            <?php echo e(app()->getLocale() === 'km' ? 'គិតការប្រាក់តែ ១ ខែ (ខែបច្ចុប្បន្ន)' : 'Accrue 1 Month Interest Only'); ?>

                        </span>
                    </div>
                </label>

                <!-- Option 2: All Remaining Months -->
                <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition <?php echo e($currentPayoffMode === 'all_remaining_months' ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'); ?>">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="payoff_interest_mode" value="all_remaining_months" <?php echo e($currentPayoffMode === 'all_remaining_months' ? 'checked' : ''); ?> class="text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-bold text-slate-800 block">
                            <?php echo e(app()->getLocale() === 'km' ? 'គិតការប្រាក់សរុបខែទាំងអស់ដែលនៅសល់' : 'All Remaining Unpaid Months Interest'); ?>

                        </span>
                    </div>
                </label>
            </div>

            <!-- Minimum Month Eligible for Payoff -->
            <?php
                $minPayoffMonth = $settings['payoff_min_month'] ?? '0';
            ?>
            <div class="border-t border-slate-100 pt-4">
                <div class="max-w-md">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        <?php echo e(app()->getLocale() === 'km' ? 'លក្ខខណ្ឌខែដែលអាចបង់ផ្តាច់បាន (Minimum Eligible Month)' : 'Minimum Eligible Month for Payoff'); ?>

                    </label>
                    <div class="relative">
                        <input type="number" step="1" min="0" name="payoff_min_month" value="<?php echo e($minPayoffMonth); ?>"
                               placeholder="0"
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">
                            <?php echo e(app()->getLocale() === 'km' ? 'ខែ' : 'months'); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Late Payment Settings (ការកំណត់បង់ប្រាក់យឺត) -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-black">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <span><?php echo e(app()->getLocale() === 'km' ? 'ការកំណត់ការបង់យឺត (Late Payment Settings)' : 'Late Payment Settings'); ?></span>
                </h2>
                <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg">
                    Late Policy
                </span>
            </div>

            <p class="text-xs text-slate-500">
                <?php echo e(app()->getLocale() === 'km' 
                    ? 'កំណត់គោលការណ៍ពិន័យម្តង ហើយប្រព័ន្ធនឹងគណនាស្វ័យប្រវត្តិ។ បុគ្គលិកគ្រាន់តែចុចទទួលប្រាក់ ហើយប្រព័ន្ធបង្ហាញចំនួនត្រូវបង់ដោយស្វ័យប្រវត្តិ។' 
                    : 'Configure late payment policy once, and system will calculate penalties automatically.'); ?>

            </p>

            <?php
                $lateFeeMethod = $settings['late_fee_method'] ?? (($settings['late_fee_type'] ?? '') === 'percentage' ? 'percentage' : 'fixed');
                $lateFeeFrequency = $settings['late_fee_frequency'] ?? 'daily';
                $lateFeeRate = $settings['late_fee_rate'] ?? ($settings['late_fee_amount'] ?? '1.00');
                $gracePeriodDays = $settings['late_grace_period_days'] ?? '3';
                $autoCalculate = ($settings['auto_calculate_late_fee'] ?? '1') === '1';
                $telegramReminder = ($settings['telegram_late_reminder'] ?? '1') === '1';
                $adminApproval = ($settings['admin_approval_late_fee'] ?? '0') === '1';
            ?>

            <!-- Method Selection -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Late Fee Method — វិធីគិតពិន័យ
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition <?php echo e($lateFeeMethod === 'fixed' ? 'border-amber-500 bg-amber-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'); ?>">
                        <input type="radio" name="late_fee_method" value="fixed" <?php echo e($lateFeeMethod === 'fixed' ? 'checked' : ''); ?> class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">Fixed Amount — គិតជាដុល្លារ ($)</span>
                            <span class="text-xs text-slate-500">ចំនួនទឹកប្រាក់ថេរកំណត់ក្នុងមួយថ្ងៃ ឬម្តង ($ Fixed)</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition <?php echo e($lateFeeMethod === 'percentage' ? 'border-amber-500 bg-amber-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'); ?>">
                        <input type="radio" name="late_fee_method" value="percentage" <?php echo e($lateFeeMethod === 'percentage' ? 'checked' : ''); ?> class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">Percentage — គិតជាភាគរយ (%)</span>
                            <span class="text-xs text-slate-500">គណនាភាគរយលើប្រាក់ដែលហួសកំណត់ (មិនមែនលើប្រាក់ដើមទាំងមូល)</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                <!-- Calculation Frequency -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Calculation Frequency — ភាពញឹកញាប់
                    </label>
                    <select name="late_fee_frequency" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                        <option value="daily" <?php echo e($lateFeeFrequency === 'daily' ? 'selected' : ''); ?>>Daily — គិតរៀងរាល់ថ្ងៃយឺត</option>
                        <option value="one_time" <?php echo e($lateFeeFrequency === 'one_time' ? 'selected' : ''); ?>>One-time — គិតតែម្តងប៉ុណ្ណោះ</option>
                    </select>
                </div>

                <!-- Fee Rate / Amount -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Late Fee Rate / Amount — តម្លៃពិន័យ
                    </label>
                    <input type="number" step="0.01" min="0" name="late_fee_rate" value="<?php echo e($lateFeeRate); ?>"
                           class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                           placeholder="1.00">
                    <p class="text-[11px] text-slate-400 mt-1">ឧទាហរណ៍៖ $1.00/ថ្ងៃ ឬ 1.00%</p>
                </div>

                <!-- Grace Period (Days) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Grace Period (Days) — ថ្ងៃអនុគ្រោះ
                    </label>
                    <input type="number" step="1" min="0" name="late_grace_period_days" value="<?php echo e($gracePeriodDays); ?>"
                           placeholder="3"
                           class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <p class="text-[11px] text-slate-400 mt-1">
                        ឧ. លើកលែង 3 ថ្ងៃដំបូង មិនទាន់គិតប្រាក់ពិន័យ
                    </p>
                </div>
            </div>

            <!-- Toggles / Checkboxes -->
            <div class="border-t border-slate-100 pt-4 space-y-3">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="auto_calculate_late_fee" value="1" <?php echo e($autoCalculate ? 'checked' : ''); ?> class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                    <div class="text-xs">
                        <span class="font-bold text-slate-800 block">Auto Calculate Late Fee — គណនាពិន័យដោយស្វ័យប្រវត្តិ</span>
                        <span class="text-slate-500">ប្រព័ន្ធគណនាប្រាក់ពិន័យស្វ័យប្រវត្តិតាមរូបមន្ត (ឧទាហរណ៍៖ (ថ្ងៃយឺត - ថ្ងៃអនុគ្រោះ) × តម្លៃពិន័យ)</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="telegram_late_reminder" value="1" <?php echo e($telegramReminder ? 'checked' : ''); ?> class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                    <div class="text-xs">
                        <span class="font-bold text-slate-800 block">Telegram Reminder — ជូនដំណឹងអតិថិជនបង់យឺត</span>
                        <span class="text-slate-500">អនុញ្ញាតឱ្យផ្ញើសាររំលឹក និងប៊ូតុងផ្ញើសេចក្តីជូនដំណឹងជាភាសាខ្មែរតាម Telegram</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="admin_approval_late_fee" value="1" <?php echo e($adminApproval ? 'checked' : ''); ?> class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500">
                    <div class="text-xs">
                        <span class="font-bold text-slate-800 block">Admin Approval — អនុម័តពេលកែប្រែ ឬលើកលែងពិន័យ</span>
                        <span class="text-slate-500">តម្រូវឱ្យមានការអនុម័តពី Admin ក្នុងករណីបុគ្គលិកចង់បញ្ចុះតម្លៃ ឬលើកលែងប្រាក់ពិន័យ</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md transition text-sm flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span><?php echo e(__('app.save_changes') ?? 'រក្សាទុកការផ្លាស់ប្តូរ'); ?></span>
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/settings/installment.blade.php ENDPATH**/ ?>