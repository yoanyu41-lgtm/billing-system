<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>របាយការណ៍ប្រចាំថ្ងៃ - {{ $date }}</title>
    <style>
        * { font-family: 'Khmer OS Siemreap', 'DejaVu Sans', sans-serif; }
        body { margin: 0; padding: 20px; font-size: 11px; color: #1f2937; }
        .header { text-align: center; border-bottom: 2px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #1e40af; }
        .date { font-size: 12px; color: #6b7280; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1d4ed8; color: #fff; padding: 8px; font-size: 10px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        .right { text-align: right; }
        .total { background: #dbeafe; font-weight: bold; padding: 10px; margin-top: 15px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">របាយការណ៍ការទូទាត់ប្រចាំថ្ងៃ</div>
        <div class="date">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</div>
    </div>

    @if($payments->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">លរ</th>
                <th>អតិថិជន</th>
                <th>ទូរស័ព្ទ</th>
                <th class="right" style="width: 100px;">ចំនួនទឹកប្រាក់</th>
                <th style="width: 80px;">វិធីសាស្ត្រ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $i => $payment)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $payment->installment->customer->name ?? '-' }}</td>
                <td>{{ $payment->installment->customer->phone ?? '-' }}</td>
                <td class="right">${{ number_format($payment->amount, 2) }}</td>
                <td>{{ $payment->payment_method ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 40px; color: #9ca3af;">
        គ្មានការទូទាត់នៅថ្ងៃនេះទេ។
    </div>
    @endif

    <div class="total">
        <div style="text-align: right;">
            សរុប: <span style="font-size: 16px; color: #1e40af;">${{ number_format($total, 2) }}</span>
        </div>
    </div>
</body>
</html>
