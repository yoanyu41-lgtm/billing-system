<!DOCTYPE html>
@php
    // Show only the active language: Khmer when locale=km, English otherwise.
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.installment_contract') }} - #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</title>

    <!-- Khmer OS Siemreap webfont (same as the system) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Siemreap&family=Battambang:wght@400;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Siemreap', 'Khmer OS Siemreap', 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.7;
            color: #000;
            background: #fff;
            padding: 20mm;
        }
        
        [lang="km"] {
            font-family: 'Siemreap', 'Khmer OS Siemreap', 'Khmer OS', sans-serif;
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
            background: #f0f0f0;
            padding: 8px 10px;
            border: 1px solid #000;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .party-box {
            border: 2px solid #000;
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
            font-size: 12pt;
            margin-bottom: 10px;
            text-decoration: underline;
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
            border: 1px solid #000;
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
            border-top: 2px solid #000;
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
            border: 1px solid #000;
        }
        
        th, td {
            padding: 8px;
            text-align: center;
        }
        
        th {
            background: #e0e0e0;
            font-weight: bold;
        }
        
        .terms {
            font-size: 10pt;
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
                padding: 0;
            }
            
            .no-print {
                display: none;
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
            
            @page {
                size: A4;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Button -->
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" style="background: #2563EB; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px;">
                🖨️ {{ $L('បោះពុម្ព', 'Print') }}
            </button>
            <button onclick="window.close()" style="background: #64748B; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
                ✖️ {{ $L('បិទ', 'Close') }}
            </button>
        </div>
        
        <!-- Header -->
        <div class="header">
            @if($isKm)
                <h1 lang="km">កិច្ចសន្យាបង់រំលោះផលិតផល</h1>
            @else
                <h1>PRODUCT INSTALLMENT PAYMENT AGREEMENT</h1>
            @endif
            
            @php
                $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'CityTech Computer Shop';
                $companyNameKm = \App\Models\Setting::where('key', 'company_name_km')->value('value') ?? 'ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច';
                $companyAddress = \App\Models\Setting::where('key', 'company_address')->value('value') ?? 'Phnom Penh, Cambodia';
                $companyAddressKm = \App\Models\Setting::where('key', 'company_address_km')->value('value') ?? $companyAddress;
                $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?? '012-345-678';
                $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?? 'info@citytech.com';

                // Pick the correct language version
                $companyNameShow = $isKm ? $companyNameKm : $companyName;
                $companyAddressShow = $isKm ? $companyAddressKm : $companyAddress;
            @endphp
            
            <div class="company-info">
                <strong>{{ $companyNameShow }}</strong><br>
                {{ $L('អាសយដ្ឋាន', 'Address') }}: {{ $companyAddressShow }}<br>
                {{ $L('ទូរស័ព្ទ', 'Phone') }}: {{ $companyPhone }} | {{ $L('អ៊ីមែល', 'Email') }}: {{ $companyEmail }}
            </div>
        </div>
        
        <!-- Contract Info -->
        <div class="contract-info">
            <div>
                <strong lang="km">{{ $L('លេខកិច្ចសន្យា', 'Contract No.') }}:</strong> 
                #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
            </div>
            <div>
                <strong lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}:</strong> 
                {{ \Carbon\Carbon::parse($installment->created_at)->format('d/m/Y') }}
            </div>
        </div>
        
        <!-- Parties Section -->
        <div class="section">
            <div class="section-title" lang="km">
                📋 {{ $L('ព័ត៌មានភាគី', 'PARTY INFORMATION') }}
            </div>
            
            <!-- Seller -->
            <div class="parties-grid">
            <div class="party-box">
                <h3 lang="km">{{ $L('ភាគីទី១ - អ្នកលក់', 'FIRST PARTY - SELLER') }}:</h3>
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('ឈ្មោះក្រុមហ៊ុន', 'Company Name') }}:</div>
                    <div class="info-value" lang="{{ $isKm ? 'km' : 'en' }}">{{ $companyNameShow }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('អាសយដ្ឋាន', 'Address') }}:</div>
                    <div class="info-value" lang="{{ $isKm ? 'km' : 'en' }}">{{ $companyAddressShow }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('លេខទូរស័ព្ទ', 'Phone') }}:</div>
                    <div class="info-value">{{ $companyPhone }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('អ៊ីមែល', 'Email') }}:</div>
                    <div class="info-value">{{ $companyEmail }}</div>
                </div>
            </div>
            
            <!-- Buyer -->
            <div class="party-box">
                <h3 lang="km">{{ $L('ភាគីទី២ - អ្នកទិញ', 'SECOND PARTY - BUYER') }}:</h3>
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('ឈ្មោះពេញ', 'Full Name') }}:</div>
                    <div class="info-value" lang="km">{{ $customer->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('ភេទ', 'Gender') }}:</div>
                    <div class="info-value" lang="km">
                        @if($customer->gender === 'male')
                            {{ $L('ប្រុស', 'Male') }}
                        @elseif($customer->gender === 'female')
                            {{ $L('ស្រី', 'Female') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('លេខ Telegram', 'Telegram No.') }}:</div>
                    <div class="info-value">{{ $customer->id_card ?? '-' }}</div>
                </div>
                @if($customer->dob)
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('ថ្ងៃខែឆ្នាំកំណើត', 'Date of Birth') }}:</div>
                    <div class="info-value">{{ $customer->dob->format('d/m/Y') }} ({{ $customer->age }} {{ __('app.years_old') }})</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('អាសយដ្ឋាន', 'Address') }}:</div>
                    <div class="info-value" lang="km">{{ $customer->address ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('លេខទូរស័ព្ទ', 'Phone') }}:</div>
                    <div class="info-value">{{ $customer->phone ?? '-' }}</div>
                </div>
                @if($guarantor)
                <div class="info-row">
                    <div class="info-label" lang="km">{{ $L('អ្នកធានា', 'Guarantor') }}:</div>
                    <div class="info-value" lang="km">{{ $guarantor->name }} ({{ $guarantor->phone }})</div>
                </div>
                @endif
            </div>
            </div>
        </div>
        
        <!-- Product & Financial (side by side) -->
        <div class="section">
            <div class="two-col-sections">
                <!-- Product Information -->
                <div class="col-section">
                    <div class="section-title" lang="km">
                        💰 {{ $L('ព័ត៌មានផលិតផល', 'PRODUCT INFORMATION') }}
                    </div>
                    <div class="party-box">
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('ឈ្មោះផលិតផល', 'Product Name') }}:</div>
                            <div class="info-value" lang="km">{{ $product->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('លេខកូដ', 'Code') }}:</div>
                            <div class="info-value">{{ $product->code ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('ប្រភេទ', 'Category') }}:</div>
                            <div class="info-value" lang="km">{{ $product->category->name ?? '-' }}</div>
                        </div>
                        @if($product->brand)
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('ម៉ាក', 'Brand') }}:</div>
                            <div class="info-value">{{ $product->brand }}</div>
                        </div>
                        @endif
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('ស្ថានភាព', 'Condition') }}:</div>
                            <div class="info-value" lang="km">
                                @switch($product->condition)
                                    @case('demo') {{ __('app.condition_demo') }} @break
                                    @case('used') {{ __('app.condition_used') }} @break
                                    @case('refurbished') {{ __('app.condition_refurbished') }} @break
                                    @default {{ __('app.condition_new') }}
                                @endswitch
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('ការធានា', 'Warranty') }}:</div>
                            <div class="info-value" lang="km">{{ $product->warranty ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Financial Details -->
                <div class="col-section">
                    <div class="section-title" lang="km">
                        💵 {{ $L('ព័ត៌មានហិរញ្ញវត្ថុ', 'FINANCIAL DETAILS') }}
                    </div>
                    <div class="payment-details">
                        @if($installment->tax_amount > 0)
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('តម្លៃមុនពន្ធ', 'Subtotal Before Tax') }}:</div>
                            <div class="info-value">{{ format_currency($installment->subtotal_before_tax ?? $installment->total_price) }}</div>
                        </div>
                        @php
                            $taxLabel = \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT';
                        @endphp
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L("ពន្ធ {$taxLabel}", "{$taxLabel} Tax") }} ({{ $installment->tax_rate }}%):</div>
                            <div class="info-value">{{ format_currency($installment->tax_amount) }}</div>
                        </div>
                        @endif
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('តម្លៃផលិតផល', 'Product Price') }}:</div>
                            <div class="info-value" style="font-weight: bold;">{{ format_currency($installment->total_price) }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('ប្រាក់កក់', 'Down Payment') }}:</div>
                            <div class="info-value">{{ format_currency($installment->down_payment) }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('អត្រាការប្រាក់', 'Interest Rate') }}:</div>
                            <div class="info-value">{{ number_format($installment->interest_rate, 2) }}% {{ __('app.per_year') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label" lang="km">{{ $L('រយៈពេល', 'Duration') }}:</div>
                            <div class="info-value">{{ $installment->duration_months }} {{ __('app.months') }}</div>
                        </div>
                        <div class="total-row">
                            <div class="info-row">
                                <div class="info-label" lang="km">{{ $L('ប្រាក់ដើម', 'Principal') }}:</div>
                                <div class="info-value">{{ format_currency($installment->total_price - $installment->down_payment) }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label" lang="km">{{ $L('ការបង់ប្រាក់ប្រចាំខែ', 'Monthly Payment') }}:</div>
                                <div class="info-value" style="color: #2563EB;">{{ format_currency($installment->monthly_payment) }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label" lang="km">{{ $L('ថ្ងៃផុតកំណត់', 'Due Date') }}:</div>
                                <div class="info-value" lang="km">{{ $L('ថ្ងៃទី '.\Carbon\Carbon::parse($installment->created_at)->format('d').' រាល់ខែ', 'Day '.\Carbon\Carbon::parse($installment->created_at)->format('d').' of each month') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Terms & Conditions -->
        <div class="section page-break-before">
            <div class="section-title" lang="km">
                📜 {{ $L('លក្ខខណ្ឌទូទៅ', 'TERMS & CONDITIONS') }}
            </div>
            
            <div class="terms">
                @if(isset($contractTerms) && $contractTerms->count())
                    @foreach($contractTerms as $term)
                    <p><strong lang="km">{{ $isKm ? $term->title_km : ($term->title_en ?: $term->title_km) }}:</strong></p>
                    <ol lang="km">
                        @foreach(($isKm ? $term->linesKm() : ($term->linesEn() ?: $term->linesKm())) as $line)
                        <li>{{ $line }}</li>
                        @endforeach
                    </ol>
                    @endforeach
                @else
                <p><strong lang="km">{{ $L('មាត្រា១ - កាតព្វកិច្ចអ្នកទិញ', 'Article 1 - BUYER\'S OBLIGATIONS') }}:</strong></p>
                <ol lang="km">
                    <li>{{ $L('អ្នកទិញព្រមព្រៀងបង់ប្រាក់ប្រចាំខែតាមកាលវិភាគដែលបានកំណត់ខាងលើ', 'The buyer agrees to pay monthly according to the schedule above.') }}</li>
                    <li>{{ $L('រក្សាផលិតផលឲ្យបានល្អ និងប្រើប្រាស់ត្រឹមត្រូវតាមការណែនាំ', 'Keep the product in good condition and use it properly.') }}</li>
                    <li>{{ $L('ជូនដំណឹងភ្លាមៗប្រសិនបើមានបញ្ហាជាមួយផលិតផល', 'Notify immediately if there is any problem with the product.') }}</li>
                </ol>
                
                <p><strong lang="km">{{ $L('មាត្រា២ - ការយឺតយ៉ាវ', 'Article 2 - LATE PAYMENT') }}:</strong></p>
                <ol lang="km">
                    <li>{{ $L('ការបង់ប្រាក់យឺត 1-5 ថ្ងៃ: គ្មានការពិន័យ', 'Late payment 1-5 days: no penalty.') }}</li>
                    <li>{{ $L('ការបង់ប្រាក់យឺត 6-15 ថ្ងៃ: ការពិន័យ $5/ថ្ងៃ', 'Late payment 6-15 days: penalty $5/day.') }}</li>
                    <li>{{ $L('ការបង់ប្រាក់យឺតលើស 15 ថ្ងៃ: ការពិន័យ $10/ថ្ងៃ', 'Late payment over 15 days: penalty $10/day.') }}</li>
                    <li>{{ $L('ការបង់ប្រាក់យឺតលើស 30 ថ្ងៃ: អាចដកផលិតផលវិញបាន', 'Late payment over 30 days: the product may be repossessed.') }}</li>
                </ol>
                
                <p><strong lang="km">{{ $L('មាត្រា៣ - កម្មសិទ្ធិ', 'Article 3 - OWNERSHIP') }}:</strong></p>
                <ol lang="km">
                    <li>{{ $L('ផលិតផលនៅជាកម្មសិទ្ធិរបស់អ្នកលក់រហូតដល់បង់ប្រាក់រួចរាល់', 'The product remains the seller\'s property until fully paid.') }}</li>
                    <li>{{ $L('អ្នកទិញមិនអាចលក់ត្រង់ផលិតផលមុនពេលបង់រួច', 'The buyer cannot resell the product before full payment.') }}</li>
                    <li>{{ $L('បន្ទាប់ពីបង់រួច កម្មសិទ្ធិផ្ទេរទៅអ្នកទិញភ្លាមៗ', 'After full payment, ownership transfers to the buyer immediately.') }}</li>
                </ol>
                
                <p><strong lang="km">{{ $L('មាត្រា៤ - ការធានា', 'Article 4 - WARRANTY') }}:</strong></p>
                <ol lang="km">
                    <li>{{ $L('ការធានា 1 ឆ្នាំសម្រាប់ផលិតផលថ្មីទាំងអស់', '1-year warranty for all new products.') }}</li>
                    <li>{{ $L('ការធានាមិនរាប់បញ្ចូលការខូចខាតដោយសារការប្រើប្រាស់មិនត្រឹមត្រូវ', 'Warranty excludes damage caused by improper use.') }}</li>
                    <li>{{ $L('សេវាថែទាំ និងជួសជុលឥតគិតថ្លៃក្នុងអំឡុងពេលធានា', 'Free maintenance and repair during the warranty period.') }}</li>
                </ol>
                @endif
            </div>
        </div>
        
        <!-- Signatures -->
        <div class="section">
            <div class="section-title" lang="km">
                ✍️ {{ $L('ហត្ថលេខា និងការអនុម័ត', 'SIGNATURES & APPROVAL') }}
            </div>
            
            <p lang="km" style="text-align: center; margin: 20px 0;">
                {{ $L('យើងខ្ញុំភាគីទាំងពីរ បានអាន យល់ និងព្រមព្រៀងតាមលក្ខខណ្ឌទាំងអស់ដែលមានក្នុងកិច្ចសន្យានេះ។', 'Both parties have read, understood, and agreed to all the terms contained in this contract.') }}
            </p>
            
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p lang="km"><strong>{{ $L('ហត្ថលេខាអ្នកលក់', 'Seller Signature') }}</strong></p>
                    <p lang="km">{{ $L('ឈ្មោះ', 'Name') }}: ________________________</p>
                    <p lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                </div>
                
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p lang="km"><strong>{{ $L('ហត្ថលេខាអ្នកទិញ', 'Buyer Signature') }}</strong></p>
                    <p lang="km">{{ $L('ឈ្មោះ', 'Name') }}: {{ $customer->name }}</p>
                    <p lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                </div>
            </div>
            
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p lang="km">{{ $L('កិច្ចសន្យានេះត្រូវបានបោះពុម្ពដោយប្រព័ន្ធ', 'This contract was printed by the system') }} {{ $companyNameShow }}</p>
            <p>{{ $L('បោះពុម្ពនៅ', 'Printed on') }}: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            <p>{{ $L('លេខកិច្ចសន្យា', 'Contract No.') }}: #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}-{{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
