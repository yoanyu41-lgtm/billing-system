<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <?php
        $isKm = app()->getLocale() === 'km';
    ?>

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <?php echo e($isKm ? 'ដកហូត / យកអីវ៉ាន់មកវិញ' : 'Product Returns / Repossessions'); ?>

            </h1>
            <p class="text-sm text-gray-500 mt-1">
                <?php echo e($isKm 
                    ? 'បញ្ជីទំនិញដែលបានដកហូត ឬទទួលយកមកវិញពីអតិថិជន និងបញ្ចូលក្នុងស្តុក' 
                    : 'List of products returned or repossessed from customers and restored to inventory'); ?>

            </p>
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

            
            <a href="<?php echo e(route('installments.pay-off-index')); ?>" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-hand-holding-usd text-emerald-600"></i>
                <span><?php echo e(__('app.pay_off')); ?></span>
            </a>

            
            <a href="<?php echo e(route('installments.clearance-index')); ?>" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-certificate text-amber-600"></i>
                <span><?php echo e(__('app.clearance_certificates')); ?></span>
            </a>

            
            <a href="<?php echo e(route('installments.return-index')); ?>" class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100/80 text-rose-700 font-semibold text-xs px-3 py-2 rounded-xl border border-rose-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-undo-alt text-rose-600"></i>
                <span><?php echo e($isKm ? 'ដកហូត / យកអីវ៉ាន់វិញ' : 'Returns'); ?></span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 text-lg leading-none">&times;</button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 text-lg leading-none">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Action & Filter Bar -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-4 mb-6">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            
            
            <div>
                <button type="button" onclick="openAddReturnModal()"
                        class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl shadow-md shadow-rose-500/20 transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle text-base"></i>
                    <span><?php echo e($isKm ? 'បន្ថែមការយកអីវ៉ាន់វិញ' : 'Add Product Return'); ?></span>
                </button>
            </div>

            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="text-xs text-slate-500 font-medium px-2 text-center sm:text-left">
                    <?php echo e($isKm ? 'សរុបដែលបានយកវិញ៖' : 'Total Returned:'); ?> 
                    <span class="font-bold text-rose-600 text-sm ml-1"><?php echo e(number_format($returnedCount)); ?></span>
                </div>

                <form method="GET" action="<?php echo e(route('installments.return-index')); ?>" class="flex items-center gap-2">
                    <div class="relative flex-1 sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                               placeholder="<?php echo e($isKm ? 'ស្វែងរកឈ្មោះ លេខទូរស័ព្ទ...' : 'Search customer, phone...'); ?>"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 bg-white">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold rounded-xl transition">
                        <?php echo e(__('app.search')); ?>

                    </button>
                    <?php if(request('search')): ?>
                        <a href="<?php echo e(route('installments.return-index')); ?>" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-xl transition">
                            <?php echo e(__('app.clear')); ?>

                        </a>
                    <?php endif; ?>
                </form>
            </div>

        </div>
    </div>

    <!-- Table of Returned Installments -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.customer')); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.product')); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e($isKm ? 'តម្លៃសរុប' : 'Total Price'); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e($isKm ? 'ថ្ងៃយកមកវិញ' : 'Return Date'); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.status')); ?></th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-bold">
                            #INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?>

                        </td>

                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900"><?php echo e($installment->customer?->name ?? 'N/A'); ?></div>
                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                <i class="fas fa-phone text-[10px] text-gray-400"></i>
                                <span><?php echo e($installment->customer?->phone ?? '-'); ?></span>
                            </div>
                        </td>

                        
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900 max-w-[220px] truncate" title="<?php echo e($installment->product?->name ?? 'N/A'); ?>">
                                <?php echo e($installment->product?->name ?? 'N/A'); ?>

                            </div>
                            <?php if($installment->product?->code): ?>
                                <div class="text-xs text-indigo-600 font-mono mt-0.5"><?php echo e($installment->product?->code); ?></div>
                            <?php endif; ?>
                        </td>

                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">
                                <?php echo e(format_currency($installment->total_price, $exchangeRate)); ?>

                            </div>
                            <div class="text-[11px] text-slate-400">
                                <?php echo e($isKm ? 'បង់មុន៖' : 'Down:'); ?> <?php echo e(format_currency($installment->down_payment, $exchangeRate)); ?>

                            </div>
                        </td>

                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-calendar-day text-rose-500"></i>
                                <span><?php echo e($installment->returned_at ? $installment->returned_at->format('d/m/Y') : ($installment->updated_at ? $installment->updated_at->format('d/m/Y') : '-')); ?></span>
                            </div>
                            <?php if($installment->returnedByUser): ?>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    <?php echo e($isKm ? 'ទទួលដោយ៖' : 'By:'); ?> <?php echo e($installment->returnedByUser->name); ?>

                                </div>
                            <?php endif; ?>
                        </td>

                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                <i class="fas fa-undo-alt text-[10px]"></i>
                                <span><?php echo e($isKm ? 'បានយកអីវ៉ាន់វិញ' : 'Returned'); ?></span>
                            </span>
                        </td>

                        
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?php echo e(route('installments.return-certificate', $installment)); ?>" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition shadow-2xs"
                               title="<?php echo e($isKm ? 'បោះពុម្ពលិខិតបញ្ជាក់ប្រគល់ទំនិញត្រឡប់' : 'Print Return Certificate'); ?>">
                                <i class="fas fa-print"></i>
                                <span><?php echo e($isKm ? 'លិខិតប្រគល់ទំនិញ' : 'Certificate'); ?></span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                <i class="fas fa-undo-alt text-2xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-600">
                                <?php echo e($isKm ? 'មិនទាន់មានទិន្នន័យដកហូត ឬយកអីវ៉ាន់វិញនៅឡើយទេ' : 'No returned items found'); ?>

                            </p>
                            <p class="text-xs text-slate-400 mt-1 mb-4">
                                <?php echo e($isKm ? 'ចុចប៊ូតុង "បន្ថែមការយកអីវ៉ាន់វិញ" ខាងលើ ដើម្បីកត់ត្រាការទទួលទំនិញពីអតិថិជន' : 'Click "+ Add Product Return" to record a customer return'); ?>

                            </p>
                            <button type="button" onclick="openAddReturnModal()"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                                <i class="fas fa-plus"></i>
                                <span><?php echo e($isKm ? 'បន្ថែមការយកអីវ៉ាន់វិញ' : 'Add Product Return'); ?></span>
                            </button>
                        </td>
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

