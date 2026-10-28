<!DOCTYPE html>
<?php
    $type = $type ?? 'sales';
    $startDate = $startDate ?? today()->toDateString();
    $endDate = $endDate ?? today()->toDateString();

    $companyNameKm = \App\Models\Setting::where('key', 'company_name_km')->value('value') ?: 'ស៊ីធី តិច កុំព្យូទ័រ';
    $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?: 'CITY TECH COMPUTER';
    $companyLogo = \App\Models\Setting::where('key', 'company_logo')->value('value');
    $companyAddress = \App\Models\Setting::where('key', 'company_address')->value('value') ?: 'ភូមិមណ្ឌលមួយ សង្កាត់ស្វាយដង្គំ ក្រុងសៀមរាប ខេត្តសៀមរាប';
    $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?: '069 244 286';
    $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?: 'citytech01@gmail.com';

    $reportTitleKm = match($type) {
        'customer' => 'របាយការណ៍អតិថិជន',
        'product' => 'របាយការណ៍ទំនិញ',
        'installment' => 'របាយការណ៍បង់រំលស់',
        'payment' => 'របាយការណ៍ការទូទាត់',
        'expense' => 'របាយការណ៍ចំណាយ',
        'profit', 'income' => 'របាយការណ៍ប្រាក់ចំណេញ',
        'overdue' => 'របាយការណ៍បង់យឺត / ហួសកាលកំណត់',
        'return' => 'របាយការណ៍ដកហូត / យកទំនិញវិញ',
        'settlement' => 'របាយការណ៍បង់ផ្តាច់',
        'guarantor' => 'របាយការណ៍អ្នកធានា',
        default => 'របាយការណ៍ការលក់'
    };
    $reportTitleEn = match($type) {
        'customer' => 'CUSTOMER REPORT',
        'product' => 'PRODUCT INVENTORY REPORT',
        'installment' => 'INSTALLMENT CONTRACTS REPORT',
        'payment' => 'PAYMENT TRANSACTIONS REPORT',
        'expense' => 'EXPENSE REPORT',
        'profit', 'income' => 'PROFIT & INCOME REPORT',
        'overdue' => 'OVERDUE & AGING REPORT',
        'return' => 'PRODUCT RETURN REPORT',
        'settlement' => 'SETTLEMENT / PAY-OFF REPORT',
        'guarantor' => 'GUARANTOR REPORT',
        default => 'SALES TRANSACTIONS REPORT'
    };
