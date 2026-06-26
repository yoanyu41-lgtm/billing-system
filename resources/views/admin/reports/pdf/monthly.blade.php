<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>របាយការណ៍ប្រចាំខែ - {{ $month }}/{{ $year }}</title>
    <style>
        * { font-family: 'Khmer UI', 'khmeros', 'DejaVu Sans', sans-serif; }
        body { margin: 0; padding: 20px; font-size: 11px; color: #1f2937; }
        .header { text-align: center; border-bottom: 2px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #1e40af; }
        .period { font-size: 12px; color: #6b7280; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1d4ed8; color: #fff; padding: 8px; font-size: 10px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        .right { text-align: right; }
        .total { background: #dbeafe; font-weight: bold; padding: 10px; margin-top: 20px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">របាយការណ៍ហិរញ្ញវត្ថុប្រចាំខែ</div>
        <div class="period">{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</div>
    </div>

    {{-- Summary Cards --}}
    <table style="width: 100%; margin-bottom: 20px; border: none;">
        <tr style="border: none;">
            <td style="width: 33%; border: 1px solid #e5e7eb; padding: 10px; background: #f9fafb;">
                <div style="color: #6b7280; font-size: 9px; text-transform: uppercase;">បង់រំលស់ (Installment)</div>
                <div style="font-size: 16px; font-weight: bold; color: #2563eb; margin-top: 3px;">${{ number_format($total, 2) }}</div>
            </td>
            <td style="width: 33%; border: 1px solid #e5e7eb; padding: 10px; background: #f9fafb;">
                <div style="color: #6b7280; font-size: 9px; text-transform: uppercase;">លក់ផ្ទាល់ (Direct Sale)</div>
                <div style="font-size: 16px; font-weight: bold; color: #059669; margin-top: 3px;">${{ number_format($salesTotal, 2) }}</div>
            </td>
            <td style="width: 33%; border: 1px solid #bfdbfe; padding: 10px; background: #eff6ff;">
                <div style="color: #1e40af; font-size: 9px; text-transform: uppercase; font-weight: bold;">សរុបរួម (Grand Total)</div>
                <div style="font-size: 16px; font-weight: bold; color: #1d4ed8; margin-top: 3px;">${{ number_format($grandTotal, 2) }}</div>
            </td>
        </tr>
    </table>

    {{-- Installment payments --}}
    <div style="font-weight: bold; color: #1e40af; margin-top: 15px; margin-bottom: 5px; font-size: 12px;">១. របាយការណ៍បង់រំលស់ / Installment Payments</div>
    @if($payments->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">លរ</th>
                <th style="width: 80px;">កាលបរិច្ឆេទ</th>
                <th>អតិថិជន</th>
                <th>ទូរស័ព្ទ</th>
                <th class="right" style="width: 100px;">ចំនួនទឹកប្រាក់</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $i => $payment)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ optional($payment->payment_date)->format('d-m-Y') }}</td>
                <td>{{ $payment->installment->customer->name ?? '-' }}</td>
                <td>{{ $payment->installment->customer->phone ?? '-' }}</td>
                <td class="right">${{ number_format($payment->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 20px; color: #9ca3af; border: 1px solid #e5e7eb; margin-top: 5px;">
        គ្មានការទូទាត់បង់រំលស់នៅខែនេះទេ។
    </div>
    @endif

    {{-- Direct sales --}}
    <div style="font-weight: bold; color: #1e40af; margin-top: 25px; margin-bottom: 5px; font-size: 12px;">២. របាយការណ៍លក់ផ្ទាល់ / Direct Sales</div>
    @if($sales->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">លរ</th>
                <th style="width: 80px;">កាលបរិច្ឆេទ</th>
                <th>លេខវិក្កយបត្រ</th>
                <th>អតិថិជន</th>
                <th class="right">តម្លៃរង (Subtotal)</th>
                <th class="right">ពន្ធ VAT</th>
                <th class="right" style="width: 100px;">សរុប (Total)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $i => $sale)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ optional($sale->sale_date)->format('d-m-Y') }}</td>
                <td style="font-weight: bold; color: #1e40af;">{{ $sale->invoice_no ?? ('#'.$sale->id) }}</td>
                <td>{{ $sale->customer_name ?: 'អតិថិជនទូទៅ' }}</td>
                <td class="right">${{ number_format($sale->subtotal_before_tax, 2) }}</td>
                <td class="right">${{ number_format($sale->tax_amount, 2) }}</td>
                <td class="right" style="font-weight: bold;">${{ number_format($sale->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 20px; color: #9ca3af; border: 1px solid #e5e7eb; margin-top: 5px;">
        គ្មានការលក់ផ្ទាល់នៅខែនេះទេ។
    </div>
    @endif

    <div class="total">
        <table style="width: 100%; border: none; margin-top: 0;">
            <tr style="border: none;">
                <td style="border: none; padding: 0; font-weight: bold; font-size: 12px; color: #1e40af;">
                    សរុបរួមប្រចាំខែ (Grand Total):
                </td>
                <td class="right" style="border: none; padding: 0; font-size: 16px; color: #1e40af; font-weight: bold;">
                    ${{ number_format($grandTotal, 2) }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
