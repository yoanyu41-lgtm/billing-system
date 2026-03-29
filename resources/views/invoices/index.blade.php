@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Invoices</h1>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Invoice Number</th>
            <th class="p-2">Customer</th>
            <th class="p-2">Amount</th>
            <th class="p-2">Date</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td class="p-2">{{ $invoice->invoice_number }}</td>
            <td class="p-2">{{ $invoice->payment->installment->customer->name }}</td>
            <td class="p-2">${{ $invoice->payment->amount }}</td>
            <td class="p-2">{{ $invoice->created_at->format('Y-m-d') }}</td>
            <td class="p-2">
                <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-500">View</a>
                <a href="{{ route('invoices.download', $invoice) }}" class="text-green-500 ml-2">Download</a>
                <a href="{{ route('invoices.print', $invoice) }}" class="text-purple-500 ml-2">Print</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $invoices->links() }}
@endsection