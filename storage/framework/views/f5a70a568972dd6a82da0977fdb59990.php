<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-6">
    <!-- Header Section (រូបភាពទី១) -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                <?php echo e(__('app.late_customers') ?? 'អតិថិជនបង់យឺត'); ?>

            </h1>
            <p class="text-sm text-slate-500 mt-1">
                <?php echo e(app()->getLocale() === 'km' 
                    ? 'បង្ហាញអតិថិជនដែលមានការបង់យឺត និងអនុវត្តប្រាក់ពិន័យចំពោះអតិថិជន' 
                    : 'Manage overdue installment payments and apply late fees.'); ?>

            </p>
        </div>
        <div>
            <form method="POST" action="<?php echo e(route('late-payments.due-reminders')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" 
                        onclick="return confirm('តើលោកអ្នកពិតជាចង់ផ្ញើសាររំលឹកជាភាសាខ្មែរ ទៅកាន់អតិថិជនដល់ថ្ងៃកំណត់ទាំងអស់មែនទេ?')"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition duration-150 flex items-center gap-2 text-sm shadow-md border-0 cursor-pointer"
                >
                    <i class="fab fa-telegram-plane text-base"></i>
                    <span><?php echo e(app()->getLocale() === 'km' ? 'ផ្ញើសេចក្តីជូនដំណឹងតាម Telegram' : 'Send Reminder via Telegram'); ?></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notices -->
    <?php if(session('success')): ?>
        <div class="px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-xs text-sm">
            <i class="fas fa-check-circle mr-2 text-emerald-500 text-lg"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center shadow-xs text-sm">
            <i class="fas fa-exclamation-circle mr-2 text-red-500 text-lg"></i>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <!-- 1. ស្វែងរក និងតម្រៀប (Search & Filter Bar) -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <form method="GET" action="<?php echo e(route('late-payments.index')); ?>" class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center">
            <!-- Search Text -->
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="<?php echo e(app()->getLocale() === 'km' ? 'ស្វែងរកអតិថិជន (ឈ្មោះ, លេខទូរស័ព្ទ, ផលិតផល...)' : 'Search customer (name, phone, product...)'); ?>"
                       class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Product Filter -->
            <div class="w-full lg:w-48">
                <select name="product_id" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value=""><?php echo e(app()->getLocale() === 'km' ? 'All Products (ផលិតផលទាំងអស់)' : 'All Products'); ?></option>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($prod->id); ?>" <?php echo e(request('product_id') == $prod->id ? 'selected' : ''); ?>>
                            <?php echo e($prod->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Days Late Range Filter -->
            <div class="w-full lg:w-48">
                <select name="days_range" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value=""><?php echo e(app()->getLocale() === 'km' ? 'All Overdue Days (ថ្ងៃយឺតទាំងអស់)' : 'All Overdue Days'); ?></option>
                    <option value="1-7" <?php echo e(request('days_range') == '1-7' ? 'selected' : ''); ?>>1 - 7 <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃ' : 'Days'); ?></option>
                    <option value="8-15" <?php echo e(request('days_range') == '8-15' ? 'selected' : ''); ?>>8 - 15 <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃ' : 'Days'); ?></option>
                    <option value="16+" <?php echo e(request('days_range') == '16+' ? 'selected' : ''); ?>>16+ <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃឡើងទៅ' : 'Days'); ?></option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-sm transition flex items-center gap-2">
                    <i class="fas fa-search text-xs"></i>
                    <span>Search</span>
                </button>
                <a href="<?php echo e(route('late-payments.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- 3. បញ្ជីអតិថិជនបង់យឺត និងប្រាក់ពិន័យ (Table) -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50/75">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customer'); ?></th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ផលិតផល' : 'Product'); ?></th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់ត្រូវបង់/នៅសល់' : 'Due Amount'); ?></th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃយឺត' : 'Days Late'); ?></th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់ពិន័យយឺត' : 'Late Fee'); ?></th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'សកម្មភាព' : 'Actions'); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $lateInstallments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $daysLate = $installment->daysLate();
                        $calculatedFee = $installment->calculatePenalty();
                        $monthlyDue = (float) $installment->monthly_payment;
                    ?>
                    <tr class="hover:bg-slate-50/80 transition duration-150">
                        <td class="px-5 py-4 whitespace-nowrap text-xs font-semibold text-slate-400">
                            <?php echo e($idx + 1); ?>

                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-900"><?php echo e($installment->customer?->name ?? 'N/A'); ?></div>
                            <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                <i class="fas fa-phone-alt text-[10px] text-slate-400"></i>
                                <span><?php echo e($installment->customer?->phone ?? 'គ្មានលេខទូរស័ព្ទ'); ?></span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-700">
                            <div class="font-medium max-w-[200px] truncate" title="<?php echo e($installment->product?->name); ?>">
                                <?php echo e($installment->product?->name ?? 'N/A'); ?>

                            </div>
                            <div class="text-[11px] text-slate-400">#INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?></div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-bold text-rose-600 font-mono">$<?php echo e(number_format($installment->remaining_balance, 2)); ?></div>
                            <div class="text-[11px] text-slate-400"><?php echo e(app()->getLocale() === 'km' ? 'ខែនេះ៖' : 'Monthly:'); ?> $<?php echo e(number_format($monthlyDue, 2)); ?></div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-amber-600 font-mono">
                                <i class="fas fa-clock text-xs"></i>
                                <?php echo e($daysLate); ?> <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃ' : 'Days'); ?>

                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-sm font-black text-red-600 font-mono">
                                $<?php echo e(number_format($calculatedFee, 2)); ?>

                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                <?php echo e(app()->getLocale() === 'km' ? 'បង់យឺត' : 'Overdue'); ?>

                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right space-x-1.5">
                            <!-- ពិនិត្យលម្អិត (Schedule / Detail View) -->
                            <a href="<?php echo e(route('installments.schedule', $installment)); ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg text-xs transition border border-blue-200 shadow-2xs">
                                <i class="fas fa-eye text-xs"></i>
                                <span><?php echo e(app()->getLocale() === 'km' ? 'ពិនិត្យលម្អិត' : 'View'); ?></span>
                            </a>

                            <!-- ៤. ប៊ូតុង ដាក់ពិន័យ (Open Modal) -->
                            <button type="button" onclick="openPenaltyModal(<?php echo e($installment->id); ?>)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs transition shadow-2xs">
                                <i class="fas fa-coins text-xs"></i>
                                <span><?php echo e(app()->getLocale() === 'km' ? 'ដាក់ពិន័យ' : 'Apply Fee'); ?></span>
                            </button>

                            <!-- Collect Payment Direct Button -->
                            <a href="<?php echo e(route('payments.create', ['installment_id' => $installment->id, 'penalty_amount' => $calculatedFee])); ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition shadow-2xs"
                               title="<?php echo e(app()->getLocale() === 'km' ? 'ទទួលប្រាក់ពីអតិថិជន' : 'Collect Payment'); ?>">
                                <i class="fas fa-cash-register text-xs"></i>
                                <span><?php echo e(app()->getLocale() === 'km' ? 'ទទួលប្រាក់' : 'Collect'); ?></span>
                            </a>

                            <!-- Telegram Reminder Button -->
                            <button type="button" onclick="document.getElementById('remind-modal-<?php echo e($installment->id); ?>').classList.remove('hidden')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition"
                                    title="<?php echo e(app()->getLocale() === 'km' ? 'ផ្ញើសាររំលឹក' : 'Notify Telegram'); ?>">
                                <i class="fab fa-telegram-plane"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">
                                    <?php echo e(__('app.all_payments_up_to_date')); ?>

                                </p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- ៥. Popup គណនាប្រាក់ពិន័យ (រូបភាពទី២) សម្រាប់អតិថិជននីមួយៗ                    -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<?php $__currentLoopData = $lateInstallments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $daysLate = $installment->daysLate();
    $graceDays = (int) ($settings['late_grace_period_days'] ?? 0);
    $defaultMethod = $settings['late_fee_method'] ?? 'fixed';
    $defaultRate = (float) ($settings['late_fee_rate'] ?? 1.00);
    $defaultFrequency = $settings['late_fee_frequency'] ?? 'daily';
    $overdueBase = (float) ($installment->monthly_payment ?: $installment->remaining_balance);
