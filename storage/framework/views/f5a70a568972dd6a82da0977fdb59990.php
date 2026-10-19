<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <?php echo e(__('app.late_payments')); ?>

            </h1>
            <p class="text-sm text-gray-500 mt-1">
                <?php echo e(__('app.late_payments_desc')); ?>

            </p>
        </div>
        <div>
            <form method="POST" action="<?php echo e(route('late-payments.due-reminders')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" 
                        onclick="return confirm('តើលោកអ្នកពិតជាចង់ផ្ញើសាររំលឹកជាភាសាខ្មែរ ទៅកាន់អតិថិជនដល់ថ្ងៃកំណត់ទាំងអស់មែនទេ?')"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-sm border-0 cursor-pointer"
                >
                    <i class="fas fa-paper-plane text-base"></i>
                    <span><?php echo e(__('app.send_due_date_reminders_today')); ?></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notices -->
    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle mr-2 text-green-500 text-lg"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-circle mr-2 text-red-500 text-lg"></i>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <!-- Payments Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <?php echo e(__('app.customer')); ?>

                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <?php echo e(__('app.product')); ?>

                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <?php echo e(__('app.remaining_balance')); ?>

                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <?php echo e(__('app.days_late')); ?>

                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <?php echo e(__('app.penalty_fee') ?? 'ប្រាក់ពិន័យ'); ?>

                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <?php echo e(__('app.actions')); ?>

                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $lateInstallments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">
                                <?php echo e($installment->customer?->name ?? 'N/A'); ?>

                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                <i class="fas fa-phone-alt mr-1 text-slate-400"></i><?php echo e($installment->customer?->phone ?? 'N/A'); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="max-w-[150px] lg:max-w-[170px] truncate" title="<?php echo e($installment->product?->name ?? 'N/A'); ?>">
                                <?php echo e($installment->product?->name ?? 'N/A'); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-rose-600">
                                $<?php echo e(number_format($installment->remaining_balance, 2)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-amber-600">
                                <?php echo e($installment->daysLate()); ?> <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃ' : 'days'); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-red-600">
                                $<?php echo e(number_format($installment->calculatePenalty(), 2)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" 
                                    onclick="document.getElementById('remind-modal-<?php echo e($installment->id); ?>').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-xs transition duration-150 border border-blue-200 cursor-pointer shadow-xs"
                            >
                                <i class="fas fa-bell"></i>
                                <span><?php echo e(__('app.send_reminder')); ?></span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400 gap-3">
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

<!-- Remind Modals (Khmer Message Composer) -->
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
        <!-- Modal Header -->
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

        <!-- Modal Form -->
        <form method="POST" action="<?php echo e(route('late-payments.remind', $installment)); ?>" class="p-6">
            <?php echo csrf_field(); ?>

            <!-- Customer Details Summary -->
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
                <div class="flex justify-between items-center pt-1 border-t border-blue-100">
                    <span class="text-gray-600 font-medium">ស្ថានភាព Telegram:</span>
                    <?php if($installment->customer?->telegram_id): ?>
                        <span class="text-emerald-700 font-bold flex items-center gap-1">
                            <i class="fas fa-check-circle text-[11px]"></i> ភ្ជាប់រួចរាល់ (ID: <?php echo e($installment->customer->telegram_id); ?>)
                        </span>
                    <?php else: ?>
                        <span class="text-amber-700 font-semibold flex items-center gap-1">
                            <i class="fas fa-exclamation-triangle text-[11px]"></i> មិនទាន់ភ្ជាប់ Telegram (អាចមិនបានទទួល)
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Message Textarea -->
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" lang="km">
                    ខ្លឹមសារសារជាភាសាខ្មែរ (អាចកែប្រែបាន)
                </label>
                <textarea name="message" rows="8" 
                          class="w-full px-3.5 py-2.5 text-xs font-normal border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent leading-relaxed text-gray-800 bg-slate-50/50 resize-y"
                          placeholder="វាយខ្លឹមសារសារជាភាសាខ្មែរនៅទីនេះ..."
                ><?php echo e($defaultKhmerMessage); ?></textarea>
                <p class="text-[11px] text-gray-400 mt-1">លោកអ្នកអាចកែប្រែខ្លឹមសារសារជាភាសាខ្មែរខាងលើនេះបានតាមការចង់បាន មុនពេលចុចផ្ញើ។</p>
            </div>

            <!-- Footer Buttons -->
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/late-payments/index.blade.php ENDPATH**/ ?>