?>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($reportTitleKm); ?> - <?php echo e($startDate); ?> ដល់ <?php echo e($endDate); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Kantumruy+Pro:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Kantumruy Pro', 'Battambang', 'Poppins', sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 20px 10px;
            font-size: 11px;
            line-height: 1.5;
        }

        .sheet-container {
            max-width: 210mm;
            min-height: auto;
            margin: 0 auto;
            background: #ffffff;
            padding: 15mm 15mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 4px;
        }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .header-table td { vertical-align: top; }

        .shop-title-km { font-size: 18px; font-weight: 700; color: #0f172a; line-height: 1.3; }
        .shop-title-en { font-size: 10.5px; font-weight: 700; color: #2563eb; letter-spacing: 0.5px; margin-top: 1px; }
        .shop-info { font-size: 9.5px; color: #64748b; margin-top: 5px; line-height: 1.4; }

        .report-title-km { font-size: 18px; font-weight: 700; color: #1e3a8a; text-align: right; line-height: 1.3; }
        .report-title-en { font-size: 9.5px; font-weight: 700; color: #64748b; text-align: right; letter-spacing: 0.5px; margin-top: 1px; }

        .meta-box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; margin-top: 8px; display: inline-block; text-align: left; font-size: 9.5px; }
        .meta-box table { border-collapse: collapse; width: 100%; }
        .meta-box td { border: none; padding: 2px 4px; }

        .divider-bar { width: 100%; height: 2px; background: #0f172a; margin: 10px 0 16px 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10px; }
        table.data-table th { border: 1px solid #cbd5e1; background: #f1f5f9; color: #0f172a; padding: 6px 8px; font-weight: 700; text-align: center; font-size: 9.5px; }
        table.data-table td { border: 1px solid #e2e8f0; padding: 5px 8px; font-size: 9.5px; }
        table.data-table td.right { text-align: right; }
        table.data-table td.center { text-align: center; }
        .total-row td { font-weight: 700; background: #f8fafc; border-top: 2px solid #334155; }

        /* Prevent tfoot / total-row from repeating on every page in print */
        tfoot {
            display: table-row-group !important;
        }

        .signature-table { width: 100%; border-collapse: collapse; margin-top: 25px; page-break-inside: avoid; }
        .signature-table td { border: none; vertical-align: top; width: 50%; text-align: center; }
        .sig-line { border-bottom: 1px dashed #94a3b8; width: 75%; margin: 35px auto 8px auto; }

        @media print {
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .sheet-container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 10mm 12mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                border: none !important;
            }

            /* Ensure table footer and totals only display at the end of data, not on each page bottom */
            tfoot {
                display: table-row-group !important;
                page-break-inside: avoid;
            }

            tr {
                page-break-inside: avoid;
            }

            @page {
                size: A4 portrait;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

    <div class="sheet-container">
        
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <?php if($companyLogo): ?>
                            <td style="width: 48px; vertical-align: middle; padding-right: 10px;">
                                <img src="<?php echo e(asset('storage/' . $companyLogo)); ?>" style="width: 44px; height: 44px; object-fit: contain;">
                            </td>
                            <?php endif; ?>
                            <td style="vertical-align: middle;">
                                <div class="shop-title-km"><?php echo e($companyNameKm); ?></div>
                                <div class="shop-title-en"><?php echo e($companyName); ?></div>
                            </td>
                        </tr>
                    </table>
                    <div class="shop-info">
                        📍 <?php echo e($companyAddress); ?><br>
                        📞 <?php echo e($companyPhone); ?> &nbsp;|&nbsp; ✉️ <?php echo e($companyEmail); ?>

                    </div>
                </td>
                <td style="width: 45%; text-align: right;">
                    <div class="report-title-km"><?php echo e($reportTitleKm); ?></div>
                    <div class="report-title-en"><?php echo e($reportTitleEn); ?></div>
                    <div class="meta-box">
                        <table>
                            <tr>
                                <td style="color: #64748b; text-align: right;">កាលបរិច្ឆេទ:</td>
                                <td style="font-weight: 700; text-align: left;">
                                    <?php if(($filter ?? '') === 'yearly' || (\Carbon\Carbon::parse($startDate)->format('m-d') === '01-01' && \Carbon\Carbon::parse($endDate)->format('m-d') === '12-31')): ?>
                                        ប្រចាំឆ្នាំ <?php echo e(\Carbon\Carbon::parse($startDate)->year); ?> (<?php echo e(\Carbon\Carbon::parse($startDate)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($endDate)->format('d/m/Y')); ?>)
                                    <?php elseif(($filter ?? '') === 'monthly' || (\Carbon\Carbon::parse($startDate)->format('d') === '01' && \Carbon\Carbon::parse($startDate)->month === \Carbon\Carbon::parse($endDate)->month)): ?>
                                        ប្រចាំខែ <?php echo e(\Carbon\Carbon::parse($startDate)->format('m/Y')); ?> (<?php echo e(\Carbon\Carbon::parse($startDate)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($endDate)->format('d/m/Y')); ?>)
                                    <?php elseif($startDate === $endDate): ?>
                                        ប្រចាំថ្ងៃទី <?php echo e(\Carbon\Carbon::parse($startDate)->format('d/m/Y')); ?>

                                    <?php else: ?>
                                        <?php echo e(\Carbon\Carbon::parse($startDate)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($endDate)->format('d/m/Y')); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; text-align: right;">ថ្ងៃបោះពុម្ព:</td>
                                <td style="font-weight: 700; text-align: left;"><?php echo e(date('d/m/Y H:i')); ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider-bar"></div>

        <?php if($type === 'customer'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th>ឈ្មោះអតិថិជន</th>
                        <th style="width: 90px;">លេខទូរស័ព្ទ</th>
                        <th style="width: 130px;">សេវាកម្ម / ប្រភេទ</th>
                        <th style="width: 90px;">ការទិញសរុប</th>
                        <th style="width: 85px;">បានបង់</th>
                        <th style="width: 85px;">នៅខ្វះ</th>
                        <th style="width: 70px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $customerList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $cStatus = strtolower($c->status ?? '') === 'active' ? 'សកម្ម' : 'បានបញ្ចប់';
                        $typeLabels = collect($c->service_types ?? [])->pluck('label')->implode(', ');
                    ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td style="font-weight: 700;"><?php echo e($c->name); ?></td>
                        <td class="center"><?php echo e($c->phone); ?></td>
                        <td class="center" style="font-size: 11px;"><?php echo e(!empty($typeLabels) ? $typeLabels : '—'); ?></td>
                        <td class="right" style="font-weight: 700;">$<?php echo e(number_format($c->total_purchase, 2)); ?></td>
                        <td class="right" style="color: #16a34a; font-weight: 700;">$<?php echo e(number_format($c->paid, 2)); ?></td>
                        <td class="right" style="color: #dc2626; font-weight: 700;">$<?php echo e(number_format($c->outstanding, 2)); ?></td>
                        <td class="center"><?php echo e($cStatus); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យទេ</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($customerList ?? []) > 0): ?>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបរួម:</td>
                        <td class="right">$<?php echo e(number_format(collect($customerList)->sum('total_purchase'), 2)); ?></td>
                        <td class="right" style="color: #16a34a;">$<?php echo e(number_format(collect($customerList)->sum('paid'), 2)); ?></td>
                        <td class="right" style="color: #dc2626;">$<?php echo e(number_format(collect($customerList)->sum('outstanding'), 2)); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif($type === 'product'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th>ឈ្មោះទំនិញ</th>
                        <th style="width: 80px;">ប្រភេទ</th>
                        <th style="width: 75px;">តម្លៃដើម</th>
                        <th style="width: 75px;">តម្លៃលក់</th>
                        <th style="width: 55px;">ស្តុក</th>
                        <th style="width: 65px;">លក់បាន</th>
                        <th style="width: 85px;">ចំណូលសរុប</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td style="font-weight: 700;"><?php echo e($p->name); ?></td>
                        <td class="center"><?php echo e($p->category ?? 'ទូទៅ'); ?></td>
                        <td class="right">$<?php echo e(number_format($p->cost_price ?? 0, 2)); ?></td>
                        <td class="right" style="font-weight: 700;">$<?php echo e(number_format($p->price, 2)); ?></td>
                        <td class="center" style="font-weight: 700; color: <?php echo e($p->stock <= 5 ? '#dc2626' : '#0f172a'); ?>;"><?php echo e($p->stock); ?></td>
                        <td class="center font-bold"><?php echo e($p->sold_qty ?? 0); ?></td>
                        <td class="right" style="color: #16a34a; font-weight: 700;">$<?php echo e(number_format($p->total_revenue ?? 0, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យទេ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif($type === 'installment'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 70px;">កិច្ចសន្យា</th>
                        <th>អតិថិជន</th>
                        <th>ទំនិញ</th>
                        <th style="width: 75px;">តម្លៃសរុប</th>
                        <th style="width: 65px;">ប្រាក់កក់</th>
                        <th style="width: 70px;">នៅសល់</th>
                        <th style="width: 60px;">បង់/ខែ</th>
                        <th style="width: 65px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $installmentList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $instStatus = strtolower($inst->status ?? '') === 'completed' ? 'បានបញ្ចប់' : (strtolower($inst->status ?? '') === 'overdue' ? 'ហួសកំណត់' : 'សកម្ម');
                    ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td class="center" style="font-weight: 700;"><?php echo e($inst->contract_no); ?></td>
                        <td style="font-weight: 700;"><?php echo e($inst->customer); ?></td>
                        <td><?php echo e($inst->product); ?></td>
                        <td class="right font-bold">$<?php echo e(number_format($inst->total_amount, 2)); ?></td>
                        <td class="right">$<?php echo e(number_format($inst->down_payment, 2)); ?></td>
                        <td class="right" style="color: #dc2626; font-weight: 700;">$<?php echo e(number_format($inst->remaining, 2)); ?></td>
                        <td class="right">$<?php echo e(number_format($inst->monthly_payment, 2)); ?></td>
                        <td class="center"><?php echo e($instStatus); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យទេ</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($installmentList ?? []) > 0): ?>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបរួម:</td>
                        <td class="right">$<?php echo e(number_format(collect($installmentList)->sum('total_amount'), 2)); ?></td>
                        <td class="right">$<?php echo e(number_format(collect($installmentList)->sum('down_payment'), 2)); ?></td>
                        <td class="right" style="color: #dc2626;">$<?php echo e(number_format(collect($installmentList)->sum('remaining'), 2)); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif($type === 'payment'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 70px;">លេខកូដទូទាត់</th>
                        <th style="width: 70px;">វិក្កយបត្រ</th>
                        <th>អតិថិជន</th>
                        <th style="width: 80px;">ចំនួនទឹកប្រាក់</th>
                        <th style="width: 90px;">វិធីសាស្ត្រ</th>
                        <th style="width: 75px;">កាលបរិច្ឆេទ</th>
                        <th style="width: 85px;">ដំណាក់កាល</th>
                        <th style="width: 65px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $paymentList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $mLower = strtolower($p->payment_method ?? '');
                        $pMethod = match(true) {
                            str_contains($mLower, 'cash') => 'សាច់ប្រាក់',
                            str_contains($mLower, 'qr') || str_contains($mLower, 'khqr') || str_contains($mLower, 'aba') => 'ស្កេន QR',
                            str_contains($mLower, 'credit') || str_contains($mLower, 'card') => 'កាតឥណទាន',
                            str_contains($mLower, 'bank') || str_contains($mLower, 'wing') || str_contains($mLower, 'acleda') || str_contains($mLower, 'transfer') => 'ផ្ទេរធនាគារ',
                            default => $p->payment_method
                        };
                        $sLower = strtolower($p->status ?? '');
                        $pStatus = match(true) {
                            str_contains($sLower, 'paid') || str_contains($sLower, 'approved') || str_contains($sLower, 'success') => 'បានបង់',
                            str_contains($sLower, 'pending') => 'រង់ចាំ',
                            str_contains($sLower, 'reject') => 'បដិសេធ',
                            default => $p->status
                        };
                    ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td class="center" style="font-weight: 700;"><?php echo e($p->payment_id); ?></td>
                        <td class="center"><?php echo e($p->invoice_no); ?></td>
                        <td style="font-weight: 700;"><?php echo e($p->customer); ?></td>
                        <td class="right font-black" style="color: #16a34a;">$<?php echo e(number_format($p->amount, 2)); ?></td>
                        <td class="center"><?php echo e($pMethod); ?></td>
                        <td class="center"><?php echo e($p->date); ?></td>
                        <td class="center font-bold"><?php echo e($p->installment_no); ?></td>
                        <td class="center"><?php echo e($pStatus); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យការទូទាត់ទេ</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($paymentList ?? []) > 0): ?>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបទឹកប្រាក់ទូទាត់:</td>
                        <td class="right" style="color: #16a34a;">$<?php echo e(number_format(collect($paymentList)->sum('amount'), 2)); ?></td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif($type === 'expense'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 85px;">លេខកូដចំណាយ</th>
                        <th style="width: 95px;">ប្រភេទ</th>
                        <th>បរិយាយ</th>
                        <th style="width: 90px;">ចំនួនទឹកប្រាក់</th>
                        <th style="width: 80px;">កាលបរិច្ឆេទ</th>
                        <th style="width: 80px;">កត់ត្រាដោយ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td class="center font-bold font-mono">EXP-<?php echo e(str_pad($exp->id, 4, '0', STR_PAD_LEFT)); ?></td>
                        <td class="center font-semibold"><?php echo e($exp->category); ?></td>
                        <td><?php echo e($exp->description ?? '-'); ?></td>
                        <td class="right font-black" style="color: #dc2626;">$<?php echo e(number_format($exp->amount, 2)); ?></td>
                        <td class="center"><?php echo e(\Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y')); ?></td>
                        <td class="center"><?php echo e($exp->user->name ?? 'Admin'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យចំណាយទេ</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($expenses ?? []) > 0): ?>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបការចំណាយ:</td>
                        <td class="right" style="color: #dc2626;">$<?php echo e(number_format(collect($expenses)->sum('amount'), 2)); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif(in_array($type, ['profit', 'income'])): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 75px;">កាលបរិច្ឆេទ</th>
                        <th style="width: 80px;">លេខយោង</th>
                        <th style="width: 75px;">ប្រភេទ</th>
                        <th>អតិថិជន</th>
                        <th style="width: 75px;">តម្លៃលក់</th>
                        <th style="width: 75px;">ថ្លៃដើម</th>
                        <th style="width: 75px;">ចំណេញដុល</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $ledger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $itemType = $item->type === 'Direct Sale' ? 'លក់ដាច់' : 'បង់រំលស់';
                    ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td class="center"><?php echo e($item->date->format('d/m/Y')); ?></td>
                        <td class="center font-bold"><?php echo e($item->ref_no); ?></td>
                        <td class="center"><?php echo e($itemType); ?></td>
                        <td style="font-weight: 700;"><?php echo e($item->customer); ?></td>
                        <td class="right font-bold">$<?php echo e(number_format($item->selling_price, 2)); ?></td>
                        <td class="right">$<?php echo e(number_format($item->cost_price, 2)); ?></td>
                        <td class="right font-black" style="color: #16a34a;">$<?php echo e(number_format($item->gross_profit, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យប្រតិបត្តិការទេ</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($ledger ?? []) > 0): ?>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" class="right">សរុបរួម:</td>
                        <td class="right font-bold">$<?php echo e(number_format($totalSelling ?? 0, 2)); ?></td>
                        <td class="right">$<?php echo e(number_format($totalCost ?? 0, 2)); ?></td>
                        <td class="right font-black" style="color: #16a34a;">$<?php echo e(number_format($grossProfit ?? 0, 2)); ?></td>
                    </tr>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="5" class="right" style="color: #dc2626;">សរុបការចំណាយ (Total Expenses):</td>
                        <td colspan="3" class="right" style="color: #dc2626;">$<?php echo e(number_format($totalExpenses ?? 0, 2)); ?></td>
                    </tr>
                    <tr style="background: #e2e8f0; font-size: 11px; font-weight: 900;">
                        <td colspan="5" class="right">ប្រាក់ចំណេញសុទ្ធ (Net Income):</td>
                        <td colspan="3" class="right" style="color: <?php echo e(($netIncome ?? 0) >= 0 ? '#16a34a' : '#dc2626'); ?>;">
                            $<?php echo e(number_format($netIncome ?? 0, 2)); ?>

                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif($type === 'overdue'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 75px;">កិច្ចសន្យា</th>
                        <th>អតិថិជន</th>
                        <th style="width: 90px;">លេខទូរស័ព្ទ</th>
                        <th>មុខទំនិញ</th>
                        <th style="width: 75px;">ថ្ងៃត្រូវបង់</th>
                        <th style="width: 65px;">ចំនួនថ្ងៃយឺត</th>
                        <th style="width: 75px;">ប្រាក់ត្រូវបង់/ខែ</th>
                        <th style="width: 65px;">ប្រាក់ពិន័យ</th>
                        <th style="width: 80px;">នៅសល់សរុប</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $overdueList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td class="center font-bold">#INS-<?php echo e(str_pad($item->installment->id, 3, '0', STR_PAD_LEFT)); ?></td>
                        <td style="font-weight: 700;"><?php echo e($item->customer?->name ?? 'N/A'); ?></td>
                        <td class="center"><?php echo e($item->customer?->phone ?? '-'); ?></td>
                        <td><?php echo e($item->product?->name ?? 'N/A'); ?></td>
                        <td class="center font-semibold" style="color: #dc2626;"><?php echo e($item->due_date->format('d/m/Y')); ?></td>
                        <td class="center font-bold" style="color: #ea580c;"><?php echo e($item->days_overdue); ?> ថ្ងៃ</td>
                        <td class="right font-semibold">$<?php echo e(number_format($item->monthly_amount, 2)); ?></td>
                        <td class="right font-bold" style="color: #ea580c;">+$<?php echo e(number_format($item->penalty, 2)); ?></td>
                        <td class="right font-black" style="color: #dc2626;">$<?php echo e(number_format($item->remaining, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="10" class="center" style="padding: 18px; color: #94a3b8;">មិនមានអតិថិជនយឺតការបង់ប្រាក់ទេ</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($overdueList ?? []) > 0): ?>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="7" class="right">សរុបរួម:</td>
                        <td class="right font-bold"></td>
                        <td class="right font-black" style="color: #ea580c;">+$<?php echo e(number_format($totalLateFee ?? 0, 2)); ?></td>
                        <td class="right font-black" style="color: #dc2626;">$<?php echo e(number_format($totalOverdueBalance ?? 0, 2)); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif($type === 'return'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 75px;">កិច្ចសន្យា</th>
                        <th>អតិថិជន</th>
                        <th style="width: 90px;">លេខទូរស័ព្ទ</th>
                        <th>ទំនិញដកហូត</th>
                        <th style="width: 80px;">ថ្ងៃដកហូត</th>
                        <th style="width: 85px;">បុគ្គលិកទទួល</th>
                        <th style="width: 80px;">ចូលស្តុកវិញ</th>
                        <th>មូលហេតុ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $returnList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $ret): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td class="center font-bold">#INS-<?php echo e(str_pad($ret->id, 3, '0', STR_PAD_LEFT)); ?></td>
                        <td style="font-weight: 700;"><?php echo e($ret->customer?->name ?? 'N/A'); ?></td>
                        <td class="center"><?php echo e($ret->customer?->phone ?? '-'); ?></td>
                        <td><?php echo e($ret->product?->name ?? 'N/A'); ?></td>
                        <td class="center"><?php echo e($ret->returned_at ? $ret->returned_at->format('d/m/Y') : '-'); ?></td>
                        <td class="center"><?php echo e($ret->user?->name ?? 'Admin'); ?></td>
                        <td class="center"><?php echo e($ret->return_restock ? 'បានចូលស្តុក' : 'មិនចូល'); ?></td>
                        <td><?php echo e($ret->return_reason ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យដកហូតទំនិញទេ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif($type === 'settlement'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 75px;">កិច្ចសន្យា</th>
                        <th>អតិថិជន</th>
                        <th style="width: 90px;">លេខទូរស័ព្ទ</th>
                        <th>មុខទំនិញ</th>
                        <th style="width: 80px;">ថ្ងៃបង់ផ្តាច់</th>
                        <th style="width: 80px;">ប្រាក់ដើមបង់ផ្តាច់</th>
                        <th style="width: 65px;">ប្រាក់ពិន័យ</th>
                        <th style="width: 85px;">សរុបបង់ផ្តាច់</th>
                        <th style="width: 70px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $settlements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td class="center font-bold">#INS-<?php echo e(str_pad($s->installment_id, 3, '0', STR_PAD_LEFT)); ?></td>
                        <td style="font-weight: 700;"><?php echo e($s->customer?->name ?? 'N/A'); ?></td>
                        <td class="center"><?php echo e($s->customer?->phone ?? '-'); ?></td>
                        <td><?php echo e($s->product?->name ?? 'N/A'); ?></td>
                        <td class="center"><?php echo e($s->payment_date); ?></td>
                        <td class="right font-bold">$<?php echo e(number_format($s->principal, 2)); ?></td>
                        <td class="right font-bold" style="color: #ea580c;">$<?php echo e(number_format($s->penalty_amount, 2)); ?></td>
                        <td class="right font-black" style="color: #16a34a;">$<?php echo e(number_format($s->total_amount, 2)); ?></td>
                        <td class="center" style="color: #16a34a; font-weight: 700;">បានបង់ផ្តាច់</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="10" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យបង់ផ្តាច់ទេ</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($settlements ?? []) > 0): ?>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="6" class="right">សរុបរួម:</td>
                        <td class="right font-bold">$<?php echo e(number_format(collect($settlements)->sum('principal'), 2)); ?></td>
                        <td class="right font-bold" style="color: #ea580c;">$<?php echo e(number_format(collect($settlements)->sum('penalty_amount'), 2)); ?></td>
                        <td class="right font-black" style="color: #16a34a;">$<?php echo e(number_format(collect($settlements)->sum('total_amount'), 2)); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif($type === 'guarantor'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th>ឈ្មោះអ្នកធានា</th>
                        <th style="width: 90px;">លេខទូរស័ព្ទ</th>
                        <th style="width: 90px;">អត្តសញ្ញាណប័ណ្ណ</th>
                        <th>ទំនាក់ទំនង</th>
                        <th>អតិថិជនធានា</th>
                        <th>អាសយដ្ឋាន</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $guarantorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td style="font-weight: 700;"><?php echo e($g->name); ?></td>
                        <td class="center font-bold"><?php echo e($g->phone ?? '-'); ?></td>
                        <td class="center"><?php echo e($g->id_card ?? '-'); ?></td>
                        <td class="center"><?php echo e($g->relationship ?: 'អ្នកធានា'); ?></td>
                        <td style="font-weight: 700;"><?php echo e($g->customer?->name ?? 'N/A'); ?></td>
                        <td><?php echo e($g->address ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យអ្នកធានាទេ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 65px;">វិក្កយបត្រ</th>
                        <th style="width: 65px;">កាលបរិច្ឆេទ</th>
                        <th>អតិថិជន</th>
                        <th>មុខទំនិញ</th>
                        <th style="width: 30px;">ចំនួន</th>
                        <th style="width: 65px;">តម្លៃឯកតា</th>
                        <th style="width: 55px;">បញ្ចុះតម្លៃ</th>
                        <th style="width: 65px;">សរុប</th>
                        <th style="width: 65px;">ប្រភេទ</th>
                        <th style="width: 55px;">អ្នកលក់</th>
                        <th style="width: 55px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $salesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $sSaleType = $s->sale_type === 'Direct' ? 'លក់ដាច់' : 'បង់រំលស់';
                        $sStatus = (strtolower($s->status ?? '') === 'completed' || strtolower($s->status ?? '') === 'paid') ? 'បានបង់' : 'សកម្ម';
                    ?>
                    <tr>
                        <td class="center" style="font-weight: 700;"><?php echo e($s->invoice_no); ?></td>
                        <td class="center"><?php echo e($s->date); ?></td>
                        <td style="font-weight: 700;"><?php echo e($s->customer); ?></td>
                        <td><?php echo e($s->product); ?></td>
                        <td class="center font-bold"><?php echo e($s->quantity); ?></td>
                        <td class="right">$<?php echo e(number_format($s->unit_price, 2)); ?></td>
                        <td class="right">$<?php echo e(number_format($s->discount, 2)); ?></td>
                        <td class="right" style="font-weight: 700;">$<?php echo e(number_format($s->total, 2)); ?></td>
                        <td class="center"><?php echo e($sSaleType); ?></td>
                        <td class="center"><?php echo e($s->cashier); ?></td>
                        <td class="center"><?php echo e($sStatus); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="11" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យការលក់ទេ</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($salesList ?? []) > 0): ?>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបរួម:</td>
                        <td class="center"><?php echo e($salesList->sum('quantity')); ?></td>
                        <td></td>
                        <td class="right">$<?php echo e(number_format($totalDiscount ?? 0, 2)); ?></td>
                        <td class="right">$<?php echo e(number_format($totalSales ?? 0, 2)); ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        <?php endif; ?>

        <table class="signature-table">
            <tr>
                <td>
                    <div style="font-weight: 700;">អ្នករៀបចំរបាយការណ៍ / Prepared By:</div>
                    <div class="sig-line"></div>
                    <div style="font-size: 9.5px; color: #64748b;">ឈ្មោះ: ___________________________</div>
                    <div style="font-size: 9.5px; color: #64748b; margin-top: 3px;">កាលបរិច្ឆេទ: _____/_____/____________</div>
                </td>
                <td>
                    <div style="font-weight: 700;">អ្នកត្រួតពិនិត្យ / Approved By:</div>
                    <div class="sig-line"></div>
                    <div style="font-size: 9.5px; color: #64748b;">ឈ្មោះ: ___________________________</div>
                    <div style="font-size: 9.5px; color: #64748b; margin-top: 3px;">កាលបរិច្ឆេទ: _____/_____/____________</div>
                </td>
            </tr>
        </table>

    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>
<?php /**PATH E:\billing-system\resources\views/admin/reports/print.blade.php ENDPATH**/ ?>