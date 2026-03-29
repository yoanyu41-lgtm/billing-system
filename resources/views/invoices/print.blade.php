<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 20px; }
        .details p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>City Tech Computer</h1>
        <p>Address: Phnom Penh, Cambodia</p>
        <p>Phone: 012 345 678</p>
    </div>
    <div class="details">
        <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>Date:</strong> {{ $invoice->created_at->format('Y-m-d') }}</p>
        <p><strong>Staff:</strong> {{ $invoice->payment->installment->user->name }}</p>
        <p><strong>Customer:</strong> {{ $invoice->payment->installment->customer->name }}</p>
        <p><strong>Phone:</strong> {{ $invoice->payment->installment->customer->phone }}</p>
        <p><strong>Product:</strong> {{ $invoice->payment->installment->product->name }}</p>
        <p><strong>Amount Paid:</strong> ${{ $invoice->payment->amount }}</p>
        <p><strong>Remaining Balance:</strong> ${{ $invoice->payment->installment->remaining_balance }}</p>
    </div>
</body>
</html>