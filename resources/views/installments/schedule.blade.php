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
                <div class="text-sm font-semibold text-indigo-700">{{ format_currency($installment->monthly_payment) }}</div>
            </div>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 no-print">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('app.total_scheduled') }}</div>
            <div class="text-xl font-extrabold text-gray-900 mt-1">{{ format_currency($summary['total_scheduled']) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('app.total_paid') }}</div>
            <div class="text-xl font-extrabold text-emerald-600 mt-1">{{ format_currency($summary['total_paid']) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('app.remaining_balance') }}</div>
            <div class="text-xl font-extrabold text-amber-700 mt-1">{{ format_currency($summary['remaining']) }}</div>
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
                        <th class="border border-gray-200 px-3 py-2 text-center font-semibold text-gray-600 text-xs no-print">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $row)
                    <tr class="hover:bg-gray-50/70">
                        <td class="border border-gray-200 px-3 py-2 text-center text-gray-700">{{ $row['month'] }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-gray-700 whitespace-nowrap">{{ $row['due_date']->format('d/m/Y') }} <span class="text-gray-400 text-xs">{{ $row['day'] }}</span></td>
                        <td class="border border-gray-200 px-3 py-2 text-right font-semibold text-gray-900">{{ format_currency($row['amount']) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-right text-gray-700">{{ format_currency($row['interest']) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-right text-gray-700">{{ format_currency($row['principal']) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-right text-gray-700">{{ format_currency($row['outstanding_principal']) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-right text-gray-700">{{ format_currency($row['outstanding_debt']) }}</td>
                        <td class="border border-gray-200 px-3 py-2 text-center">
                            @if($row['status'] === 'paid')
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">{{ __('app.paid') }}</span>
                            @elseif($row['status'] === 'overdue')
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">{{ __('app.overdue') }}</span>
                            @else
                                <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full bg-gray-100 text-gray-700 border border-gray-200">{{ __('app.pending') }}</span>
                            @endif
                        </td>
                        <td class="border border-gray-200 px-3 py-2 text-center no-print">
                            @if($row['status'] !== 'paid')
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Send QR via Telegram --}}
                                @if(empty($installment->customer?->telegram_id))
                                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded-lg inline-flex items-center gap-1 cursor-not-allowed" title="{{ __('app.telegram_id_missing') }}">
                                        <i class="fab fa-telegram-plane"></i>
                                        <span>{{ __('app.send_qr_telegram') }}</span>
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('installments.send-telegram-qr', [$installment, $row['month']]) }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 text-xs text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border-0 cursor-pointer flex items-center gap-1 transition font-bold" title="{{ __('app.send_qr_telegram') }}">
                                            <i class="fab fa-telegram-plane"></i>
                                            <span>{{ __('app.send_qr_telegram') }}</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- Record Payment --}}
                                <button type="button" onclick="openRecordPaymentModal({{ $row['month'] }}, {{ $row['amount'] }}, '{{ $row['due_date']->toDateString() }}')" class="px-2 py-1 text-xs text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg border-0 cursor-pointer flex items-center gap-1 transition font-bold" title="{{ __('app.record_payment') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span>{{ __('app.record_payment') }}</span>
                                </button>
                            </div>
                            @else
                                <span class="text-xs text-gray-400 font-medium">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200 font-bold">
                    <tr>
                        <td colspan="2" class="border border-gray-200 px-3 py-2.5 text-center text-gray-700" lang="km">ទឹកប្រាក់សរុប<br><span class="font-normal text-xs">Total Amount</span></td>
                        <td class="border border-gray-200 px-3 py-2.5 text-right text-indigo-700">{{ format_currency($summary['total_scheduled']) }}</td>
                        <td class="border border-gray-200 px-3 py-2.5 text-right text-gray-900">{{ format_currency($summary['total_interest']) }}</td>
                        <td class="border border-gray-200 px-3 py-2.5 text-right text-gray-900">{{ format_currency($summary['total_principal']) }}</td>
                        <td class="border border-gray-200 px-3 py-2.5 text-center text-gray-400">-</td>
                        <td class="border border-gray-200 px-3 py-2.5 text-center text-gray-400">-</td>
                        <td class="border border-gray-200 px-3 py-2.5"></td>
                        <td class="border border-gray-200 px-3 py-2.5 no-print"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="text-right text-xs text-gray-500 mt-2">
            *{{ session('display_currency', 'USD') === 'KHR' ? (__('app.amounts_in_khr') ?? 'Amounts in KHR') : __('app.amounts_in_usd') }}
        </p>

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

