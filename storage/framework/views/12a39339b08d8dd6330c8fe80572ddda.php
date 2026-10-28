<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 py-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800" lang="km"><?php echo e(__('app.payment_schedule')); ?></h1>
            <p class="text-sm text-gray-500 mt-1" lang="km"><?php echo e(__('app.payment_schedule_sub')); ?></p>
        </div>

        <div class="flex flex-nowrap items-center gap-1.5 sm:gap-2 shrink-0 overflow-x-auto max-w-full">
            
            <a href="<?php echo e(route('installments.schedule-index')); ?>" class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100/80 text-indigo-700 font-semibold text-xs px-3 py-2 rounded-xl border border-indigo-200 shadow-sm transition whitespace-nowrap shrink-0">
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
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="<?php echo e(route('installments.schedule-index')); ?>" class="flex flex-col sm:flex-row gap-3 max-w-xl">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" id="search-input" value="<?php echo e(request('search')); ?>" autocomplete="off"
                       placeholder="<?php echo e(app()->getLocale() === 'km' ? 'ស្វែងរកកាលវិភាគបង់ប្រាក់ (ឈ្មោះអតិថិជន)...' : 'Search payment schedules (customer)...'); ?>"
                       class="w-full pl-10 pr-9 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">

                <?php if(request('search')): ?>
                <button type="button" onclick="clearSearchInput(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" title="Clear">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <?php endif; ?>
                
                <div id="suggestions-box" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"></div>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors shrink-0">
                <?php echo e(__('app.search')); ?>

            </button>
            <?php if(request('search')): ?>
            <a href="<?php echo e(route('installments.schedule-index')); ?>" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors text-center shrink-0">
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
                        <th class="px-3.5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.customer')); ?></th>
                        <th class="px-3.5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.product')); ?></th>
                        <th class="px-3.5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ប្រភេទរំលស់' : 'Installment Type'); ?></th>
                        <th class="px-3.5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'អត្រាការប្រាក់' : 'Interest Rate'); ?></th>
                        <th class="px-3.5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.monthly_payment')); ?></th>
                        <th class="px-3.5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.duration')); ?></th>
                        <th class="px-3.5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.remaining_balance')); ?></th>
                        <th class="px-3.5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isCancelled = ($installment->status === 'cancelled');
                        $isPaidOff = !$isCancelled && (($installment->remaining_balance <= 0) || in_array($installment->status, ['completed', 'paid', 'paid_off']));
                    ?>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-3.5 py-3 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($installment->customer?->name ?? 'N/A'); ?></div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs text-gray-400">#INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?></span>
                                <?php if($isCancelled): ?>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">
                                        <?php echo e(__('app.cancelled')); ?>

                                    </span>
                                <?php elseif($isPaidOff): ?>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">
                                        <?php echo e(__('app.completed')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-3.5 py-3 text-sm font-medium text-gray-900">
                            <div class="max-w-[140px] truncate" title="<?php echo e($installment->product?->name ?? 'N/A'); ?>">
                                <?php echo e($installment->product?->name ?? 'N/A'); ?>

                            </div>
                        </td>
                        <td class="px-3.5 py-3 whitespace-nowrap">
                            <?php
                                $isKm = app()->getLocale() === 'km';
                                $typeKey = $installment->interest_type ?? 'flat';
                                $badgeConfig = [
                                    'flat' => [
                                        'label' => $isKm ? 'ការប្រាក់ថេរ' : 'Flat Rate',
                                        'class' => 'bg-blue-50 text-blue-700 border-blue-200'
                                    ],
                                    'declining' => [
                                        'label' => $isKm ? 'ដើមថេរ ថយការប្រាក់' : 'Declining Balance',
                                        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                    ],
                                    'amortization' => [
                                        'label' => $isKm ? 'ការរំលស់ថយដើមថយការ' : 'Declining Balance / EMI',
                                        'class' => 'bg-purple-50 text-purple-700 border-purple-200'
                                    ],
                                    'bullet' => [
                                        'label' => $isKm ? 'បង់តែការប្រាក់' : 'Bullet Payment',
                                        'class' => 'bg-amber-50 text-amber-800 border-amber-200'
                                    ],
                                    'zero_interest' => [
                                        'label' => $isKm ? 'ការប្រាក់ ០%' : '0% Interest',
                                        'class' => 'bg-rose-50 text-rose-700 border-rose-200'
                                    ],
                                ];
                                $cfg = $badgeConfig[$typeKey] ?? $badgeConfig['flat'];
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-lg border <?php echo e($cfg['class']); ?>">
                                <?php echo e($cfg['label']); ?>

                            </span>
                        </td>
                        <td class="px-3.5 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <?php echo e(number_format($installment->interest_rate, 2)); ?>%
                        </td>
                        <td class="px-3.5 py-3 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo e(format_currency($installment->monthly_payment)); ?></td>
                        <td class="px-3.5 py-3 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($installment->duration_months); ?> <?php echo e(__('app.duration_unit')); ?></div>
                            <div class="text-xs mt-0.5 font-medium">
                                <?php if($isCancelled): ?>
                                    <span class="inline-flex items-center text-rose-600 font-semibold">
                                        <i class="fas fa-times-circle text-[10px] mr-1"></i>
                                        <?php echo e(__('app.cancelled')); ?>

                                    </span>
                                <?php elseif($isPaidOff || $installment->remaining_months <= 0): ?>
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
                        <td class="px-3.5 py-3 whitespace-nowrap text-sm font-bold <?php echo e($isCancelled ? 'text-slate-400' : ($isPaidOff ? 'text-emerald-600' : 'text-amber-700')); ?>">
                            <?php echo e(format_currency($installment->remaining_balance)); ?>

                            <?php if($isCancelled): ?>
                                <span class="text-[11px] text-rose-500 font-medium block">(<?php echo e(__('app.cancelled')); ?>)</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3.5 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-1.5">
                                <?php if($isCancelled): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold">
                                        <i class="fas fa-ban"></i>
                                        <?php echo e(__('app.cancelled')); ?>

                                    </span>
                                    <a href="<?php echo e(route('installments.schedule', $installment)); ?>" class="p-2 text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition" title="<?php echo e(__('app.payment_schedule')); ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('installments.schedule', $installment)); ?>" class="inline-flex items-center gap-1.5 <?php echo e($isPaidOff ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-500 hover:bg-amber-600'); ?> text-white font-medium px-3.5 py-2 rounded-lg transition duration-150 shadow-sm text-xs" title="<?php echo e(__('app.payment_schedule')); ?>">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span><?php echo e(app()->getLocale() === 'km' ? 'កាលវិភាគ' : 'Schedule'); ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
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

<script>
    function clearSearchInput(btn) {
        const input = document.getElementById('search-input');
        if (input) {
            input.value = '';
            input.closest('form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const suggestions = <?php echo json_encode($suggestions ?? [], 15, 512) ?>;
        const input = document.getElementById('search-input');
        const box = document.getElementById('suggestions-box');

        if (!input || !box) return;

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

        input.addEventListener('input', function() {
            filterSuggestions(this.value);
            const urlParams = new URLSearchParams(window.location.search);
            if (this.value.trim() === '' && urlParams.has('search') && urlParams.get('search') !== '') {
                this.closest('form').submit();
            }
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
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/installments/schedule-index.blade.php ENDPATH**/ ?>