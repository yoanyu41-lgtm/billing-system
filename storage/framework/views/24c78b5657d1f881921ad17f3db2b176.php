<?php $__env->startSection('content'); ?>
<?php $isDirect = ($type ?? 'installment') === 'direct'; ?>
<div class="<?php echo e($isDirect ? 'max-w-2xl' : 'max-w-4xl'); ?> mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('customers.index', ['type' => $type ?? 'installment'])); ?>" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800"><?php echo e(__('app.add_customer')); ?></h1>
            <p class="text-sm text-gray-500 mt-0.5"><?php echo e($isDirect ? __('app.direct_customers') : __('app.customer_management')); ?></p>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('customers.store')); ?>" enctype="multipart/form-data" class="space-y-5">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="type" value="<?php echo e($type ?? 'installment'); ?>">

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">១</span>
                <?php echo e(__('app.personal_information')); ?>

            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.full_name')); ?> <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.phone')); ?></label>
                    <input type="text" name="phone" value="<?php echo e(old('phone')); ?>"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <?php if(($type ?? 'installment') !== 'direct'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.gender')); ?></label>
                    <select name="gender" class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value=""><?php echo e(__('app.select')); ?></option>
                        <option value="male"   <?php echo e(old('gender') === 'male'   ? 'selected' : ''); ?>><?php echo e(__('app.male')); ?></option>
                        <option value="female" <?php echo e(old('gender') === 'female' ? 'selected' : ''); ?>><?php echo e(__('app.female')); ?></option>
                        <option value="other"  <?php echo e(old('gender') === 'other'  ? 'selected' : ''); ?>><?php echo e(__('app.other_gender')); ?></option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.dob')); ?></label>
                    <input type="date" name="dob" value="<?php echo e(old('dob')); ?>"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.id_card')); ?></label>
                    <input type="text" name="id_card" value="<?php echo e(old('id_card')); ?>"
                           placeholder="012345678"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>
                <?php endif; ?>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.address')); ?></label>
                    <textarea name="address" rows="2"
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"><?php echo e(old('address')); ?></textarea>
                </div>
            </div>
        </div>

        <?php if(($type ?? 'installment') === 'direct'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">២</span>
                <?php echo e(__('app.product')); ?>

            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.product')); ?></label>
                    <select name="product_id" id="productSelect"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="fillProductPrice(this)">
                        <option value=""><?php echo e(__('app.select_product')); ?></option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>" data-price="<?php echo e($p->price); ?>"><?php echo e($p->name); ?> (<?php echo e(__('app.stock')); ?>: <?php echo e($p->stock); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.quantity')); ?></label>
                    <input type="number" name="quantity" value="1" min="1"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e(__('app.unit_price')); ?> ($)</label>
                    <input type="number" step="0.01" min="0" name="price" id="priceInput" placeholder="0.00"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(($type ?? 'installment') !== 'direct'): ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-5 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">២</span>
                <?php echo e(__('app.photo')); ?>

            </h2>
            <div class="flex items-center gap-6">
                <div id="photo-preview" class="w-24 h-24 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <label class="cursor-pointer inline-flex items-center gap-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <?php echo e(__('app.upload_photo')); ?>

                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewImage(this, 'photo-preview')">
                    </label>
                    <p class="text-xs text-gray-400 mt-1.5">JPG, PNG រហូតដល់ 2MB</p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-5 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">៣</span>
                <?php echo e(__('app.documents')); ?>

            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <?php
                $docs = [
                    ['name' => 'id_card_photo', 'label' => __('app.id_card_photo'), 'icon' => '🪪'],
                    ['name' => 'family_photo',  'label' => __('app.family_photo'),  'icon' => '📖'],
                    ['name' => 'income_proof',  'label' => __('app.income_proof'),  'icon' => '💰'],
                ];
                ?>

                <?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-100 rounded-lg p-3 bg-gray-50">
                    <p class="text-xs font-medium text-gray-600 mb-2"><?php echo e($doc['icon']); ?> <?php echo e($doc['label']); ?></p>

                    
                    <div id="preview-<?php echo e($doc['name']); ?>"
                         class="w-full h-32 rounded-lg border-2 border-dashed border-gray-200 bg-white flex flex-col items-center justify-center gap-1 mb-2 overflow-hidden cursor-pointer relative group"
                         onclick="document.getElementById('file-<?php echo e($doc['name']); ?>').click()">
                        <div id="preview-placeholder-<?php echo e($doc['name']); ?>" class="flex flex-col items-center gap-1">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span class="text-xs text-gray-400"><?php echo e(__('app.upload')); ?></span>
                        </div>
                        <img id="preview-img-<?php echo e($doc['name']); ?>" src="" alt=""
                             class="hidden absolute inset-0 w-full h-full object-cover rounded-lg">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors rounded-lg"></div>
                    </div>

                    <input type="file" id="file-<?php echo e($doc['name']); ?>" name="<?php echo e($doc['name']); ?>" accept="image/*,.pdf"
                           class="hidden" onchange="previewDoc(this, '<?php echo e($doc['name']); ?>')">
                    <p class="text-xs text-gray-400 truncate" id="fn-<?php echo e($doc['name']); ?>"><?php echo e(__('app.no_file_chosen')); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="flex items-center justify-end gap-3">
            <a href="<?php echo e(route('customers.index')); ?>"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <?php echo e(__('app.cancel')); ?>

            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <?php echo e(__('app.save')); ?>

            </button>
        </div>

    </form>
</div>

<script>
function fillProductPrice(select) {
    const opt = select.options[select.selectedIndex];
    const price = opt.getAttribute('data-price');
    const priceInput = document.getElementById('priceInput');
    if (price && priceInput && !priceInput.value) {
        priceInput.value = parseFloat(price).toFixed(2);
    }
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewDoc(input, name) {
    const fn   = document.getElementById('fn-' + name);
    const img  = document.getElementById('preview-img-' + name);
    const ph   = document.getElementById('preview-placeholder-' + name);

    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    if (fn) fn.textContent = file.name;

    const isImage = file.type.startsWith('image/');
    if (isImage && img) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.classList.remove('hidden');
            if (ph) ph.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        // PDF — show icon
        if (ph) {
            ph.innerHTML = `<svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-xs text-red-500 font-medium">PDF</span>`;
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/customers/create.blade.php ENDPATH**/ ?>