<!-- Record Payment Modal -->
<div id="recordPaymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRecordPaymentModal()"></div>

        <!-- Modal Center spacer -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal content card -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="m-0">
                @csrf
                <input type="hidden" name="installment_id" value="{{ $installment->id }}">
                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                
                <div class="bg-white px-6 pt-6 pb-4 sm:p-6 sm:pb-4 space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                            <span>{{ __('app.record_payment') }}</span>
                        </h3>
                        <button type="button" onclick="closeRecordPaymentModal()" class="text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer text-xl">
                            &times;
                        </button>
                    </div>

                    <!-- Details Display -->
                    <div class="bg-indigo-50/50 rounded-xl p-4 text-sm text-indigo-950 border border-indigo-100 flex justify-between">
                        <div>
                            <span class="font-medium block text-xs text-indigo-500 uppercase tracking-wider">{{ __('app.customer') }}</span>
                            <span class="font-semibold">{{ $installment->customer?->name }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-medium block text-xs text-indigo-500 uppercase tracking-wider" id="modalMonthLabel">Month</span>
                            <span class="font-semibold" id="modalMonthVal">1</span>
                        </div>
                    </div>

                    <!-- Amount and Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('app.amount') }} (USD)</label>
                            <input 
                                type="number" 
                                name="amount" 
                                id="modalAmountInput" 
                                step="0.01" 
                                min="0.01" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('app.payment_date') }}</label>
                            <input 
                                type="date" 
                                name="payment_date" 
                                value="{{ now()->toDateString() }}" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                        </div>
                    </div>

                    <!-- Payment Method selector -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('app.payment_method') }}</label>
                        <select name="payment_method_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @foreach($paymentMethods as $method)
                                @php
                                    $methodKey = strtolower(str_replace(' ', '_', $method->name));
                                    $translatedName = __('app.' . $methodKey);
                                    if ($translatedName === 'app.' . $methodKey) {
                                        $translatedName = $method->name;
                                    }
                                @endphp
                                <option value="{{ $method->id }}" {{ $methodKey === 'qr_code' ? 'selected' : '' }}>
                                    {{ $translatedName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer Slip Attachment -->
                    <div class="rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/30 p-4">
                        <label class="block text-sm font-bold text-blue-900 mb-1.5">
                            <i class="fas fa-image mr-1"></i>
                            {{ __('app.upload_customer_slip') }}
                        </label>
                        <input type="file" name="qr_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="mt-1 text-[11px] text-gray-500">
                            {{ app()->getLocale() === 'km' ? 'សូមបញ្ចូលរូបភាពបង្កាន់ដៃទូទាត់ QR / Slip របស់អតិថិជន។' : 'Please upload the customer\'s QR payment receipt/slip image.' }}
                        </p>
                    </div>

                    <!-- Approve Immediately (for authorized users) -->
                    @can('approve-payment')
                    <div class="flex items-center gap-2 pt-1">
                        <input 
                            type="checkbox" 
                            name="approve_now" 
                            id="modalApproveCheckbox" 
                            value="1" 
                            checked 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="modalApproveCheckbox" class="text-sm font-semibold text-gray-700 cursor-pointer flex items-center gap-1 select-none">
                            <i class="fas fa-check-double text-emerald-600"></i>
                            <span>{{ __('app.approve_now') }}</span>
                        </label>
                    </div>
                    @endcan
                </div>

                <!-- Footer buttons -->
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition text-sm shadow-sm border-0 cursor-pointer"
                    >
                        {{ __('app.confirm_and_approve') }}
                    </button>
                    <button 
                        type="button" 
                        onclick="closeRecordPaymentModal()" 
                        class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition text-sm"
                    >
                        {{ __('app.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRecordPaymentModal(month, amount, dueDate) {
        document.getElementById('modalMonthVal').innerText = month;
        document.getElementById('modalMonthLabel').innerText = "{{ __('app.installment_month') }} " + month;
        document.getElementById('modalAmountInput').value = amount;
        
        const modal = document.getElementById('recordPaymentModal');
        modal.classList.remove('hidden');
    }

    function closeRecordPaymentModal() {
        const modal = document.getElementById('recordPaymentModal');
        modal.classList.add('hidden');
    }
</script>
@endsection
