<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-gray-400 mb-1 flex items-center gap-1.5">
                <a href="<?php echo e(route('installments.index')); ?>" class="hover:text-indigo-600 transition"><?php echo e(__('app.installment_plans')); ?></a>
                <span>/</span>
                <span class="text-gray-600">#INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?></span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo e(__('app.installment_plan_details')); ?></h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('app.installment_plan_details_sub')); ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('installments.index')); ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <?php echo e(__('app.back')); ?>

            </a>
            <a href="<?php echo e(route('installments.contract', $installment)); ?>" target="_blank" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <?php echo e(__('app.print_contract')); ?>

            </a>

            <?php if($installment->status === 'completed' || $installment->remaining_balance <= 0): ?>
            <a href="<?php echo e(route('installments.clearance', $installment)); ?>" target="_blank" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <?php echo e(__('app.print_clearance')); ?>

            </a>
            <?php endif; ?>

            <?php if($installment->status !== 'returned' && $installment->status !== 'cancelled' && $installment->remaining_balance > 0): ?>
            <button type="button" onclick="document.getElementById('modal-return-from-show').classList.remove('hidden')" class="inline-flex items-center bg-rose-600 hover:bg-rose-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <i class="fas fa-undo-alt mr-2 text-sm"></i>
                <?php echo e(app()->getLocale() === 'km' ? 'យកអីវ៉ាន់វិញ' : 'Return Item'); ?>

            </button>
            <?php endif; ?>

            <?php if(auth()->user()->role === 'admin'): ?>
            <a href="<?php echo e(route('installments.edit', $installment)); ?>" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <?php echo e(__('app.edit_plan')); ?>

            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Customer & Product Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Customer Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base"><?php echo e(__('app.customer_profile')); ?></h3>
                        <p class="text-xs text-gray-500"><?php echo e(__('app.contract_holder')); ?></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5"><?php echo e(__('app.name')); ?></div>
                        <div class="text-sm font-semibold text-gray-800"><?php echo e($installment->customer?->name ?? 'N/A'); ?></div>
                    </div>
                    <?php if($installment->customer?->phone): ?>
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5"><?php echo e(__('app.phone')); ?></div>
                        <div class="text-sm font-semibold text-gray-800"><?php echo e($installment->customer?->phone); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($installment->customer?->email): ?>
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5"><?php echo e(__('app.email')); ?></div>
                        <div class="text-sm text-gray-800"><?php echo e($installment->customer?->email); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base"><?php echo e(__('app.product_details')); ?></h3>
                        <p class="text-xs text-gray-500"><?php echo e(__('app.financed_asset')); ?></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5"><?php echo e(__('app.model_name')); ?></div>
                        <div class="text-sm font-semibold text-gray-800"><?php echo e($installment->product?->name ?? 'N/A'); ?></div>
                    </div>
                    <?php if($installment->product?->code): ?>
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5"><?php echo e(__('app.item_code')); ?></div>
                        <div class="text-sm font-semibold text-indigo-600"><?php echo e($installment->product?->code); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right: Financial Breakdown -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Metrics -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3"><?php echo e(__('app.financial_schedule')); ?></h3>

                <div class="overflow-x-auto rounded-xl border border-gray-100 mb-6">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <?php if($installment->tax_amount > 0): ?>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium"><?php echo e(__('app.subtotal')); ?></td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">$<?php echo e(number_format($installment->subtotal_before_tax ?? $installment->total_price, 2)); ?></td>
                            </tr>
                            <?php
                                $taxLabel = \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT';
                            ?>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium"><?php echo e(__('app.tax')); ?> <?php echo e($taxLabel); ?> (<?php echo e($installment->tax_rate); ?>%)</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">$<?php echo e(number_format($installment->tax_amount, 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium"><?php echo e(__('app.total_price')); ?></td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">$<?php echo e(number_format($installment->total_price, 2)); ?></td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium"><?php echo e(__('app.down_payment')); ?></td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">$<?php echo e(number_format($installment->down_payment, 2)); ?></td>
                            </tr>
                            <?php
                                $types = \App\Services\InstallmentCalculationService::getTypes();
                                $typeKey = $installment->interest_type ?? 'flat';
                                $isKm = app()->getLocale() === 'km';
                                $typeName = $isKm ? ($types[$typeKey]['name_km'] ?? 'ការប្រាក់ថេរ (Flat Rate)') : ($types[$typeKey]['name_en'] ?? 'Flat Rate');
                            ?>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium"><?php echo e($isKm ? 'ប្រភេទរំលស់' : 'Installment Plan Type'); ?></td>
                                <td class="px-5 py-3.5 text-right font-bold text-indigo-700">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        <?php echo e($typeName); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium"><?php echo e(__('app.interest_rate')); ?></td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900"><?php echo e($installment->interest_rate); ?>%</td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium"><?php echo e(__('app.duration')); ?></td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-bold text-gray-900"><?php echo e($installment->duration_months); ?> <?php echo e(__('app.duration_unit')); ?></span>
                                    <span class="text-xs block font-semibold <?php echo e(($installment->remaining_balance <= 0 || $installment->remaining_months <= 0) ? 'text-emerald-600' : 'text-amber-600'); ?>">
                                        <?php echo e(app()->getLocale() === 'km' ? ($installment->remaining_months <= 0 ? 'នៅសល់ ០ ខែ' : 'នៅសល់ ' . $installment->remaining_months . ' ខែ') : ($installment->remaining_months <= 0 ? '0 months left' : $installment->remaining_months . ' ' . __('app.months') . ' left')); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr class="bg-indigo-50/60 hover:bg-indigo-50">
                                <td class="px-5 py-3.5 text-indigo-700 font-semibold"><?php echo e(__('app.monthly_payment')); ?></td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-extrabold text-indigo-700 text-base block">$<?php echo e(number_format($installment->monthly_payment, 2)); ?></span>
                                </td>
                            </tr>
                            <tr class="bg-amber-50/60 hover:bg-amber-50">
                                <td class="px-5 py-3.5 text-amber-800 font-semibold"><?php echo e(__('app.remaining_balance')); ?></td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-extrabold text-amber-800 text-base block">$<?php echo e(number_format($installment->remaining_balance, 2)); ?></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Metadata details -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider"><?php echo e(__('app.plan_status')); ?></span>
                        <div class="mt-1">
                            <?php if($installment->status === 'active' || $installment->status === 'ongoing'): ?>
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200"><?php echo e(__('app.active')); ?></span>
                            <?php elseif($installment->status === 'completed' || $installment->status === 'paid'): ?>
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200"><?php echo e(__('app.completed')); ?></span>
                            <?php elseif($installment->status === 'returned'): ?>
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                    <i class="fas fa-undo-alt mr-1"></i> <?php echo e(app()->getLocale() === 'km' ? 'បានយកអីវ៉ាន់វិញ' : 'Returned'); ?>

                                </span>
                            <?php elseif($installment->status === 'cancelled'): ?>
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200"><?php echo e(__('app.cancelled')); ?></span>
                            <?php else: ?>
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200"><?php echo e($installment->status); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if($installment->next_due_date): ?>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider"><?php echo e(__('app.next_due_date')); ?></span>
                        <div class="mt-1 text-sm font-semibold text-gray-800">
                            <?php echo e(\Carbon\Carbon::parse($installment->next_due_date)->format('d M Y')); ?>

                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    </div>

<!-- Record Payment Modal -->
<div id="recordPaymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRecordPaymentModal()"></div>

        <!-- Modal Center spacer -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal content card -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <form method="POST" action="<?php echo e(route('payments.store')); ?>" enctype="multipart/form-data" class="m-0">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="installment_id" value="<?php echo e($installment->id); ?>">
                <input type="hidden" name="redirect_to" value="<?php echo e(request()->fullUrl()); ?>">
                
                <div class="bg-white px-6 pt-6 pb-4 sm:p-6 sm:pb-4 space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                            <span><?php echo e(__('app.record_payment')); ?></span>
                        </h3>
                        <button type="button" onclick="closeRecordPaymentModal()" class="text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer text-xl">
                            &times;
                        </button>
                    </div>

                    <!-- Details Display -->
                    <div class="bg-indigo-50/50 rounded-xl p-4 text-sm text-indigo-950 border border-indigo-100 flex justify-between">
                        <div>
                            <span class="font-medium block text-xs text-indigo-500 uppercase tracking-wider"><?php echo e(__('app.customer')); ?></span>
                            <span class="font-semibold"><?php echo e($installment->customer?->name); ?></span>
                        </div>
                        <div class="text-right">
                            <span class="font-medium block text-xs text-indigo-500 uppercase tracking-wider" id="modalMonthLabel">Month</span>
                            <span class="font-semibold" id="modalMonthVal">1</span>
                        </div>
                    </div>

                    <!-- Amount, Penalty, and Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5"><?php echo e(__('app.amount')); ?> (USD)</label>
                            <input 
                                type="number" 
                                name="amount" 
                                id="modalAmountInput" 
                                step="0.01" 
                                min="0.01" 
                                required 
                                oninput="updateModalTotal()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                <?php echo e(__('app.penalty_fee') ?? 'ប្រាក់ពិន័យ'); ?> (USD)
                            </label>
                            <input 
                                type="number" 
                                name="penalty_amount" 
                                id="modalPenaltyInput" 
                                step="0.01" 
                                min="0" 
                                value="0.00"
                                oninput="updateModalTotal()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-semibold text-red-600"
                            >
                            <span class="text-[11px] text-gray-400 mt-0.5 block" id="modalPenaltyDaysHint"></span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5"><?php echo e(__('app.payment_date')); ?></label>
                            <input 
                                type="date" 
                                name="payment_date" 
                                id="modalPaymentDateInput"
                                value="<?php echo e(now()->toDateString()); ?>" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                        </div>
                    </div>

                    <!-- Total payment summary in modal -->
                    <div class="flex justify-between items-center px-3.5 py-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold">
                        <span class="text-slate-600"><?php echo e(app()->getLocale() === 'km' ? 'ទឹកប្រាក់សរុបរួមទាំងប្រាក់ពិន័យ:' : 'Total with Penalty:'); ?></span>
                        <span class="text-sm font-bold text-indigo-700" id="modalTotalWithPenalty">$0.00</span>
                    </div>

                    <!-- Payment Method selector -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5"><?php echo e(__('app.payment_method')); ?></label>
                        <select name="payment_method_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $methodKey = strtolower(str_replace(' ', '_', $method->name));
                                    $translatedName = trans()->has('app.' . $methodKey) ? __('app.' . $methodKey) : $method->name;
                                ?>
                                <option value="<?php echo e($method->id); ?>" <?php echo e($methodKey === 'qr_code' ? 'selected' : ''); ?>>
                                    <?php echo e($translatedName); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Customer Slip Attachment -->
                    <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-4">
                        <label class="block text-sm font-bold text-blue-900 mb-1.5">
                            <i class="fas fa-image mr-1"></i>
                            <?php echo e(__('app.upload_customer_slip')); ?>

                        </label>
                        <input type="file" name="qr_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="mt-1 text-[11px] text-gray-500">
                            <?php echo e(app()->getLocale() === 'km' ? 'សូមបញ្ចូលរូបភាពបង្កាន់ដៃទូទាត់ QR / Slip របស់អតិថិជន។' : 'Please upload the customer\'s QR payment receipt/slip image.'); ?>

                        </p>
                    </div>

                    <!-- Approve Immediately (for authorized users) -->
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approve-payment')): ?>
                    <div class="flex items-center gap-2 pt-1">
                        <input 
                            type="checkbox" 
                            name="approve_now" 
                            id="modalApproveCheckbox" 
                            value="1" 
                            checked 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="modalApproveCheckbox" class="text-sm font-semibold text-gray-700 cursor-pointer flex items-center gap-1 select-none">
                            <i class="fas fa-check-double text-emerald-600"></i>
                            <span><?php echo e(__('app.approve_now')); ?></span>
                        </label>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Footer buttons -->
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition text-sm shadow-sm border-0 cursor-pointer"
                    >
                        <?php echo e(__('app.confirm_and_approve')); ?>

                    </button>
                    <button 
                        type="button" 
                        onclick="closeRecordPaymentModal()" 
                        class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition text-sm"
                    >
                        <?php echo e(__('app.cancel')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateModalTotal() {
        const amt = parseFloat(document.getElementById('modalAmountInput').value) || 0;
        const pen = parseFloat(document.getElementById('modalPenaltyInput').value) || 0;
        const total = amt + pen;
        document.getElementById('modalTotalWithPenalty').innerText = '$' + total.toFixed(2);
    }

    function openRecordPaymentModal(month, amount, dueDateStr) {
        document.getElementById('modalMonthVal').innerText = month;
        document.getElementById('modalMonthLabel').innerText = "<?php echo e(__('app.installment_month')); ?> " + month;
        document.getElementById('modalAmountInput').value = parseFloat(amount).toFixed(2);
        
        // Calculate penalty based on due date vs today
        const dueDate = new Date(dueDateStr);
        const today = new Date();
        today.setHours(0,0,0,0);
        dueDate.setHours(0,0,0,0);

        let penalty = 0;
        let daysLate = 0;

        if (today > dueDate) {
            const diffTime = Math.abs(today - dueDate);
            daysLate = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            // Standard system penalty rule:
            // <= 5 days: $0 (Grace period)
            // 6-15 days: $5/day
            // > 15 days: $10/day
            if (daysLate > 15) {
                penalty = daysLate * 10.00;
            } else if (daysLate > 5) {
                penalty = daysLate * 5.00;
            }
        }

        const penaltyInput = document.getElementById('modalPenaltyInput');
        penaltyInput.value = penalty.toFixed(2);

        const hint = document.getElementById('modalPenaltyDaysHint');
        if (daysLate > 0) {
            hint.innerText = (daysLate > 5) 
                ? 'យឺតយ៉ាវ ' + daysLate + ' ថ្ងៃ (ពិន័យ: $' + penalty.toFixed(2) + ')' 
                : 'យឺតយ៉ាវ ' + daysLate + ' ថ្ងៃ (នៅក្នុងអំឡុងពេលអនុគ្រោះ ៥ ថ្ងៃ គ្មានការពិន័យ)';
            hint.className = (daysLate > 5) ? 'text-[11px] text-red-500 font-semibold mt-0.5 block' : 'text-[11px] text-amber-600 mt-0.5 block';
        } else {
            hint.innerText = '';
        }

        updateModalTotal();
        
        const modal = document.getElementById('recordPaymentModal');
        modal.classList.remove('hidden');
    }

    function closeRecordPaymentModal() {
        const modal = document.getElementById('recordPaymentModal');
        modal.classList.add('hidden');
    }
</script>


<?php
    $tgSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
    $hiddenSetting = $tgSettings['hidden_payment_methods'] ?? '[]';
    $hiddenList = json_decode($hiddenSetting, true) ?: [];
    $deletedSetting = $tgSettings['deleted_default_qr'] ?? '[]';
    $deletedList = json_decode($deletedSetting, true) ?: [];
    $allHiddenTg = array_unique(array_merge($hiddenList, $deletedList));

    $tgQrList = [];
    $tgQrMap = [
        'qr_aba'          => 'ABA Bank KHQR',
        'qr_acleda'       => 'ACLEDA KHQR',
        'qr_wing'         => 'Wing KHQR',
        'qr_truemoney'    => 'TrueMoney KHQR',
        'qr_creditcard'   => 'Credit Card',
        'qr_bakong'       => 'Bakong KHQR',
        'company_bank_qr' => 'QR Code ធនាគារ (Default)',
    ];
    foreach ($tgQrMap as $k => $lbl) {
        $variant = str_replace('qr_', '', $k) . '_qr';
        if (in_array($k, $allHiddenTg) || in_array($variant, $allHiddenTg)) continue;
        if (!empty($tgSettings[$k])) $tgQrList[] = ['key' => $k, 'label' => $lbl, 'img' => $tgSettings[$k]];
    }
    $tgCustom = json_decode($tgSettings['custom_qr_list'] ?? '[]', true) ?: [];
    foreach ($tgCustom as $ci) {
        if (!empty($ci['key']) && !empty($ci['label']) && !empty($tgSettings[$ci['key']])) {
            if (in_array($ci['key'], $allHiddenTg)) continue;
            $tgQrList[] = ['key' => $ci['key'], 'label' => $ci['label'], 'img' => $tgSettings[$ci['key']]];
        }
    }
?>

<div id="telegramQrModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(15,23,42,0.55);backdrop-filter:blur(4px);">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-blue-600 to-indigo-600">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fab fa-telegram-plane text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-base"><?php echo e(app()->getLocale() === 'km' ? 'ផ្ញើ QR Code តាម Telegram' : 'Send QR Code via Telegram'); ?></h3>
                    <p id="tgModalSubtitle" class="text-blue-100 text-xs"></p>
                </div>
            </div>
            <button onclick="closeTelegramQrModal()" class="text-white/70 hover:text-white border-0 bg-transparent cursor-pointer text-xl leading-none">&times;</button>
        </div>

        <form id="tgQrForm" method="POST" action="" class="p-6 space-y-5">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="qr_key" id="tgSelectedQrKey" value="<?php echo e(!empty($tgQrList) ? $tgQrList[0]['key'] : ''); ?>">

            
            <?php if(!empty($tgQrList)): ?>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
                    <i class="fas fa-qrcode text-purple-500"></i>
                    <?php echo e(app()->getLocale() === 'km' ? 'ជ្រើសរើស QR Code ដែលត្រូវផ្ញើ' : 'Select QR Code to Send'); ?>

                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="tgQrGrid">
                    <?php $__currentLoopData = $tgQrList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $qr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div onclick="selectTgQr('<?php echo e($qr['key']); ?>', this)"
                        class="tg-qr-card cursor-pointer rounded-xl border-2 p-3 flex flex-col items-center gap-2 transition-all duration-150 hover:shadow-md <?php echo e($idx === 0 ? 'border-purple-500 bg-purple-50 shadow-sm ring-2 ring-purple-200' : 'border-slate-200 bg-white hover:border-purple-300 hover:bg-purple-50'); ?>"
                        data-qr-key="<?php echo e($qr['key']); ?>">
                        <div class="relative">
                            <img src="<?php echo e(asset('storage/' . $qr['img'])); ?>"
                                class="w-16 h-16 object-contain rounded-lg border border-slate-100 bg-white p-1"
                                onerror="this.src='https://ui-avatars.com/api/?name=QR&background=EEF2FF&color=4F46E5'">
                            <?php if($idx === 0): ?>
                            <div id="tgCheckBadge_<?php echo e($idx); ?>" class="tg-check-badge absolute -top-1.5 -right-1.5 w-5 h-5 bg-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs" style="font-size:9px"></i>
                            </div>
                            <?php else: ?>
                            <div id="tgCheckBadge_<?php echo e($idx); ?>" class="tg-check-badge hidden absolute -top-1.5 -right-1.5 w-5 h-5 bg-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs" style="font-size:9px"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs font-semibold text-slate-700 text-center leading-tight"><?php echo e($qr['label']); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php else: ?>
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-800 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-500"></i>
                <span><?php echo e(app()->getLocale() === 'km' ? 'មិនមាន QR Code ណាមួយត្រូវបានកំណត់ក្នុង Settings ទេ។' : 'No QR codes are configured in Settings yet.'); ?></span>
            </div>
            <?php endif; ?>

            
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeTelegramQrModal()"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg border-0 cursor-pointer transition">
                    <?php echo e(app()->getLocale() === 'km' ? 'បោះបង់' : 'Cancel'); ?>

                </button>
                <button type="submit" id="tgSendBtn"
                    class="px-5 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg border-0 cursor-pointer transition flex items-center gap-2 shadow-sm <?php echo e(empty($tgQrList) ? 'opacity-50 pointer-events-none' : ''); ?>">
                    <i class="fab fa-telegram-plane"></i>
                    <?php echo e(app()->getLocale() === 'km' ? 'ផ្ញើ QR Code' : 'Send QR Code'); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const tgBaseUrl = "<?php echo e(route('installments.send-telegram-qr', [$installment, '__MONTH__'])); ?>";

    function openTelegramQrModal(month, amount, dueDate) {
        const url = tgBaseUrl.replace('__MONTH__', month);
        document.getElementById('tgQrForm').action = url;
        document.getElementById('tgModalSubtitle').textContent =
            '<?php echo e(app()->getLocale() === "km" ? "ខែ" : "Month"); ?> ' + month +
            ' · $' + amount + ' · ' + dueDate;
        document.getElementById('telegramQrModal').classList.remove('hidden');
    }

    function closeTelegramQrModal() {
        document.getElementById('telegramQrModal').classList.add('hidden');
    }

    function selectTgQr(key, el) {
        // Deselect all
        document.querySelectorAll('.tg-qr-card').forEach(function(c) {
            c.classList.remove('border-purple-500', 'bg-purple-50', 'shadow-sm', 'ring-2', 'ring-purple-200');
            c.classList.add('border-slate-200', 'bg-white');
        });
        document.querySelectorAll('.tg-check-badge').forEach(function(b) {
            b.classList.add('hidden');
        });
        // Select clicked
        el.classList.remove('border-slate-200', 'bg-white');
        el.classList.add('border-purple-500', 'bg-purple-50', 'shadow-sm', 'ring-2', 'ring-purple-200');
        const badge = el.querySelector('.tg-check-badge');
        if (badge) badge.classList.remove('hidden');
        document.getElementById('tgSelectedQrKey').value = key;
    }

    // Close on backdrop click
    document.getElementById('telegramQrModal').addEventListener('click', function(e) {
        if (e.target === this) closeTelegramQrModal();
    });
</script>

<!-- Modal: Return Product from Show View -->
<div id="modal-return-from-show" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 my-8">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-rose-50/50">
            <h3 class="text-base font-bold text-rose-900 flex items-center gap-2">
                <i class="fas fa-undo-alt text-rose-600"></i>
                <span><?php echo e(app()->getLocale() === 'km' ? 'ទទួលយកទំនិញមកវិញ (Return Customer)' : 'Return / Repossess Product'); ?></span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-return-from-show').classList.add('hidden')" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition">
                &times;
            </button>
        </div>

        <form method="POST" action="<?php echo e(route('installments.return', $installment)); ?>" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>

            <!-- Info Preview Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500"><?php echo e(app()->getLocale() === 'km' ? 'អតិថិជន៖' : 'Customer:'); ?></span>
                    <span class="font-bold text-slate-800"><?php echo e($installment->customer?->name ?? 'N/A'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500"><?php echo e(app()->getLocale() === 'km' ? 'ទំនិញ៖' : 'Product:'); ?></span>
                    <span class="font-bold text-slate-800"><?php echo e($installment->product?->name ?? 'N/A'); ?></span>
                </div>
                <div class="flex justify-between border-t border-slate-200/60 pt-1.5">
                    <span class="text-slate-500"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់នៅសល់មិនទាន់បង់៖' : 'Remaining Balance:'); ?></span>
                    <span class="font-black text-rose-600">$<?php echo e(number_format($installment->remaining_balance, 2)); ?></span>
                </div>
            </div>

            <!-- Return Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃយកទំនិញមកវិញ' : 'Return Date'); ?> <span class="text-red-500">*</span>
                </label>
                <input type="date" name="return_date" value="<?php echo e(now()->toDateString()); ?>" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- Restock Checkbox -->
            <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-xl">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="restock" value="1" checked class="mt-0.5 w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
                    <div class="text-xs">
                        <span class="font-bold text-emerald-900 block"><?php echo e(app()->getLocale() === 'km' ? 'បញ្ចូលទំនិញចូលក្នុងស្តុកវិញ (+1 Stock)' : 'Return item back to inventory (+1 Stock)'); ?></span>
                        <span class="text-emerald-700"><?php echo e(app()->getLocale() === 'km' ? 'ប្រព័ន្ធនឹងបូកចំនួនស្តុកទំនិញនេះចំនួន ១ ចូលក្នុងឃ្លាំងវិញដោយស្វ័យប្រវត្តិ។' : 'Automatically increments the product stock quantity by 1.'); ?></span>
                    </div>
                </label>
            </div>

            <!-- Reason / Note -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    <?php echo e(app()->getLocale() === 'km' ? 'មូលហេតុនៃការយកទំនិញមកវិញ / កំណត់ចំណាំ' : 'Reason / Note'); ?>

                </label>
                <textarea name="return_reason" rows="3" placeholder="<?php echo e(app()->getLocale() === 'km' ? 'ឧ. អតិថិជនខ្វះលទ្ធភាពបង់បន្ត, ដកហូតទំនិញ, ទំនិញនៅមានគុណភាពល្អ...' : 'e.g. Customer unable to continue payment, repossessed...'); ?>"
                          class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
            </div>

            <!-- Warning Notice -->
            <div class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-2.5 flex items-start gap-2">
                <i class="fas fa-exclamation-triangle mt-0.5"></i>
                <span><?php echo e(app()->getLocale() === 'km' ? 'ចំណាំ៖ នៅពេលបញ្ជាក់ កិច្ចសន្យានេះនឹងត្រូវបិទបញ្ចប់ (ស្ថានភាព ដកហូត/យកទំនិញវិញ) ហើយសមតុល្យនៅសល់នឹងក្លាយជា $0.00។' : 'Note: Submitting will close this installment contract with status "Returned" and clear remaining balance to $0.00.'); ?></span>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-return-from-show').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    <?php echo e(app()->getLocale() === 'km' ? 'បោះបង់' : 'Cancel'); ?>

                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span><?php echo e(app()->getLocale() === 'km' ? 'បញ្ជាក់ការយកអីវ៉ាន់វិញ' : 'Confirm Return'); ?></span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/installments/show.blade.php ENDPATH**/ ?>