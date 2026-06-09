<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.installment_contract') }} - #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: #fff;
            padding: 20mm;
        }
        
        [lang="km"] {
            font-family: 'Khmer OS Battambang', 'Khmer OS', sans-serif;
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
                🖨️ បោះពុម្ព (Print)
            </button>
            <button onclick="window.close()" style="background: #64748B; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
                ✖️ បិទ (Close)
            </button>
        </div>
        
        <!-- Header -->
        <div class="header">
            <h1 lang="km">កិច្ចសន្យាបង់រំលោះផលិតផល</h1>
            <h2>PRODUCT INSTALLMENT PAYMENT AGREEMENT</h2>
            
            @php
                $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'CityTech Computer Shop';
                $companyNameKm = \App\Models\Setting::where('key', 'company_name_km')->value('value') ?? 'ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច';
                $companyAddress = \App\Models\Setting::where('key', 'company_address')->value('value') ?? 'Phnom Penh, Cambodia';
                $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?? '012-345-678';
                $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?? 'info@citytech.com';
            @endphp
            
            <div class="company-info">
                <strong>{{ $companyName }}</strong><br>
                Address: {{ $companyAddress }}<br>
                Phone: {{ $companyPhone }} | Email: {{ $companyEmail }}
            </div>
        </div>
        
        <!-- Contract Info -->
        <div class="contract-info">
            <div>
                <strong lang="km">លេខកិច្ចសន្យា (Contract No.):</strong> 
                #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
            </div>
            <div>
                <strong lang="km">កាលបរិច្ឆេទ (Date):</strong> 
                {{ \Carbon\Carbon::parse($installment->created_at)->format('d/m/Y') }}
            </div>
        </div>
        
        <!-- Parties Section -->
        <div class="section">
            <div class="section-title" lang="km">
                📋 ព័ត៌មានភាគី (PARTY INFORMATION)
            </div>
            
            <!-- Seller -->
            <div class="party-box">
                <h3 lang="km">ភាគីទី១ - អ្នកលក់ (FIRST PARTY - SELLER):</h3>
                <div class="info-row">
                    <div class="info-label" lang="km">ឈ្មោះក្រុមហ៊ុន:</div>
                    <div class="info-value">{{ $companyName }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">អាសយដ្ឋាន:</div>
                    <div class="info-value">{{ $companyAddress }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">លេខទូរស័ព្ទ:</div>
                    <div class="info-value">{{ $companyPhone }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $companyEmail }}</div>
                </div>
            </div>
            
            <!-- Buyer -->
            <div class="party-box">
                <h3 lang="km">ភាគីទី២ - អ្នកទិញ (SECOND PARTY - BUYER):</h3>
                <div class="info-row">
                    <div class="info-label" lang="km">ឈ្មោះពេញ:</div>
                    <div class="info-value" lang="km">{{ $customer->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">ភេទ:</div>
                    <div class="info-value" lang="km">
                        @if($customer->gender === 'male')
                            ប្រុស (Male)
                        @elseif($customer->gender === 'female')
                            ស្រី (Female)
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">លេខ Telegram:</div>
                    <div class="info-value">{{ $customer->id_card ?? '-' }}</div>
                </div>
                @if($customer->dob)
                <div class="info-row">
                    <div class="info-label" lang="km">ថ្ងៃខែឆ្នាំកំណើត:</div>
                    <div class="info-value">{{ $customer->dob->format('d/m/Y') }} ({{ $customer->age }} {{ __('app.years_old') }})</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label" lang="km">អាសយដ្ឋាន:</div>
                    <div class="info-value" lang="km">{{ $customer->address ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">លេខទូរស័ព្ទ:</div>
                    <div class="info-value">{{ $customer->phone ?? '-' }}</div>
                </div>
                @if($guarantor)
                <div class="info-row">
                    <div class="info-label" lang="km">អ្នកធានា:</div>
                    <div class="info-value" lang="km">{{ $guarantor->name }} ({{ $guarantor->phone }})</div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Product Information -->
        <div class="section">
            <div class="section-title" lang="km">
                💰 ព័ត៌មានផលិតផល (PRODUCT INFORMATION)
            </div>
            
            <div class="party-box">
                <div class="info-row">
                    <div class="info-label" lang="km">ឈ្មោះផលិតផល:</div>
                    <div class="info-value" lang="km">{{ $product->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">លេខកូដ:</div>
                    <div class="info-value">{{ $product->code ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">ប្រភេទ:</div>
                    <div class="info-value" lang="km">{{ $product->category->name ?? '-' }}</div>
                </div>
                @if($product->brand)
                <div class="info-row">
                    <div class="info-label" lang="km">ម៉ាក:</div>
                    <div class="info-value">{{ $product->brand }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label" lang="km">ស្ថានភាព:</div>
                    <div class="info-value" lang="km">ថ្មី 100% (Brand New)</div>
                </div>
                <div class="info-row">
                    <div class="info-label" lang="km">ការធានា:</div>
                    <div class="info-value" lang="km">1 ឆ្នាំ (1 Year Warranty)</div>
                </div>
            </div>
        </div>
        
        <!-- Financial Details -->
        <div class="section">
            <div class="section-title" lang="km">
                💵 ព័ត៌មានហិរញ្ញវត្ថុ (FINANCIAL DETAILS)
            </div>
            
            <div class="payment-details">
                <div class="payment-grid">
                    <div class="info-row">
                        <div class="info-label" lang="km">តម្លៃផលិតផល:</div>
                        <div class="info-value">${{ number_format($installment->total_price, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">ប្រាក់កក់:</div>
                        <div class="info-value">${{ number_format($installment->down_payment, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">អត្រាការប្រាក់:</div>
                        <div class="info-value">{{ number_format($installment->interest_rate, 2) }}% {{ __('app.per_year') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">រយៈពេល:</div>
                        <div class="info-value">{{ $installment->duration_months }} {{ __('app.months') }}</div>
                    </div>
                </div>
                
                <div class="total-row">
                    <div class="info-row">
                        <div class="info-label" lang="km">ប្រាក់ដើម (Principal):</div>
                        <div class="info-value">${{ number_format($installment->total_price - $installment->down_payment, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">ការបង់ប្រាក់ប្រចាំខែ:</div>
                        <div class="info-value" style="color: #2563EB;">${{ number_format($installment->monthly_payment, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">ថ្ងៃផុតកំណត់:</div>
                        <div class="info-value" lang="km">ថ្ងៃទី {{ \Carbon\Carbon::parse($installment->created_at)->format('d') }} រាល់ខែ</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Schedule -->
        <div class="section">
            <div class="section-title" lang="km">
                📅 កាលវិភាគបង់ប្រាក់លម្អិត (PAYMENT SCHEDULE)
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th lang="km">ខែ</th>
                        <th lang="km">កាលបរិច្ឆេទ</th>
                        <th lang="km">ប្រាក់ដើម</th>
                        <th lang="km">ការប្រាក់</th>
                        <th lang="km">សរុប</th>
                        <th lang="km">ហត្ថលេខា</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentSchedule as $payment)
                    <tr>
                        <td>{{ $payment['month'] }}</td>
                        <td>{{ $payment['date'] }}</td>
                        <td>${{ number_format($payment['principal'], 2) }}</td>
                        <td>${{ number_format($payment['interest'], 2) }}</td>
                        <td><strong>${{ number_format($payment['total'], 2) }}</strong></td>
                        <td>_________</td>
                    </tr>
                    @endforeach
                    <tr style="background: #f0f0f0; font-weight: bold;">
                        <td colspan="2" lang="km">សរុប (TOTAL)</td>
                        <td>${{ number_format(array_sum(array_column($paymentSchedule, 'principal')), 2) }}</td>
                        <td>${{ number_format(array_sum(array_column($paymentSchedule, 'interest')), 2) }}</td>
                        <td>${{ number_format(array_sum(array_column($paymentSchedule, 'total')), 2) }}</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Terms & Conditions -->
        <div class="section">
            <div class="section-title" lang="km">
                📜 លក្ខខណ្ឌទូទៅ (TERMS & CONDITIONS)
            </div>
            
            <div class="terms">
                @if(isset($contractTerms) && $contractTerms->count())
                    @foreach($contractTerms as $term)
                    <p><strong lang="km">{{ $term->title_km }}{{ $term->title_en ? ' ('.$term->title_en.')' : '' }}:</strong></p>
                    <ol lang="km">
                        @foreach($term->linesKm() as $line)
                        <li>{{ $line }}</li>
                        @endforeach
                    </ol>
                    @endforeach
                @else
                <p><strong lang="km">មាត្រា១ - កាតព្វកិច្ចអ្នកទិញ (BUYER'S OBLIGATIONS):</strong></p>
                <ol lang="km">
                    <li>អ្នកទិញព្រមព្រៀងបង់ប្រាក់ប្រចាំខែតាមកាលវិភាគដែលបានកំណត់ខាងលើ</li>
                    <li>រក្សាផលិតផលឲ្យបានល្អ និងប្រើប្រាស់ត្រឹមត្រូវតាមការណែនាំ</li>
                    <li>ជូនដំណឹងភ្លាមៗប្រសិនបើមានបញ្ហាជាមួយផលិតផល</li>
                </ol>
                
                <p><strong lang="km">មាត្រា២ - ការយឺតយ៉ាវ (LATE PAYMENT):</strong></p>
                <ol lang="km">
                    <li>ការបង់ប្រាក់យឺត 1-5 ថ្ងៃ: គ្មានការពិន័យ</li>
                    <li>ការបង់ប្រាក់យឺត 6-15 ថ្ងៃ: ការពិន័យ $5/ថ្ងៃ</li>
                    <li>ការបង់ប្រាក់យឺតលើស 15 ថ្ងៃ: ការពិន័យ $10/ថ្ងៃ</li>
                    <li>ការបង់ប្រាក់យឺតលើស 30 ថ្ងៃ: អាចដកផលិតផលវិញបាន</li>
                </ol>
                
                <p><strong lang="km">មាត្រា៣ - កម្មសិទ្ធិ (OWNERSHIP):</strong></p>
                <ol lang="km">
                    <li>ផលិតផលនៅជាកម្មសិទ្ធិរបស់អ្នកលក់រហូតដល់បង់ប្រាក់រួចរាល់</li>
                    <li>អ្នកទិញមិនអាចលក់ត្រង់ផលិតផលមុនពេលបង់រួច</li>
                    <li>បន្ទាប់ពីបង់រួច កម្មសិទ្ធិផ្ទេរទៅអ្នកទិញភ្លាមៗ</li>
                </ol>
                
                <p><strong lang="km">មាត្រា៤ - ការធានា (WARRANTY):</strong></p>
                <ol lang="km">
                    <li>ការធានា 1 ឆ្នាំសម្រាប់ផលិតផលថ្មីទាំងអស់</li>
                    <li>ការធានាមិនរាប់បញ្ចូលការខូចខាតដោយសារការប្រើប្រាស់មិនត្រឹមត្រូវ</li>
                    <li>សេវាថែទាំ និងជួសជុលឥតគិតថ្លៃក្នុងអំឡុងពេលធានា</li>
                </ol>
                @endif
            </div>
        </div>
        
        <!-- Signatures -->
        <div class="section">
            <div class="section-title" lang="km">
                ✍️ ហត្ថលេខា និងការអនុម័ត (SIGNATURES & APPROVAL)
            </div>
            
            <p lang="km" style="text-align: center; margin: 20px 0;">
                យើងខ្ញុំភាគីទាំងពីរ បានអាន យល់ និងព្រមព្រៀងតាមលក្ខខណ្ឌទាំងអស់ដែលមានក្នុងកិច្ចសន្យានេះ។
            </p>
            
            <div class="signatures">
                <div class="signature-box">
                    <p><strong lang="km">អ្នកលក់ (SELLER)</strong></p>
                    <div class="signature-line"></div>
                    <p lang="km">ហត្ថលេខា (Signature)</p>
                    <p lang="km">ឈ្មោះ: ________________________</p>
                    <p lang="km">តួនាទី: Manager</p>
                    <p lang="km">កាលបរិច្ឆេទ: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                </div>
                
                <div class="signature-box">
                    <p><strong lang="km">អ្នកទិញ (BUYER)</strong></p>
                    <div class="signature-line"></div>
                    <p lang="km">ហត្ថលេខា (Signature)</p>
                    <p lang="km">ឈ្មោះ: {{ $customer->name }}</p>
                    <p lang="km">លេខទូរស័ព្ទ: {{ $customer->phone }}</p>
                    <p lang="km">កាលបរិច្ឆេទ: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                </div>
            </div>
            
            @if($guarantor)
            <div class="signatures" style="margin-top: 30px;">
                <div class="signature-box">
                    <p><strong lang="km">អ្នកធានា (GUARANTOR)</strong></p>
                    <div class="signature-line"></div>
                    <p lang="km">ហត្ថលេខា (Signature)</p>
                    <p lang="km">ឈ្មោះ: {{ $guarantor->name }}</p>
                    <p lang="km">លេខទូរស័ព្ទ: {{ $guarantor->phone }}</p>
                    <p lang="km">កាលបរិច្ឆេទ: __________________</p>
                </div>
                
                <div class="signature-box">
                    <p><strong lang="km">សាក្សី (WITNESS)</strong></p>
                    <div class="signature-line"></div>
                    <p lang="km">ហត្ថលេខា (Signature)</p>
                    <p lang="km">ឈ្មោះ: ________________________</p>
                    <p lang="km">លេខទូរស័ព្ទ: __________________</p>
                    <p lang="km">កាលបរិច្ឆេទ: __________________</p>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p lang="km">កិច្ចសន្យានេះត្រូវបានបោះពុម្ពដោយប្រព័ន្ធ {{ $companyName }}</p>
            <p>Printed on: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            <p>Contract No.: #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}-{{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