?>
<div id="penalty-modal-<?php echo e($installment->id); ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 my-8">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span>Apply Late Fee (ដាក់ពិន័យយឺត)</span>
            </h3>
            <button type="button" onclick="closePenaltyModal(<?php echo e($installment->id); ?>)" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="<?php echo e(route('late-payments.apply-fee', $installment)); ?>" id="penalty-form-<?php echo e($installment->id); ?>" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="installment_id" value="<?php echo e($installment->id); ?>">
            <input type="hidden" name="late_fee_method" id="penalty_method_<?php echo e($installment->id); ?>" value="<?php echo e($defaultMethod); ?>">

            <!-- ៥. ជ្រើសរើសវិធីគិតពិន័យ (Radio Tabs) -->
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                <label onclick="switchPenaltyMethod(<?php echo e($installment->id); ?>, 'fixed')"
                       class="penalty-method-card-<?php echo e($installment->id); ?> flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold cursor-pointer transition <?php echo e($defaultMethod === 'fixed' ? 'bg-white shadow-xs text-blue-700' : 'text-slate-600 hover:text-slate-900'); ?>"
                       data-method="fixed">
                    <input type="radio" name="method_radio_<?php echo e($installment->id); ?>" value="fixed" <?php echo e($defaultMethod === 'fixed' ? 'checked' : ''); ?> class="hidden">
                    <span>គិតជាប្រាក់រៀងរាល់ថ្ងៃ</span>
                </label>
                <label onclick="switchPenaltyMethod(<?php echo e($installment->id); ?>, 'percentage')"
                       class="penalty-method-card-<?php echo e($installment->id); ?> flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold cursor-pointer transition <?php echo e($defaultMethod === 'percentage' ? 'bg-white shadow-xs text-blue-700' : 'text-slate-600 hover:text-slate-900'); ?>"
                       data-method="percentage">
                    <input type="radio" name="method_radio_<?php echo e($installment->id); ?>" value="percentage" <?php echo e($defaultMethod === 'percentage' ? 'checked' : ''); ?> class="hidden">
                    <span>គិតជាភាគរយ</span>
                </label>
            </div>

            <!-- Inputs Grid -->
            <div class="grid grid-cols-3 gap-3">
                <!-- Days Late -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">ចំនួនថ្ងៃយឺត</label>
                    <div class="relative">
                        <input type="number" name="days_late" id="penalty_days_<?php echo e($installment->id); ?>" value="<?php echo e($daysLate); ?>"
                               oninput="recalcPenalty(<?php echo e($installment->id); ?>)"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">ថ្ងៃ</span>
                    </div>
                </div>

                <!-- Rate / Amount -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1" id="penalty_rate_label_<?php echo e($installment->id); ?>">
                        <?php echo e($defaultMethod === 'percentage' ? 'អត្រាការប្រាក់ (%)' : 'ចំនួនដុល្លារក្នុងមួយថ្ងៃ'); ?>

                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" name="rate_or_amount" id="penalty_rate_<?php echo e($installment->id); ?>" value="<?php echo e($defaultRate); ?>"
                               oninput="recalcPenalty(<?php echo e($installment->id); ?>)"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400" id="penalty_rate_unit_<?php echo e($installment->id); ?>">
                            <?php echo e($defaultMethod === 'percentage' ? '%' : '$'); ?>

                        </span>
                    </div>
                </div>

                <!-- Total Penalty Fee (Auto Calculated) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">ប្រាក់ពិន័យសរុប</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="late_fee" id="penalty_total_<?php echo e($installment->id); ?>"
                               class="w-full border border-amber-300 bg-amber-50 rounded-xl px-3 py-2 text-sm font-black text-amber-700 focus:outline-none">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-amber-600">$</span>
                    </div>
                </div>
            </div>

            <!-- Note -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Note (សម្គាល់)</label>
                <input type="text" name="note" id="penalty_note_<?php echo e($installment->id); ?>"
                       value="ឧ. ធ្លាក់ពិន័យបង់យឺត <?php echo e($daysLate); ?> ថ្ងៃ"
                       placeholder="e.g. Late fee applied..."
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePenaltyModal(<?php echo e($installment->id); ?>)"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-md flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span>Confirm</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Remind Modals (Khmer Message Composer for Telegram) -->
<?php $__currentLoopData = $lateInstallments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $daysLate = $installment->daysLate();
    $penalty = $installment->calculatePenalty();
    $custName = $installment->customer?->name ?? 'អតិថិជន';
    $prodName = $installment->product?->name ?? 'ទំនិញ';
    $monthlyAmt = number_format($installment->monthly_payment, 2);
    $remAmt = number_format($installment->remaining_balance, 2);
    $penAmt = number_format($penalty, 2);

    $defaultKhmerMessage = "⏰ *សេចក្តីជូនដំណឹងអំពីការទូទាត់ប្រាក់យឺតយ៉ាវ*\n\n"
        . "សូមជម្រាបជូនអតិថិជន៖ *{$custName}*\n"
        . "• ទំនិញ៖ *{$prodName}*\n"
        . "• ទឹកប្រាក់ប្រចាំខែ៖ *\${$monthlyAmt}*\n"
        . "• ទឹកប្រាក់នៅសល់សរុប៖ *\${$remAmt}*\n"
        . ($daysLate > 0 ? "• ចំនួនថ្ងៃយឺតយ៉ាវ៖ *{$daysLate} ថ្ងៃ*\n" : "")
        . ($penalty > 0 ? "• ប្រាក់ពិន័យយឺតយ៉ាវ៖ *\${$penAmt}*\n" : "")
        . "\nសូមអតិថិជនមេត្តាធ្វើការទូទាត់ប្រាក់ឱ្យបានឆាប់រហ័ស។ ប្រសិនបើលោកអ្នកបានទូទាត់រួចរាល់ សូមមេត្តាទាក់ទងមកកាន់ហាងដើម្បីផ្ទៀងផ្ទាត់។ សូមអរគុណ! 🙏";
