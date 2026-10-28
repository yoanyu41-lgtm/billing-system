<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo e(__('app.suppliers')); ?></h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('app.supplier_subtitle')); ?></p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?php echo e(route('admin.products.stock')); ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <?php echo e(__('app.back')); ?> to <?php echo e(__('app.manage_stock')); ?>

            </a>
            <a href="<?php echo e(route('admin.suppliers.create')); ?>" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <?php echo e(__('app.add_supplier')); ?>

            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Search Bar -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="<?php echo e(route('admin.suppliers.index')); ?>" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        id="search-input"
                        value="<?php echo e(request('search')); ?>" 
                        autocomplete="off"
                        class="block w-full pl-10 pr-9 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm transition duration-150" 
                        placeholder="<?php echo e(app()->getLocale() === 'km' ? 'ស្វែងរកតាមឈ្មោះ, អ៊ីមែល, ឬ អាសយដ្ឋាន...' : 'Search by name, email, or address...'); ?>"
                    >

                    <?php if(request('search')): ?>
                    <button type="button" onclick="clearSearchInput(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" title="Clear">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <?php endif; ?>

                    <div id="suggestions-box" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"></div>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <?php echo e(__('app.search')); ?>

                </button>
                <?php if(request('search')): ?>
                    <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <?php echo e(__('app.clear')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Suppliers Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.name')); ?></th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.phone')); ?></th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.email')); ?></th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e(__('app.address')); ?></th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-40"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <?php echo e($s->name); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo e($s->phone ?? '—'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo e($s->email ?? '—'); ?>

                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php echo e($s->address ?? '—'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-1.5">
                                <a href="<?php echo e(route('admin.suppliers.edit', $s)); ?>" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 hover:text-yellow-900 rounded-lg transition duration-150" title="<?php echo e(__('app.edit')); ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="<?php echo e(route('admin.suppliers.destroy', $s)); ?>" method="POST" class="inline-block" onsubmit="return confirm('<?php echo e(__('app.confirm_delete')); ?>');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-900 rounded-lg transition duration-150" title="<?php echo e(__('app.delete')); ?>">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                            <?php echo e(__('app.no_suppliers')); ?>. Click the button above to add one.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <?php echo e($suppliers->links()); ?>

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
            if (this.value.trim() === '' && urlParams.has('search') && urlParams.get('search') !== '') {
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/suppliers/index.blade.php ENDPATH**/ ?>