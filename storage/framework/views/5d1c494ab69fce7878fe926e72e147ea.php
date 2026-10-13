
<?php if($paginator->hasPages()): ?>
<div class="flex items-center justify-between px-2 py-3">
    
    <div class="text-sm text-gray-500">
        Showing
        <span class="font-semibold text-gray-700"><?php echo e($paginator->firstItem()); ?></span>
        to
        <span class="font-semibold text-gray-700"><?php echo e($paginator->lastItem()); ?></span>
        of
        <span class="font-semibold text-gray-700"><?php echo e($paginator->total()); ?></span>
        results
    </div>

    
    <div class="flex items-center gap-1">
        
        <?php if($paginator->onFirstPage()): ?>
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </span>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
        <?php endif; ?>

        
        <?php $__currentLoopData = $paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($page == $paginator->currentPage()): ?>
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-800 text-white font-semibold text-sm shadow-sm">
                    <?php echo e($page); ?>

                </span>
            <?php else: ?>
                <a href="<?php echo e($url); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition text-sm shadow-sm">
                    <?php echo e($page); ?>

                </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        <?php else: ?>
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        <?php endif; ?>
    </div>
</div>
<?php elseif($paginator->total() > 0): ?>
<div class="px-2 py-3 text-sm text-gray-500">
    Showing all
    <span class="font-semibold text-gray-700"><?php echo e($paginator->total()); ?></span>
    results
</div>
<?php endif; ?>
<?php /**PATH E:\billing-system\resources\views/partials/pagination.blade.php ENDPATH**/ ?>