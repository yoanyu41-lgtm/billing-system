<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo e(__('app.contract_terms')); ?></h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('app.contract_terms_subtitle')); ?></p>
        </div>
        <a href="<?php echo e(route('admin.contract-terms.create')); ?>" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm transition duration-150">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <?php echo e(__('app.add_term')); ?>

        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3.5 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm text-sm font-medium">
            <svg class="w-5 h-5 mr-2.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider w-20">#</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider"><?php echo e(__('app.term_title')); ?></th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider w-36"><?php echo e(__('app.status')); ?></th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider w-36"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/70 transition duration-150">
                        <td class="px-6 py-5 text-center text-base text-slate-700 font-bold"><?php echo e($term->sort_order); ?></td>
                        <td class="px-6 py-5">
                            <div class="text-base font-bold text-slate-900 leading-snug"><?php echo e($term->title_km); ?></div>
                            <?php if($term->title_en): ?>
                                <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mt-0.5"><?php echo e($term->title_en); ?></div>
                            <?php endif; ?>
                            <div class="text-sm text-slate-600 mt-1.5 leading-relaxed"><?php echo e(\Illuminate\Support\Str::limit($term->content_km, 120)); ?></div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            <?php if($term->is_active): ?>
                                <span class="px-3.5 py-1 inline-flex text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-check-circle mr-1 self-center"></i> <?php echo e(__('app.active')); ?>

                                </span>
                            <?php else: ?>
                                <span class="px-3.5 py-1 inline-flex text-xs font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                    <i class="fas fa-pause-circle mr-1 self-center"></i> <?php echo e(__('app.inactive')); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-2">
                                <a href="<?php echo e(route('admin.contract-terms.edit', $term)); ?>" class="p-2.5 text-amber-600 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition duration-150 shadow-2xs" title="<?php echo e(__('app.edit')); ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form method="POST" action="<?php echo e(route('admin.contract-terms.destroy', $term)); ?>" class="inline-block" onsubmit="return confirm('<?php echo e(__('app.confirm_delete')); ?>')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-2.5 text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition duration-150 shadow-2xs cursor-pointer" title="<?php echo e(__('app.delete')); ?>">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 font-medium"><?php echo e(__('app.no_terms')); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/contract_terms/index.blade.php ENDPATH**/ ?>