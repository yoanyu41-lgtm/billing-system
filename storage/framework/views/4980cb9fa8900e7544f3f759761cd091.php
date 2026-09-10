<?php $__env->startSection('content'); ?>
<?php $isDirect = $customer->type === 'direct'; ?>

<?php if($isDirect): ?>

<div class="max-w-3xl mx-auto space-y-5">
    
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('customers.index', ['type' => 'direct'])); ?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800"><?php echo e($customer->name); ?></h1>
                <p class="text-sm text-gray-500 mt-0.5"><?php echo e(__('app.direct_customers')); ?> · #<?php echo e($customer->id); ?></p>
            </div>
        </div>
        <a href="<?php echo e(route('customers.edit', $customer)); ?>"
           class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <?php echo e(__('app.edit')); ?>

        </a>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center mx-auto shadow">
                <span class="text-white text-2xl font-bold"><?php echo e(strtoupper(mb_substr($customer->name, 0, 1))); ?></span>
            </div>
            <h2 class="text-base font-bold text-gray-800 mt-3"><?php echo e($customer->name); ?></h2>
            <?php if($customer->phone): ?>
                <p class="text-sm text-gray-500 mt-1">📞 <?php echo e($customer->phone); ?></p>
            <?php endif; ?>
            <?php if($customer->address): ?>
                <p class="text-xs text-gray-400 mt-1">📍 <?php echo e($customer->address); ?></p>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
            <p class="text-xs text-gray-500 uppercase tracking-wide"><?php echo e(__('app.items_count')); ?></p>
            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo e($sales->count()); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
            <p class="text-xs text-gray-500 uppercase tracking-wide"><?php echo e(__('app.total_amount')); ?></p>
            <p class="text-2xl font-bold text-emerald-600 mt-1"><?php echo e(format_currency($totalSpent)); ?></p>
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700"><?php echo e(__('app.sales_list')); ?></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.invoice_no')); ?></th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.sale_date')); ?></th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.product')); ?></th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.total')); ?></th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-5 py-3 font-semibold text-blue-600"><?php echo e($sale->invoice_no ?? ('#'.$sale->id)); ?></td>
                        <td class="px-5 py-3 text-gray-600 whitespace-nowrap"><?php echo e(optional($sale->sale_date)->format('d M Y')); ?></td>
                        <td class="px-5 py-3 text-gray-700">
                            <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between gap-3 <?php echo e(!$loop->last ? 'mb-1' : ''); ?>">
                                    <span><?php echo e($item->product->name ?? '—'); ?></span>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">x<?php echo e($item->quantity); ?> · <?php echo e(format_currency($item->price)); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-gray-800 whitespace-nowrap"><?php echo e(format_currency($sale->total)); ?></td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="<?php echo e(route('admin.sales.show', $sale)); ?>" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                <i class="fas fa-eye"></i> <?php echo e(__('app.view_receipt')); ?>

                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400"><?php echo e(__('app.no_sales_yet')); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>

