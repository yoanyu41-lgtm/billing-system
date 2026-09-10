<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                <?php echo e(app()->getLocale() === 'km' ? 'របាយការណ៍ចំណាយ' : 'Expense Report'); ?>

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
            <a href="<?php echo e(route('admin.reports.expense', ['filter' => 'daily'])); ?>" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border <?php echo e(in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <?php echo e(app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily'); ?>

            </a>
            <a href="<?php echo e(route('admin.reports.expense', ['filter' => 'this_week'])); ?>" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border <?php echo e(($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <?php echo e(app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week'); ?>

            </a>
            <a href="<?php echo e(route('admin.reports.expense', ['filter' => 'monthly'])); ?>" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border <?php echo e(in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <?php echo e(app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly'); ?>

            </a>
            <a href="<?php echo e(route('admin.reports.expense', ['filter' => 'yearly'])); ?>" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border <?php echo e(in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <?php echo e(app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly'); ?>

            </a>
        </div>

        <form method="GET" action="<?php echo e(route('admin.reports.expense')); ?>" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="filter" value="custom">
            
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

            <button type="button" onclick="document.getElementById('addExpenseModal').classList.remove('hidden')" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition border-0 shadow-sm cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-plus text-[11px]"></i> <?php echo e(app()->getLocale() === 'km' ? 'កត់ត្រាចំណាយ' : 'Add Expense'); ?>

            </button>

            <button type="button" onclick="printReportDirect('<?php echo e(route('admin.reports.print', ['type' => 'expense', 'start_date' => $startDate, 'end_date' => $endDate, 'filter' => $filter ?? ''])); ?>')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-print text-slate-500"></i> <?php echo e(app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print'); ?>

            </button>
        </form>
    </div>

    <?php if(session('success')): ?>
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-500/20 text-lg">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ចំណាយសរុប' : 'TOTAL EXPENSE'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">$<?php echo e(number_format($totalExpenses, 2)); ?></div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 text-lg">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ចំណាយការិយាល័យ' : 'OFFICE EXPENSE'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">$<?php echo e(number_format($officeExpense, 2)); ?></div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-purple-500/20 text-lg">
                <i class="fas fa-users-gear"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់បៀវត្ស/បុគ្គលិក' : 'SALARY'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">$<?php echo e(number_format($salaryExpense, 2)); ?></div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20 text-lg">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ទឹក ភ្លើង សេវា' : 'UTILITY / ELEC'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">$<?php echo e(number_format($utilityExpense, 2)); ?></div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-sm transition">
            <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-teal-500/20 text-lg">
                <i class="fas fa-ellipsis"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider"><?php echo e(app()->getLocale() === 'km' ? 'ផ្សេងៗ' : 'OTHER'); ?></span>
                <div class="text-lg font-black text-slate-900 mt-0.5 tracking-tight">$<?php echo e(number_format($otherExpense, 2)); ?></div>
            </div>
        </div>
    </div>

    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">
                <?php echo e(app()->getLocale() === 'km' ? 'តារាងកំណត់ត្រាចំណាយ' : 'Expense Records'); ?>

            </h2>
            <span class="text-xs font-medium text-slate-500">
                <?php echo e(app()->getLocale() === 'km' ? 'សរុប:' : 'Total:'); ?> <strong class="text-slate-800 font-bold"><?php echo e(count($expenses)); ?></strong> <?php echo e(app()->getLocale() === 'km' ? 'កំណត់ត្រា' : 'records'); ?>

            </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold text-xs tracking-wide">
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'លេខកូដចំណាយ' : 'Expense ID'); ?></th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'ប្រភេទ' : 'Category'); ?></th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'បរិយាយ' : 'Description'); ?></th>
                            <th class="py-3.5 px-4 text-right font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'ចំនួនទឹកប្រាក់' : 'Amount'); ?></th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                            <th class="py-3.5 px-4 font-bold whitespace-nowrap"><?php echo e(app()->getLocale() === 'km' ? 'កត់ត្រាដោយ' : 'Recorded By'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $catName = match($exp->category) {
                                'Office Expense' => (app()->getLocale() === 'km' ? 'ចំណាយការិយាល័យ' : 'Office Expense'),
                                'Salary'         => (app()->getLocale() === 'km' ? 'ប្រាក់បៀវត្ស/បុគ្គលិក' : 'Salary'),
                                'Utility'        => (app()->getLocale() === 'km' ? 'ទឹក ភ្លើង សេវា' : 'Electricity / Utility'),
                                'Transport'      => (app()->getLocale() === 'km' ? 'ដឹកជញ្ជូន/ធ្វើដំណើរ' : 'Transport / Delivery'),
                                default          => (app()->getLocale() === 'km' ? 'ផ្សេងៗ' : ($exp->category ?: 'Other'))
                            };
                        ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900 font-mono">
                                EXP-<?php echo e(str_pad($exp->id, 4, '0', STR_PAD_LEFT)); ?>

                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">
                                    <?php echo e($catName); ?>

                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                <?php echo e($exp->description ?? '-'); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-rose-600">
                                $<?php echo e(number_format($exp->amount, 2)); ?>

                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono font-medium">
                                <?php echo e(\Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y')); ?>

                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                <?php echo e($exp->user->name ?? 'Admin'); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                                <?php echo e(app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យចំណាយក្នុងចន្លោះពេលនេះទេ' : 'No expense records found.'); ?>

                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if(count($expenses) > 0): ?>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3.5 px-4 text-right uppercase text-[11px] text-slate-500 font-bold">
                                <?php echo e(app()->getLocale() === 'km' ? 'សរុបរួម:' : 'Grand Total:'); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-rose-600 text-sm">
                                $<?php echo e(number_format($totalExpenses, 2)); ?>

                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Add Expense -->
<div id="addExpenseModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <i class="fas fa-receipt text-blue-600"></i> <?php echo e(app()->getLocale() === 'km' ? 'កត់ត្រាចំណាយថ្មី' : 'Record New Expense'); ?>

            </h3>
            <button onclick="document.getElementById('addExpenseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 border-0 bg-transparent cursor-pointer text-base">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo e(route('admin.expenses.store')); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1"><?php echo e(app()->getLocale() === 'km' ? 'ប្រភេទចំណាយ' : 'Category'); ?></label>
                <select name="category" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
                    <option value="Office Expense"><?php echo e(app()->getLocale() === 'km' ? 'ចំណាយការិយាល័យ (Office Expense)' : 'Office Expense'); ?></option>
                    <option value="Salary"><?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់បៀវត្ស/បុគ្គលិក (Salary)' : 'Salary'); ?></option>
                    <option value="Utility"><?php echo e(app()->getLocale() === 'km' ? 'ទឹក ភ្លើង សេវា (Electricity / Utility)' : 'Electricity / Utility'); ?></option>
                    <option value="Transport"><?php echo e(app()->getLocale() === 'km' ? 'ដឹកជញ្ជូន/ធ្វើដំណើរ (Transport / Delivery)' : 'Transport / Delivery'); ?></option>
                    <option value="Other"><?php echo e(app()->getLocale() === 'km' ? 'ផ្សេងៗ (Other)' : 'Other'); ?></option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1"><?php echo e(app()->getLocale() === 'km' ? 'ចំនួនទឹកប្រាក់ ($)' : 'Amount ($)'); ?></label>
                <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1"><?php echo e(app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></label>
                <input type="date" name="expense_date" value="<?php echo e(date('Y-m-d')); ?>" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1"><?php echo e(app()->getLocale() === 'km' ? 'បរិយាយ' : 'Description'); ?></label>
                <textarea name="description" rows="3" placeholder="..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addExpenseModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs border-0 cursor-pointer">
                    <?php echo e(app()->getLocale() === 'km' ? 'បោះបង់' : 'Cancel'); ?>

                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs border-0 cursor-pointer shadow-sm">
                    <?php echo e(app()->getLocale() === 'km' ? 'រក្សាទុក' : 'Save Expense'); ?>

                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/reports/expense.blade.php ENDPATH**/ ?>