<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo e(__('app.pay_off')); ?></h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('app.pay_off_note')); ?></p>
        </div>

        <div class="flex flex-nowrap items-center gap-1.5 sm:gap-2 shrink-0 overflow-x-auto max-w-full">
            
            <a href="<?php echo e(route('installments.schedule-index')); ?>" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-calendar-alt text-indigo-600"></i>
                <span><?php echo e(__('app.payment_schedule')); ?></span>
            </a>

            
            <a href="<?php echo e(route('installments.contract-index')); ?>" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-file-signature text-purple-600"></i>
                <span><?php echo e(__('app.contracts')); ?></span>
            </a>

            
            <a href="<?php echo e(route('installments.pay-off-index')); ?>" class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100/80 text-emerald-700 font-semibold text-xs px-3 py-2 rounded-xl border border-emerald-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-hand-holding-usd text-emerald-600"></i>
                <span><?php echo e(__('app.pay_off')); ?></span>
            </a>

            
            <a href="<?php echo e(route('installments.clearance-index')); ?>" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-certificate text-amber-600"></i>
                <span><?php echo e(__('app.clearance_certificates')); ?></span>
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm">
            <ul class="list-disc pl-5 space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="<?php echo e(route('installments.pay-off-index')); ?>" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" id="search-input" value="<?php echo e(request('search')); ?>" autocomplete="off"
                       placeholder="<?php echo e(app()->getLocale() === 'km' ? 'ស្វែងរកគម្រោងបង់ផ្តាច់ (ឈ្មោះអតិថិជន លេខទូរស័ព្ទ ឬផលិតផល)...' : 'Search payoff plans (customer, phone, product)...'); ?>"
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                <!-- Suggestions box -->
                <div id="suggestions-box" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"></div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                <?php echo e(__('app.search')); ?>

            </button>
            <?php if(request('search')): ?>
            <a href="<?php echo e(route('installments.pay-off-index')); ?>" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors text-center flex items-center justify-center" style="text-decoration: none;">
                <?php echo e(__('app.clear')); ?>

            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Plans Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.customer')); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.product')); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.duration')); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <span lang="km">ទឹកប្រាក់នៅសល់</span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-amber-700 uppercase tracking-wider">
                            <span lang="km">ប្រាក់ដើម</span>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-medium">
                            <?php echo e(($installments->currentPage() - 1) * $installments->perPage() + $i + 1); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($installment->customer?->name ?? 'N/A'); ?></div>
                            <div class="text-xs text-gray-400">#INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <div class="max-w-[150px] lg:max-w-[170px] truncate" title="<?php echo e($installment->product?->name ?? 'N/A'); ?>">
                                <?php echo e($installment->product?->name ?? 'N/A'); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($installment->duration_months); ?> <?php echo e(__('app.duration_unit')); ?></div>
                            <div class="text-xs mt-0.5 font-medium">
                                <?php if($installment->remaining_months <= 0): ?>
                                    <span class="inline-flex items-center text-emerald-600">
                                        <i class="fas fa-check-circle text-[10px] mr-1"></i>
                                        <?php echo e(app()->getLocale() === 'km' ? 'នៅសល់ ០ ខែ' : '0 months left'); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center text-amber-600">
                                        <i class="far fa-clock text-[10px] mr-1"></i>
                                        <?php echo e(app()->getLocale() === 'km' ? 'នៅសល់ ' . $installment->remaining_months . ' ខែ' : $installment->remaining_months . ' ' . __('app.months') . ' left'); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo e(format_currency($installment->scheduled_remaining ?? $installment->remaining_balance)); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-amber-700"><?php echo e(format_currency($installment->payoff_amount)); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <?php if($installment->payoff_amount > 0): ?>
                                <?php if(isset($installment->is_payoff_eligible) && !$installment->is_payoff_eligible): ?>
                                    <div class="inline-flex flex-col items-end">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span lang="km">មិនទាន់ដល់លក្ខខណ្ឌ</span>
                                        </span>
                                        <span class="text-[11px] text-gray-500 mt-0.5">
                                            បង់បាន <?php echo e($installment->paid_months); ?>/<?php echo e($installment->payoff_min_required_month); ?> ខែ
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <button type="button" onclick="document.getElementById('payoff-modal-<?php echo e($installment->id); ?>').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-medium px-4 py-2 rounded-lg transition duration-150 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <?php echo e(__('app.pay_off')); ?>

                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-6 text-center text-gray-500"><?php echo e(__('app.no_installments')); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <?php echo e($installments->links()); ?>

    </div>
