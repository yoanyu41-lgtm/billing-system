<!DOCTYPE html>
<?php
    // Show only the active language: Khmer when locale=km, English otherwise.
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
?>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(__('app.installment_contract')); ?> - #INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?></title>

    <!-- Poppins & Battambang webfonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Battambang:wght@400;700;900&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', 'Battambang', 'Khmer OS Battambang', sans-serif;
            font-size: 12pt;
            line-height: 1.7;
            color: #000;
            background: #fff;
            padding: 20mm;
        }
        
        [lang="km"] {
            font-family: 'Battambang', 'Khmer OS Battambang', 'Khmer OS', sans-serif;
            line-height: 1.8;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            margin-bottom: 15px;
        }
        
        .company-info {
            font-size: 10pt;
            margin-bottom: 10px;
        }
        
        .contract-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            background: #f1f5f9;
            padding: 8px 12px;
            border-left: 4px solid #2563eb;
            font-weight: bold;
            margin-bottom: 12px;
            color: #1e293b;
            border-radius: 0 6px 6px 0;
        }
        
        .party-box {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .parties-grid {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .parties-grid .party-box {
            flex: 1;
            margin-bottom: 0;
        }
        
        .two-col-sections {
            display: flex;
            gap: 15px;
            align-items: stretch;
        }
        .two-col-sections .col-section {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .two-col-sections .party-box,
        .two-col-sections .payment-details {
            flex: 1;
            margin-bottom: 0;
        }
        .two-col-sections .info-row {
            font-size: 12pt;
            margin-bottom: 4px;
        }
        .two-col-sections .info-label {
            min-width: 95px;
        }
        .two-col-sections .total-row {
            font-size: 12pt;
        }
        
        .party-box h3 {
            font-size: 11pt;
            margin-bottom: 10px;
            color: #1e40af;
            font-weight: bold;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            min-width: 150px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .payment-details {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .total-row {
            border-top: 2px dashed #cbd5e1;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 14pt;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table, th, td {
            border: 1px solid #e2e8f0;
        }
        
        th, td {
            padding: 8px;
            text-align: center;
        }
        
        th {
            background: #f1f5f9;
            color: #1e40af;
            font-weight: bold;
        }
        
        .terms {
            font-size: 12pt;
            line-height: 1.8;
        }
        
        .terms ol {
            margin-left: 20px;
        }
        
        .terms li {
            margin-bottom: 8px;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin: 60px 20px 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            color: #666;
        }
        
        @media print {
            body {
                padding: 15mm !important;
                font-size: 10.5pt;
                line-height: 1.5;
            }
            
            [lang="km"] {
                line-height: 1.6;
            }
            
            .no-print {
                display: none;
            }

            .header {
                margin-bottom: 12px;
                padding-bottom: 5px;
            }
            
            .header h1 {
                font-size: 15pt;
            }
            
            .header h2 {
                font-size: 11pt;
            }
            
            .company-info {
                font-size: 9pt;
            }
            
            .contract-info {
                margin-bottom: 12px;
                font-size: 9pt;
            }

            .section {
                margin-bottom: 12px;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .section-title {
                padding: 5px 8px;
                margin-bottom: 8px;
                font-size: 11pt;
            }

            .party-box {
                padding: 10px;
                margin-bottom: 10px;
            }
            
            .parties-grid {
                margin-bottom: 10px;
                gap: 10px;
            }
            
            .two-col-sections {
                gap: 10px;
            }
            
            .payment-details {
                padding: 10px;
                margin-bottom: 10px;
            }
            
            .info-row {
                margin-bottom: 3px;
                font-size: 10.5pt;
            }

            /* Start Terms & Conditions on a new page */
            .page-break-before {
                page-break-before: always;
                break-before: page;
            }
            
            /* Avoid breaking a term block across pages */
            .terms p, .terms ol {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .signatures {
                margin-top: 25px;
                gap: 20px;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .signature-line {
                margin: 40px 20px 10px;
            }
            
            .footer {
                margin-top: 25px;
                padding-top: 15px;
                font-size: 8.5pt;
            }
            
            @page {
                size: A4;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Button -->
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" style="background: #2563EB; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px;">
                🖨️ <?php echo e($L('បោះពុម្ព', 'Print')); ?>

            </button>
            <button onclick="window.close()" style="background: #64748B; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
                ✖️ <?php echo e($L('បិទ', 'Close')); ?>

            </button>
        </div>
        
        <!-- Header -->
        <div class="header">
            <?php
                $companyLogoPath = \App\Models\Setting::where('key', 'company_logo')->value('value');
                $companyLogo = !empty($companyLogoPath) ? asset('storage/' . $companyLogoPath) : null;
            ?>
            <?php if($companyLogo): ?>
                <div style="text-align: center; margin-bottom: 10px;">
                    <img src="<?php echo e($companyLogo); ?>" alt="Logo" style="max-height: 65px; object-fit: contain;">
                </div>
            <?php endif; ?>

            <?php if($isKm): ?>
                <h1 lang="km">កិច្ចសន្យាបង់រំលោះផលិតផល</h1>
            <?php else: ?>
                <h1>PRODUCT INSTALLMENT PAYMENT AGREEMENT</h1>
            <?php endif; ?>
            
            <?php
                $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'CityTech Computer Shop';
                $companyNameKm = \App\Models\Setting::where('key', 'company_name_km')->value('value') ?? 'ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច';
                $companyAddress = \App\Models\Setting::where('key', 'company_address')->value('value') ?? 'Phnom Penh, Cambodia';
                $companyAddressKm = \App\Models\Setting::where('key', 'company_address_km')->value('value') ?? $companyAddress;
                $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?? '012-345-678';
                $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?? 'info@citytech.com';

                // Pick the correct language version
                $companyNameShow = $isKm ? $companyNameKm : $companyName;
                $companyAddressShow = $isKm ? $companyAddressKm : $companyAddress;
            ?>
            
            <div class="company-info">
                <strong><?php echo e($companyNameShow); ?></strong><br>
                <?php echo e($L('អាសយដ្ឋាន', 'Address')); ?>: <?php echo e($companyAddressShow); ?><br>
                <?php echo e($L('ទូរស័ព្ទ', 'Phone')); ?>: <?php echo e($companyPhone); ?> | <?php echo e($L('អ៊ីមែល', 'Email')); ?>: <?php echo e($companyEmail); ?>

            </div>
        </div>
        
        <!-- Contract Info -->
        <div class="contract-info">
            <div>
                <strong lang="km"><?php echo e($L('លេខកិច្ចសន្យា', 'Contract No.')); ?>:</strong> 
                #INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?>-<?php echo e(date('Y')); ?>

            </div>
            <div>
                <strong lang="km"><?php echo e($L('កាលបរិច្ឆេទ', 'Date')); ?>:</strong> 
                <?php echo e(\Carbon\Carbon::parse($installment->created_at)->format('d/m/Y')); ?>

            </div>
        </div>
        
        <!-- Parties Section -->
        <div class="section">
            <div class="section-title" lang="km">
                📋 <?php echo e($L('ព័ត៌មានភាគី', 'PARTY INFORMATION')); ?>

            </div>
            
            <!-- Seller -->
            <div class="parties-grid">
            <div class="party-box">
                <h3 lang="km"><?php echo e($L('ភាគីទី១ - អ្នកលក់', 'FIRST PARTY - SELLER')); ?>:</h3>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('ឈ្មោះក្រុមហ៊ុន', 'Company Name')); ?>:</div>
                    <div class="info-value" lang="<?php echo e($isKm ? 'km' : 'en'); ?>"><?php echo e($companyNameShow); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('អាសយដ្ឋាន', 'Address')); ?>:</div>
                    <div class="info-value" lang="<?php echo e($isKm ? 'km' : 'en'); ?>"><?php echo e($companyAddressShow); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('លេខទូរស័ព្ទ', 'Phone')); ?>:</div>
                    <div class="info-value"><?php echo e($companyPhone); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('អ៊ីមែល', 'Email')); ?>:</div>
                    <div class="info-value"><?php echo e($companyEmail); ?></div>
                </div>
            </div>
            
            <!-- Buyer -->
            <div class="party-box">
                <h3 lang="km"><?php echo e($L('ភាគីទី២ - អ្នកទិញ', 'SECOND PARTY - BUYER')); ?>:</h3>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('ឈ្មោះពេញ', 'Full Name')); ?>:</div>
                    <div class="info-value" lang="km"><?php echo e($customer->name); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('ភេទ', 'Gender')); ?>:</div>
                    <div class="info-value" lang="km">
                        <?php if($customer->gender === 'male'): ?>
                            <?php echo e($L('ប្រុស', 'Male')); ?>

                        <?php elseif($customer->gender === 'female'): ?>
                            <?php echo e($L('ស្រី', 'Female')); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('លេខ Telegram', 'Telegram No.')); ?>:</div>
                    <div class="info-value"><?php echo e($customer->id_card ?? '-'); ?></div>
                </div>
                <?php if($customer->dob): ?>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('ថ្ងៃខែឆ្នាំកំណើត', 'Date of Birth')); ?>:</div>
                    <div class="info-value"><?php echo e($customer->dob->format('d/m/Y')); ?> (<?php echo e($customer->age); ?> <?php echo e(__('app.years_old')); ?>)</div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('អាសយដ្ឋាន', 'Address')); ?>:</div>
                    <div class="info-value" lang="km"><?php echo e($customer->address ?? '-'); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('លេខទូរស័ព្ទ', 'Phone')); ?>:</div>
                    <div class="info-value"><?php echo e($customer->phone ?? '-'); ?></div>
                </div>
                <?php if($guarantor): ?>
                <div class="info-row">
                    <div class="info-label" lang="km"><?php echo e($L('អ្នកធានា', 'Guarantor')); ?>:</div>
                    <div class="info-value" lang="km"><?php echo e($guarantor->name); ?> (<?php echo e($guarantor->phone); ?>)</div>
                </div>
                <?php endif; ?>
            </div>
            </div>
        </div>
        
        <!-- Product & Financial (side by side) -->
        <div class="section">
            <div class="two-col-sections">
                <!-- Product Information -->
                <div class="col-section">
                    <div class="section-title" lang="km">
                        💰 <?php echo e($L('ព័ត៌មានផលិតផល', 'PRODUCT INFORMATION')); ?>

                    </div>
                    <div class="party-box">
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('ឈ្មោះផលិតផល', 'Product Name')); ?>:</div>
                            <div class="info-value" lang="km"><?php echo e($product->name); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('លេខកូដ', 'Code')); ?>:</div>
                            <div class="info-value"><?php echo e($product->code ?? '-'); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('ប្រភេទ', 'Category')); ?>:</div>
                            <div class="info-value" lang="km"><?php echo e($product->category->name ?? '-'); ?></div>
                        </div>
                        <?php if($product->brand): ?>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('ម៉ាក', 'Brand')); ?>:</div>
                            <div class="info-value"><?php echo e($product->brand); ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('ស្ថានភាព', 'Condition')); ?>:</div>
                            <div class="info-value" lang="km">
                                <?php switch($product->condition):
                                    case ('demo'): ?> <?php echo e(__('app.condition_demo')); ?> <?php break; ?>
                                    <?php case ('used'): ?> <?php echo e(__('app.condition_used')); ?> <?php break; ?>
                                    <?php case ('refurbished'): ?> <?php echo e(__('app.condition_refurbished')); ?> <?php break; ?>
                                    <?php default: ?> <?php echo e(__('app.condition_new')); ?>

                                <?php endswitch; ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('ការធានា', 'Warranty')); ?>:</div>
                            <div class="info-value" lang="km"><?php echo e($product->warranty ?: '-'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Financial Details -->
                <div class="col-section">
                    <div class="section-title" lang="km">
                        💵 <?php echo e($L('ព័ត៌មានហិរញ្ញវត្ថុ', 'FINANCIAL DETAILS')); ?>

                    </div>
                    <div class="payment-details">
                        <?php if($installment->tax_amount > 0): ?>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('តម្លៃមុនពន្ធ', 'Subtotal Before Tax')); ?>:</div>
                            <div class="info-value"><?php echo e(format_currency($installment->subtotal_before_tax ?? $installment->total_price)); ?></div>
                        </div>
                        <?php
                            $taxLabel = \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT';
                        ?>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L("ពន្ធ {$taxLabel}", "{$taxLabel} Tax")); ?> (<?php echo e($installment->tax_rate); ?>%):</div>
                            <div class="info-value"><?php echo e(format_currency($installment->tax_amount)); ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('តម្លៃផលិតផល', 'Product Price')); ?>:</div>
                            <div class="info-value" style="font-weight: bold;"><?php echo e(format_currency($installment->total_price)); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('ប្រាក់កក់', 'Down Payment')); ?>:</div>
                            <div class="info-value"><?php echo e(format_currency($installment->down_payment)); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('អត្រាការប្រាក់', 'Interest Rate')); ?>:</div>
                            <div class="info-value"><?php echo e(number_format($installment->interest_rate, 2)); ?>% <?php echo e(__('app.per_year')); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km"><?php echo e($L('រយៈពេល', 'Duration')); ?>:</div>
                            <div class="info-value"><?php echo e($installment->duration_months); ?> <?php echo e(__('app.months')); ?></div>
                        </div>
                        <div class="total-row">
                            <div class="info-row">
                                <div class="info-label" lang="km"><?php echo e($L('ប្រាក់ដើម', 'Principal')); ?>:</div>
                                <div class="info-value"><?php echo e(format_currency($installment->total_price - $installment->down_payment)); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label" lang="km"><?php echo e($L('ការបង់ប្រាក់ប្រចាំខែ', 'Monthly Payment')); ?>:</div>
                                <div class="info-value" style="color: #2563EB;"><?php echo e(format_currency($installment->monthly_payment)); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label" lang="km"><?php echo e($L('ថ្ងៃផុតកំណត់', 'Due Date')); ?>:</div>
                                <div class="info-value" lang="km"><?php echo e($L('ថ្ងៃទី '.\Carbon\Carbon::parse($installment->created_at)->format('d').' រាល់ខែ', 'Day '.\Carbon\Carbon::parse($installment->created_at)->format('d').' of each month')); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Terms & Conditions -->
        <div class="section page-break-before">
            <div class="section-title" lang="km">
                📜 <?php echo e($L('លក្ខខណ្ឌទូទៅ', 'TERMS & CONDITIONS')); ?>

            </div>
            
            <div class="terms">
                <?php if(isset($contractTerms) && $contractTerms->count()): ?>
                    <?php $__currentLoopData = $contractTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><strong lang="km"><?php echo e($isKm ? $term->title_km : ($term->title_en ?: $term->title_km)); ?>:</strong></p>
                    <ol lang="km">
                        <?php $__currentLoopData = ($isKm ? $term->linesKm() : ($term->linesEn() ?: $term->linesKm())); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($line); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ol>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                <p><strong lang="km"><?php echo e($L('មាត្រា១ - កាតព្វកិច្ចអ្នកទិញ', 'Article 1 - BUYER\'S OBLIGATIONS')); ?>:</strong></p>
                <ol lang="km">
                    <li><?php echo e($L('អ្នកទិញព្រមព្រៀងបង់ប្រាក់ប្រចាំខែតាមកាលវិភាគដែលបានកំណត់ខាងលើ', 'The buyer agrees to pay monthly according to the schedule above.')); ?></li>
                    <li><?php echo e($L('រក្សាផលិតផលឲ្យបានល្អ និងប្រើប្រាស់ត្រឹមត្រូវតាមការណែនាំ', 'Keep the product in good condition and use it properly.')); ?></li>
                    <li><?php echo e($L('ជូនដំណឹងភ្លាមៗប្រសិនបើមានបញ្ហាជាមួយផលិតផល', 'Notify immediately if there is any problem with the product.')); ?></li>
                </ol>
                
                <p><strong lang="km"><?php echo e($L('មាត្រា២ - ការយឺតយ៉ាវ', 'Article 2 - LATE PAYMENT')); ?>:</strong></p>
                <ol lang="km">
                    <li><?php echo e($L('ការបង់ប្រាក់យឺត 1-5 ថ្ងៃ: គ្មានការពិន័យ', 'Late payment 1-5 days: no penalty.')); ?></li>
                    <li><?php echo e($L('ការបង់ប្រាក់យឺត 6-15 ថ្ងៃ: ការពិន័យ $5/ថ្ងៃ', 'Late payment 6-15 days: penalty $5/day.')); ?></li>
                    <li><?php echo e($L('ការបង់ប្រាក់យឺតលើស 15 ថ្ងៃ: ការពិន័យ $10/ថ្ងៃ', 'Late payment over 15 days: penalty $10/day.')); ?></li>
                    <li><?php echo e($L('ការបង់ប្រាក់យឺតលើស 30 ថ្ងៃ: អាចដកផលិតផលវិញបាន', 'Late payment over 30 days: the product may be repossessed.')); ?></li>
                </ol>
                
                <p><strong lang="km"><?php echo e($L('មាត្រា៣ - កម្មសិទ្ធិ', 'Article 3 - OWNERSHIP')); ?>:</strong></p>
                <ol lang="km">
                    <li><?php echo e($L('ផលិតផលនៅជាកម្មសិទ្ធិរបស់អ្នកលក់រហូតដល់បង់ប្រាក់រួចរាល់', 'The product remains the seller\'s property until fully paid.')); ?></li>
                    <li><?php echo e($L('អ្នកទិញមិនអាចលក់ត្រង់ផលិតផលមុនពេលបង់រួច', 'The buyer cannot resell the product before full payment.')); ?></li>
                    <li><?php echo e($L('បន្ទាប់ពីបង់រួច កម្មសិទ្ធិផ្ទេរទៅអ្នកទិញភ្លាមៗ', 'After full payment, ownership transfers to the buyer immediately.')); ?></li>
                </ol>
                
                <p><strong lang="km"><?php echo e($L('មាត្រា៤ - ការធានា', 'Article 4 - WARRANTY')); ?>:</strong></p>
                <ol lang="km">
                    <li><?php echo e($L('ការធានា 1 ឆ្នាំសម្រាប់ផលិតផលថ្មីទាំងអស់', '1-year warranty for all new products.')); ?></li>
                    <li><?php echo e($L('ការធានាមិនរាប់បញ្ចូលការខូចខាតដោយសារការប្រើប្រាស់មិនត្រឹមត្រូវ', 'Warranty excludes damage caused by improper use.')); ?></li>
                    <li><?php echo e($L('សេវាថែទាំ និងជួសជុលឥតគិតថ្លៃក្នុងអំឡុងពេលធានា', 'Free maintenance and repair during the warranty period.')); ?></li>
                </ol>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Signatures -->
        <div class="section">
            <div class="section-title" lang="km">
                ✍️ <?php echo e($L('ហត្ថលេខា និងស្នាមមេដៃ', 'SIGNATURES & THUMBPRINTS')); ?>

            </div>
            
            <p lang="km" style="text-align: center; margin: 20px 0;">
                <?php echo e($L('យើងខ្ញុំភាគីទាំងអស់ បានអាន យល់ និងព្រមព្រៀងតាមលក្ខខណ្ឌទាំងអស់ដែលមានក្នុងកិច្ចសន្យានេះ។', 'All parties have read, understood, and agreed to all the terms contained in this contract.')); ?>

            </p>
            
            <div class="signatures" style="grid-template-columns: <?php echo e($guarantor ? '1fr 1fr 1fr' : '1fr 1fr'); ?>;">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p lang="km"><strong><?php echo e($L('ហត្ថលេខា និងមេដៃអ្នកលក់', 'Seller Signature & Thumbprint')); ?></strong></p>
                    <p lang="km"><?php echo e($L('ឈ្មោះ', 'Name')); ?>: ________________________</p>
                    <p lang="km"><?php echo e($L('កាលបរិច្ឆេទ', 'Date')); ?>: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y')); ?></p>
                </div>
                
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p lang="km"><strong><?php echo e($L('ហត្ថលេខា និងមេដៃអ្នកទិញ', 'Buyer Signature & Thumbprint')); ?></strong></p>
                    <p lang="km"><?php echo e($L('ឈ្មោះ', 'Name')); ?>: <?php echo e($customer->name); ?></p>
                    <p lang="km"><?php echo e($L('កាលបរិច្ឆេទ', 'Date')); ?>: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y')); ?></p>
                </div>

                <?php if($guarantor): ?>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p lang="km"><strong><?php echo e($L('ហត្ថលេខា និងមេដៃអ្នកធានា', 'Guarantor Signature & Thumbprint')); ?></strong></p>
                    <p lang="km"><?php echo e($L('ឈ្មោះ', 'Name')); ?>: <?php echo e($guarantor->name); ?></p>
                    <p lang="km"><?php echo e($L('កាលបរិច្ឆេទ', 'Date')); ?>: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y')); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p lang="km"><?php echo e($L('កិច្ចសន្យានេះត្រូវបានបោះពុម្ពដោយប្រព័ន្ធ', 'This contract was printed by the system')); ?> <?php echo e($companyNameShow); ?></p>
            <p><?php echo e($L('បោះពុម្ពនៅ', 'Printed on')); ?>: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?></p>
            <p><?php echo e($L('លេខកិច្ចសន្យា', 'Contract No.')); ?>: #INS-<?php echo e(str_pad($installment->id, 3, '0', STR_PAD_LEFT)); ?>-<?php echo e(date('Y')); ?></p>
        </div>
    </div>
</body>
</html>
<?php /**PATH E:\billing-system\resources\views/installments/contract.blade.php ENDPATH**/ ?>