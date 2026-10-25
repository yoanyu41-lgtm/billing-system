<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold shadow-2xs">
                    🗑️
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">
                        <?php echo e(app()->getLocale() === 'km' ? 'ធុងសំរាមរួម' : 'Recycle Bin'); ?>

                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <?php echo e(app()->getLocale() === 'km' ? 'សម្គាល់៖ ទិន្នន័យដែលបានលុបនឹងត្រូវលុបចោលទាំងស្រុងដោយស្វ័យប្រវត្តិនៅពេលក្រោយរយៈពេល ៣០ ថ្ងៃ។' : 'Note: Deleted items will be automatically permanently deleted after 30 days.'); ?>

                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php
        $expiringCount = 0;
        $allCollections = [
            $customers ?? [],
            $installments ?? [],
            $products ?? [],
            $payments ?? [],
            $sales ?? [],
            $users ?? [],
            $suppliers ?? [],
            $categories ?? []
        ];
        foreach ($allCollections as $col) {
            if ($col instanceof \Illuminate\Pagination\LengthAwarePaginator || is_iterable($col)) {
                foreach ($col as $item) {
                    if (isset($item->deleted_at)) {
                        $daysLeft = 30 - (int) $item->deleted_at->diffInDays(now());
                        if ($daysLeft <= 3 && $daysLeft >= 0) {
                            $expiringCount++;
                        }
                    }
                }
            }
        }
    ?>

    <?php if($expiringCount > 0): ?>
        <div class="mb-6 rounded-2xl bg-gradient-to-r from-rose-50 via-amber-50 to-orange-50 border border-rose-200/80 p-4 flex items-center justify-between shadow-sm animate-in fade-in duration-200">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-rose-900">
                        <?php echo e(app()->getLocale() === 'km' ? 'មានទិន្នន័យចំនួន ' . $expiringCount . ' ជិតដល់ថ្ងៃផុតកំណត់លុបចោលរហូត (នៅសល់ ≤ ៣ ថ្ងៃ)' : $expiringCount . ' item(s) expiring within 3 days!'); ?>

                    </h4>
                    <p class="text-xs text-rose-700 mt-0.5">
                        <?php echo e(app()->getLocale() === 'km' ? 'ទិន្នន័យទាំងនេះនឹងត្រូវលុបបាត់ពីប្រព័ន្ធរហូតដោយស្វ័យប្រវត្តិ ក្នុងពេលឆាប់ៗនេះ ប្រសិនបើអ្នកមិនបានចុច «ស្ដារឡើងវិញ» ទេ!' : 'These items will be permanently erased soon unless restored.'); ?>

                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-xs flex items-center gap-2">
            <span>✅</span>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Pill Tabs Bar -->
    <div class="mb-6 overflow-x-auto pb-2 scrollbar-none">
        <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 min-w-max">
            <?php
                $tabsConfig = [
                    'customers'    => ['name' => 'អតិថិជន',          'icon' => '👥', 'count' => $customers->total()],
                    'installments' => ['name' => 'គម្រោងបង់រំលស់',     'icon' => '📄', 'count' => $installments->total()],
                    'products'     => ['name' => 'ផលិតផល',          'icon' => '📦', 'count' => $products->total()],
                    'payments'     => ['name' => 'ការទូទាត់ប្រាក់',       'icon' => '💰', 'count' => $payments->total()],
                    'sales'        => ['name' => 'ការលក់ដាច់',         'icon' => '🛒', 'count' => $sales->total()],
                    'users'        => ['name' => 'បុគ្គលិក',           'icon' => '👨‍💼', 'count' => $users->total()],
                    'suppliers'    => ['name' => 'អ្នកផ្គត់ផ្គង់',       'icon' => '🚚', 'count' => $suppliers->total()],
                    'categories'   => ['name' => 'ប្រភេទផលិតផល',     'icon' => '🏷️', 'count' => $categories->total()],
                ];
            ?>

            <?php $__currentLoopData = $tabsConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $isActive = ($tab === $key); ?>
                <a href="<?php echo e(route('customers.trash', ['tab' => $key])); ?>"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 no-underline <?php echo e($isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'); ?>">
                    <span><?php echo e($cfg['icon']); ?></span>
                    <span><?php echo e($cfg['name']); ?></span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold <?php echo e($isActive ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600'); ?>">
                        <?php echo e($cfg['count']); ?>

                    </span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <?php
        $hasItems = false;
        if ($tab === 'customers' && $customers->total() > 0) $hasItems = true;
        if ($tab === 'installments' && $installments->total() > 0) $hasItems = true;
        if ($tab === 'products' && $products->total() > 0) $hasItems = true;
        if ($tab === 'users' && $users->total() > 0) $hasItems = true;
        if ($tab === 'payments' && $payments->total() > 0) $hasItems = true;
        if ($tab === 'suppliers' && $suppliers->total() > 0) $hasItems = true;
        if ($tab === 'categories' && $categories->total() > 0) $hasItems = true;
        if ($tab === 'sales' && $sales->total() > 0) $hasItems = true;
    ?>

    <?php if($hasItems): ?>
    <!-- Action Bar: Real-Time Search & Bulk Buttons -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
        <div class="relative w-full sm:w-80">
            <input type="text" id="trashSearchInput" onkeyup="filterTrashTable()" placeholder="<?php echo e(app()->getLocale() === 'km' ? 'ស្វែងរកទិន្នន័យក្នុងធុងសំរាម...' : 'Search trash items...'); ?>" class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs">
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <form action="<?php echo e(route('trash.restore-all', ['tab' => $tab])); ?>" method="POST"
                  onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារទិន្នន័យទាំងអស់ក្នុងផ្នែកនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore all items in this section?'); ?>')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-xs hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span><?php echo e(app()->getLocale() === 'km' ? 'ស្ដារឡើងវិញទាំងអស់' : 'Restore All'); ?></span>
                </button>
            </form>

            <form action="<?php echo e(route('trash.empty', ['tab' => $tab])); ?>" method="POST"
                  onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបចោលទាំងអស់មែនទេ? ទិន្នន័យមិនអាចស្ដារវិញបានឡើយ!' : 'Are you sure you want to permanently delete all items in this section?'); ?>')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-xs hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span><?php echo e(app()->getLocale() === 'km' ? 'សម្អាតធុងសំរាម' : 'Empty Trash'); ?></span>
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Content Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
    <?php if($tab === 'customers'): ?>
        <?php if($customers->isEmpty()): ?>
            <div class="p-12 text-center text-slate-400 font-medium text-sm">
                <?php echo e(app()->getLocale() === 'km' ? 'គ្មានទិន្នន័យអតិថិជនក្នុងធុងសំរាមទេ' : 'No deleted customers in trash.'); ?>

            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">ឈ្មោះអតិថិជន</th>
                            <th class="px-5 py-3.5">លេខទូរស័ព្ទ</th>
                            <th class="px-5 py-3.5">កាលបរិច្ឆេទលុប</th>
                            <th class="px-5 py-3.5 text-center">ថ្ងៃនៅសល់</th>
                            <th class="px-5 py-3.5 text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-400 font-mono text-xs font-bold"><?php echo e($customer->id); ?></td>
                                <td class="px-5 py-3 font-semibold text-slate-800">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs">
                                            <?php echo e(mb_substr($customer->name, 0, 1)); ?>

                                        </div>
                                        <span><?php echo e($customer->name); ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-slate-600 font-medium"><?php echo e($customer->phone ?? '—'); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap">
                                    <?php echo e($customer->deleted_at->format('Y-m-d H:i')); ?>

                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <?php $daysLeft = max(0, 30 - (int) $customer->deleted_at->diffInDays(now())); ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?>">
                                        ⏱️ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="<?php echo e(route('customers.restore', $customer->id)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>ស្ដារវិញ</span>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('customers.force-delete', $customer->id)); ?>" method="POST" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបអតិថិជននេះជាស្ថាពរមែនទេ?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>លុបរហូត</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                <?php echo e($customers->appends(['tab' => 'customers'])->links()); ?>

            </div>
        <?php endif; ?>

    <?php elseif($tab === 'installments'): ?>
        <?php if($installments->isEmpty()): ?>
            <div class="p-12 text-center text-slate-400 font-medium text-sm">
                <?php echo e(app()->getLocale() === 'km' ? 'គ្មានទិន្នន័យគម្រោងបង់រំលស់ក្នុងធុងសំរាមទេ' : 'No deleted installments in trash.'); ?>

            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">អតិថិជន</th>
                            <th class="px-5 py-3.5">ផលិតផល</th>
                            <th class="px-5 py-3.5">តម្លៃសរុប</th>
                            <th class="px-5 py-3.5">កាលបរិច្ឆេទលុប</th>
                            <th class="px-5 py-3.5 text-center">ថ្ងៃនៅសល់</th>
                            <th class="px-5 py-3.5 text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-400 font-mono text-xs font-bold"><?php echo e($installment->id); ?></td>
                                <td class="px-5 py-3 font-semibold text-slate-800"><?php echo e($installment->customer?->name ?? 'N/A'); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-medium"><?php echo e($installment->product?->name ?? 'N/A'); ?></td>
                                <td class="px-5 py-3 text-indigo-600 font-bold font-mono"><?php echo e(format_currency($installment->total_price)); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap"><?php echo e($installment->deleted_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <?php $daysLeft = max(0, 30 - (int) $installment->deleted_at->diffInDays(now())); ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?>">
                                        ⏱️ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="<?php echo e(route('installments.restore', $installment->id)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>ស្ដារវិញ</span>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('installments.force-delete', $installment->id)); ?>" method="POST" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបគម្រោងបង់រំលស់នេះជាស្ថាពរមែនទេ?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>លុបរហូត</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                <?php echo e($installments->appends(['tab' => 'installments'])->links()); ?>

            </div>
        <?php endif; ?>

    <?php elseif($tab === 'products'): ?>
        <?php if($products->isEmpty()): ?>
            <div class="p-12 text-center text-slate-400 font-medium text-sm">
                <?php echo e(app()->getLocale() === 'km' ? 'គ្មានទិន្នន័យផលិតផលក្នុងធុងសំរាមទេ' : 'No deleted products in trash.'); ?>

            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">ឈ្មោះផលិតផល</th>
                            <th class="px-5 py-3.5">តម្លៃ</th>
                            <th class="px-5 py-3.5">ស្តុក</th>
                            <th class="px-5 py-3.5">កាលបរិច្ឆេទលុប</th>
                            <th class="px-5 py-3.5 text-center">ថ្ងៃនៅសល់</th>
                            <th class="px-5 py-3.5 text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-400 font-mono text-xs font-bold"><?php echo e($product->id); ?></td>
                                <td class="px-5 py-3 font-semibold text-slate-800"><?php echo e($product->name); ?></td>
                                <td class="px-5 py-3 text-emerald-600 font-bold font-mono"><?php echo e(format_currency($product->price)); ?></td>
                                <td class="px-5 py-3 font-bold text-slate-700"><?php echo e($product->stock); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap"><?php echo e($product->deleted_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <?php $daysLeft = max(0, 30 - (int) $product->deleted_at->diffInDays(now())); ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?>">
                                        ⏱️ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="<?php echo e(route('products.restore', $product->id)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>ស្ដារវិញ</span>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('products.force-delete', $product->id)); ?>" method="POST" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបផលិតផលនេះជាស្ថាពរមែនទេ?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>លុបរហូត</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                <?php echo e($products->appends(['tab' => 'products'])->links()); ?>

            </div>
        <?php endif; ?>

    <?php elseif($tab === 'payments'): ?>
        <?php if($payments->isEmpty()): ?>
            <div class="p-12 text-center text-slate-400 font-medium text-sm">
                <?php echo e(app()->getLocale() === 'km' ? 'គ្មានទិន្នន័យការទូទាត់ក្នុងធុងសំរាមទេ' : 'No deleted payments in trash.'); ?>

            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">អតិថិជន</th>
                            <th class="px-5 py-3.5">ចំនួនទឹកប្រាក់</th>
                            <th class="px-5 py-3.5">កាលបរិច្ឆេទលុប</th>
                            <th class="px-5 py-3.5 text-center">ថ្ងៃនៅសល់</th>
                            <th class="px-5 py-3.5 text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-400 font-mono text-xs font-bold"><?php echo e($payment->id); ?></td>
                                <td class="px-5 py-3 font-semibold text-slate-800"><?php echo e($payment->installment?->customer?->name ?? 'N/A'); ?></td>
                                <td class="px-5 py-3 font-bold text-indigo-600 font-mono"><?php echo e(format_currency($payment->amount)); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap"><?php echo e($payment->deleted_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <?php $daysLeft = max(0, 30 - (int) $payment->deleted_at->diffInDays(now())); ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?>">
                                        ⏱️ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="<?php echo e(route('payments.restore', $payment->id)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>ស្ដារវិញ</span>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('payments.force-delete', $payment->id)); ?>" method="POST" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបការទូទាត់នេះជាស្ថាពរមែនទេ?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>លុបរហូត</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                <?php echo e($payments->appends(['tab' => 'payments'])->links()); ?>

            </div>
        <?php endif; ?>

    <?php elseif($tab === 'sales'): ?>
        <?php if($sales->isEmpty()): ?>
            <div class="p-12 text-center text-slate-400 font-medium text-sm">
                <?php echo e(app()->getLocale() === 'km' ? 'គ្មានទិន្នន័យការលក់ក្នុងធុងសំរាមទេ' : 'No deleted sales in trash.'); ?>

            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">អតិថិជន</th>
                            <th class="px-5 py-3.5">សរុប</th>
                            <th class="px-5 py-3.5">កាលបរិច្ឆេទលុប</th>
                            <th class="px-5 py-3.5 text-center">ថ្ងៃនៅសល់</th>
                            <th class="px-5 py-3.5 text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-400 font-mono text-xs font-bold"><?php echo e($sale->id); ?></td>
                                <td class="px-5 py-3 font-semibold text-slate-800"><?php echo e($sale->customer?->name ?? 'N/A'); ?></td>
                                <td class="px-5 py-3 font-bold text-indigo-600 font-mono"><?php echo e(format_currency($sale->total)); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap"><?php echo e($sale->deleted_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <?php $daysLeft = max(0, 30 - (int) $sale->deleted_at->diffInDays(now())); ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?>">
                                        ⏱️ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="<?php echo e(route('sales.restore', $sale->id)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>ស្ដារវិញ</span>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('sales.force-delete', $sale->id)); ?>" method="POST" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបការលក់នេះជាស្ថាពរមែនទេ?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>លុបរហូត</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                <?php echo e($sales->appends(['tab' => 'sales'])->links()); ?>

            </div>
        <?php endif; ?>

    <?php elseif($tab === 'users'): ?>
        <?php if($users->isEmpty()): ?>
            <div class="p-12 text-center text-slate-400 font-medium text-sm">
                <?php echo e(app()->getLocale() === 'km' ? 'គ្មានទិន្នន័យបុគ្គលិកក្នុងធុងសំរាមទេ' : 'No deleted users in trash.'); ?>

            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">ឈ្មោះបុគ្គលិក</th>
                            <th class="px-5 py-3.5">អ៉ីមែល</th>
                            <th class="px-5 py-3.5">កាលបរិច្ឆេទលុប</th>
                            <th class="px-5 py-3.5 text-center">ថ្ងៃនៅសល់</th>
                            <th class="px-5 py-3.5 text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-400 font-mono text-xs font-bold"><?php echo e($user->id); ?></td>
                                <td class="px-5 py-3 font-semibold text-slate-800"><?php echo e($user->name); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-medium"><?php echo e($user->email); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap"><?php echo e($user->deleted_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <?php $daysLeft = max(0, 30 - (int) $user->deleted_at->diffInDays(now())); ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?>">
                                        ⏱️ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="<?php echo e(route('users.restore', $user->id)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>ស្ដារវិញ</span>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('users.force-delete', $user->id)); ?>" method="POST" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបបុគ្គលិកនេះជាស្ថាពរមែនទេ?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>លុបរហូត</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                <?php echo e($users->appends(['tab' => 'users'])->links()); ?>

            </div>
        <?php endif; ?>

    <?php elseif($tab === 'suppliers'): ?>
        <?php if($suppliers->isEmpty()): ?>
            <div class="p-12 text-center text-slate-400 font-medium text-sm">
                <?php echo e(app()->getLocale() === 'km' ? 'គ្មានទិន្នន័យអ្នកផ្គត់ផ្គង់ក្នុងធុងសំរាមទេ' : 'No deleted suppliers in trash.'); ?>

            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">ឈ្មោះអ្នកផ្គត់ផ្គង់</th>
                            <th class="px-5 py-3.5">លេខទូរស័ព្ទ</th>
                            <th class="px-5 py-3.5">កាលបរិច្ឆេទលុប</th>
                            <th class="px-5 py-3.5 text-center">ថ្ងៃនៅសល់</th>
                            <th class="px-5 py-3.5 text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-400 font-mono text-xs font-bold"><?php echo e($supplier->id); ?></td>
                                <td class="px-5 py-3 font-semibold text-slate-800"><?php echo e($supplier->name); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-medium"><?php echo e($supplier->phone ?? '—'); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap"><?php echo e($supplier->deleted_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <?php $daysLeft = max(0, 30 - (int) $supplier->deleted_at->diffInDays(now())); ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?>">
                                        ⏱️ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="<?php echo e(route('suppliers.restore', $supplier->id)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>ស្ដារវិញ</span>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('suppliers.force-delete', $supplier->id)); ?>" method="POST" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបអ្នកផ្គត់ផ្គង់នេះជាស្ថាពរមែនទេ?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>លុបរហូត</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                <?php echo e($suppliers->appends(['tab' => 'suppliers'])->links()); ?>

            </div>
        <?php endif; ?>

    <?php elseif($tab === 'categories'): ?>
        <?php if($categories->isEmpty()): ?>
            <div class="p-12 text-center text-slate-400 font-medium text-sm">
                <?php echo e(app()->getLocale() === 'km' ? 'គ្មានទិន្នន័យប្រភេទផលិតផលក្នុងធុងសំរាមទេ' : 'No deleted categories in trash.'); ?>

            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">ឈ្មោះប្រភេទ</th>
                            <th class="px-5 py-3.5">ម៉ាក (Brand)</th>
                            <th class="px-5 py-3.5">កាលបរិច្ឆេទលុប</th>
                            <th class="px-5 py-3.5 text-center">ថ្ងៃនៅសល់</th>
                            <th class="px-5 py-3.5 text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-400 font-mono text-xs font-bold"><?php echo e($category->id); ?></td>
                                <td class="px-5 py-3 font-semibold text-slate-800"><?php echo e($category->name); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-medium"><?php echo e($category->brand ?? '—'); ?></td>
                                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap"><?php echo e($category->deleted_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <?php $daysLeft = max(0, 30 - (int) $category->deleted_at->diffInDays(now())); ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?>">
                                        ⏱️ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="<?php echo e(route('categories.restore', $category->id)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>ស្ដារវិញ</span>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('categories.force-delete', $category->id)); ?>" method="POST" onsubmit="return confirm('តើអ្នកប្រាកដជាចង់លុបប្រភេទនេះជាស្ថាពរមែនទេ?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition-all cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>លុបរហូត</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                <?php echo e($categories->appends(['tab' => 'categories'])->links()); ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</div>

<script>
function filterTrashTable() {
    const query = document.getElementById('trashSearchInput')?.value.toLowerCase().trim() || '';
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (query === '' || text.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <?php echo e(app()->getLocale() === 'km' ? 'ធុងសំរាមរួម' : 'Recycle Bin'); ?>

            </h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('app.auto_delete_note')); ?></p>
        </div>
    </div>

    <?php
        $expiringCount = 0;
        $allCollections = [
            $customers ?? [],
            $installments ?? [],
            $products ?? [],
            $payments ?? [],
            $sales ?? [],
            $users ?? [],
            $suppliers ?? [],
            $categories ?? []
        ];
        foreach ($allCollections as $col) {
            if ($col instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                foreach ($col->items() as $item) {
                    if (isset($item->deleted_at)) {
                        $daysLeft = 30 - (int) $item->deleted_at->diffInDays(now());
                        if ($daysLeft <= 3 && $daysLeft >= 0) {
                            $expiringCount++;
                        }
                    }
                }
            }
        }
    ?>

    <?php if($expiringCount > 0): ?>
        <div class="mb-6 rounded-2xl bg-gradient-to-r from-red-50 to-amber-50 border border-red-200 p-4 flex items-center justify-between shadow-xs animate-in fade-in duration-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-red-900">
                        <?php echo e(app()->getLocale() === 'km' ? 'មានទិន្នន័យចំនួន ' . $expiringCount . ' ជិតដល់ថ្ងៃផុតកំណត់លុបចោលរហូត (នៅសល់ ≤ ៣ ថ្ងៃ)' : $expiringCount . ' item(s) expiring within 3 days!'); ?>

                    </h4>
                    <p class="text-xs text-red-700">
                        <?php echo e(app()->getLocale() === 'km' ? 'ទិន្នន័យទាំងនេះនឹងត្រូវលុបបាត់ពីប្រព័ន្ធរហូតដោយស្វ័យប្រវត្តិ ក្នុងពេលឆាប់ៗនេះ ប្រសិនបើអ្នកមិនបានចុច «ស្ដារឡើងវិញ» ទេ!' : 'These items will be permanently erased soon unless restored.'); ?>

                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <!-- Tabs Header (Horizontal Scrollable on Mobile) -->
    <div class="flex border-b border-gray-200 mb-6 overflow-x-auto whitespace-nowrap scrollbar-thin">
        <a href="<?php echo e(route('customers.trash', ['tab' => 'customers'])); ?>" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 <?php echo e($tab === 'customers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>" style="text-decoration: none;">
            <i class="fas fa-users"></i>
            <?php echo e(__('app.customers')); ?> (<?php echo e($customers->total()); ?>)
        </a>
        <a href="<?php echo e(route('customers.trash', ['tab' => 'installments'])); ?>" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 <?php echo e($tab === 'installments' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>" style="text-decoration: none;">
            <i class="fas fa-file-contract"></i>
            <?php echo e(__('app.installment_plans')); ?> (<?php echo e($installments->total()); ?>)
        </a>
        <a href="<?php echo e(route('customers.trash', ['tab' => 'products'])); ?>" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 <?php echo e($tab === 'products' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>" style="text-decoration: none;">
            <i class="fas fa-box"></i>
            <?php echo e(__('app.products')); ?> (<?php echo e($products->total()); ?>)
        </a>
        <a href="<?php echo e(route('customers.trash', ['tab' => 'payments'])); ?>" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 <?php echo e($tab === 'payments' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>" style="text-decoration: none;">
            <i class="fas fa-hand-holding-usd"></i>
            <?php echo e(app()->getLocale() === 'km' ? 'ការទូទាត់ប្រាក់' : 'Payments'); ?> (<?php echo e($payments->total()); ?>)
        </a>
        <a href="<?php echo e(route('customers.trash', ['tab' => 'sales'])); ?>" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 <?php echo e($tab === 'sales' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>" style="text-decoration: none;">
            <i class="fas fa-shopping-cart"></i>
            <?php echo e(app()->getLocale() === 'km' ? 'ការលក់ដាច់' : 'Sales'); ?> (<?php echo e($sales->total()); ?>)
        </a>
        <a href="<?php echo e(route('customers.trash', ['tab' => 'users'])); ?>" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 <?php echo e($tab === 'users' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>" style="text-decoration: none;">
            <i class="fas fa-users-cog"></i>
            <?php echo e(app()->getLocale() === 'km' ? 'បុគ្គលិក' : 'Users'); ?> (<?php echo e($users->total()); ?>)
        </a>
        <a href="<?php echo e(route('customers.trash', ['tab' => 'suppliers'])); ?>" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 <?php echo e($tab === 'suppliers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>" style="text-decoration: none;">
            <i class="fas fa-truck"></i>
            <?php echo e(app()->getLocale() === 'km' ? 'អ្នកផ្គត់ផ្គង់' : 'Suppliers'); ?> (<?php echo e($suppliers->total()); ?>)
        </a>
        <a href="<?php echo e(route('customers.trash', ['tab' => 'categories'])); ?>" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 <?php echo e($tab === 'categories' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>" style="text-decoration: none;">
            <i class="fas fa-tags"></i>
            <?php echo e(app()->getLocale() === 'km' ? 'ប្រភេទផលិតផល' : 'Categories'); ?> (<?php echo e($categories->total()); ?>)
        </a>
    </div>

    <?php
        $hasItems = false;
        if ($tab === 'customers' && $customers->total() > 0) $hasItems = true;
        if ($tab === 'installments' && $installments->total() > 0) $hasItems = true;
        if ($tab === 'products' && $products->total() > 0) $hasItems = true;
        if ($tab === 'users' && $users->total() > 0) $hasItems = true;
        if ($tab === 'payments' && $payments->total() > 0) $hasItems = true;
        if ($tab === 'suppliers' && $suppliers->total() > 0) $hasItems = true;
        if ($tab === 'categories' && $categories->total() > 0) $hasItems = true;
        if ($tab === 'sales' && $sales->total() > 0) $hasItems = true;
    ?>

    <?php if($hasItems): ?>
    <!-- Action Bar: Real-Time Search & Bulk Buttons -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
        <!-- Real-Time Trash Search Bar -->
        <div class="relative w-full sm:w-80">
            <input type="text" id="trashSearchInput" onkeyup="filterTrashTable()" placeholder="<?php echo e(app()->getLocale() === 'km' ? 'ស្វែងរកទិន្នន័យក្នុងធុងសំរាម...' : 'Search trash items...'); ?>" class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs">
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <form action="<?php echo e(route('trash.restore-all', ['tab' => $tab])); ?>" method="POST"
                  onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារទិន្នន័យទាំងអស់ក្នុងផ្នែកនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore all items in this section?'); ?>')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all shadow-xs hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span><?php echo e(app()->getLocale() === 'km' ? 'ស្ដារឡើងវិញទាំងអស់' : 'Restore All'); ?></span>
                </button>
            </form>

            <form action="<?php echo e(route('trash.empty', ['tab' => $tab])); ?>" method="POST"
                  onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបចោលទាំងអស់មែនទេ? ទិន្នន័យមិនអាចស្ដារវិញបានឡើយ!' : 'Are you sure you want to permanently delete all items in this section?'); ?>')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                </button>
            </form>
        </div>
    <?php endif; ?>

    <?php if($tab === 'customers'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.id')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.customers')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.phone')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប'); ?></th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono"><?php echo e($customer->id); ?></td>
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-3">
                                    <?php if($customer->photo): ?>
                                        <img src="<?php echo e(asset('storage/' . $customer->photo)); ?>" alt="<?php echo e($customer->name); ?>"
                                             class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                    <?php else: ?>
                                        <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-sm font-bold"><?php echo e(strtoupper(substr($customer->name, 0, 1))); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="font-semibold text-gray-800"><?php echo e($customer->name); ?></div>
                                        <?php if($customer->address): ?>
                                            <div class="text-xs text-gray-400 truncate max-w-[160px]"><?php echo e($customer->address); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-gray-600"><?php echo e($customer->phone ?? '—'); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-700"><?php echo e($customer->deleted_at->format('Y-m-d H:i')); ?></div>
                                <?php $daysLeft = max(0, 30 - (int) $customer->deleted_at->diffInDays(now())); ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?> border px-2 py-0.5 rounded-full shadow-2xs">
                                        ⏱️ នៅសល់ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="<?php echo e(route('customers.restore', $customer->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(__('app.confirm_restore_customer')); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            <?php echo e(__('app.restore')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('customers.force-delete', $customer->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(__('app.confirm_force_delete_customer')); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            <?php echo e(__('app.force_delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span><?php echo e(__('app.trash_empty')); ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($customers->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($customers->links()); ?>

            </div>
            <?php endif; ?>
        </div>

    <?php elseif($tab === 'installments'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.id')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.customer')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.product')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.total_price')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប'); ?></th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">#INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?></td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800"><?php echo e($installment->customer?->name ?? 'N/A'); ?></div>
                                <?php if($installment->customer?->phone): ?>
                                    <div class="text-xs text-gray-400"><?php echo e($installment->customer?->phone); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-2.5 text-gray-600"><?php echo e($installment->product?->name ?? 'N/A'); ?></td>
                            <td class="px-5 py-2.5 text-gray-900 font-bold"><?php echo e(format_currency($installment->total_price)); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-700"><?php echo e($installment->deleted_at->format('Y-m-d H:i')); ?></div>
                                <?php $daysLeft = max(0, 30 - (int) $installment->deleted_at->diffInDays(now())); ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?> border px-2 py-0.5 rounded-full shadow-2xs">
                                        ⏱️ នៅសល់ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="<?php echo e(route('installments.restore', $installment->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារគម្រោងបង់រំលស់នេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this installment plan?'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            <?php echo e(__('app.restore')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('installments.force-delete', $installment->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបគម្រោងបង់រំលស់នេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this installment plan permanently? This action cannot be undone!'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            <?php echo e(__('app.force_delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span><?php echo e(__('app.trash_empty')); ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($installments->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($installments->links()); ?>

            </div>
            <?php endif; ?>
        </div>

    <?php elseif($tab === 'products'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.item_code')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.products')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.price')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.stock')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប'); ?></th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono"><?php echo e($product->code ?? '—'); ?></td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800"><?php echo e($product->name); ?></div>
                                <?php if($product->category): ?>
                                    <div class="text-xs text-gray-400"><?php echo e($product->category); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-2.5 text-gray-900 font-bold"><?php echo e(format_currency($product->price)); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-semibold"><?php echo e($product->stock); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-700"><?php echo e($product->deleted_at->format('Y-m-d H:i')); ?></div>
                                <?php $daysLeft = max(0, 30 - (int) $product->deleted_at->diffInDays(now())); ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?> border px-2 py-0.5 rounded-full shadow-2xs">
                                        ⏱️ នៅសល់ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="<?php echo e(route('products.restore', $product->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារផលិតផលនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this product?'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            <?php echo e(__('app.restore')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('products.force-delete', $product->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបផលិតផលនេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this product permanently? This action cannot be undone!'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            <?php echo e(__('app.force_delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span><?php echo e(__('app.trash_empty')); ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($products->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($products->links()); ?>

            </div>
            <?php endif; ?>
        </div>

    <?php elseif($tab === 'payments'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.id')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.customer')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.amount')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទ & វិធីបង់ប្រាក់' : 'Date & Method'); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប'); ?></th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">#PAY-<?php echo e(str_pad($payment->id, 3, '0', STR_PAD_LEFT)); ?></td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800"><?php echo e($payment->installment?->customer?->name ?? 'N/A'); ?></div>
                                <div class="text-xs text-gray-400">#INS-<?php echo e(str_pad($payment->installment_id, 3, '0', STR_PAD_LEFT)); ?></div>
                            </td>
                            <td class="px-5 py-2.5 text-gray-900 font-bold">
                                <?php echo e(format_currency($payment->amount)); ?>

                                <?php if($payment->penalty_amount > 0): ?>
                                    <div class="text-xs text-red-600 font-semibold">+<?php echo e(format_currency($payment->penalty_amount)); ?> <?php echo e(app()->getLocale() === 'km' ? 'ពិន័យ' : 'Penalty'); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-2.5">
                                <div class="text-xs text-gray-700 font-medium"><?php echo e($payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : '—'); ?></div>
                                <div class="text-xs text-gray-400 font-medium"><?php echo e($payment->paymentMethod?->name ?? '—'); ?></div>
                            </td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-700"><?php echo e($payment->deleted_at->format('Y-m-d H:i')); ?></div>
                                <?php $daysLeft = max(0, 30 - (int) $payment->deleted_at->diffInDays(now())); ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?> border px-2 py-0.5 rounded-full shadow-2xs">
                                        ⏱️ នៅសល់ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="<?php echo e(route('payments.restore', $payment->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារការទូទាត់ប្រាក់នេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this payment?'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            <?php echo e(__('app.restore')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('payments.force-delete', $payment->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបការទូទាត់ប្រាក់នេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this payment permanently? This action cannot be undone!'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            <?php echo e(__('app.force_delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span><?php echo e(__('app.trash_empty')); ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($payments->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($payments->links()); ?>

            </div>
            <?php endif; ?>
        </div>

    <?php elseif($tab === 'sales'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.invoice_no')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.customer')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.total_price')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទលក់' : 'Sale Date'); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប'); ?></th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono font-semibold"><?php echo e($sale->invoice_no ?? '#SAL-' . str_pad($sale->id, 3, '0', STR_PAD_LEFT)); ?></td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800"><?php echo e($sale->customer_name ?? 'N/A'); ?></div>
                                <?php if($sale->customer_phone): ?>
                                    <div class="text-xs text-gray-400"><?php echo e($sale->customer_phone); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-2.5 text-gray-900 font-bold"><?php echo e(format_currency($sale->total)); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 text-xs font-medium"><?php echo e($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') : '—'); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-700"><?php echo e($sale->deleted_at->format('Y-m-d H:i')); ?></div>
                                <?php $daysLeft = max(0, 30 - (int) $sale->deleted_at->diffInDays(now())); ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?> border px-2 py-0.5 rounded-full shadow-2xs">
                                        ⏱️ នៅសល់ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="<?php echo e(route('sales.restore', $sale->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារការលក់នេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this sale?'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            <?php echo e(__('app.restore')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('sales.force-delete', $sale->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបការលក់នេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this sale permanently? This action cannot be undone!'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            <?php echo e(__('app.force_delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span><?php echo e(__('app.trash_empty')); ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($sales->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($sales->links()); ?>

            </div>
            <?php endif; ?>
        </div>

    <?php elseif($tab === 'users'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.id')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(app()->getLocale() === 'km' ? 'ឈ្មោះ & អ៊ីមែល' : 'Name & Email'); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(app()->getLocale() === 'km' ? 'តួនាទី' : 'Role'); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប'); ?></th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono"><?php echo e($user->id); ?></td>
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-3">
                                    <?php if($user->profile_image): ?>
                                        <img src="<?php echo e(asset('storage/' . $user->profile_image)); ?>" alt="<?php echo e($user->name); ?>"
                                             class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                    <?php else: ?>
                                        <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-xs font-bold"><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="font-semibold text-gray-800"><?php echo e($user->name); ?></div>
                                        <div class="text-xs text-gray-400 font-medium"><?php echo e($user->email); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-2.5">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo e($user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-700"><?php echo e($user->deleted_at->format('Y-m-d H:i')); ?></div>
                                <?php $daysLeft = max(0, 30 - (int) $user->deleted_at->diffInDays(now())); ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?> border px-2 py-0.5 rounded-full shadow-2xs">
                                        ⏱️ នៅសល់ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="<?php echo e(route('users.restore', $user->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារគណនីបុគ្គលិកនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this user?'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            <?php echo e(__('app.restore')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('users.force-delete', $user->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបគណនីបុគ្គលិកនេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this user permanently? This action cannot be undone!'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            <?php echo e(__('app.force_delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span><?php echo e(__('app.trash_empty')); ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($users->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($users->links()); ?>

            </div>
            <?php endif; ?>
        </div>

    <?php elseif($tab === 'suppliers'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.id')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(app()->getLocale() === 'km' ? 'អ្នកផ្គត់ផ្គង់' : 'Supplier Name'); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.phone')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.email')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប'); ?></th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono"><?php echo e($supplier->id); ?></td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800"><?php echo e($supplier->name); ?></div>
                                <?php if($supplier->address): ?>
                                    <div class="text-xs text-gray-400"><?php echo e($supplier->address); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-2.5 text-gray-600 font-medium"><?php echo e($supplier->phone ?? '—'); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-medium"><?php echo e($supplier->email ?? '—'); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-700"><?php echo e($supplier->deleted_at->format('Y-m-d H:i')); ?></div>
                                <?php $daysLeft = max(0, 30 - (int) $supplier->deleted_at->diffInDays(now())); ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?> border px-2 py-0.5 rounded-full shadow-2xs">
                                        ⏱️ នៅសល់ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="<?php echo e(route('suppliers.restore', $supplier->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារអ្នកផ្គត់ផ្គង់នេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this supplier?'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            <?php echo e(__('app.restore')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('suppliers.force-delete', $supplier->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបអ្នកផ្គត់ផ្គង់នេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this supplier permanently? This action cannot be undone!'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            <?php echo e(__('app.force_delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span><?php echo e(__('app.trash_empty')); ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($suppliers->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($suppliers->links()); ?>

            </div>
            <?php endif; ?>
        </div>

    <?php elseif($tab === 'categories'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.id')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(app()->getLocale() === 'km' ? 'ឈ្មោះប្រភេទផលិតផល' : 'Category Name'); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.brand')); ?></th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប'); ?></th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e(__('app.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono"><?php echo e($category->id); ?></td>
                            <td class="px-5 py-2.5 text-gray-800 font-semibold"><?php echo e($category->name); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-medium"><?php echo e($category->brand ?? '—'); ?></td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-700"><?php echo e($category->deleted_at->format('Y-m-d H:i')); ?></div>
                                <?php $daysLeft = max(0, 30 - (int) $category->deleted_at->diffInDays(now())); ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold <?php echo e($daysLeft <= 5 ? 'text-rose-700 bg-rose-50 border-rose-200' : ($daysLeft <= 15 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200')); ?> border px-2 py-0.5 rounded-full shadow-2xs">
                                        ⏱️ នៅសល់ <?php echo e($daysLeft); ?> ថ្ងៃ
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="<?php echo e(route('categories.restore', $category->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារប្រភេទផលិតផលនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this category?'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            <?php echo e(__('app.restore')); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('categories.force-delete', $category->id)); ?>" method="POST"
                                          onsubmit="return confirm('<?php echo e(app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបប្រភេទផលិតផលនេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this category permanently? This action cannot be undone!'); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            <?php echo e(__('app.force_delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span><?php echo e(__('app.trash_empty')); ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($categories->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($categories->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>
<script>
function filterTrashTable() {
    const query = document.getElementById('trashSearchInput')?.value.toLowerCase().trim() || '';
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (query === '' || text.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/customers/trash.blade.php ENDPATH**/ ?>