<!-- Modal: Add Product Return (ទម្រង់បន្ថែមការយកអីវ៉ាន់វិញ) -->
<div id="modal-add-return" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 my-8 animate-in fade-in zoom-in-95 duration-200">
        
        
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-rose-50/60">
            <h3 class="text-base font-bold text-rose-900 flex items-center gap-2">
                <i class="fas fa-undo-alt text-rose-600"></i>
                <span><?php echo e($isKm ? 'បន្ថែមការដកហូត / យកអីវ៉ាន់វិញ' : 'Add Product Return'); ?></span>
            </h3>
            <button type="button" onclick="closeAddReturnModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center text-lg leading-none transition">
                &times;
            </button>
        </div>

        <form method="POST" id="form-add-return" action="" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>

            <!-- 1. Select Active Installment -->
            <div>
                <label for="select-installment" class="block text-xs font-bold text-slate-700 mb-1.5">
                    <?php echo e($isKm ? 'ជ្រើសរើសការបង់រំលស់របស់អតិថិជន' : 'Select Customer Installment'); ?> <span class="text-red-500">*</span>
                </label>
                <select id="select-installment" required onchange="handleInstallmentChange(this)"
                        class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 bg-white">
                    <option value=""><?php echo e($isKm ? '-- សូមជ្រើសរើសការបង់រំលស់ដែលត្រូវដកហូត/យកវិញ --' : '-- Choose active installment to return --'); ?></option>
                    <?php $__currentLoopData = $availableInstallments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($avail->id); ?>"
                                data-customer="<?php echo e($avail->customer?->name ?? 'N/A'); ?>"
                                data-phone="<?php echo e($avail->customer?->phone ?? '-'); ?>"
                                data-product="<?php echo e($avail->product?->name ?? 'N/A'); ?>"
                                data-product-code="<?php echo e($avail->product?->code ?? ''); ?>"
                                data-total="<?php echo e(format_currency($avail->total_price, $exchangeRate)); ?>"
                                data-remaining="<?php echo e(format_currency($avail->remaining_balance, $exchangeRate)); ?>">
                            #INS-<?php echo e(str_pad($avail->id, 3, '0', STR_PAD_LEFT)); ?> - <?php echo e($avail->customer?->name); ?> (<?php echo e($avail->product?->name); ?> | នៅសល់: <?php echo e(format_currency($avail->remaining_balance, $exchangeRate)); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($availableInstallments->isEmpty()): ?>
                    <p class="text-[11px] text-amber-600 mt-1">
                        <?php echo e($isKm ? 'ពុំមានការបង់រំលស់កំពុងដំណើរការដែលមានសមតុល្យនៅសល់ឡើយ។' : 'No active installments available to return.'); ?>

                    </p>
                <?php endif; ?>
            </div>

            <!-- 2. Selected Installment Info Card (Dynamically displayed) -->
            <div id="selected-contract-card" class="hidden bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs space-y-2">
                <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                    <span class="text-slate-500"><?php echo e($isKm ? 'អតិថិជន៖' : 'Customer:'); ?></span>
                    <span class="font-bold text-slate-900" id="card-customer">-</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                    <span class="text-slate-500"><?php echo e($isKm ? 'ទំនិញ៖' : 'Product:'); ?></span>
                    <span class="font-bold text-slate-900 text-right max-w-[240px] truncate" id="card-product">-</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                    <span class="text-slate-500"><?php echo e($isKm ? 'តម្លៃសរុប៖' : 'Total Price:'); ?></span>
                    <span class="font-bold text-slate-900" id="card-total">$0.00</span>
                </div>
                <div class="flex justify-between items-center pt-0.5">
                    <span class="text-slate-500 font-semibold"><?php echo e($isKm ? 'សមតុល្យនៅសល់ (នឹងត្រូវកាត់ចោល)៖' : 'Remaining (will be cleared):'); ?></span>
                    <span class="font-black text-rose-600 text-sm" id="card-remaining">$0.00</span>
                </div>
            </div>

            <!-- 3. Return Date -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    <?php echo e($isKm ? 'កាលបរិច្ឆេទយកទំនិញមកវិញ' : 'Return Date'); ?> <span class="text-red-500">*</span>
                </label>
                <input type="date" name="return_date" value="<?php echo e(now()->toDateString()); ?>" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            </div>

            <!-- 4. Restock Checkbox -->
            <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="restock" value="1" checked class="mt-0.5 w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
                    <div class="text-xs">
                        <span class="font-bold text-emerald-900 block"><?php echo e($isKm ? 'បញ្ចូលទំនិញចូលក្នុងស្តុកវិញ (+1 Stock)' : 'Return item back to inventory (+1 Stock)'); ?></span>
                        <span class="text-emerald-700 leading-relaxed"><?php echo e($isKm ? 'ប្រព័ន្ធនឹងបូកចំនួនស្តុកទំនិញនេះចំនួន ១ ចូលក្នុងឃ្លាំងវិញដោយស្វ័យប្រវត្តិ។' : 'Automatically increments the product stock quantity by 1.'); ?></span>
                    </div>
                </label>
            </div>

            <!-- 5. Reason / Note -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    <?php echo e($isKm ? 'មូលហេតុនៃការយកទំនិញមកវិញ / កំណត់ចំណាំ' : 'Reason / Note'); ?>

                </label>
                <textarea name="return_reason" rows="3" 
                          placeholder="<?php echo e($isKm ? 'ឧ. អតិថិជនខ្វះលទ្ធភាពបង់បន្ត, ដកហូតទំនិញមកវិញ, ស្ថានភាពទំនិញនៅថ្មី ៩០%...' : 'e.g. Customer unable to pay, repossessed item in good condition...'); ?>"
                          class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
            </div>

            <!-- Notice -->
            <div class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-2">
                <i class="fas fa-exclamation-triangle mt-0.5 text-amber-600 shrink-0"></i>
                <span class="leading-relaxed">
                    <?php echo e($isKm 
                        ? 'ចំណាំ៖ បន្ទាប់ពីចុចរក្សាទុក ការបង់រំលស់នេះនឹងត្រូវបិទបញ្ចប់ (ស្ថានភាព៖ បានយកអីវ៉ាន់វិញ) ហើយនឹងបង្ហាញក្នុងតារាងប្រវត្តិដកហូតខាងលើ។' 
                        : 'Note: Once saved, this installment will be closed as "Returned" and appear in the returned history table.'); ?>

                </span>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeAddReturnModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    <?php echo e($isKm ? 'បោះបង់' : 'Cancel'); ?>

                </button>
                <button type="submit" id="btn-submit-return" disabled
                        class="px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span><?php echo e($isKm ? 'រក្សាទុកការយកអីវ៉ាន់វិញ' : 'Save Return'); ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddReturnModal() {
        const modal = document.getElementById('modal-add-return');
        modal.classList.remove('hidden');
    }

    function closeAddReturnModal() {
        const modal = document.getElementById('modal-add-return');
        modal.classList.add('hidden');
    }

    function handleInstallmentChange(select) {
        const selectedId = select.value;
        const form = document.getElementById('form-add-return');
        const card = document.getElementById('selected-contract-card');
        const submitBtn = document.getElementById('btn-submit-return');

        if (!selectedId) {
            form.action = '';
            card.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }

        form.action = `/installments/${selectedId}/return`;
        submitBtn.disabled = false;

        const opt = select.options[select.selectedIndex];
        const customer = opt.getAttribute('data-customer');
        const phone = opt.getAttribute('data-phone');
        const product = opt.getAttribute('data-product');
        const total = opt.getAttribute('data-total');
        const remaining = opt.getAttribute('data-remaining');

        document.getElementById('card-customer').innerText = customer + (phone && phone !== '-' ? ` (${phone})` : '');
        document.getElementById('card-product').innerText = product;
        document.getElementById('card-total').innerText = total;
        document.getElementById('card-remaining').innerText = remaining;

        card.classList.remove('hidden');
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/installments/return-index.blade.php ENDPATH**/ ?>