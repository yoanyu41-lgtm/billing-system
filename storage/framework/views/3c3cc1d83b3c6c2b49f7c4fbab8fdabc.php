<div class="flex items-center gap-6 overflow-x-auto border-b border-slate-200 text-sm font-semibold no-print">
    <a href="<?php echo e(route('admin.reports.sales')); ?>" class="pb-2.5 transition whitespace-nowrap no-underline <?php echo e(request()->routeIs('admin.reports.sales') || request()->routeIs('admin.reports.daily') ? 'text-slate-900 border-b-2 border-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 border-b-2 border-transparent'); ?>">
        <?php echo e(app()->getLocale() === 'km' ? 'ការលក់' : 'Sales'); ?>

    </a>

    <a href="<?php echo e(route('admin.reports.payment')); ?>" class="pb-2.5 transition whitespace-nowrap no-underline <?php echo e(request()->routeIs('admin.reports.payment') ? 'text-slate-900 border-b-2 border-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 border-b-2 border-transparent'); ?>">
        <?php echo e(app()->getLocale() === 'km' ? 'ការទូទាត់' : 'Payments'); ?>

    </a>

    <a href="<?php echo e(route('admin.reports.installment')); ?>" class="pb-2.5 transition whitespace-nowrap no-underline <?php echo e(request()->routeIs('admin.reports.installment') ? 'text-slate-900 border-b-2 border-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 border-b-2 border-transparent'); ?>">
        <?php echo e(app()->getLocale() === 'km' ? 'បង់រំលស់' : 'Installments'); ?>

    </a>

    <a href="<?php echo e(route('admin.reports.customer')); ?>" class="pb-2.5 transition whitespace-nowrap no-underline <?php echo e(request()->routeIs('admin.reports.customer') ? 'text-slate-900 border-b-2 border-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 border-b-2 border-transparent'); ?>">
        <?php echo e(app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customers'); ?>

    </a>

    <a href="<?php echo e(route('admin.reports.product')); ?>" class="pb-2.5 transition whitespace-nowrap no-underline <?php echo e(request()->routeIs('admin.reports.product') ? 'text-slate-900 border-b-2 border-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 border-b-2 border-transparent'); ?>">
        <?php echo e(app()->getLocale() === 'km' ? 'ផលិតផល' : 'Products'); ?>

    </a>

    <a href="<?php echo e(route('admin.reports.expense')); ?>" class="pb-2.5 transition whitespace-nowrap no-underline <?php echo e(request()->routeIs('admin.reports.expense') ? 'text-slate-900 border-b-2 border-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 border-b-2 border-transparent'); ?>">
        <?php echo e(app()->getLocale() === 'km' ? 'ចំណាយ' : 'Expenses'); ?>

    </a>

    <a href="<?php echo e(route('admin.reports.profit')); ?>" class="pb-2.5 transition whitespace-nowrap no-underline <?php echo e(request()->routeIs('admin.reports.profit') || request()->routeIs('admin.reports.income') || request()->routeIs('admin.reports.monthly') || request()->routeIs('admin.reports.yearly') ? 'text-slate-900 border-b-2 border-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 border-b-2 border-transparent'); ?>">
        <?php echo e(app()->getLocale() === 'km' ? 'ប្រាក់ចំណេញ' : 'Profit / Income'); ?>

    </a>
</div>
<?php /**PATH E:\billing-system\resources\views/admin/reports/_nav.blade.php ENDPATH**/ ?>