<div class="space-y-6">

    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('customers.index', ['type' => $customer->type])); ?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-800"><?php echo e($customer->name); ?></h1>
                    <?php if($latestCredit): ?>
                        <?php $rc = $latestCredit->risk_color; ?>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold
                            <?php echo e($rc === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($rc === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600')); ?>">
                            <?php echo e(ucfirst($latestCredit->risk_level)); ?> Risk
                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-500 mt-0.5"><?php echo e(__('app.customer_id')); ?>: #<?php echo e($customer->id); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('customers.edit', $customer)); ?>"
               class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <?php echo e(__('app.edit')); ?>

            </a>
            <?php if (! ($isDirect)): ?>
            <a href="<?php echo e(route('installments.create', ['customer_id' => $customer->id])); ?>"
               class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <?php echo e(__('app.new_installment')); ?>

            </a>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if (! ($isDirect)): ?>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide"><?php echo e(__('app.total_installments')); ?></p>
            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo e($installments->count()); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide"><?php echo e(__('app.total_paid')); ?></p>
            <p class="text-2xl font-bold text-emerald-600 mt-1"><?php echo e(format_currency($totalPaid)); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide"><?php echo e(__('app.balance_due')); ?></p>
            <p class="text-2xl font-bold text-blue-600 mt-1"><?php echo e(format_currency($totalBalance)); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide"><?php echo e(__('app.overdue')); ?></p>
            <p class="text-2xl font-bold text-red-500 mt-1"><?php echo e($totalLate); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <?php if($customer->photo): ?>
                    <img src="<?php echo e(asset('storage/' . $customer->photo)); ?>" alt="<?php echo e($customer->name); ?>"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md mx-auto">
                <?php else: ?>
                    <div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center mx-auto shadow-md">
                        <span class="text-white text-3xl font-bold"><?php echo e(strtoupper(substr($customer->name, 0, 1))); ?></span>
                    </div>
                <?php endif; ?>
                <h2 class="text-lg font-bold text-gray-800 mt-4"><?php echo e($customer->name); ?></h2>
                <?php if($customer->gender): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                        <?php echo e($customer->gender === 'male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'); ?>">
                        <?php echo e(__('app.' . $customer->gender)); ?>

                    </span>
                <?php endif; ?>

                <div class="mt-4 space-y-2.5 text-left border-t border-gray-50 pt-4">
                    <?php if($customer->phone): ?>
                    <div class="flex items-center gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <?php echo e($customer->phone); ?>

                    </div>
                    <?php endif; ?>
                    <?php if($customer->dob): ?>
                    <div class="flex items-center gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <?php echo e($customer->dob->format('d M Y')); ?> (<?php echo e($customer->age); ?>y)
                    </div>
                    <?php endif; ?>
                    <?php if($customer->id_card): ?>
                    <div class="flex items-center gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                            </svg>
                        </div>
                        <span class="font-mono text-xs"><?php echo e($customer->id_card); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($customer->telegram_id): ?>
                    <div class="flex items-center gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-sky-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-sky-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15.82-1.05 4.79-1.5 7.15-.19 1-.56 1.34-.92 1.37-.79.07-1.39-.52-2.15-1.02-1.19-.78-1.86-1.27-3.02-2.03-1.34-.88-.47-1.37.29-2.16.2-.2.36-.62.68-1.08.31-.47.62-1 .92-1.5c.16-.27.24-.52.12-.73-.12-.2-.53-.13-.74-.08-.3.07-1.92 1.13-2.92 1.8-.73.49-1.39.73-1.98.72-.65-.01-1.9-.36-2.83-.66-1.14-.37-2.05-.57-1.97-1.21.04-.33.5-.67 1.38-1.02 5.37-2.33 8.96-3.88 10.77-4.63 5.12-2.13 6.18-2.5 6.88-2.5.15 0 .5.04.73.22.19.16.25.38.27.53-.02.15-.02.48-.04.79z"/>
                            </svg>
                        </div>
                        <span class="font-mono text-xs"><?php echo e($customer->telegram_id); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($customer->address): ?>
                    <div class="flex items-start gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <?php echo e($customer->address); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if (! ($isDirect)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4"><?php echo e(__('app.documents')); ?></h3>
                <div class="grid grid-cols-3 gap-3">
                    <?php $__currentLoopData = [
                        ['key' => 'id_card_photo', 'label' => __('app.id_card_photo'), 'emoji' => '🪪'],
                        ['key' => 'family_photo',  'label' => __('app.family_photo'),  'emoji' => '📖'],
                        ['key' => 'income_proof',  'label' => __('app.income_proof'),  'emoji' => '💰'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <div>
                        <p class="text-xs text-gray-400 mb-1.5"><?php echo e($doc['emoji']); ?> <?php echo e($doc['label']); ?></p>
                        <?php if($customer->{$doc['key']}): ?>
                            <?php $ext = pathinfo($customer->{$doc['key']}, PATHINFO_EXTENSION); ?>
                            <?php if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp'])): ?>
                                <a href="<?php echo e(asset('storage/' . $customer->{$doc['key']})); ?>" target="_blank"
                                   onclick="openLightbox(this.href); return false;">
                                    <img src="<?php echo e(asset('storage/' . $customer->{$doc['key']})); ?>"
                                         alt="<?php echo e($doc['label']); ?>"
                                         class="w-full h-20 object-cover rounded-lg border border-gray-200 hover:opacity-90 transition-opacity cursor-zoom-in">
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(asset('storage/' . $customer->{$doc['key']})); ?>" target="_blank"
                                   class="flex flex-col items-center justify-center h-20 rounded-lg border border-dashed border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors gap-1">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-xs text-blue-600 font-medium"><?php echo e(__('app.view')); ?> PDF</span>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="flex items-center justify-center h-20 rounded-lg border border-dashed border-gray-100 bg-gray-50">
                                <span class="text-xs text-gray-300"><?php echo e(__('app.not_uploaded')); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        
        <?php if (! ($isDirect)): ?>
        <div class="lg:col-span-2">

            
            <div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-5" id="tab-nav">
                <?php
                $activeTab = session('activeTab', 'installments');
                if(request()->has('tab')) $activeTab = request('tab');
                ?>
                <?php $__currentLoopData = [
                    ['id' => 'installments', 'label' => __('app.installment_plans'), 'count' => $installments->count()],
                    ['id' => 'payments',     'label' => __('app.payments'),           'count' => $payments->count()],
                    ['id' => 'guarantors',   'label' => __('app.guarantors'),         'count' => $guarantors->count()],
                    ['id' => 'credit',       'label' => __('app.credit_check'),       'count' => $creditChecks->count()],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button onclick="switchTab('<?php echo e($tab['id']); ?>')"
                        id="tab-btn-<?php echo e($tab['id']); ?>"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-sm font-medium transition-all
                            <?php echo e($activeTab === $tab['id'] ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700'); ?>">
                    <?php echo e($tab['label']); ?>

                    <?php if($tab['count'] > 0): ?>
                        <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                            <?php echo e($activeTab === $tab['id'] ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-500'); ?>">
                            <?php echo e($tab['count']); ?>

                        </span>
                    <?php endif; ?>
                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div id="tab-installments" class="<?php echo e($activeTab !== 'installments' ? 'hidden' : ''); ?> space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-gray-800"><?php echo e($inst->product->name ?? 'N/A'); ?></span>
                                <?php $sc = ['active'=>'bg-emerald-100 text-emerald-700','paid'=>'bg-blue-100 text-blue-700','overdue'=>'bg-red-100 text-red-600','cancelled'=>'bg-gray-100 text-gray-500']; ?>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($sc[$inst->status] ?? 'bg-gray-100 text-gray-500'); ?>">
                                    <?php echo e(__('app.' . $inst->status)); ?>

                                </span>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                                <span>💵 <?php echo e(format_currency($inst->monthly_payment)); ?>/<?php echo e(__('app.months')); ?></span>
                                <span>📅 <?php echo e($inst->duration_months); ?> <?php echo e(__('app.months')); ?></span>
                                <?php if($inst->next_due_date): ?>
                                    <span class="<?php echo e(\Carbon\Carbon::parse($inst->next_due_date)->isPast() ? 'text-red-500 font-semibold' : ''); ?>">
                                        🔔 <?php echo e(__('app.next_due_date')); ?>: <?php echo e(\Carbon\Carbon::parse($inst->next_due_date)->format('d M Y')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-sm font-bold text-gray-800"><?php echo e(format_currency($inst->remaining_balance)); ?></div>
                            <div class="text-xs text-gray-400"><?php echo e(__('app.remaining_balance')); ?></div>
                        </div>
                    </div>
                    <?php if($inst->total_price > 0): ?>
                    <?php $pct = round((($inst->total_price - $inst->remaining_balance) / $inst->total_price) * 100); ?>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span><?php echo e($pct); ?>% <?php echo e(__('app.paid')); ?></span>
                            <span><?php echo e(__('app.total')); ?>: <?php echo e(format_currency($inst->total_price)); ?></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full <?php echo e($pct >= 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-blue-500' : 'bg-amber-400')); ?>"
                                 style="width: <?php echo e($pct); ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400 text-sm">
                    <?php echo e(__('app.no_installments')); ?>

                </div>
                <?php endif; ?>
            </div>

            
            <div id="tab-payments" class="<?php echo e($activeTab !== 'payments' ? 'hidden' : ''); ?>">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    
                    <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
                        <div class="p-4 text-center">
                            <div class="text-xs text-gray-400 font-medium"><?php echo e(__('app.approved')); ?></div>
                            <div class="text-lg font-bold text-emerald-600 mt-0.5"><?php echo e(format_currency($payments->where('status','approved')->sum('amount'))); ?></div>
                        </div>
                        <div class="p-4 text-center">
                            <div class="text-xs text-gray-400 font-medium"><?php echo e(__('app.pending')); ?></div>
                            <div class="text-lg font-bold text-amber-500 mt-0.5"><?php echo e(format_currency($totalPending)); ?></div>
                        </div>
                        <div class="p-4 text-center">
                            <div class="text-xs text-gray-400 font-medium"><?php echo e(__('app.rejected')); ?></div>
                            <div class="text-lg font-bold text-red-500 mt-0.5"><?php echo e(format_currency($payments->where('status','rejected')->sum('amount'))); ?></div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.date')); ?></th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.installment')); ?></th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.amount')); ?></th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.payment_method')); ?></th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase"><?php echo e(__('app.status')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3.5 text-gray-600 text-xs">
                                        <?php echo e(\Carbon\Carbon::parse($pay->payment_date)->format('d M Y')); ?>

                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600 text-xs">
                                        <?php echo e($pay->installment->product->name ?? '—'); ?>

                                    </td>
                                    <td class="px-5 py-3.5 text-right font-bold text-gray-800">
                                        <?php echo e(format_currency($pay->amount)); ?>

                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <?php if($pay->paymentMethod): ?>
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded"><?php echo e($pay->paymentMethod->name); ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-300 text-xs">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <?php $sc2 = ['approved'=>'bg-emerald-100 text-emerald-700','pending'=>'bg-amber-100 text-amber-700','rejected'=>'bg-red-100 text-red-600']; ?>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($sc2[$pay->status] ?? 'bg-gray-100 text-gray-500'); ?>">
                                            <?php echo e(__('app.' . $pay->status)); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm"><?php echo e(__('app.no_payments')); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div id="tab-guarantors" class="<?php echo e($activeTab !== 'guarantors' ? 'hidden' : ''); ?> space-y-4">

                
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <button onclick="toggleSection('guarantor-form')"
                            class="w-full flex items-center justify-between px-5 py-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <?php echo e(__('app.add_guarantor')); ?>

                        </span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" id="guarantor-form-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="guarantor-form" class="hidden border-t border-gray-100">
                        <form method="POST" action="<?php echo e(route('guarantors.store', $customer)); ?>" enctype="multipart/form-data" class="p-5 space-y-4">
                            <?php echo csrf_field(); ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.full_name')); ?> <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required placeholder="Guarantor full name"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                                    <input type="text" name="phone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Relationship</label>
                                    <input type="text" name="relationship" placeholder="e.g., Brother, Sister, Friend, etc."
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Gender</label>
                                    <select name="gender" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">—</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.id_card')); ?></label>
                                    <input type="text" name="id_card" placeholder="012345678"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.occupation')); ?></label>
                                    <input type="text" name="occupation" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.monthly_income')); ?> ($)</label>
                                    <input type="number" name="monthly_income" step="0.01" min="0"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.address')); ?></label>
                                    <textarea name="address" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.photo')); ?></label>
                                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-gray-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.id_card_photo')); ?></label>
                                    <input type="file" name="id_card_photo" accept="image/*" class="w-full text-xs text-gray-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.notes')); ?></label>
                                    <textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    <?php echo e(__('app.add_guarantor')); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                
                <?php $__empty_1 = true; $__currentLoopData = $guarantors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start gap-4">
                        
                        <?php if($g->photo): ?>
                            <img src="<?php echo e(asset('storage/' . $g->photo)); ?>" class="w-12 h-12 rounded-full object-cover border border-gray-200 flex-shrink-0">
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold"><?php echo e(strtoupper(substr($g->name,0,1))); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-gray-800"><?php echo e($g->name); ?></span>
                                <?php if($g->relationship): ?>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        <?php echo e(ucfirst($g->relationship)); ?>

                                    </span>
                                <?php endif; ?>
                                <?php if($g->gender): ?>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($g->gender === 'male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'); ?>">
                                        <?php echo e(ucfirst($g->gender)); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1 text-xs text-gray-500">
                                <?php if($g->phone): ?><span>📞 <?php echo e($g->phone); ?></span><?php endif; ?>
                                <?php if($g->id_card): ?><span>🪪 <span class="font-mono"><?php echo e($g->id_card); ?></span></span><?php endif; ?>
                                <?php if($g->occupation): ?><span>💼 <?php echo e($g->occupation); ?></span><?php endif; ?>
                                <?php if($g->monthly_income): ?><span>💰 <?php echo e(format_currency($g->monthly_income)); ?>/month</span><?php endif; ?>
                            </div>
                            <?php if($g->address): ?>
                                <div class="text-xs text-gray-400 mt-0.5">📍 <?php echo e($g->address); ?></div>
                            <?php endif; ?>
                            <?php if($g->notes): ?>
                                <div class="text-xs text-gray-500 bg-gray-50 rounded p-2 mt-2"><?php echo e($g->notes); ?></div>
                            <?php endif; ?>
                            <?php if($g->id_card_photo): ?>
                                <a href="<?php echo e(asset('storage/' . $g->id_card_photo)); ?>" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-1">
                                    🪪 View ID Card Photo
                                </a>
                            <?php endif; ?>
                        </div>

                        
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <form method="POST" action="<?php echo e(route('guarantors.destroy', [$customer, $g])); ?>"
                                  style="display:inline;" onsubmit="return confirm('<?php echo e(__('app.confirm_delete')); ?>')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" title="Delete" class="text-red-400 hover:text-red-600 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400 text-sm">
                    <?php echo e(__('app.no_guarantors')); ?>

                </div>
                <?php endif; ?>
            </div>

            
            <div id="tab-credit" class="<?php echo e($activeTab !== 'credit' ? 'hidden' : ''); ?> space-y-4">

                
                <?php if($latestCredit): ?>
                <?php
                    $score = $latestCredit->credit_score;
                    $rc    = $latestCredit->risk_color;
                    $barColor = $rc === 'emerald' ? '#10b981' : ($rc === 'amber' ? '#f59e0b' : '#ef4444');
                ?>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Latest Credit Assessment</h3>
                        <form method="POST" action="<?php echo e(route('credit-checks.destroy', [$customer, $latestCredit])); ?>"
                              class="inline-block" onsubmit="return confirm('<?php echo e(__('app.confirm_delete')); ?>')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1 cursor-pointer" title="<?php echo e(__('app.delete')); ?>">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div class="flex items-center gap-6">
                        
                        <div class="w-20 h-20 rounded-2xl flex-shrink-0 flex items-center justify-center
                            <?php echo e($rc === 'emerald' ? 'bg-emerald-50 text-emerald-500 border border-emerald-200 shadow-sm' : ($rc === 'amber' ? 'bg-amber-50 text-amber-500 border border-amber-200 shadow-sm' : 'bg-red-50 text-red-500 border border-red-200 shadow-sm')); ?>">
                            <?php if($latestCredit->risk_level === 'low'): ?>
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            <?php elseif($latestCredit->risk_level === 'medium'): ?>
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            <?php else: ?>
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg font-bold text-gray-800">
                                    <?php echo e($score >= 70 ? __('app.credit_good') : ($score >= 40 ? __('app.credit_fair') : __('app.credit_poor'))); ?>

                                </span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                    <?php echo e($rc === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($rc === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600')); ?>">
                                    <?php echo e(__('app.' . $latestCredit->risk_level . '_risk')); ?>

                                </span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                    <?php echo e($latestCredit->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($latestCredit->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700')); ?>">
                                    <?php echo e(__('app.' . $latestCredit->status)); ?>

                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-500">
                                <?php if($latestCredit->employment_status): ?>
                                    <span>💼 <?php echo e(ucfirst(str_replace('-', ' ', $latestCredit->employment_status))); ?></span>
                                <?php endif; ?>
                                <?php if($latestCredit->monthly_income): ?>
                                    <span>💰 <?php echo e(format_currency($latestCredit->monthly_income)); ?>/<?php echo e(__('app.months')); ?></span>
                                <?php endif; ?>
                                <?php if($latestCredit->existing_debt): ?>
                                    <span>💳 <?php echo e(format_currency($latestCredit->existing_debt)); ?> <?php echo e(__('app.existing_debt')); ?></span>
                                <?php endif; ?>
                                <span>👤 <?php echo e(__('app.checked_by')); ?>: <?php echo e($latestCredit->checker->name ?? '—'); ?></span>
                                <span class="col-span-2">📅 <?php echo e($latestCredit->created_at->format('d M Y H:i')); ?></span>
                            </div>
                            <?php if($latestCredit->notes): ?>
                                <div class="mt-2 text-xs text-gray-500 bg-gray-50 rounded p-2"><?php echo e($latestCredit->notes); ?></div>
                            <?php endif; ?>

                            <?php if($latestCredit->status === 'pending'): ?>
                            <div class="mt-4 flex items-center gap-2 border-t border-gray-50 pt-3">
                                <span class="text-xs text-gray-400 mr-1"><?php echo e(app()->getLocale() === 'km' ? 'ប្តូរស្ថានភាព៖' : 'Change Status:'); ?></span>
                                <form method="POST" action="<?php echo e(route('credit-checks.update', [$customer, $latestCredit])); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="inline-flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1.5 rounded-lg border border-emerald-200 transition-colors cursor-pointer">
                                        ✅ <?php echo e(__('app.approve')); ?>

                                    </button>
                                </form>
                                <form method="POST" action="<?php echo e(route('credit-checks.update', [$customer, $latestCredit])); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold px-2.5 py-1.5 rounded-lg border border-red-200 transition-colors cursor-pointer">
                                        ❌ <?php echo e(__('app.reject')); ?>

                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <button onclick="toggleSection('credit-form')"
                            class="w-full flex items-center justify-between px-5 py-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <?php echo e(__('app.new_assessment')); ?>

                        </span>
                        <svg class="w-4 h-4 text-gray-400" id="credit-form-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="credit-form" class="hidden border-t border-gray-100">
                        <form method="POST" action="<?php echo e(route('credit-checks.store', $customer)); ?>" class="p-5 space-y-4">
                            <?php echo csrf_field(); ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.employment_status')); ?></label>
                                    <input type="text" name="employment_status" placeholder="e.g., Employed, Self-employed, Student, etc."
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.monthly_income')); ?> ($)</label>
                                    <input type="number" name="monthly_income" step="0.01" min="0" placeholder="0.00"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.existing_debt')); ?> ($)</label>
                                    <input type="number" name="existing_debt" step="0.01" min="0" value="0" placeholder="0.00"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.decision')); ?></label>
                                    <select name="status" required
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        <option value="pending"><?php echo e(__('app.pending')); ?></option>
                                        <option value="approved"><?php echo e(__('app.approved')); ?></option>
                                        <option value="rejected"><?php echo e(__('app.rejected')); ?></option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1"><?php echo e(__('app.notes')); ?></label>
                                    <textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    <?php echo e(__('app.save_assessment')); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                
                <?php if($creditChecks->count() > 0): ?>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-700"><?php echo e(__('app.assessment_history')); ?></h4>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <?php $__currentLoopData = $creditChecks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="px-5 py-3.5 flex items-center gap-4">
                            <?php $ccRc = $cc->risk_color; ?>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                <?php echo e($ccRc === 'emerald' ? 'bg-emerald-100 text-emerald-600' : ($ccRc === 'amber' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600')); ?>">
                                <?php if($cc->risk_level === 'low'): ?>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                <?php elseif($cc->risk_level === 'medium'): ?>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-700">
                                        <?php echo e(__('app.risk_level')); ?>: 
                                        <?php if($cc->risk_level === 'low'): ?>
                                            <span class="text-emerald-600 font-bold"><?php echo e(__('app.low_risk')); ?></span>
                                        <?php elseif($cc->risk_level === 'medium'): ?>
                                            <span class="text-amber-600 font-bold"><?php echo e(__('app.medium_risk')); ?></span>
                                        <?php else: ?>
                                            <span class="text-red-600 font-bold"><?php echo e(__('app.high_risk')); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        <?php echo e($cc->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($cc->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700')); ?>">
                                        <?php echo e(__('app.' . $cc->status)); ?>

                                    </span>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    <?php echo e($cc->created_at->format('d M Y')); ?> · <?php echo e(__('app.checked_by')); ?>: <?php echo e($cc->checker->name ?? '—'); ?>

                                    <?php if($cc->monthly_income): ?> · <?php echo e(format_currency($cc->monthly_income)); ?>/<?php echo e(__('app.months')); ?> <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <form method="POST" action="<?php echo e(route('credit-checks.destroy', [$customer, $cc])); ?>"
                                      class="inline-block" onsubmit="return confirm('<?php echo e(__('app.confirm_delete')); ?>')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1 cursor-pointer" title="<?php echo e(__('app.delete')); ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
function switchTab(id) {
    ['installments','payments','guarantors','credit'].forEach(t => {
        document.getElementById('tab-' + t).classList.add('hidden');
        document.getElementById('tab-btn-' + t).classList.remove('bg-white','text-gray-800','shadow-sm');
        document.getElementById('tab-btn-' + t).classList.add('text-gray-500');
    });
    document.getElementById('tab-' + id).classList.remove('hidden');
    document.getElementById('tab-btn-' + id).classList.add('bg-white','text-gray-800','shadow-sm');
    document.getElementById('tab-btn-' + id).classList.remove('text-gray-500');
}

function toggleSection(id) {
    const el = document.getElementById(id);
    const chevron = document.getElementById(id + '-chevron');
    el.classList.toggle('hidden');
    if (chevron) chevron.style.transform = el.classList.contains('hidden') ? '' : 'rotate(180deg)';
}

// Auto-open tab from URL fragment
document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace('#tab-', '');
    if (['installments','payments','guarantors','credit'].includes(hash)) {
        switchTab(hash);
    }
});

// ── Lightbox ──
function openLightbox(src) {
    const lb = document.getElementById('lightbox');
    document.getElementById('lightbox-img').src = src;
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    const lb = document.getElementById('lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>


<div id="lightbox" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/80 p-4"
     onclick="closeLightbox()">
    <div class="relative max-w-3xl w-full" onclick="event.stopPropagation()">
        <button onclick="closeLightbox()"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors flex items-center gap-1 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Close
        </button>
        <img id="lightbox-img" src="" alt="Document Preview"
             class="w-full max-h-[80vh] object-contain rounded-xl shadow-2xl">
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\billing-system\resources\views/customers/show.blade.php ENDPATH**/ ?>