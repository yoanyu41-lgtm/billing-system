@extends('layouts.app')

@section('content')
<style>
.schedule-table th, .schedule-table td {
    padding: 4px 6px !important;
    font-size: 11px !important;
    line-height: 1.25 !important;
}
.schedule-table th { font-size: 10px !important; }
@media print {
    /* Hide app chrome */
    #sidebar, .topbar, .no-print { display: none !important; }
    .main-wrapper { margin: 0 !important; width: 100% !important; }
    body { background: #fff !important; }
    /* Remove card styling for clean print */
    .print-area { box-shadow: none !important; border: none !important; padding: 0 !important; }
    .schedule-table th, .schedule-table td { border: 1px solid #000 !important; padding: 2px 4px !important; font-size: 10px !important; }
    @page { size: A4 portrait; margin: 12mm; }
}
</style>
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="no-print">
            <nav class="text-xs text-gray-400 mb-1 flex items-center gap-1.5 no-print">
                <a href="{{ route('installments.schedule-index') }}" class="hover:text-indigo-600 transition">{{ __('app.payment_schedule') }}</a>
                <span>/</span>
                <span class="text-gray-600">#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.payment_schedule') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.payment_schedule_sub') }}</p>
        </div>
        <div class="flex items-center gap-3 no-print">
            <a href="{{ route('installments.schedule-index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.back') }}
            </a>
            <button onclick="window.print()" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                {{ __('app.print') }}
            </button>
        </div>
    </div>

    <!-- Customer / Product summary -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6 no-print">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.customer') ?? 'Customer' }}</div>
                <div class="text-sm font-semibold text-gray-800">{{ $installment->customer?->name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.product_details') }}</div>
                <div class="text-sm font-semibold text-gray-800">{{ $installment->product?->name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.monthly_payment') }}</div>
                <div class="text-sm font-semibold text-indigo-700">${{ number_format($installment->monthly_payment, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 no-print">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('app.total_scheduled') }}</div>
            <div class="text-xl font-extrabold text-gray-900 mt-1">${{ number_format($summary['total_scheduled'], 2) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('app.total_paid') }}</div>
            <div class="text-xl font-extrabold text-emerald-600 mt-1">${{ number_format($summary['total_paid'], 2) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('app.remaining_balance') }}</div>
            <div class="text-xl font-extrabold text-amber-700 mt-1">${{ number_format($summary['remaining'], 2) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('app.overdue') }}</div>
            <div class="text-xl font-extrabold {{ $summary['overdue_count'] > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $summary['overdue_count'] }}</div>
        </div>
    </div>

    <!-- Schedule table -->
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 print-area">
        <div class="text-center mb-6">
            <h3 class="text-xl font-bold text-gray-800" lang="km">កាលវិភាគបង់ប្រាក់ / Payment Schedule</h3>
        </div>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full border-collapse text-sm schedule-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border border-gray-200 px-3 py-2 text-center font-semibold text-gray-600 text-xs">ល.រ<br><span class="font-normal">No.</span></th>
                        <th class="border border-gray-200 px-3 py-2 text-center font-semibold text-gray-600 text-xs" lang="km">កាលបរិច្ឆេទបង់ប្រាក់<br><span class="font-normal">Payment Date</span></th>
                        <th class="border border-gray-200 px-3 py-2 text-right font-semibold text-gray-600 text-xs" lang="km">ទឹកប្រាក់សរុបត្រូវបង់<br><span class="font-normal">Total Payment</span></th>
                        <th class="border border-gray-200 px-3 py-2 text-right font-semibold text-gray-600 text-xs" lang="km">ការប្រាក់<br><span class="font-normal">Interests</span></th>
                        <th class="border border-gray-200 px-3 py-2 text-right font-semibold text-gray-600 text-xs" lang="km">ប្រាក់ដើម<br><span class="font-normal">Principals</span></th>
                        <th class="border border-gray-200 px-3 py-2 text-right font-semibold text-gray-600 text-xs" lang="km">សមតុល្យប្រាក់ដើម<br><span class="font-normal">Outstanding Principals</span></th>
                        <th class="border border-gray-200 px-3 py-2 text-right font-semibold text-gray-600 text-xs" lang="km">សមតុល្យបំណុល<br><span class="font-normal">Outstanding Debts</span></th>
                        <th class="border border-gray-200 px-3 py-2 text-center font-semibold text-gray-600 text-xs">{{ __('app.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $row)
                    <tr class="hover:bg-gray-50/70">
                        <td class="border border-gray-200 px-3 py-2 text-center text-gray-700">{{ $row['month'] }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-gray-700 whitespace-nowrap">{{ $row['due_date']->format('d/m/Y') }} <span class="text-gray-400 text-xs">{{ $row['day'] }}</span></td>
                        <td class="border border-gray-200 px-3 py-2 text-right font-semibold text-gray-900">{{ number_format($row['amount'], 2) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-right text-gray-700">{{ number_format($row['interest'], 2) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-right text-gray-700">{{ number_format($row['principal'], 2) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-right text-gray-700">{{ number_format($row['outstanding_principal'], 2) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-right text-gray-700">{{ number_format($row['outstanding_debt'], 2) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-center">
                            @if($row['status'] === 'paid')
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">{{ __('app.paid') }}</span>
                            @elseif($row['status'] === 'overdue')
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">{{ __('app.overdue') }}</span>
                            @else
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-gray-100 text-gray-700 border border-gray-200">{{ __('app.pending') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold">
                    <tr>
                        <td colspan="2" class="border border-gray-200 px-3 py-2.5 text-center text-gray-700" lang="km">ទឹកប្រាក់សរុប<br><span class="font-normal text-xs">Total Amount</span></td>
                        <td class="border border-gray-200 px-3 py-2.5 text-right text-indigo-700">{{ number_format($summary['total_scheduled'], 2) }}</td>
                        <td class="border border-gray-200 px-3 py-2.5 text-right text-gray-900">{{ number_format($summary['total_interest'], 2) }}</td>
                        <td class="border border-gray-200 px-3 py-2.5 text-right text-gray-900">{{ number_format($summary['total_principal'], 2) }}</td>
                        <td class="border border-gray-200 px-3 py-2.5 text-center text-gray-400">-</td>
                        <td class="border border-gray-200 px-3 py-2.5 text-center text-gray-400">-</td>
                        <td class="border border-gray-200 px-3 py-2.5"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="text-right text-xs text-gray-500 mt-2">*{{ __('app.amounts_in_usd') }}</p>

        <!-- Customer Service note -->
        <div class="mt-6 text-sm text-gray-600 space-y-1">
            <p lang="km">ប្រសិនបើអតិថិជនមានចម្ងល់លើកាលវិភាគបង់ប្រាក់នេះ សូមធ្វើការទាក់ទងមកកាន់ផ្នែកសេវាកម្មអតិថិជន។</p>
            <p>If you have any inquiry on this Payment Schedule, please contact our Customer Service.</p>
        </div>

        <!-- Signature / Thumbprint section -->
        <div class="mt-10 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-700 mb-1" lang="km">បានឃើញនិងយល់ព្រមលើកាលវិភាគបង់ប្រាក់នេះ៖</p>
            <p class="text-sm text-gray-700 mb-8">Seen and Agreed on This Payment Schedule:</p>

            <div class="flex flex-col items-end gap-1 max-w-sm ml-auto">
                <div class="w-full border-b border-dotted border-gray-400 h-16 flex items-end justify-center">
                    <span class="text-xs text-gray-400 mb-1" lang="km">ស្នាមមេដៃ / Thumbprint</span>
                </div>
                <div class="w-full flex justify-between text-sm text-gray-700 mt-2">
                    <span lang="km">ឈ្មោះ / Name:</span>
                    <span class="font-semibold border-b border-dotted border-gray-400 flex-1 ml-2 text-center">{{ $installment->customer?->name ?? '' }}</span>
                </div>
                <div class="w-full flex justify-between text-sm text-gray-700 mt-3">
                    <span lang="km">កាលបរិច្ឆេទ / Date:</span>
                    <span class="border-b border-dotted border-gray-400 flex-1 ml-2"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