?>
<div id="remind-modal-<?php echo e($installment->id); ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-gray-100">
        <div class="px-6 py-4 bg-slate-50 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-base">
                    <i class="fab fa-telegram-plane"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900" lang="km">ផ្ញើសាររំលឹកតាម Telegram</h3>
                    <p class="text-xs text-gray-500">Send Payment Reminder via Telegram</p>
                </div>
            </div>
            <button type="button" 
                    onclick="document.getElementById('remind-modal-<?php echo e($installment->id); ?>').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="<?php echo e(route('late-payments.remind', $installment)); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            <div class="bg-blue-50/60 rounded-xl p-3.5 border border-blue-100 mb-4 text-xs space-y-1.5">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">អតិថិជន:</span>
                    <span class="font-bold text-gray-900"><?php echo e($custName); ?> (<?php echo e($installment->customer?->phone ?? 'គ្មានលេខទូរស័ព្ទ'); ?>)</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">ទំនិញ:</span>
                    <span class="font-bold text-gray-800"><?php echo e($prodName); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">យឺតយ៉ាវ:</span>
                    <span class="font-bold text-amber-600"><?php echo e($daysLate); ?> ថ្ងៃ</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">ទឹកប្រាក់នៅសល់:</span>
                    <span class="font-bold text-rose-600">$<?php echo e($remAmt); ?></span>
                </div>
                <?php if($penalty > 0): ?>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">ប្រាក់ពិន័យ:</span>
                    <span class="font-bold text-red-600">$<?php echo e($penAmt); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" lang="km">
                    ខ្លឹមសារសារជាភាសាខ្មែរ (អាចកែប្រែបាន)
                </label>
                <textarea name="message" rows="7" 
                          class="w-full px-3.5 py-2.5 text-xs font-normal border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent leading-relaxed text-gray-800 bg-slate-50/50 resize-y"
                ><?php echo e($defaultKhmerMessage); ?></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" 
                        onclick="document.getElementById('remind-modal-<?php echo e($installment->id); ?>').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition border-0 cursor-pointer"
                >
                    <?php echo e(__('app.cancel')); ?>

                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition flex items-center gap-2 shadow-sm border-0 cursor-pointer"
                >
                    <i class="fab fa-telegram-plane"></i>
                    <span>ផ្ញើសារឥឡូវនេះ (Send Now)</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
    const installmentData = {
        <?php $__currentLoopData = $lateInstallments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo e($ins->id); ?>: {
            days: <?php echo e((int) $ins->daysLate()); ?>,
            monthlyDue: <?php echo e((float) ($ins->monthly_payment ?: $ins->remaining_balance)); ?>,
            remaining: <?php echo e((float) $ins->remaining_balance); ?>,
            graceDays: <?php echo e((int) ($settings['late_grace_period_days'] ?? 0)); ?>,
            defaultMethod: '<?php echo e($settings['late_fee_method'] ?? 'fixed'); ?>',
            defaultRate: <?php echo e((float) ($settings['late_fee_rate'] ?? 1.00)); ?>

        },
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    };

    function openPenaltyModal(id) {
        const modal = document.getElementById('penalty-modal-' + id);
        if (modal) {
            modal.classList.remove('hidden');
            recalcPenalty(id);
        }
    }

    function closePenaltyModal(id) {
        const modal = document.getElementById('penalty-modal-' + id);
        if (modal) modal.classList.add('hidden');
    }

    function switchPenaltyMethod(id, method) {
        const methodInput = document.getElementById('penalty_method_' + id);
        if (methodInput) methodInput.value = method;

        const cards = document.querySelectorAll('.penalty-method-card-' + id);
        cards.forEach(c => {
            if (c.getAttribute('data-method') === method) {
                c.className = 'penalty-method-card-' + id + ' flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold cursor-pointer transition bg-white shadow-xs text-blue-700';
            } else {
                c.className = 'penalty-method-card-' + id + ' flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold cursor-pointer transition text-slate-600 hover:text-slate-900';
            }
        });

        const label = document.getElementById('penalty_rate_label_' + id);
        const unit = document.getElementById('penalty_rate_unit_' + id);

        if (method === 'percentage') {
            if (label) label.textContent = 'អត្រាការប្រាក់ (%)';
            if (unit) unit.textContent = '%';
        } else {
            if (label) label.textContent = 'ចំនួនដុល្លារក្នុងមួយថ្ងៃ';
            if (unit) unit.textContent = '$';
        }

        recalcPenalty(id);
    }

    function recalcPenalty(id) {
        const data = installmentData[id];
        if (!data) return;

        const methodInput = document.getElementById('penalty_method_' + id);
        const daysInput = document.getElementById('penalty_days_' + id);
        const rateInput = document.getElementById('penalty_rate_' + id);
        const totalInput = document.getElementById('penalty_total_' + id);
        const noteInput = document.getElementById('penalty_note_' + id);

        const method = methodInput ? methodInput.value : 'fixed';
        const days = parseInt(daysInput ? daysInput.value : 0) || 0;
        const rate = parseFloat(rateInput ? rateInput.value : 0) || 0;

        const effectiveDays = Math.max(0, days - data.graceDays);
        let totalFee = 0;

        if (effectiveDays > 0) {
            if (method === 'percentage') {
                totalFee = (data.monthlyDue * (rate / 100)) * effectiveDays;
            } else {
                totalFee = effectiveDays * rate;
            }
        }

        if (totalInput) {
            totalInput.value = totalFee.toFixed(2);
        }

        if (noteInput && days > 0) {
            noteInput.value = 'ធ្លាក់ពិន័យបង់យឺត ' + days + ' ថ្ងៃ';
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/late-payments/index.blade.php ENDPATH**/ ?>