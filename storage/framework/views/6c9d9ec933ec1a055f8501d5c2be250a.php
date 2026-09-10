<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <?php echo e(__('app.payments')); ?>

            </h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('app.manage_your_business_easily')); ?></p>
        </div>
        <div>
            <a href="<?php echo e(route('payments.create')); ?>" 
               class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-sm"
               style="text-decoration: none;"
            >
                <i class="fas fa-plus"></i>
                <span><?php echo e(__('app.add_payment')); ?></span>
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="<?php echo e(route('payments.index')); ?>" class="flex flex-col md:flex-row gap-4 items-end w-full">
            <!-- Search Bar -->
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2"><?php echo e(__('app.search')); ?></label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="searchInput"
                        value="<?php echo e(request('search')); ?>" 
                        placeholder="<?php echo e(__('app.search_placeholder')); ?>" 
                        class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-48">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2"><?php echo e(__('app.status')); ?></label>
                <select 
                    name="status" 
                    class="px-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                    <option value=""><?php echo e(__('app.all')); ?></option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>><?php echo e(__('app.pending')); ?></option>
                    <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>><?php echo e(__('app.approved')); ?></option>
                    <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>><?php echo e(__('app.rejected')); ?></option>
                </select>
            </div>

            <!-- Filter Button -->
            <button 
                type="submit" 
                class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-150 flex items-center justify-center gap-2 text-sm border-0"
            >
                <i class="fas fa-filter"></i>
                <span><?php echo e(__('app.filter')); ?></span>
            </button>

            <!-- Clear Button -->
            <?php if(request('status') || request('search')): ?>
            <a 
                href="<?php echo e(route('payments.index')); ?>" 
                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-150 flex items-center justify-center gap-2 text-sm"
                style="text-decoration: none;"
            >
                <i class="fas fa-times"></i>
                <span><?php echo e(__('app.clear')); ?></span>
            </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle mr-2 text-green-500 text-lg"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Payments Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.customer')); ?></th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.payment_method')); ?></th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.amount')); ?></th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.date')); ?></th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.status')); ?></th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody id="paymentsTableBody" class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            <?php echo e($payment->installment?->customer?->name ?? 'N/A'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php
                                $methodKey = strtolower(str_replace(' ', '_', $payment->paymentMethod->name ?? ''));
                                $badgeColor = 'bg-slate-50 text-slate-700 border-slate-200';
                                $mIcon = 'fa-money-bill-wave';

                                if (str_contains($methodKey, 'aba')) {
                                    $badgeColor = 'bg-sky-50 text-sky-700 border-sky-200';
                                    $mIcon = 'fa-university';
                                } elseif (str_contains($methodKey, 'acleda')) {
                                    $badgeColor = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                    $mIcon = 'fa-building-columns';
                                } elseif (str_contains($methodKey, 'wing')) {
                                    $badgeColor = 'bg-lime-50 text-lime-800 border-lime-200';
                                    $mIcon = 'fa-wallet';
                                } elseif (str_contains($methodKey, 'truemoney')) {
                                    $badgeColor = 'bg-orange-50 text-orange-700 border-orange-200';
                                    $mIcon = 'fa-mobile-screen';
                                } elseif (str_contains($methodKey, 'bank') || str_contains($methodKey, 'transfer')) {
                                    $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                    $mIcon = 'fa-money-check-dollar';
                                } elseif (str_contains($methodKey, 'qr')) {
                                    $badgeColor = 'bg-purple-50 text-purple-700 border-purple-200';
                                    $mIcon = 'fa-qrcode';
                                } elseif (str_contains($methodKey, 'credit') || str_contains($methodKey, 'card')) {
                                    $badgeColor = 'bg-slate-100 text-slate-700 border-slate-300';
                                    $mIcon = 'fa-credit-card';
                                } elseif (str_contains($methodKey, 'cash')) {
                                    $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    $mIcon = 'fa-money-bill-wave';
                                }
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border <?php echo e($badgeColor); ?>">
                                <i class="fas <?php echo e($mIcon); ?>"></i>
                                <span><?php echo e(trans()->has('app.' . $methodKey) ? __('app.' . $methodKey) : ($payment->paymentMethod->name ?? 'N/A')); ?></span>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="font-bold text-gray-900"><?php echo e(format_currency($payment->amount, $exchangeRate)); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo e($payment->payment_date); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $pstatus = $payment->status ?? 'pending';
                                $statusColors = [
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'rejected' => 'bg-red-100 text-red-600'
                                ];
                            ?>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo e($statusColors[$pstatus] ?? 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo e(__('app.'.$pstatus)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-1.5">
                                <a href="<?php echo e(route('payments.show', $payment)); ?>" 
                                   class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-900 rounded-lg transition duration-150" 
                                   title="<?php echo e(__('app.view')); ?>"
                                >
                                    <i class="fas fa-eye text-base"></i>
                                </a>
                                <?php if($payment->status === 'pending' && auth()->user()->role === 'admin'): ?>
                                <form method="POST" action="<?php echo e(route('payments.approve', $payment)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" 
                                            class="p-2 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-900 rounded-lg transition duration-150 border-0 cursor-pointer" 
                                            title="<?php echo e(__('app.approve')); ?>"
                                    >
                                        <i class="fas fa-check text-base"></i>
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo e(route('payments.reject', $payment)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" 
                                            class="p-2 text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-900 rounded-lg transition duration-150 border-0 cursor-pointer" 
                                            title="<?php echo e(__('app.reject')); ?>"
                                    >
                                        <i class="fas fa-times text-base"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php if(auth()->user()->role === 'admin'): ?>
                                <form method="POST" action="<?php echo e(route('payments.destroy', $payment)); ?>" class="inline" onsubmit="return confirm('តើលោកអ្នកប្រាកដជាចង់លុបការទូទាត់នេះមែនទេ?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" 
                                            class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 hover:text-rose-900 rounded-lg transition duration-150 border-0 cursor-pointer" 
                                            title="លុបចោល / Delete"
                                    >
                                        <i class="fas fa-trash text-base"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                            <?php echo e(__('app.no_payments')); ?>

                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <?php echo e($payments->appends(request()->query())->links()); ?>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('paymentsTableBody');
    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('input', function () {
        const filter = this.value.toLowerCase().trim();
        const rows = tableBody.querySelectorAll('tr:not(.no-results-row)');
        let visibleCount = 0;

        rows.forEach(row => {
            // Find text in columns (Customer name, Method, Amount, Date, Status)
            const text = row.innerText.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        let noResultsRow = tableBody.querySelector('.no-results-row');
        if (visibleCount === 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                        <?php echo e(__('app.no_payments')); ?>

                    </td>
                `;
                tableBody.appendChild(noResultsRow);
            } else {
                noResultsRow.style.display = '';
            }
        } else {
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/payments/index.blade.php ENDPATH**/ ?>