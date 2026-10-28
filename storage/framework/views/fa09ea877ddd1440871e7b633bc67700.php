<?php $__env->startSection('content'); ?>
<?php
    $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') ?? '0';
    $hasValidImage = $product->image && !\Illuminate\Support\Str::contains(strtolower($product->image), 'undefined');
    $imgSrc = $hasValidImage ? (\Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : asset('storage/' . $product->image)) : null;
    $unitCost = $product->cost_price ?? $product->price;
    $stockValue = (float)$unitCost * (int)$product->stock;
    $profit = $product->cost_price ? ($product->price - $product->cost_price) : null;
    $isKm = app()->getLocale() === 'km';
?>

<div class="container mx-auto px-4 py-8 max-w-6xl">

    
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <nav class="flex items-center gap-2 text-sm text-gray-400">
            <?php if(request('from') === 'stock'): ?>
                <a href="<?php echo e(route('admin.products.stock')); ?>" class="hover:text-indigo-600 transition font-medium"><?php echo e(__('app.manage_stock')); ?></a>
            <?php else: ?>
                <a href="<?php echo e(route('admin.products.index')); ?>" class="hover:text-indigo-600 transition font-medium"><?php echo e(__('app.products')); ?></a>
            <?php endif; ?>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-600 font-medium truncate max-w-xs"><?php echo e($product->name); ?></span>
        </nav>
        <div class="flex items-center gap-2">
            <?php if(request('from') === 'stock'): ?>
                <a href="<?php echo e(route('admin.products.stock')); ?>" class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <?php echo e(__('app.back_to_manage_stock')); ?>

                </a>
            <?php else: ?>
                <a href="<?php echo e(route('admin.products.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <?php echo e(__('app.back_to_product_list')); ?>

                </a>
            <?php endif; ?>
            <?php if(auth()->user()->role === 'admin'): ?>
            <a href="<?php echo e(route('admin.products.edit', [$product, 'from' => request('from')])); ?>" class="inline-flex items-center gap-1.5 text-sm bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <?php echo e(__('app.edit_product')); ?>

            </a>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
        <div class="flex flex-col md:flex-row">
            
            <div class="md:w-64 lg:w-72 shrink-0 bg-gradient-to-br from-indigo-50 to-slate-50 flex flex-col items-center justify-center p-6 border-b md:border-b-0 md:border-r border-gray-100">
                <?php if($imgSrc): ?>
                    <img id="detail_prod_img" src="<?php echo e($imgSrc); ?>" alt="<?php echo e($product->name); ?>"
                         class="w-52 h-52 object-cover rounded-xl shadow-md border border-gray-100"
                         onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?php echo e(urlencode($product->name)); ?>&color=4F46E5&background=EEF2FF&bold=true&size=200'">
                    <button type="button" onclick="copyDetailImageToClipboard('<?php echo e($imgSrc); ?>')" 
                            class="mt-3 w-full inline-flex items-center justify-center gap-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-3 py-2 rounded-lg transition shadow-2xs cursor-pointer">
                        <i class="fas fa-copy"></i>
                        <span><?php echo e(app()->getLocale() === 'km' ? 'Copy រូបភាព' : 'Copy Image'); ?></span>
                    </button>
                <?php else: ?>
                    <div class="w-52 h-52 bg-indigo-50 rounded-xl border border-dashed border-indigo-200 flex flex-col items-center justify-center text-indigo-300">
                        <svg class="w-14 h-14 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-medium text-indigo-300"><?php echo e(__('app.no_image')); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="flex-1 p-6 lg:p-8">
                
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <?php if($product->category): ?>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <?php echo e($product->category); ?>

                    </span>
                    <?php endif; ?>
                    <?php if($product->condition): ?>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-100">
                        <?php echo e($product->condition === 'new' ? ($isKm ? 'ថ្មី 100%' : 'Brand New') : ucfirst($product->condition)); ?>

                    </span>
                    <?php endif; ?>
                    <?php if($product->is_active): ?>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-100 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span><?php echo e(__('app.active')); ?>

                    </span>
                    <?php else: ?>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span><?php echo e(__('app.inactive')); ?>

                    </span>
                    <?php endif; ?>
                </div>

                
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-1"><?php echo e($product->name); ?></h1>
                <?php if($product->name2): ?>
                <p class="text-sm text-gray-500 mb-3"><?php echo e($product->name2); ?></p>
                <?php endif; ?>

                
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <?php echo e($isKm ? 'កូដ' : 'Item Code'); ?>: <?php echo e($product->code); ?>

                    </span>
                    <?php if($product->barcode): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-mono border border-gray-200">
                        🏷️ <?php echo e($isKm ? 'បារកូដ' : 'Barcode'); ?>: <?php echo e($product->barcode); ?>

                    </span>
                    <?php endif; ?>
                    <?php if($product->supplier): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-semibold border border-green-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <?php echo e($product->supplier->name); ?>

                    </span>
                    <?php endif; ?>
                </div>

                
                <?php if($product->cpu || $product->ram || $product->storage): ?>
                <div class="flex flex-wrap gap-1.5 mt-4">
                    <?php if($product->cpu): ?>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">CPU: <?php echo e($product->cpu); ?></span>
                    <?php endif; ?>
                    <?php if($product->ram): ?>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">RAM: <?php echo e($product->ram); ?></span>
                    <?php endif; ?>
                    <?php if($product->storage): ?>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200"><?php echo e($isKm ? 'ឧបករណ៍ផ្ទុក' : 'Storage'); ?>: <?php echo e($product->storage); ?></span>
                    <?php endif; ?>
                    <?php if($product->graphics_card): ?>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">GPU: <?php echo e($product->graphics_card); ?></span>
                    <?php endif; ?>
                    <?php if($product->warranty): ?>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-teal-50 text-teal-700 border border-teal-100">🛡️ <?php echo e($product->warranty); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-5 text-white shadow-sm shadow-indigo-200">
            <div class="text-xs font-semibold text-indigo-200 uppercase tracking-wider mb-2">
                <?php echo e($isKm ? 'តម្លៃលក់' : 'Price'); ?>

            </div>
            <div class="text-2xl font-bold">$<?php echo e(number_format($product->price, 2)); ?></div>
            <div class="text-xs text-indigo-200 mt-1"><?php echo e($isKm ? 'ក្នុង ១ ឯកតា' : 'per unit'); ?></div>
        </div>

        
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                <?php echo e($isKm ? 'តម្លៃដើម' : 'Supply Price'); ?>

            </div>
            <div class="text-2xl font-bold text-gray-800">
                <?php echo e($product->cost_price ? '$' . number_format($product->cost_price, 2) : '—'); ?>

            </div>
            <?php if($profit !== null): ?>
            <div class="text-xs mt-1 <?php echo e($profit >= 0 ? 'text-green-500' : 'text-red-500'); ?> font-medium">
                <?php echo e($profit >= 0 ? '▲' : '▼'); ?> $<?php echo e(number_format(abs($profit), 2)); ?> <?php echo e($isKm ? 'ចំណេញ' : 'profit'); ?>

            </div>
            <?php else: ?>
            <div class="text-xs text-gray-400 mt-1"><?php echo e($isKm ? 'មិនបានបញ្ចូល' : 'not set'); ?></div>
            <?php endif; ?>
        </div>

        
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                <?php echo e($isKm ? 'ចំនួនស្តុក' : 'Stock Qty.'); ?>

            </div>
            <div class="text-2xl font-bold
                <?php if($product->stock <= 0): ?> text-red-600
                <?php elseif($product->stock <= ($product->low_stock_threshold ?? 5)): ?> text-amber-600
                <?php else: ?> text-green-600 <?php endif; ?>">
                <?php echo e($product->stock); ?>

            </div>
            <div class="text-xs mt-1
                <?php if($product->stock <= 0): ?> text-red-400
                <?php elseif($product->stock <= ($product->low_stock_threshold ?? 5)): ?> text-amber-400
                <?php else: ?> text-green-400 <?php endif; ?> font-medium">
                <?php if($product->stock <= 0): ?> <?php echo e(__('app.out_of_stock')); ?>

                <?php elseif($product->stock <= ($product->low_stock_threshold ?? 5)): ?> <?php echo e(__('app.low_stock')); ?>

                <?php else: ?> <?php echo e(__('app.in_stock')); ?> <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl p-5 border border-emerald-100 shadow-sm">
            <div class="text-xs font-semibold text-emerald-500 uppercase tracking-wider mb-2">
                <?php echo e($isKm ? 'តម្លៃស្តុកសរុប' : 'Stock Value'); ?>

            </div>
            <div class="text-2xl font-bold text-emerald-700">$<?php echo e(number_format($stockValue, 2)); ?></div>
            <div class="text-xs text-emerald-400 mt-1">
                <?php if($product->cost_price): ?>
                    $<?php echo e(number_format($product->cost_price, 2)); ?> × <?php echo e($product->stock); ?>

                <?php else: ?>
                    $<?php echo e(number_format($product->price, 2)); ?> × <?php echo e($product->stock); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="space-y-6">

            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <?php echo e($isKm ? 'ព័ត៌មានស្តុក' : 'Stock Details'); ?>

                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500"><?php echo e($isKm ? 'ចំនួនបច្ចុប្បន្ន' : 'Current Stock'); ?></span>
                        <span class="text-sm font-bold text-gray-800"><?php echo e($product->stock); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500"><?php echo e($isKm ? 'ស្តុកទាប' : 'Low Stock Alert'); ?></span>
                        <span class="text-sm font-semibold text-amber-600"><?php echo e($product->low_stock_threshold ?? 5); ?></span>
                    </div>
                    <?php if($product->max_stock_qty !== null): ?>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500"><?php echo e($isKm ? 'ស្តុកអតិបរមា' : 'Max Stock'); ?></span>
                        <span class="text-sm font-semibold text-gray-800"><?php echo e($product->max_stock_qty); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($product->unit): ?>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500"><?php echo e($isKm ? 'ឯកតា' : 'Unit'); ?></span>
                        <span class="text-sm font-semibold text-gray-800"><?php echo e($product->unit); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($product->exchange_unit): ?>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500"><?php echo e($isKm ? 'ឯកតាប្ដូរ' : 'Exchange Unit'); ?></span>
                        <span class="text-sm font-semibold text-indigo-600"><?php echo e($product->exchange_unit); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($product->last_stock_in_at): ?>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-500"><?php echo e($isKm ? 'ស្តុកចូលចុងក្រោយ' : 'Last Stock In'); ?></span>
                        <span class="text-xs font-semibold text-gray-700"><?php echo e(\Carbon\Carbon::parse($product->last_stock_in_at)->format('d/m/Y')); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <?php echo e(__('app.supplier')); ?>

                </div>
                <?php if($product->supplier): ?>
                    <div class="flex items-center gap-3 bg-green-50 rounded-xl px-4 py-3 border border-green-100">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-sm">
                            <?php echo e(strtoupper(substr($product->supplier->name, 0, 1))); ?>

                        </div>
                        <span class="text-sm font-semibold text-green-800"><?php echo e($product->supplier->name); ?></span>
                    </div>
                <?php elseif($suppliers->count()): ?>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-2 bg-green-50 rounded-xl px-3 py-2 border border-green-100">
                            <div class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-xs">
                                <?php echo e(strtoupper(substr($supplier->name, 0, 1))); ?>

                            </div>
                            <span class="text-xs font-medium text-green-800"><?php echo e($supplier->name); ?></span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-400 italic"><?php echo e(__('app.no_supplier_recorded')); ?></p>
                <?php endif; ?>
            </div>

            
            <?php if($taxEnabled == '1'): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                    VAT
                </div>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500"><?php echo e(__('app.taxable')); ?></span>
                        <span class="text-xs font-bold <?php echo e($product->is_taxable ? 'text-green-600' : 'text-gray-400'); ?>">
                            <?php echo e($product->is_taxable ? __('app.yes') : __('app.no')); ?>

                        </span>
                    </div>
                    <?php if($product->is_taxable): ?>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500"><?php echo e(__('app.tax_rate')); ?></span>
                        <span class="text-xs font-bold text-indigo-600"><?php echo e((float)$product->tax_rate); ?>%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500"><?php echo e(__('app.tax_type')); ?></span>
                        <span class="text-xs font-bold text-gray-700">
                            <?php echo e($product->tax_type === 'inclusive' ? __('app.tax_inclusive') : __('app.tax_exclusive')); ?>

                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="lg:col-span-2 space-y-6">

            
            <?php if($product->description || $product->summary): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <?php echo e($isKm ? 'ការពិពណ៌នា' : 'Description'); ?>

                </div>
                <?php if($product->summary): ?>
                <p class="text-sm text-gray-600 leading-relaxed mb-3"><?php echo e($product->summary); ?></p>
                <?php endif; ?>
                <?php if($product->description): ?>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line"><?php echo e($product->description); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            
            <?php if($product->imei || $product->stock_note || $product->name2 || $product->seo): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo e($isKm ? 'ព័ត៌មានបន្ថែម' : 'Additional Info'); ?>

                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php if($product->name2): ?>
                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                        <div class="text-xs text-gray-400 font-medium mb-1"><?php echo e($isKm ? 'ឈ្មោះ២' : 'Secondary Name'); ?></div>
                        <div class="text-sm font-semibold text-gray-800"><?php echo e($product->name2); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($product->imei): ?>
                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                        <div class="text-xs text-gray-400 font-medium mb-1">IMEI / Serial No.</div>
                        <div class="text-sm font-mono font-semibold text-gray-800"><?php echo e($product->imei); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if($product->stock_note): ?>
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 mt-3">
                    <div class="text-xs text-amber-700 font-bold mb-1">📋 <?php echo e($isKm ? 'កំណត់ចំណាំស្តុក' : 'Stock Note'); ?></div>
                    <div class="text-sm text-amber-900 whitespace-pre-line"><?php echo e($product->stock_note); ?></div>
                </div>
                <?php endif; ?>
                <?php if($product->seo): ?>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mt-3">
                    <div class="text-xs text-gray-400 font-bold mb-1">SEO Info</div>
                    <div class="text-sm text-gray-600 whitespace-pre-line"><?php echo e($product->seo); ?></div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            
            <?php if($product->cpu || $product->ram || $product->storage || $product->graphics_card || $product->color || $product->warranty): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    <?php echo e(__('app.computer_specifications')); ?>

                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <?php if($product->cpu): ?>
                    <div class="flex items-center gap-3 bg-indigo-50/60 rounded-xl p-3.5 border border-indigo-100/60">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-indigo-400 font-medium">CPU</div>
                            <div class="text-xs font-bold text-indigo-800"><?php echo e($product->cpu); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($product->ram): ?>
                    <div class="flex items-center gap-3 bg-emerald-50/60 rounded-xl p-3.5 border border-emerald-100/60">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 12a2 2 0 00-2 2v2a2 2 0 002 2h14a2 2 0 002-2v-2a2 2 0 00-2-2"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-emerald-400 font-medium">RAM</div>
                            <div class="text-xs font-bold text-emerald-800"><?php echo e($product->ram); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($product->storage): ?>
                    <div class="flex items-center gap-3 bg-amber-50/60 rounded-xl p-3.5 border border-amber-100/60">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-amber-400 font-medium"><?php echo e($isKm ? 'ឧបករណ៍ផ្ទុក' : 'Storage'); ?></div>
                            <div class="text-xs font-bold text-amber-800"><?php echo e($product->storage); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($product->graphics_card): ?>
                    <div class="flex items-center gap-3 bg-rose-50/60 rounded-xl p-3.5 border border-rose-100/60">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-rose-400 font-medium">GPU</div>
                            <div class="text-xs font-bold text-rose-800"><?php echo e($product->graphics_card); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($product->color): ?>
                    <div class="flex items-center gap-3 bg-sky-50/60 rounded-xl p-3.5 border border-sky-100/60">
                        <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-sky-400 font-medium"><?php echo e($isKm ? 'ពណ៌' : 'Color'); ?></div>
                            <div class="text-xs font-bold text-sky-800"><?php echo e($product->color); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($product->warranty): ?>
                    <div class="flex items-center gap-3 bg-teal-50/60 rounded-xl p-3.5 border border-teal-100/60">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-teal-400 font-medium"><?php echo e($isKm ? 'ការធានា' : 'Warranty'); ?></div>
                            <div class="text-xs font-bold text-teal-800"><?php echo e($product->warranty); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($purchaseHistory->count()): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <?php echo e(__('app.purchase_history')); ?>

                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500"><?php echo e(__('app.supplier')); ?></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500"><?php echo e(__('app.purchase_date')); ?></th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500"><?php echo e(__('app.qty')); ?></th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500"><?php echo e($isKm ? 'តម្លៃដើម' : 'Supply Price'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php $__currentLoopData = $purchaseHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50/70 transition duration-150">
                                <td class="px-4 py-3 text-gray-900 font-medium"><?php echo e(optional(optional($item->purchase)->supplier)->name ?? '—'); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo e(optional(optional($item->purchase)->purchase_date)->format('d/m/Y') ?? '—'); ?></td>
                                <td class="px-4 py-3 text-right text-gray-700 font-semibold"><?php echo e($item->quantity); ?></td>
                                <td class="px-4 py-3 text-right text-gray-900 font-bold">$<?php echo e(number_format($item->cost_price, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div id="detail_toast_notification" class="fixed bottom-5 right-5 z-50 transform translate-y-10 opacity-0 pointer-events-none transition-all duration-300 flex items-center gap-2 bg-slate-900/90 text-white text-sm font-semibold px-4 py-3 rounded-xl shadow-2xl backdrop-blur-sm border border-slate-700">
    <i class="fas fa-info-circle text-indigo-400"></i>
    <span id="detail_toast_message"></span>
</div>

<script>
    async function copyDetailImageToClipboard(imgSrc) {
        if (!imgSrc) return;
        try {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.src = imgSrc;
            await new Promise((resolve) => {
                img.onload = resolve;
                img.onerror = resolve;
            });

            const canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth || img.width || 300;
            canvas.height = img.naturalHeight || img.height || 300;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            canvas.toBlob(async (blob) => {
                if (!blob) {
                    await fallbackDetailCopyLink(imgSrc);
                    return;
                }
                try {
                    await navigator.clipboard.write([
                        new ClipboardItem({ [blob.type || 'image/png']: blob })
                    ]);
                    showDetailToast("📋 <?php echo e(app()->getLocale() === 'km' ? 'បាន Copy រូបភាពទៅ Clipboard! អាចយកទៅ Paste ក្នុង Telegram/Facebook បាន' : 'Image copied to clipboard!'); ?>");
                } catch (err) {
                    await fallbackDetailCopyLink(imgSrc);
                }
            }, 'image/png');
        } catch (e) {
            await fallbackDetailCopyLink(imgSrc);
        }
    }

    async function fallbackDetailCopyLink(url) {
        try {
            await navigator.clipboard.writeText(url.startsWith('http') ? url : window.location.origin + url);
            showDetailToast("🔗 <?php echo e(app()->getLocale() === 'km' ? 'បាន Copy តំណភ្ជាប់រូបភាពទៅ Clipboard!' : 'Image link copied to clipboard!'); ?>");
        } catch (err) {
            showDetailToast("❌ <?php echo e(app()->getLocale() === 'km' ? 'មិនអាច Copy បានទេ' : 'Could not copy image'); ?>");
        }
    }

    function showDetailToast(message) {
        const toast = document.getElementById('detail_toast_notification');
        const msg = document.getElementById('detail_toast_message');
        if (!toast || !msg) return;
        msg.textContent = message;
        toast.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
        }, 3500);
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/products/show.blade.php ENDPATH**/ ?>