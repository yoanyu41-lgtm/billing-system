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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.remaining_balance')); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.outstanding_principal')); ?></th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo e(format_currency($installment->remaining_balance)); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-amber-700"><?php echo e(format_currency($installment->payoff_amount)); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <?php if($installment->payoff_amount > 0): ?>
                            <button type="button" onclick="document.getElementById('payoff-modal-<?php echo e($installment->id); ?>').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-medium px-4 py-2 rounded-lg transition duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <?php echo e(__('app.pay_off')); ?>

                            </button>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500"><?php echo e(__('app.no_installments')); ?></td>
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
    <div id="payoff-modal-<?php echo e($installment->id); ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800"><?php echo e(__('app.pay_off')); ?> — <?php echo e($installment->customer?->name); ?></h3>
                <button type="button" onclick="document.getElementById('payoff-modal-<?php echo e($installment->id); ?>').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5">
                <div class="flex items-baseline justify-between">
                    <span class="text-sm text-gray-600"><?php echo e(__('app.outstanding_principal')); ?></span>
                    <span class="text-2xl font-extrabold text-amber-700"><?php echo e(format_currency($installment->payoff_amount)); ?></span>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('installments.pay-off', $installment)); ?>">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('app.settlement_title')); ?></label>
                        <input type="text" name="title" placeholder="<?php echo e(__('app.settlement_title_placeholder')); ?>"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('app.settlement_interest_rate')); ?></label>
                        <input type="number" step="0.01" min="0" max="100" name="interest_rate" value="0"
                               data-principal="<?php echo e($installment->payoff_amount); ?>"
                               oninput="updatePayoffTotal(<?php echo e($installment->id); ?>)"
                               class="payoff-rate-<?php echo e($installment->id); ?> w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('app.payment_method')); ?></label>
                        <select name="payment_method_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($method->id); ?>"><?php echo e($method->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('app.payment_date')); ?></label>
                        <input type="date" name="payment_date" value="<?php echo e(now()->toDateString()); ?>" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex items-baseline justify-between border-t pt-3">
                        <span class="text-sm font-medium text-gray-700"><?php echo e(__('app.total_payoff_amount')); ?></span>
                        <span class="text-xl font-extrabold text-amber-700 payoff-total-<?php echo e($installment->id); ?>"><?php echo e(format_currency($installment->payoff_amount)); ?></span>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('payoff-modal-<?php echo e($installment->id); ?>').classList.add('hidden')" class="px-5 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"><?php echo e(__('app.cancel')); ?></button>
                    <button type="submit" onclick="return confirm('<?php echo e(__('app.confirm_pay_off')); ?>')" class="px-5 py-2.5 font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-sm transition"><?php echo e(__('app.confirm')); ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php
    $currency = session('display_currency', 'USD');
    $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
?>
<script>
    function updatePayoffTotal(id) {
        const input = document.querySelector('.payoff-rate-' + id);
        const principal = parseFloat(input.getAttribute('data-principal')) || 0;
        const rate = parseFloat(input.value) || 0;
        const total = principal + (principal * rate / 100);
        const label = document.querySelector('.payoff-total-' + id);
        
        const currency = "<?php echo e($currency); ?>";
        const exchangeRate = <?php echo e($exchangeRate); ?>;
        
        if (label) {
            if (currency === 'KHR') {
                const khrVal = Math.round(total * exchangeRate);
                label.textContent = khrVal.toLocaleString('en-US') + ' ៛';
            } else {
                label.textContent = '$' + total.toFixed(2);
            }
        }
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
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/installments/pay-off-index.blade.php ENDPATH**/ ?>