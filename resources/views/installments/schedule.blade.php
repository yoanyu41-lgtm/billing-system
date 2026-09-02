@extends('layouts.app')

@section('content')
@php
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
    $companyName = $settings['company_name'] ?? 'CityTech Computer';
    $companyNameKm = $settings['company_name_km'] ?? 'ស៊ីធីធិច កុំព្យូទ័រ';
    $companyPhone = $settings['company_phone'] ?? '';
    $companyAddress = $settings['company_address'] ?? '';
    $companyAddressKm = $settings['company_address_km'] ?? $companyAddress;
    $companyEmail = $settings['company_email'] ?? '';
    $companyLogo = !empty($settings['company_logo']) ? asset('storage/' . $settings['company_logo']) : null;
@endphp
<style>
@media print {
    /* Main print resets */
    .main-wrapper { margin: 0 !important; width: 100% !important; padding: 0 !important; }
    body { background: #fff !important; padding: 0 !important; margin: 0 !important; }
    @page { size: A4 portrait; margin: 6mm 8mm 6mm 8mm; }
    
    /* Remove card styling for clean print */
    .print-area { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; }
    .print-header { border-bottom: 2px solid #1e1b4b !important; padding-bottom: 6px !important; margin-bottom: 8px !important; }
    .schedule-table th, .schedule-table td { border: 1px solid #334155 !important; padding: 2px 4px !important; font-size: 8pt !important; line-height: 1.15 !important; }
    
    /* Force grid elements inside print-area to remain side-by-side in print */
    .print-area .grid { display: grid !important; }
    .print-area .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .print-area .sm\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }

    /* Hide app chrome & no-print sections */
    #sidebar, .topbar, .no-print, .no-print * { display: none !important; height: 0 !important; visibility: hidden !important; }
}

/* PDF Export mode styles */
.pdf-export-mode .schedule-table th, 
.pdf-export-mode .schedule-table td { 
    border: 1px solid #334155 !important; 
    padding: 2px 4px !important; 
    font-size: 7.8pt !important; 
    line-height: 1.15 !important;
}
.pdf-export-mode .no-print,
.pdf-export-mode .no-print * {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
    visibility: hidden !important;
}
</style>
<div class="container mx-auto px-4 py-8 max-w-7xl">
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
            <button type="button" onclick="window.print()" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150 cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                {{ __('app.print') }}
            </button>
        </div>
    </div>

    <!-- Customer / Product summary (Screen dashboard) -->
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

    <!-- Schedule table & Official Print Container -->
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 print-area">
        <!-- ══════════ OFFICIAL PRINT/PDF HEADER (Visible only in Print / PDF) ══════════ -->
        <div class="hidden print:block print-only-element border-b-2 border-indigo-900 pb-4 mb-5 print-header">
            <div class="flex flex-row items-center justify-between gap-4">
                <!-- Shop Logo & Details -->
                <div class="flex items-center gap-3.5">
                    @if($companyLogo)
                        <img src="{{ $companyLogo }}" alt="Logo" class="w-16 h-16 object-contain rounded-xl border border-gray-200 p-1 bg-white shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-tr from-indigo-700 to-blue-600 text-white flex items-center justify-center text-xl font-black shrink-0 shadow-sm">
                            {{ mb_substr($companyNameKm ?: $companyName, 0, 1, 'UTF-8') }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-extrabold text-gray-900 leading-tight" lang="km">
                            {{ $companyNameKm ?: $companyName }}
                        </h1>
                        <p class="text-xs font-bold text-indigo-700 uppercase tracking-wide">
                            {{ $companyName ?: 'CityTech Computer' }}
                        </p>
                        <div class="text-[11px] text-gray-600 mt-1 space-y-0.5">
                            @if($companyAddressKm || $companyAddress)
                                <p class="flex items-center gap-1">
                                    <i class="fas fa-location-dot text-indigo-500 text-[10px]"></i>
                                    <span>{{ app()->getLocale() === 'km' ? ($companyAddressKm ?: $companyAddress) : ($companyAddress ?: $companyAddressKm) }}</span>
                                </p>
                            @endif
                            <p class="flex items-center gap-3">
                                @if($companyPhone)
                                    <span><i class="fas fa-phone text-indigo-500 text-[10px]"></i> {{ $companyPhone }}</span>
                                @endif
                                @if($companyEmail)
                                    <span><i class="fas fa-envelope text-indigo-500 text-[10px]"></i> {{ $companyEmail }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Document Metadata -->
                <div class="text-right">
                    <h2 class="text-lg font-bold text-indigo-900 uppercase tracking-wide" lang="km">
                        កាលវិភាគបង់ប្រាក់
                    </h2>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                        PAYMENT SCHEDULE
                    </p>
                    <div class="mt-2 inline-block bg-indigo-50/70 border border-indigo-100 rounded-lg px-3 py-1.5 text-xs text-left">
                        <div class="flex justify-between gap-3">
                            <span class="text-gray-500 font-medium">លេខកិច្ចសន្យា:</span>
                            <span class="font-bold text-indigo-950">#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="text-gray-500 font-medium">កាលបរិច្ឆេទ:</span>
                            <span class="font-bold text-gray-800">{{ $installment->created_at?->format('d/m/Y') ?? date('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-xs">
            <table class="min-w-full border-collapse schedule-table">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th class="border border-gray-200 px-4 py-3.5 text-center font-bold text-slate-700 text-xs tracking-wider">ល.រ<br><span class="font-normal text-[11px] text-slate-400">No.</span></th>
                        <th class="border border-gray-200 px-4 py-3.5 text-center font-bold text-slate-700 text-xs tracking-wider" lang="km">កាលបរិច្ឆេទបង់ប្រាក់<br><span class="font-normal text-[11px] text-slate-400">Payment Date</span></th>
                        <th class="border border-gray-200 px-4 py-3.5 text-right font-bold text-slate-700 text-xs tracking-wider" lang="km">ទឹកប្រាក់សរុបត្រូវបង់<br><span class="font-normal text-[11px] text-slate-400">Total Payment</span></th>
                        <th class="border border-gray-200 px-4 py-3.5 text-right font-bold text-slate-700 text-xs tracking-wider" lang="km">ការប្រាក់<br><span class="font-normal text-[11px] text-slate-400">Interests</span></th>
                        <th class="border border-gray-200 px-4 py-3.5 text-right font-bold text-slate-700 text-xs tracking-wider" lang="km">ប្រាក់ដើម<br><span class="font-normal text-[11px] text-slate-400">Principals</span></th>
                        <th class="border border-gray-200 px-4 py-3.5 text-right font-bold text-slate-700 text-xs tracking-wider" lang="km">សមតុល្យប្រាក់ដើម<br><span class="font-normal text-[11px] text-slate-400">Outstanding Principals</span></th>
                        <th class="border border-gray-200 px-4 py-3.5 text-right font-bold text-slate-700 text-xs tracking-wider" lang="km">សមតុល្យបំណុល<br><span class="font-normal text-[11px] text-slate-400">Outstanding Debts</span></th>
                        <th class="border border-gray-200 px-4 py-3.5 text-center font-bold text-slate-700 text-xs tracking-wider">{{ __('app.status') }}</th>
                        <th class="border border-gray-200 px-4 py-3.5 text-center font-bold text-slate-700 text-xs tracking-wider no-print">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($schedule as $row)
                    <tr class="hover:bg-slate-50/80 transition duration-150">
                        <td class="border border-gray-200 px-4 py-3.5 text-center text-slate-800 font-bold text-sm">{{ $row['month'] }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-center text-slate-800 whitespace-nowrap text-sm">
                            <span class="font-semibold">{{ $row['due_date']->format('d/m/Y') }}</span>
                            <span class="text-slate-400 text-xs block font-medium">({{ $row['day'] }})</span>
                        </td>
                        <td class="border border-gray-200 px-4 py-3.5 text-right font-black text-slate-900 text-base">{{ format_currency($row['amount']) }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-right text-slate-700 font-medium text-sm">{{ format_currency($row['interest']) }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-right text-slate-700 font-medium text-sm">{{ format_currency($row['principal']) }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-right text-slate-700 font-medium text-sm">{{ format_currency($row['outstanding_principal']) }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-right text-slate-700 font-medium text-sm">{{ format_currency($row['outstanding_debt']) }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-center whitespace-nowrap">
                            @if($row['status'] === 'paid')
                                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-check-circle mr-1 self-center"></i> {{ __('app.paid') }}
                                </span>
                            @elseif($row['status'] === 'overdue')
                                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="fas fa-exclamation-triangle mr-1 self-center"></i> {{ __('app.overdue') }}
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="fas fa-clock mr-1 self-center"></i> {{ __('app.pending') }}
                                </span>
                            @endif
                        </td>
                        <td class="border border-gray-200 px-4 py-3.5 text-center no-print whitespace-nowrap">
                            @if($row['status'] !== 'paid')
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Send QR via Telegram --}}
                                @if(empty($installment->customer?->telegram_id))
                                    <span class="px-2.5 py-1.5 text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded-xl inline-flex items-center gap-1 cursor-not-allowed font-medium" title="{{ __('app.telegram_id_missing') }}">
                                        <i class="fab fa-telegram-plane"></i>
                                        <span>{{ __('app.send_qr_telegram') }}</span>
                                    </span>
                                @else
                                    <button type="button" 
                                        onclick="openTelegramQrModal({{ $row['month'] }}, '{{ number_format($row['amount'], 2) }}', '{{ $row['due_date']->toDateString() }}')"
                                        class="px-2.5 py-1.5 text-xs text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-200 cursor-pointer flex items-center gap-1 transition font-bold" 
                                        title="{{ __('app.send_qr_telegram') }}">
                                        <i class="fab fa-telegram-plane"></i>
                                        <span>{{ __('app.send_qr_telegram') }}</span>
                                    </button>
                                @endif

                                {{-- Record Payment --}}
                                <button type="button" onclick="openRecordPaymentModal({{ $row['month'] }}, {{ $row['amount'] }}, '{{ $row['due_date']->toDateString() }}')" class="px-2.5 py-1.5 text-xs text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl border border-emerald-200 cursor-pointer flex items-center gap-1 transition font-bold" title="{{ __('app.record_payment') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span>{{ __('app.record_payment') }}</span>
                                </button>
                            </div>
                            @else
                                <span class="text-xs text-slate-400 font-bold">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-gray-300 font-bold">
                    <tr>
                        <td colspan="2" class="border border-gray-200 px-4 py-3.5 text-center text-slate-800 text-sm" lang="km">ទឹកប្រាក់សរុប<br><span class="font-normal text-xs text-slate-500">Total Amount</span></td>
                        <td class="border border-gray-200 px-4 py-3.5 text-right text-indigo-700 font-black text-base">{{ format_currency($summary['total_scheduled']) }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-right text-slate-900 font-bold text-sm">{{ format_currency($summary['total_interest']) }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-right text-slate-900 font-bold text-sm">{{ format_currency($summary['total_principal']) }}</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-center text-gray-400">-</td>
                        <td class="border border-gray-200 px-4 py-3.5 text-center text-gray-400">-</td>
                        <td class="border border-gray-200 px-4 py-3.5"></td>
                        <td class="border border-gray-200 px-4 py-3.5 no-print"></td>
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
        <div class="print-footer hidden" style="display: none;">
            @php
                $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'CityTech Computer Shop';
                $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?? '';
            @endphp
            {{ $companyName }} @if($companyPhone)| Tel: {{ $companyPhone }}@endif
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
                                    $translatedName = trans()->has('app.' . $methodKey) ? __('app.' . $methodKey) : $method->name;
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

{{-- ===== Telegram QR Picker Modal ===== --}}
@php
    $tgSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
    $hiddenSetting = $tgSettings['hidden_payment_methods'] ?? '[]';
    $hiddenList = json_decode($hiddenSetting, true) ?: [];
    $deletedSetting = $tgSettings['deleted_default_qr'] ?? '[]';
    $deletedList = json_decode($deletedSetting, true) ?: [];
    $allHiddenTg = array_unique(array_merge($hiddenList, $deletedList));

    $tgQrList = [];
    $tgQrMap = [
        'qr_aba'          => 'ABA Bank KHQR',
        'qr_acleda'       => 'ACLEDA KHQR',
        'qr_wing'         => 'Wing KHQR',
        'qr_truemoney'    => 'TrueMoney KHQR',
        'qr_creditcard'   => 'Credit Card',
        'qr_bakong'       => 'Bakong KHQR',
        'company_bank_qr' => 'QR Code ធនាគារ (Default)',
    ];
    foreach ($tgQrMap as $k => $lbl) {
        $variant = str_replace('qr_', '', $k) . '_qr';
        if (in_array($k, $allHiddenTg) || in_array($variant, $allHiddenTg)) continue;
        if (!empty($tgSettings[$k])) $tgQrList[] = ['key' => $k, 'label' => $lbl, 'img' => $tgSettings[$k]];
    }
    $tgCustom = json_decode($tgSettings['custom_qr_list'] ?? '[]', true) ?: [];
    foreach ($tgCustom as $ci) {
        if (!empty($ci['key']) && !empty($ci['label']) && !empty($tgSettings[$ci['key']])) {
            if (in_array($ci['key'], $allHiddenTg)) continue;
            $tgQrList[] = ['key' => $ci['key'], 'label' => $ci['label'], 'img' => $tgSettings[$ci['key']]];
        }
    }
@endphp

<div id="telegramQrModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(15,23,42,0.55);backdrop-filter:blur(4px);">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-blue-600 to-indigo-600">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fab fa-telegram-plane text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-base">{{ app()->getLocale() === 'km' ? 'ផ្ញើ QR Code តាម Telegram' : 'Send QR Code via Telegram' }}</h3>
                    <p id="tgModalSubtitle" class="text-blue-100 text-xs"></p>
                </div>
            </div>
            <button onclick="closeTelegramQrModal()" class="text-white/70 hover:text-white border-0 bg-transparent cursor-pointer text-xl leading-none">&times;</button>
        </div>

        <form id="tgQrForm" method="POST" action="" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="qr_key" id="tgSelectedQrKey" value="{{ !empty($tgQrList) ? $tgQrList[0]['key'] : '' }}">

            {{-- QR Code Selector Grid --}}
            @if(!empty($tgQrList))
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
                    <i class="fas fa-qrcode text-purple-500"></i>
                    {{ app()->getLocale() === 'km' ? 'ជ្រើសរើស QR Code ដែលត្រូវផ្ញើ' : 'Select QR Code to Send' }}
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="tgQrGrid">
                    @foreach($tgQrList as $idx => $qr)
                    <div onclick="selectTgQr('{{ $qr['key'] }}', this)"
                        class="tg-qr-card cursor-pointer rounded-xl border-2 p-3 flex flex-col items-center gap-2 transition-all duration-150 hover:shadow-md {{ $idx === 0 ? 'border-purple-500 bg-purple-50 shadow-sm ring-2 ring-purple-200' : 'border-slate-200 bg-white hover:border-purple-300 hover:bg-purple-50' }}"
                        data-qr-key="{{ $qr['key'] }}">
                        <div class="relative">
                            <img src="{{ asset('storage/' . $qr['img']) }}"
                                class="w-16 h-16 object-contain rounded-lg border border-slate-100 bg-white p-1"
                                onerror="this.src='https://ui-avatars.com/api/?name=QR&background=EEF2FF&color=4F46E5'">
                            @if($idx === 0)
                            <div id="tgCheckBadge_{{ $idx }}" class="tg-check-badge absolute -top-1.5 -right-1.5 w-5 h-5 bg-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs" style="font-size:9px"></i>
                            </div>
                            @else
                            <div id="tgCheckBadge_{{ $idx }}" class="tg-check-badge hidden absolute -top-1.5 -right-1.5 w-5 h-5 bg-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs" style="font-size:9px"></i>
                            </div>
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-slate-700 text-center leading-tight">{{ $qr['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-800 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-500"></i>
                <span>{{ app()->getLocale() === 'km' ? 'មិនមាន QR Code ណាមួយត្រូវបានកំណត់ក្នុង Settings ទេ។' : 'No QR codes are configured in Settings yet.' }}</span>
            </div>
            @endif

            {{-- Footer Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeTelegramQrModal()"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg border-0 cursor-pointer transition">
                    {{ app()->getLocale() === 'km' ? 'បោះបង់' : 'Cancel' }}
                </button>
                <button type="submit" id="tgSendBtn"
                    class="px-5 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg border-0 cursor-pointer transition flex items-center gap-2 shadow-sm {{ empty($tgQrList) ? 'opacity-50 pointer-events-none' : '' }}">
                    <i class="fab fa-telegram-plane"></i>
                    {{ app()->getLocale() === 'km' ? 'ផ្ញើ QR Code' : 'Send QR Code' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const tgBaseUrl = "{{ route('installments.send-telegram-qr', [$installment, '__MONTH__']) }}";

    function openTelegramQrModal(month, amount, dueDate) {
        const url = tgBaseUrl.replace('__MONTH__', month);
        document.getElementById('tgQrForm').action = url;
        document.getElementById('tgModalSubtitle').textContent =
            '{{ app()->getLocale() === "km" ? "ខែ" : "Month" }} ' + month +
            ' · $' + amount + ' · ' + dueDate;
        document.getElementById('telegramQrModal').classList.remove('hidden');
    }

    function closeTelegramQrModal() {
        document.getElementById('telegramQrModal').classList.add('hidden');
    }

    function selectTgQr(key, el) {
        document.querySelectorAll('.tg-qr-card').forEach(function(c) {
            c.classList.remove('border-purple-500', 'bg-purple-50', 'shadow-sm', 'ring-2', 'ring-purple-200');
            c.classList.add('border-slate-200', 'bg-white');
        });
        document.querySelectorAll('.tg-check-badge').forEach(function(b) {
            b.classList.add('hidden');
        });
        el.classList.remove('border-slate-200', 'bg-white');
        el.classList.add('border-purple-500', 'bg-purple-50', 'shadow-sm', 'ring-2', 'ring-purple-200');
        const badge = el.querySelector('.tg-check-badge');
        if (badge) badge.classList.remove('hidden');
        document.getElementById('tgSelectedQrKey').value = key;
    }

    const modalBackdrop = document.getElementById('telegramQrModal');
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', function(e) {
            if (e.target === this) closeTelegramQrModal();
        });
    }
</script>

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

<!-- PDF & Print Execution Script -->
<script>
    function saveSchedulePDF() {
        window.print();
    }
</script>
@endsection
