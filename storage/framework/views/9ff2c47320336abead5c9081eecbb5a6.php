<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                <?php echo e(app()->getLocale() === 'km' ? 'របាយការណ៍បង់រំលស់' : 'Installment Report'); ?>

            </h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                <?php echo e(app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទបង្ហាញ:' : 'Reporting Period:'); ?> 
                <span class="font-semibold text-slate-700">
                    <?php echo e(\Carbon\Carbon::parse($startDate)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($endDate)->format('d/m/Y')); ?>

                </span>
            </p>
        </div>
        
        <?php echo $__env->make('admin.reports._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print">
        
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.reports.installment', ['filter' => 'daily', 'status' => $statusFilter ?? ''])); ?>" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border <?php echo e(in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <?php echo e(app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily'); ?>

            </a>
            <a href="<?php echo e(route('admin.reports.installment', ['filter' => 'this_week', 'status' => $statusFilter ?? ''])); ?>" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border <?php echo e(($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <?php echo e(app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week'); ?>

            </a>
            <a href="<?php echo e(route('admin.reports.installment', ['filter' => 'monthly', 'status' => $statusFilter ?? ''])); ?>" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border <?php echo e(in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <?php echo e(app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly'); ?>

            </a>
            <a href="<?php echo e(route('admin.reports.installment', ['filter' => 'yearly', 'status' => $statusFilter ?? ''])); ?>" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border <?php echo e(in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <?php echo e(app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly'); ?>

            </a>
        </div>

        <form method="GET" action="<?php echo e(route('admin.reports.installment')); ?>" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="filter" value="custom">
            
            <select name="status" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
                <option value=""><?php echo e(app()->getLocale() === 'km' ? '-- ស្ថានភាពទាំងអស់ --' : '-- All Status --'); ?></option>
                <option value="active" <?php echo e($statusFilter === 'active' ? 'selected' : ''); ?>>Active</option>
                <option value="completed" <?php echo e($statusFilter === 'completed' ? 'selected' : ''); ?>>Completed</option>
                <option value="pending" <?php echo e($statusFilter === 'pending' ? 'selected' : ''); ?>>Pending</option>
            </select>

            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span><?php echo e(app()->getLocale() === 'km' ? 'ចាប់ពី:' : 'From'); ?></span>
                <input type="date" name="start_date" value="<?php echo e($startDate); ?>" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span><?php echo e(app()->getLocale() === 'km' ? 'ដល់:' : 'To'); ?></span>
                <input type="date" name="end_date" value="<?php echo e($endDate); ?>" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <button type="submit" class="px-4 py-2 bg-[#0b1f3a] hover:bg-[#07162b] text-white font-bold rounded-xl text-xs transition shadow-sm border-0 cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-search text-[11px]"></i> <?php echo e(app()->getLocale() === 'km' ? 'ស្វែងរក' : 'Search'); ?>

            </button>

            <button type="button" onclick="printReportDirect('<?php echo e(route('admin.reports.print', ['type' => 'installment', 'start_date' => $startDate, 'end_date' => $endDate, 'status' => $statusFilter ?? '', 'filter' => $filter ?? ''])); ?>')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> <?php echo e(app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print'); ?>

            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 text-lg">
                <i class="fas fa-file-contract"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'កិច្ចសន្យាកំពុងបង់' : 'ACTIVE'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight"><?php echo e(number_format($activeCount)); ?></div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-500/20 text-lg">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់ដើមនៅសល់' : 'OUTSTANDING'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">$<?php echo e(number_format($totalOutstanding, 2)); ?></div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20 text-lg">
                <i class="fas fa-sack-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់ប្រមូលបាន' : 'COLLECTED'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">$<?php echo e(number_format($totalCollected, 2)); ?></div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20 text-lg">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ហួសកាលកំណត់' : 'OVERDUE'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight"><?php echo e(number_format($overdueCount)); ?></div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-teal-500/20 text-lg">
                <i class="fas fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'បានបញ្ចប់' : 'COMPLETED'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight"><?php echo e(number_format($completedCount)); ?></div>
            </div>
        </div>

    </div>

    <!-- Data Table Section -->
    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">
                <?php echo e(app()->getLocale() === 'km' ? 'តារាងរបាយការណ៍កិច្ចសន្យាបង់រំលស់' : 'Installment Contracts'); ?>

            </h2>
            <span class="text-xs font-medium text-slate-500">
                <?php echo e(app()->getLocale() === 'km' ? 'សរុប:' : 'Total:'); ?> <strong class="text-slate-800 font-bold"><?php echo e(count($installmentList)); ?></strong> <?php echo e(app()->getLocale() === 'km' ? 'កិច្ចសន្យា' : 'contracts'); ?>

            </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold text-xs tracking-wide">
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'លេខកិច្ចសន្យា' : 'Contract Number'); ?></th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customer'); ?></th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'ទំនិញ' : 'Product'); ?></th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'តម្លៃសរុប' : 'Total Amount'); ?></th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់កក់' : 'Down Payment'); ?></th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'នៅសល់' : 'Remaining'); ?></th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'រយៈពេល' : 'Duration'); ?></th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'បង់ប្រចាំខែ' : 'Monthly Pay'); ?></th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'បានបង់' : 'Paid'); ?></th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'នៅខ្វះ' : 'Left'); ?></th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'ថ្ងៃត្រូវបង់បន្ទាប់' : 'Next Due'); ?></th>
                            <th class="py-3.5 px-4 text-center font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        <?php $__empty_1 = true; $__currentLoopData = $installmentList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <?php echo e($inst->contract_no); ?>

                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                <?php echo e($inst->customer); ?>

                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <?php echo e($inst->product); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                $<?php echo e(number_format($inst->total_amount, 2)); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-600">
                                $<?php echo e(number_format($inst->down_payment, 2)); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-rose-600">
                                $<?php echo e(number_format($inst->remaining, 2)); ?>

                            </td>
                            <td class="py-3.5 px-4 text-center font-medium text-slate-700">
                                <?php echo e($inst->duration); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-800">
                                $<?php echo e(number_format($inst->monthly_payment, 2)); ?>

                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-emerald-600">
                                <?php echo e($inst->paid_months); ?>

                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-amber-600">
                                <?php echo e($inst->remaining_months); ?>

                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                                <?php echo e($inst->next_due_date); ?>

                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <?php if(strtolower($inst->status) === 'completed'): ?>
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#dcfce7] text-[#16a34a]">
                                        <?php echo e(app()->getLocale() === 'km' ? 'បានបញ្ចប់' : 'Completed'); ?>

                                    </span>
                                <?php elseif(strtolower($inst->status) === 'overdue'): ?>
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#fee2e2] text-[#dc2626]">
                                        <?php echo e(app()->getLocale() === 'km' ? 'ហួសកំណត់' : 'Overdue'); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#e0f2fe] text-[#0284c7]">
                                        <?php echo e(app()->getLocale() === 'km' ? 'សកម្ម' : 'Active'); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="12" class="py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                                <?php echo e(app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យកិច្ចសន្យាបង់រំលស់ទេ' : 'No installment contracts found.'); ?>

                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if(count($installmentList) > 0): ?>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3.5 px-4 text-right uppercase text-[11px] text-slate-500 font-bold">
                                <?php echo e(app()->getLocale() === 'km' ? 'សរុបរួម:' : 'Grand Total:'); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                $<?php echo e(number_format(collect($installmentList)->sum('total_amount'), 2)); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-600">
                                $<?php echo e(number_format(collect($installmentList)->sum('down_payment'), 2)); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-rose-600">
                                $<?php echo e(number_format(collect($installmentList)->sum('remaining'), 2)); ?>

                            </td>
                            <td colspan="6"></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/reports/installment.blade.php ENDPATH**/ ?>