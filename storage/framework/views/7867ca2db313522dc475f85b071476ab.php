<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <!-- Header Section -->
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                <?php echo e(app()->getLocale() === 'km' ? 'របាយការណ៍បង់យឺត / ហួសកាលកំណត់' : 'Overdue & Aging Report'); ?>

            </h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                <?php echo e(app()->getLocale() === 'km' ? 'តាមដានអតិថិជនដែលយឺតការបង់ប្រាក់ ចែកតាមកម្រិតថ្ងៃ និងប្រាក់ពិន័យ' : 'Track overdue customer payments, aging brackets, and late penalty fees'); ?>

            </p>
        </div>
        
        <?php echo $__env->make('admin.reports._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <!-- Unified Filter Bar -->
    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 no-print space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-600 mr-1"><?php echo e(app()->getLocale() === 'km' ? 'កម្រិតថ្ងៃយឺត៖' : 'Aging Bracket:'); ?></span>
                <a href="<?php echo e(route('admin.reports.overdue', ['start_date' => $startDate ?? '', 'end_date' => $endDate ?? ''])); ?>" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border <?php echo e(empty($agingFilter) ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200'); ?>">
                    <?php echo e(app()->getLocale() === 'km' ? 'ទាំងអស់' : 'All'); ?>

                </a>
                <a href="<?php echo e(route('admin.reports.overdue', ['aging' => '1-7', 'start_date' => $startDate ?? '', 'end_date' => $endDate ?? ''])); ?>" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border <?php echo e(($agingFilter ?? '') === '1-7' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100'); ?>">
                    <?php echo e(app()->getLocale() === 'km' ? '១-៧ ថ្ងៃ' : '1-7 Days'); ?> (<?php echo e($bucket1To7); ?>)
                </a>
                <a href="<?php echo e(route('admin.reports.overdue', ['aging' => '8-30', 'start_date' => $startDate ?? '', 'end_date' => $endDate ?? ''])); ?>" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border <?php echo e(($agingFilter ?? '') === '8-30' ? 'bg-orange-600 text-white border-orange-600' : 'bg-orange-50 text-orange-800 border-orange-200 hover:bg-orange-100'); ?>">
                    <?php echo e(app()->getLocale() === 'km' ? '៨-៣០ ថ្ងៃ' : '8-30 Days'); ?> (<?php echo e($bucket8To30); ?>)
                </a>
                <a href="<?php echo e(route('admin.reports.overdue', ['aging' => '30+', 'start_date' => $startDate ?? '', 'end_date' => $endDate ?? ''])); ?>" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border <?php echo e(($agingFilter ?? '') === '30+' ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100'); ?>">
                    <?php echo e(app()->getLocale() === 'km' ? 'លើសពី ៣០ ថ្ងៃ' : '30+ Days'); ?> (<?php echo e($bucket30Plus); ?>)
                </a>
            </div>

            <div class="text-xs text-slate-500 font-semibold px-2">
                <?php echo e(app()->getLocale() === 'km' ? 'គិតត្រឹមថ្ងៃនេះ៖' : 'As of:'); ?> <strong><?php echo e(now()->format('d/m/Y')); ?></strong>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <form method="GET" action="<?php echo e(route('admin.reports.overdue')); ?>" class="flex flex-wrap items-center gap-2">
                <?php if(!empty($agingFilter)): ?>
                    <input type="hidden" name="aging" value="<?php echo e($agingFilter); ?>">
                <?php endif; ?>

                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                    <span><?php echo e(app()->getLocale() === 'km' ? 'ចាប់ពី:' : 'From'); ?></span>
                    <input type="date" name="start_date" value="<?php echo e($startDate ?? ''); ?>" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
                </div>

                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                    <span><?php echo e(app()->getLocale() === 'km' ? 'ដល់:' : 'To'); ?></span>
                    <input type="date" name="end_date" value="<?php echo e($endDate ?? ''); ?>" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
                </div>

                <button type="submit" class="px-4 py-2 bg-[#0b1f3a] hover:bg-[#07162b] text-white font-bold rounded-xl text-xs transition shadow-sm border-0 cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-search text-[11px]"></i> <?php echo e(app()->getLocale() === 'km' ? 'ស្វែងរក' : 'Search'); ?>

                </button>

                <?php if(request('start_date') || request('end_date') || $agingFilter): ?>
                <a href="<?php echo e(route('admin.reports.overdue')); ?>" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold no-underline">
                    <?php echo e(app()->getLocale() === 'km' ? 'សម្អាត' : 'Clear'); ?>

                </a>
                <?php endif; ?>

                <button type="button" onclick="printReportDirect('<?php echo e(route('admin.reports.print', ['type' => 'overdue', 'aging' => $agingFilter ?? '', 'start_date' => $startDate ?? '', 'end_date' => $endDate ?? ''])); ?>')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fas fa-print text-slate-500"></i> <?php echo e(app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print'); ?>

                </button>
            </form>
        </div>
    </div>

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Overdue Contracts -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-500/20 text-lg">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'កិច្ចសន្យាត្រូវទារ' : 'OVERDUE CONTRACTS'); ?></span>
                <div class="text-xl font-black text-rose-600 mt-0.5"><?php echo e(number_format($totalOverdueContracts)); ?></div>
            </div>
        </div>

        <!-- Total Overdue Balance -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-slate-800 text-white flex items-center justify-center shrink-0 shadow-sm text-lg">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'សមតុល្យប្រាក់ដើមនៅសល់' : 'OUTSTANDING BALANCE'); ?></span>
                <div class="text-xl font-black text-slate-900 mt-0.5">$<?php echo e(number_format($totalOverdueBalance, 2)); ?></div>
            </div>
        </div>

        <!-- Total Estimated Late Fee -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20 text-lg">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់ពិន័យប៉ាន់ស្មាន' : 'ESTIMATED PENALTY'); ?></span>
                <div class="text-xl font-black text-amber-600 mt-0.5">$<?php echo e(number_format($totalLateFee, 2)); ?></div>
            </div>
        </div>

        <!-- Severe Bad Debt (> 30 days) -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-red-800 text-white flex items-center justify-center shrink-0 shadow-sm text-lg">
                <i class="fas fa-user-xmark"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ហានិភ័យខ្ពស់ (> ៣០ ថ្ងៃ)' : 'CRITICAL (>30 DAYS)'); ?></span>
                <div class="text-xl font-black text-red-700 mt-0.5"><?php echo e(number_format($bucket30Plus)); ?></div>
            </div>
        </div>

    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800">
                <?php echo e(app()->getLocale() === 'km' ? 'បញ្ជីអតិថិជនយឺតការបង់ប្រាក់' : 'Overdue Customer List'); ?>

            </h2>
            <span class="text-xs text-slate-500 font-semibold">
                <?php echo e(app()->getLocale() === 'km' ? 'បង្ហាញ:' : 'Showing:'); ?> <?php echo e($overdueList->count()); ?>

            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 font-semibold">#</th>
                        <th class="py-3 px-4 font-semibold"><?php echo e(__('app.customer')); ?></th>
                        <th class="py-3 px-4 font-semibold"><?php echo e(__('app.product')); ?></th>
                        <th class="py-3 px-4 font-semibold text-center"><?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃត្រូវបង់' : 'Due Date'); ?></th>
                        <th class="py-3 px-4 font-semibold text-center"><?php echo e(app()->getLocale() === 'km' ? 'ចំនួនថ្ងៃយឺត' : 'Days Overdue'); ?></th>
                        <th class="py-3 px-4 font-semibold text-right"><?php echo e(app()->getLocale() === 'km' ? 'ទឹកប្រាក់/ខែ' : 'Monthly Due'); ?></th>
                        <th class="py-3 px-4 font-semibold text-right"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់ពិន័យ' : 'Late Fee'); ?></th>
                        <th class="py-3 px-4 font-semibold text-right"><?php echo e(app()->getLocale() === 'km' ? 'នៅសល់សរុប' : 'Remaining'); ?></th>
                        <th class="py-3 px-4 font-semibold text-center"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $overdueList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-500">
                            #INS-<?php echo e(str_pad($item->installment->id, 3, '0', STR_PAD_LEFT)); ?>

                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900"><?php echo e($item->customer?->name ?? 'N/A'); ?></div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                <?php echo e($item->customer?->phone ?? '-'); ?>

                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-900 max-w-[180px] truncate" title="<?php echo e($item->product?->name); ?>">
                                <?php echo e($item->product?->name ?? 'N/A'); ?>

                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-semibold text-rose-600">
                            <?php echo e($item->due_date->format('d/m/Y')); ?>

                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <?php if($item->days_overdue <= 7): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                    <?php echo e($item->days_overdue); ?> <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃ' : 'days'); ?>

                                </span>
                            <?php elseif($item->days_overdue <= 30): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800">
                                    <?php echo e($item->days_overdue); ?> <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃ' : 'days'); ?>

                                </span>
                            <?php else: ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-600 text-white">
                                    <?php echo e($item->days_overdue); ?> <?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃ (ធ្ងន់ធ្ងរ)' : 'days'); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                            $<?php echo e(number_format($item->monthly_amount, 2)); ?>

                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-amber-600">
                            +$<?php echo e(number_format($item->penalty, 2)); ?>

                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-rose-600">
                            $<?php echo e(number_format($item->remaining, 2)); ?>

                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="<?php echo e(route('installments.show', $item->installment)); ?>" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition">
                                <i class="fas fa-eye text-[10px]"></i>
                                <span><?php echo e(__('app.view')); ?></span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="fas fa-check-circle text-2xl text-emerald-500 block mb-2 opacity-80"></i>
                            <span><?php echo e(app()->getLocale() === 'km' ? 'ពុំមានអតិថិជនយឺតការបង់ប្រាក់ឡើយ' : 'No overdue installment records found'); ?></span>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/reports/overdue.blade.php ENDPATH**/ ?>