</div>

<!-- Pay Off Modals -->
<?php $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($installment->payoff_amount > 0): ?>
    <?php
        $principal = (float) $installment->payoff_amount;
        $defaultRate = (float) ($installment->interest_rate ?? 0);
        $defaultAccruedInterest = round($principal * $defaultRate / 100, 2);
    ?>
    <div id="payoff-modal-<?php echo e($installment->id); ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 my-8">
            <?php
                $isKm = app()->getLocale() === 'km';
            ?>
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span><?php echo e($isKm ? 'បង់ផ្តាច់' : 'Pay Off'); ?></span>
                </h3>
                <button type="button" onclick="closePayoffModal(<?php echo e($installment->id); ?>)" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="<?php echo e(route('installments.pay-off', $installment)); ?>" id="payoff-form-<?php echo e($installment->id); ?>" class="p-6 space-y-4">
                <?php echo csrf_field(); ?>
                <?php
                    $initPayoffType = $defaultPayoffType ?? 'principal_only';
                ?>
                <input type="hidden" name="installment_id" value="<?php echo e($installment->id); ?>">
                <input type="hidden" name="payoff_type" id="payoff_type_<?php echo e($installment->id); ?>" value="<?php echo e($initPayoffType); ?>">

                <!-- Payoff Type Display (Configured from Settings) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <?php echo e($isKm ? 'ប្រភេទបង់ផ្តាច់' : 'Payoff Type'); ?>

                    </label>
                    <?php if($initPayoffType === 'principal_interest'): ?>
                        <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50/60">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-sm font-bold text-slate-800">
                                <?php echo e($isKm ? 'ប្រាក់ដើម + ការប្រាក់' : 'Principal + Interest'); ?>

                            </span>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-blue-500 bg-blue-50/60">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-sm font-bold text-slate-800">
                                <?php echo e($isKm ? 'បង់តែប្រាក់ដើមនៅសល់' : 'Principal Only'); ?>

                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Outstanding Principal Card -->
                <div class="bg-amber-50/80 border border-amber-200/80 rounded-xl px-4 py-3 flex items-center justify-between">
                    <span class="text-xs sm:text-sm font-semibold text-slate-700">
                        <?php echo e($isKm ? 'ប្រាក់ដើមនៅសល់' : 'Outstanding Principal'); ?>

                    </span>
                    <span class="text-xl sm:text-2xl font-black text-amber-700">
                        $<?php echo e(number_format($principal, 2)); ?>

                    </span>
                </div>

                <!-- Title / Note -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        <?php echo e($isKm ? 'ចំណាំ' : 'Title / Note'); ?>

                    </label>
                    <input type="text" name="title" placeholder="<?php echo e($isKm ? 'ឧ. ការបង់ផ្តាច់, បញ្ចុះតម្លៃពិសេស...' : 'e.g. Early settlement, special discount...'); ?>"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>

                <?php
                    $isInitialInterest = ($initPayoffType === 'principal_interest');
                    $initialInterestAmount = $isInitialInterest ? ($installment->default_interest_amount ?? $defaultAccruedInterest) : 0;
                    $initialTotal = $principal + $initialInterestAmount + ($installment->calculated_late_fee ?? 0);
                ?>

                <!-- Principal + Interest specific fields (hidden when Principal Only) -->
                <div id="interest-fields-<?php echo e($installment->id); ?>" class="<?php echo e($isInitialInterest ? '' : 'hidden'); ?> grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            <?php echo e($isKm ? 'អត្រាការប្រាក់ (%)' : 'Interest Rate (%)'); ?>

                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" max="100" name="interest_rate"
                                   id="interest_rate_<?php echo e($installment->id); ?>"
                                   value="<?php echo e(number_format($defaultRate, 2, '.', '')); ?>"
                                   readonly
                                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-slate-50 font-bold text-slate-800 focus:outline-none">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            <?php echo e($isKm ? 'ការប្រាក់ត្រូវបង់ ($)' : 'Accrued Interest ($)'); ?>

                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" name="interest_amount"
                                   id="interest_amount_<?php echo e($installment->id); ?>"
                                   value="<?php echo e(number_format($installment->default_interest_amount ?? $defaultAccruedInterest, 2, '.', '')); ?>"
                                   readonly
                                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-slate-50 font-bold text-emerald-700 focus:outline-none cursor-not-allowed">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">$</span>
                        </div>
                    </div>
                </div>

                <!-- Discount & Late Fee -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            <?php echo e($isKm ? 'បញ្ចុះតម្លៃ ($)' : 'Discount ($)'); ?>

                        </label>
                        <input type="number" step="0.01" min="0" name="discount"
                               id="discount_<?php echo e($installment->id); ?>"
                               value="0.00"
                               oninput="recalcPayoff(<?php echo e($installment->id); ?>)"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700">
                                <?php echo e($isKm ? 'ប្រាក់ពិន័យ ($)' : 'Late Fee ($)'); ?>

                            </label>
                            <?php if(($installment->overdue_days ?? 0) > 0): ?>
                                <span class="text-[11px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full border border-red-200">
                                    <?php echo e($isKm ? 'យឺត ' . $installment->overdue_days . ' ថ្ងៃ' : $installment->overdue_days . ' days late'); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <input type="number" step="0.01" min="0" name="late_fee"
                               id="late_fee_<?php echo e($installment->id); ?>"
                               value="<?php echo e(number_format($installment->calculated_late_fee ?? 0, 2, '.', '')); ?>"
                               oninput="recalcPayoff(<?php echo e($installment->id); ?>)"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold <?php echo e(($installment->calculated_late_fee ?? 0) > 0 ? 'text-red-600 border-red-300 bg-red-50/20' : ''); ?>">
                    </div>
                </div>

                <!-- Payment Method & Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            <?php echo e($isKm ? 'វិធីទូទាត់' : 'Payment Method'); ?>

                        </label>
                        <select name="payment_method_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($method->id); ?>"><?php echo e($method->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            <?php echo e($isKm ? 'ថ្ងៃទូទាត់' : 'Payment Date'); ?>

                        </label>
                        <input type="date" name="payment_date" value="<?php echo e(now()->toDateString()); ?>" required
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Payment Summary Box -->
                <div id="summary-box-<?php echo e($installment->id); ?>" class="rounded-xl p-4 <?php echo e($isInitialInterest ? 'bg-emerald-50/70 border border-emerald-100' : 'bg-blue-50/70 border border-blue-100'); ?> transition space-y-2">
                    <div id="summary-title-<?php echo e($installment->id); ?>" class="text-xs font-bold <?php echo e($isInitialInterest ? 'text-emerald-700' : 'text-blue-700'); ?> uppercase tracking-wider mb-2">
                        <?php echo e($isKm ? 'សរុបការទូទាត់' : 'Payment Summary'); ?>

                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-600">
                        <span><?php echo e($isKm ? 'ប្រាក់ដើមនៅសល់' : 'Remaining Principal'); ?></span>
                        <span class="font-bold text-slate-900">$<?php echo e(number_format($principal, 2)); ?></span>
                    </div>

                    <div id="summary-row-interest-<?php echo e($installment->id); ?>" class="<?php echo e($isInitialInterest ? '' : 'hidden'); ?> flex items-center justify-between text-xs text-slate-600">
                        <span><?php echo e($isKm ? 'ការប្រាក់' : 'Interest'); ?></span>
                        <span class="font-bold text-slate-900" id="summary-val-interest-<?php echo e($installment->id); ?>">$<?php echo e(number_format($initialInterestAmount, 2)); ?></span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-600">
                        <span><?php echo e($isKm ? 'បញ្ចុះតម្លៃ' : 'Discount'); ?></span>
                        <span class="font-bold text-red-600" id="summary-val-discount-<?php echo e($installment->id); ?>">-$0.00</span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-600">
                        <span><?php echo e($isKm ? 'ប្រាក់ពិន័យ' : 'Late Fee'); ?></span>
                        <span class="font-bold <?php echo e(($installment->calculated_late_fee ?? 0) > 0 ? 'text-red-600' : 'text-slate-900'); ?>" id="summary-val-latefee-<?php echo e($installment->id); ?>">$<?php echo e(number_format($installment->calculated_late_fee ?? 0, 2)); ?></span>
                    </div>

                    <div class="pt-2 border-t border-slate-200/80 flex items-baseline justify-between">
                        <span class="text-sm font-bold text-slate-800">
                            <?php echo e($isKm ? 'សរុបត្រូវបង់' : 'Total Payoff Amount'); ?>

                        </span>
                        <span class="text-2xl font-black <?php echo e($isInitialInterest ? 'text-emerald-700' : 'text-blue-700'); ?>" id="summary-val-total-<?php echo e($installment->id); ?>">
                            $<?php echo e(number_format($initialTotal, 2)); ?>

                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closePayoffModal(<?php echo e($installment->id); ?>)"
                            class="px-5 py-2.5 font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition text-sm">
                        <?php echo e($isKm ? 'បោះបង់' : 'Cancel'); ?>

                    </button>
                    <button type="submit" id="btn-submit-<?php echo e($installment->id); ?>"
                            class="px-6 py-2.5 font-bold text-white <?php echo e($isInitialInterest ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700'); ?> rounded-xl shadow-md transition text-sm flex items-center gap-2">
                        <?php echo e($isKm ? 'បញ្ជាក់ការបង់ផ្តាច់' : 'Confirm Payoff'); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
    const payoffPrincipals = {
        <?php $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($ins->payoff_amount > 0): ?>
                <?php echo e($ins->id); ?>: <?php echo e((float) $ins->payoff_amount); ?>,
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    };

    function closePayoffModal(id) {
        const modal = document.getElementById('payoff-modal-' + id);
        if (modal) modal.classList.add('hidden');
    }

    function switchPayoffType(id, type) {
        const typeInput = document.getElementById('payoff_type_' + id);
        if (typeInput) typeInput.value = type;

        // Radios
        const radios = document.getElementsByName('payoff_type_radio_' + id);
        radios.forEach(r => { r.checked = (r.value === type); });

        // Card styling
        const cards = document.querySelectorAll('.payoff-type-card-' + id);
        cards.forEach(c => {
            const cardType = c.getAttribute('data-type');
            if (cardType === type) {
                if (type === 'principal_only') {
                    c.className = 'payoff-type-card-' + id + ' flex items-center gap-3 p-3 rounded-xl border-2 border-blue-500 bg-blue-50/60 cursor-pointer transition';
                } else {
                    c.className = 'payoff-type-card-' + id + ' flex items-center gap-3 p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50/60 cursor-pointer transition';
                }
            } else {
                c.className = 'payoff-type-card-' + id + ' flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition';
            }
        });

        // Toggle interest fields
        const interestFields = document.getElementById('interest-fields-' + id);
        const interestRow = document.getElementById('summary-row-interest-' + id);
        const summaryBox = document.getElementById('summary-box-' + id);
        const summaryTitle = document.getElementById('summary-title-' + id);
        const submitBtn = document.getElementById('btn-submit-' + id);
        const totalVal = document.getElementById('summary-val-total-' + id);

        if (type === 'principal_interest') {
            interestFields.classList.remove('hidden');
            interestRow.classList.remove('hidden');
            summaryBox.className = 'rounded-xl p-4 bg-emerald-50/70 border border-emerald-100 transition space-y-2';
            summaryTitle.className = 'text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2';
            submitBtn.className = 'px-6 py-2.5 font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md transition text-sm flex items-center gap-2';
            totalVal.className = 'text-2xl font-black text-emerald-700';
        } else {
            interestFields.classList.add('hidden');
            interestRow.classList.add('hidden');
            summaryBox.className = 'rounded-xl p-4 bg-blue-50/70 border border-blue-100 transition space-y-2';
            summaryTitle.className = 'text-xs font-bold text-blue-700 uppercase tracking-wider mb-2';
            submitBtn.className = 'px-6 py-2.5 font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md transition text-sm flex items-center gap-2';
            totalVal.className = 'text-2xl font-black text-blue-700';
        }

        recalcPayoff(id);
    }

    function onInterestAmountInput(id) {
        recalcPayoff(id);
    }

    function recalcPayoff(id) {
        const principal = payoffPrincipals[id] || 0;
        const typeInput = document.getElementById('payoff_type_' + id);
        const isInterestMode = typeInput && typeInput.value === 'principal_interest';

        let interest = 0;
        if (isInterestMode) {
            const amountInput = document.getElementById('interest_amount_' + id);
            interest = parseFloat(amountInput ? amountInput.value : 0) || 0;
        }

        const discountInput = document.getElementById('discount_' + id);
        const discount = parseFloat(discountInput ? discountInput.value : 0) || 0;

        const lateFeeInput = document.getElementById('late_fee_' + id);
        const lateFee = parseFloat(lateFeeInput ? lateFeeInput.value : 0) || 0;

        const total = Math.max(principal + interest - discount + lateFee, 0);

        // Update summary values
        const interestValEl = document.getElementById('summary-val-interest-' + id);
        if (interestValEl) interestValEl.textContent = '$' + interest.toFixed(2);

        const discountValEl = document.getElementById('summary-val-discount-' + id);
        if (discountValEl) discountValEl.textContent = '-$' + discount.toFixed(2);

        const lateFeeValEl = document.getElementById('summary-val-latefee-' + id);
        if (lateFeeValEl) lateFeeValEl.textContent = '$' + lateFee.toFixed(2);

        const totalValEl = document.getElementById('summary-val-total-' + id);
        if (totalValEl) totalValEl.textContent = '$' + total.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('search-input');
        const box = document.getElementById('suggestions-box');
        const suggestions = <?php echo json_encode($suggestions ?? [], 15, 512) ?>;

        function filterSuggestions(val) {
            if (!val || val.trim().length < 1) {
                box.innerHTML = '';
                box.classList.add('hidden');
                return;
            }

            const query = val.toLowerCase();
            const matches = suggestions.filter(item => 
                item.label.toLowerCase().includes(query) || 
                item.value.toLowerCase().includes(query)
            ).slice(0, 8);

            if (matches.length === 0) {
                box.innerHTML = '';
                box.classList.add('hidden');
                return;
            }

            box.innerHTML = matches.map(match => {
                return `
                    <div class="suggestion-item px-4 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700 transition duration-150 font-medium border-b border-gray-50 last:border-0" data-value="${escapeHtml(match.value)}">
                        ${escapeHtml(match.label)}
                    </div>
                `;
            }).join('');

            box.classList.remove('hidden');
        }

        function escapeHtml(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        if (input && box) {
            input.addEventListener('input', function() {
                filterSuggestions(this.value);
            });

            input.addEventListener('focus', function() {
                filterSuggestions(this.value);
            });

            box.addEventListener('click', function(e) {
                const item = e.target.closest('.suggestion-item');
                if (item) {
                    input.value = item.getAttribute('data-value');
                    box.classList.add('hidden');
                    input.closest('form').submit();
                }
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !box.contains(e.target)) {
                    box.classList.add('hidden');
                }
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/installments/pay-off-index.blade.php ENDPATH**/ ?>