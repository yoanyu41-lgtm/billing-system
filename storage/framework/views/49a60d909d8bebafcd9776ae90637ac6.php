<?php $__env->startSection('content'); ?>
<div class="content">
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-cash-register text-blue-600"></i>
                <?php echo e(__('app.sales_list')); ?>

            </h1>
            <p class="text-sm text-gray-600 mt-1"><?php echo e(__('app.sales_list_subtitle')); ?></p>
        </div>
        <?php if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.create')): ?>
        <a href="<?php echo e(route('admin.sales.create')); ?>"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> <?php echo e(__('app.new_direct_sale')); ?>

        </a>
        <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <form method="GET" class="mb-4">
        <div class="flex items-center gap-2 max-w-md">
            <div class="relative flex-1">
                <input type="text" name="q" id="search-input" value="<?php echo e(request('q')); ?>" autocomplete="off"
                       placeholder="<?php echo e(__('app.invoice_no')); ?> / <?php echo e(__('app.customer_name')); ?> / <?php echo e(__('app.customer_phone')); ?>"
                       class="w-full pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <?php if(request('q')): ?>
                <button type="button" onclick="clearSearchInput(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" title="Clear">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <?php endif; ?>

                <div id="suggestions-box" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"></div>
            </div>
            <button type="submit" class="px-4 py-2.5 text-sm bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                <i class="fas fa-search"></i>
            </button>
            <?php if(request('q')): ?>
            <a href="<?php echo e(route('admin.sales.index')); ?>" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors text-center shrink-0">
                <?php echo e(__('app.clear')); ?>

            </a>
            <?php endif; ?>
        </div>
    </form>

    <div class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.invoice_no')); ?></th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.customer')); ?></th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.sale_date')); ?></th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.product')); ?></th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.total')); ?></th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.payment_method')); ?></th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600"><?php echo e($sale->invoice_no ?? ('#'.$sale->id)); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($sale->customer_name ?: __('app.walk_in_customer')); ?>

                                <?php if($sale->customer_phone): ?><div class="text-xs text-gray-400"><?php echo e($sale->customer_phone); ?></div><?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e(optional($sale->sale_date)->format('d M Y')); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center gap-2 <?php echo e(!$loop->last ? 'mb-1' : ''); ?>">
                                        <span class="max-w-[180px] lg:max-w-[220px] truncate block" title="<?php echo e($item->product->name ?? '—'); ?>"><?php echo e($item->product->name ?? '—'); ?></span>
                                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded shrink-0">x<?php echo e($item->quantity); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <span class="font-bold text-gray-900"><?php echo e(format_currency($sale->total, $exchangeRate)); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                                    <?php echo e(\Illuminate\Support\Facades\Lang::has('app.'.$sale->payment_method) ? __('app.'.$sale->payment_method) : $sale->payment_method); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                <a href="<?php echo e(route('admin.sales.show', [$sale, 'from' => request('from')])); ?>" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 hover:text-blue-800 transition" 
                                   title="<?php echo e(__('app.view_receipt')); ?>">
                                    <i class="fas fa-eye text-base"></i>
                                </a>
                                <?php if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.edit')): ?>
                                <a href="<?php echo e(route('admin.sales.edit', [$sale, 'from' => request('from')])); ?>" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-600 hover:bg-amber-50 hover:text-amber-800 transition" 
                                   title="<?php echo e(__('app.edit') ?? 'Edit'); ?>">
                                    <i class="fas fa-edit text-base"></i>
                                </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.delete')): ?>
                                <form action="<?php echo e(route('admin.sales.destroy', $sale)); ?>" method="POST" class="inline"
                                      onsubmit="return confirm('<?php echo e(__('app.confirm_delete_sale')); ?>')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-800 transition"
                                            title="<?php echo e(__('app.delete') ?? 'Delete'); ?>">
                                        <i class="fas fa-trash-alt text-base"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                <?php echo e(__('app.no_sales_yet')); ?>

                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <?php echo e($sales->links()); ?>

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
            if (this.value.trim() === '' && urlParams.has('q') && urlParams.get('q') !== '') {
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/sales/index.blade.php ENDPATH**/ ?>