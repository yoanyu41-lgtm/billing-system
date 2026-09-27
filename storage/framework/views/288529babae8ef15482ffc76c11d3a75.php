<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo e(__('app.add_product')); ?></h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('app.enter_details_to_create')); ?></p>
        </div>
        <?php if(request('from') === 'stock'): ?>
        <a href="<?php echo e(route('admin.products.stock')); ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <?php echo e(__('app.back_to_manage_stock')); ?>

        </a>
        <?php else: ?>
        <a href="<?php echo e(route('admin.products.index')); ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <?php echo e(__('app.back_to_product_list')); ?>

        </a>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?php echo e(route('admin.products.store', ['from' => request('from')])); ?>" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <?php echo csrf_field(); ?>

        <?php if($errors->any()): ?>
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800">
            <div class="font-semibold mb-1"><?php echo e(__('app.please_fix_errors')); ?></div>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Item Code -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.item_code')); ?> <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="<?php echo e(old('code')); ?>" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 <?php echo e($errors->has('code') ? 'border-red-500' : 'border-gray-300'); ?>" placeholder="e.g., PROD-001">
                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Barcode -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.barcode')); ?></label>
                <input type="text" name="barcode" value="<?php echo e(old('barcode')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g. 880123456789">
            </div>

            <!-- Name -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.name')); ?> <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 <?php echo e($errors->has('name') ? 'border-red-500' : 'border-gray-300'); ?>" placeholder="<?php echo e(__('app.product_name')); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.category')); ?></label>
                <input type="text" name="category" value="<?php echo e(old('category')); ?>" list="categories-list" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 <?php echo e($errors->has('category') ? 'border-red-500' : 'border-gray-300'); ?>" placeholder="e.g. Laptop, Keyboard, Monitor...">
                <datalist id="categories-list">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </datalist>
                <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Stock -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.stock_quantity')); ?> <span class="text-red-500">*</span></label>
                <input type="number" name="stock" value="<?php echo e(old('stock')); ?>" required class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 <?php echo e($errors->has('stock') ? 'border-red-500' : 'border-gray-300'); ?>" placeholder="0">
                <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.selling_price')); ?> <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" name="price" step="0.01" value="<?php echo e(old('price')); ?>" required class="w-full border pl-8 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 <?php echo e($errors->has('price') ? 'border-red-500' : 'border-gray-300'); ?>" placeholder="0.00">
                </div>
                <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Cost Price -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.cost_price')); ?></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" name="cost_price" step="0.01" value="<?php echo e(old('cost_price')); ?>" class="w-full border pl-8 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 <?php echo e($errors->has('cost_price') ? 'border-red-500' : 'border-gray-300'); ?>" placeholder="0.00">
                </div>
                <?php $__errorArgs = ['cost_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Low Stock Threshold -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.low_stock_threshold')); ?></label>
                <input type="number" name="low_stock_threshold" value="<?php echo e(old('low_stock_threshold', 5)); ?>" min="0" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 <?php echo e($errors->has('low_stock_threshold') ? 'border-red-500' : 'border-gray-300'); ?>" placeholder="5">
                <?php $__errorArgs = ['low_stock_threshold'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <!-- Tax Settings Section -->
        <?php
            $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') ?? '0';
            $defaultTaxRate = \App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? '10';
        ?>
        
        <?php if($taxEnabled == '1'): ?>
        <div class="mb-8 border-t border-gray-100 pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span lang="km"><?php echo e(__('app.tax')); ?></span>
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Is Taxable -->
                <div class="md:col-span-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_taxable" value="1" <?php echo e(old('is_taxable', '1') == '1' ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-sm font-medium text-gray-700" lang="km"><?php echo e(__('app.taxable')); ?> (មាន VAT)</span>
                    </label>
                </div>

                <!-- Tax Rate -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]" lang="km"><?php echo e(__('app.tax_rate')); ?> (%)</label>
                    <input type="number" name="tax_rate" step="0.01" min="0" max="100" value="<?php echo e(old('tax_rate', $defaultTaxRate)); ?>" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 border-gray-300" placeholder="10.00">
                </div>

                <!-- Tax Type -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]" lang="km"><?php echo e(__('app.tax_type')); ?></label>
                    <select name="tax_type" class="w-full border px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 border-gray-300">
                        <option value="exclusive" <?php echo e(old('tax_type', 'exclusive') === 'exclusive' ? 'selected' : ''); ?>><?php echo e(__('app.tax_exclusive')); ?></option>
                        <option value="inclusive" <?php echo e(old('tax_type') === 'inclusive' ? 'selected' : ''); ?>><?php echo e(__('app.tax_inclusive')); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Computer Specifications Section -->
        <div class="mb-8 border-t border-gray-100 pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4"><?php echo e(__('app.computer_specs')); ?></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- CPU -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.cpu')); ?></label>
                    <input type="text" name="cpu" value="<?php echo e(old('cpu')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., Intel Core i5">
                </div>

                <!-- RAM -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.ram')); ?></label>
                    <input type="text" name="ram" value="<?php echo e(old('ram')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., 16GB DDR4">
                </div>

                <!-- Storage -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.storage')); ?></label>
                    <input type="text" name="storage" value="<?php echo e(old('storage')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., 512GB NVMe SSD">
                </div>

                <!-- Graphics Card -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.graphics_card')); ?></label>
                    <input type="text" name="graphics_card" value="<?php echo e(old('graphics_card')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., Intel Iris Xe / NVIDIA RTX 3050">
                </div>

                <!-- Color -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.color')); ?></label>
                    <input type="text" name="color" value="<?php echo e(old('color')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g., Space Gray, Black, Silver">
                </div>

                <!-- Warranty -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.warranty')); ?></label>
                    <input type="text" name="warranty" value="<?php echo e(old('warranty')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="<?php echo e(__('app.warranty_placeholder')); ?>">
                </div>

                <!-- Condition -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.condition')); ?></label>
                    <select name="condition" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150">
                        <option value="new" <?php echo e(old('condition', 'new') === 'new' ? 'selected' : ''); ?>><?php echo e(__('app.condition_new')); ?></option>
                        <option value="demo" <?php echo e(old('condition') === 'demo' ? 'selected' : ''); ?>><?php echo e(__('app.condition_demo')); ?></option>
                        <option value="used" <?php echo e(old('condition') === 'used' ? 'selected' : ''); ?>><?php echo e(__('app.condition_used')); ?></option>
                        <option value="refurbished" <?php echo e(old('condition') === 'refurbished' ? 'selected' : ''); ?>><?php echo e(__('app.condition_refurbished')); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Extended Excel Information Section -->
        <div class="mb-8 border-t border-gray-100 pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>📦</span>
                <span><?php echo e(app()->getLocale() === 'km' ? 'ព័ត៌មានបន្ថែមទំនិញ (Extended Fields)' : 'Extended Excel Fields'); ?></span>
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Secondary Name (Name2) -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">Name2 (Secondary Name)</label>
                    <input type="text" name="name2" value="<?php echo e(old('name2')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="Alternative / Local Name">
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.unit')); ?></label>
                    <input type="text" name="unit" value="<?php echo e(old('unit')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g. Pcs, Set, Box">
                </div>

                <!-- Exchange Unit -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">Exchange Unit</label>
                    <input type="text" name="exchange_unit" value="<?php echo e(old('exchange_unit')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g. 1">
                </div>

                <!-- Max Stock Qty -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">Max Stock Qty</label>
                    <input type="number" name="max_stock_qty" value="<?php echo e(old('max_stock_qty')); ?>" min="0" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g. 100">
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">Stock Location</label>
                    <input type="text" name="location" value="<?php echo e(old('location')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="e.g. Shelf A-01">
                </div>

                <!-- IMEI / Serial -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">IMEI / Serial No.</label>
                    <input type="text" name="imei" value="<?php echo e(old('imei')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="Enter IMEI or Serial">
                </div>

                <!-- Last Stock In Date -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">Last Stock In Date</label>
                    <input type="datetime-local" name="last_stock_in_at" value="<?php echo e(old('last_stock_in_at')); ?>" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Summary -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">Summary</label>
                    <textarea name="summary" rows="3" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="Short summary..."><?php echo e(old('summary')); ?></textarea>
                </div>

                <!-- Stock Note -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">Stock Note</label>
                    <textarea name="stock_note" rows="3" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="Notes about stock..."><?php echo e(old('stock_note')); ?></textarea>
                </div>

                <!-- SEO -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]">SEO Info</label>
                    <textarea name="seo" rows="3" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" placeholder="SEO keywords / description..."><?php echo e(old('seo')); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Product Image -->
        <?php echo $__env->make('partials.product-image-picker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Status -->
        <div class="mb-8">
            <label class="block text-gray-700 text-sm font-medium mb-2 text-justify [text-align-last:justify]"><?php echo e(__('app.status')); ?></label>
            <label class="inline-flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', '1') ? 'checked' : ''); ?> class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700"><?php echo e(__('app.active')); ?></span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <?php if(request('from') === 'stock'): ?>
            <a href="<?php echo e(route('admin.products.stock')); ?>" class="px-6 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150 shadow-sm"><?php echo e(__('app.cancel')); ?></a>
            <?php else: ?>
            <a href="<?php echo e(route('admin.products.index')); ?>" class="px-6 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150 shadow-sm"><?php echo e(__('app.cancel')); ?></a>
            <?php endif; ?>
            <button type="submit" class="px-6 py-2.5 font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition duration-150"><?php echo e(__('app.save_product')); ?></button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/admin/products/create.blade.php ENDPATH**/ ?>