@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-6">
    <!-- Header Section (រូបភាពទី១) -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                {{ __('app.late_customers') ?? 'អតិថិជនបង់យឺត' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ app()->getLocale() === 'km' 
                    ? 'បង្ហាញអតិថិជនដែលមានការបង់យឺត និងអនុវត្តប្រាក់ពិន័យចំពោះអតិថិជន' 
                    : 'Manage overdue installment payments and apply late fees.' }}
            </p>
        </div>
    </div>

    <!-- Alert Notices -->
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-xs text-sm">
            <i class="fas fa-check-circle mr-2 text-emerald-500 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center shadow-xs text-sm">
            <i class="fas fa-exclamation-circle mr-2 text-red-500 text-lg"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- 1. ស្វែងរក (Search Bar) -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <form method="GET" action="{{ route('late-payments.index') }}" class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center">
            <!-- Search Text -->
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកអតិថិជន (ឈ្មោះ, លេខទូរស័ព្ទ, ផលិតផល...)' : 'Search customer (name, phone, product...)' }}"
                       class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-sm transition flex items-center gap-2">
                    <i class="fas fa-search text-xs"></i>
                    <span>Search</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 3. បញ្ជីអតិថិជនបង់យឺត និងប្រាក់ពិន័យ (Table) -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50/75">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customer' }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ផលិតផល' : 'Product' }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ប្រាក់ត្រូវបង់/នៅសល់' : 'Due Amount' }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ថ្ងៃយឺត' : 'Days Late' }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ប្រាក់ពិន័យយឺត' : 'Late Fee' }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'ស្ថានភាព' : 'Status' }}</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'សកម្មភាព' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($lateInstallments as $idx => $installment)
                    @php
                        $daysLate = $installment->daysLate();
                        $calculatedFee = $installment->calculatePenalty();
                        $monthlyDue = (float) $installment->monthly_payment;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition duration-150">
                        <td class="px-5 py-4 whitespace-nowrap text-xs font-semibold text-slate-400">
                            {{ $idx + 1 }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-900">{{ $installment->customer?->name ?? 'N/A' }}</span>
                                @if(!empty($installment->customer?->telegram_id))
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200" title="បានភ្ជាប់ Telegram (ID: {{ $installment->customer->telegram_id }})">
                                        <i class="fab fa-telegram-plane text-sky-500"></i>
                                        <span>ភ្ជាប់រួច</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-400" title="មិនទាន់ភ្ជាប់ Telegram">
                                        <i class="fab fa-telegram text-slate-400"></i>
                                        <span>មិនទាន់ភ្ជាប់</span>
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                <i class="fas fa-phone-alt text-[10px] text-slate-400"></i>
                                <span>{{ $installment->customer?->phone ?? 'គ្មានលេខទូរស័ព្ទ' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-700">
                            <div class="font-medium max-w-[200px] truncate" title="{{ $installment->product?->name }}">
                                {{ $installment->product?->name ?? 'N/A' }}
                            </div>
                            <div class="text-[11px] text-slate-400">#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-bold text-rose-600 font-mono">${{ number_format($installment->remaining_balance, 2) }}</div>
                            <div class="text-[11px] text-slate-400">{{ app()->getLocale() === 'km' ? 'ខែនេះ៖' : 'Monthly:' }} ${{ number_format($monthlyDue, 2) }}</div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-amber-600 font-mono">
                                <i class="fas fa-clock text-xs"></i>
                                {{ $daysLate }} {{ app()->getLocale() === 'km' ? 'ថ្ងៃ' : 'Days' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-sm font-black text-red-600 font-mono">
                                ${{ number_format($calculatedFee, 2) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                {{ app()->getLocale() === 'km' ? 'បង់យឺត' : 'Overdue' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right space-x-1.5">
                            <!-- Collect Payment Direct Button -->
                            <a href="{{ route('payments.create', ['installment_id' => $installment->id, 'penalty_amount' => $calculatedFee]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition shadow-2xs"
                               title="{{ app()->getLocale() === 'km' ? 'ទូទាត់ប្រាក់ពីអតិថិជន' : 'Make Payment' }}">
                                <i class="fas fa-cash-register text-xs"></i>
                                <span>{{ app()->getLocale() === 'km' ? 'ទូទាត់' : 'Pay' }}</span>
                            </a>

                            <!-- Telegram Reminder Button -->
                            @if(!empty($installment->customer?->telegram_id))
                            <button type="button" onclick="document.getElementById('remind-modal-{{ $installment->id }}').classList.remove('hidden')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 border border-sky-200 transition"
                                    title="{{ app()->getLocale() === 'km' ? 'ផ្ញើសាររំលឹកតាម Telegram' : 'Notify Telegram' }}">
                                <i class="fab fa-telegram-plane"></i>
                            </button>
                            @else
                            <button type="button" disabled
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-300 cursor-not-allowed border border-slate-200"
                                    title="{{ app()->getLocale() === 'km' ? 'អតិថិជនមិនទាន់បានភ្ជាប់ Telegram ទេ' : 'Telegram not connected' }}">
                                <i class="fab fa-telegram-plane"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">
                                    {{ __('app.all_payments_up_to_date') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- ៥. Popup គណនាប្រាក់ពិន័យ (រូបភាពទី២) សម្រាប់អតិថិជននីមួយៗ                    -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
@foreach($lateInstallments as $installment)
@php
    $daysLate = $installment->daysLate();
    $graceDays = (int) ($settings['late_grace_period_days'] ?? 0);
    $defaultMethod = $settings['late_fee_method'] ?? 'fixed';
    $defaultRate = (float) ($settings['late_fee_rate'] ?? 1.00);
    $defaultFrequency = $settings['late_fee_frequency'] ?? 'daily';
    $overdueBase = (float) ($installment->monthly_payment ?: $installment->remaining_balance);
@endphp
<div id="penalty-modal-{{ $installment->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 my-8">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span>Apply Late Fee (ដាក់ពិន័យយឺត)</span>
            </h3>
            <button type="button" onclick="closePenaltyModal({{ $installment->id }})" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('late-payments.apply-fee', $installment) }}" id="penalty-form-{{ $installment->id }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="installment_id" value="{{ $installment->id }}">
            <input type="hidden" name="late_fee_method" id="penalty_method_{{ $installment->id }}" value="{{ $defaultMethod }}">

            <!-- ៥. ជ្រើសរើសវិធីគិតពិន័យ (Radio Tabs) -->
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                <label onclick="switchPenaltyMethod({{ $installment->id }}, 'fixed')"
                       class="penalty-method-card-{{ $installment->id }} flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold cursor-pointer transition {{ $defaultMethod === 'fixed' ? 'bg-white shadow-xs text-blue-700' : 'text-slate-600 hover:text-slate-900' }}"
                       data-method="fixed">
                    <input type="radio" name="method_radio_{{ $installment->id }}" value="fixed" {{ $defaultMethod === 'fixed' ? 'checked' : '' }} class="hidden">
                    <span>គិតជាប្រាក់រៀងរាល់ថ្ងៃ</span>
                </label>
                <label onclick="switchPenaltyMethod({{ $installment->id }}, 'percentage')"
                       class="penalty-method-card-{{ $installment->id }} flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold cursor-pointer transition {{ $defaultMethod === 'percentage' ? 'bg-white shadow-xs text-blue-700' : 'text-slate-600 hover:text-slate-900' }}"
                       data-method="percentage">
                    <input type="radio" name="method_radio_{{ $installment->id }}" value="percentage" {{ $defaultMethod === 'percentage' ? 'checked' : '' }} class="hidden">
                    <span>គិតជាភាគរយ</span>
                </label>
            </div>

            <!-- Inputs Grid -->
            <div class="grid grid-cols-3 gap-3">
                <!-- Days Late -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">ចំនួនថ្ងៃយឺត</label>
                    <div class="relative">
                        <input type="number" name="days_late" id="penalty_days_{{ $installment->id }}" value="{{ $daysLate }}"
                               oninput="recalcPenalty({{ $installment->id }})"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">ថ្ងៃ</span>
                    </div>
                </div>

                <!-- Rate / Amount -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1" id="penalty_rate_label_{{ $installment->id }}">
                        {{ $defaultMethod === 'percentage' ? 'អត្រាការប្រាក់ (%)' : 'ចំនួនដុល្លារក្នុងមួយថ្ងៃ' }}
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" name="rate_or_amount" id="penalty_rate_{{ $installment->id }}" value="{{ $defaultRate }}"
                               oninput="recalcPenalty({{ $installment->id }})"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400" id="penalty_rate_unit_{{ $installment->id }}">
                            {{ $defaultMethod === 'percentage' ? '%' : '$' }}
                        </span>
                    </div>
                </div>

                <!-- Total Penalty Fee (Auto Calculated) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">ប្រាក់ពិន័យសរុប</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="late_fee" id="penalty_total_{{ $installment->id }}"
                               class="w-full border border-amber-300 bg-amber-50 rounded-xl px-3 py-2 text-sm font-black text-amber-700 focus:outline-none">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-amber-600">$</span>
                    </div>
                </div>
            </div>

            <!-- Note -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Note (សម្គាល់)</label>
                <input type="text" name="note" id="penalty_note_{{ $installment->id }}"
                       value="ឧ. ធ្លាក់ពិន័យបង់យឺត {{ $daysLate }} ថ្ងៃ"
                       placeholder="e.g. Late fee applied..."
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePenaltyModal({{ $installment->id }})"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-md flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span>Confirm</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Remind Modals (Khmer Message Composer for Telegram) -->
@foreach($lateInstallments as $installment)
@php
    $daysLate = $installment->daysLate();
    $penalty = $installment->calculatePenalty();
    $custName = $installment->customer?->name ?? 'អតិថិជន';
    $prodName = $installment->product?->name ?? 'ទំនិញ';
    $monthlyAmt = number_format($installment->monthly_payment, 2);
    $remAmt = number_format($installment->remaining_balance, 2);
    $penAmt = number_format($penalty, 2);

    $defaultKhmerMessage = "⏰ *សេចក្តីជូនដំណឹងអំពីការទូទាត់ប្រាក់យឺតយ៉ាវ*\n\n"
        . "សូមជម្រាបជូនអតិថិជន៖ *{$custName}*\n"
        . "• ទំនិញ៖ *{$prodName}*\n"
        . "• ទឹកប្រាក់ប្រចាំខែ៖ *\${$monthlyAmt}*\n"
        . "• ទឹកប្រាក់នៅសល់សរុប៖ *\${$remAmt}*\n"
        . ($daysLate > 0 ? "• ចំនួនថ្ងៃយឺតយ៉ាវ៖ *{$daysLate} ថ្ងៃ*\n" : "")
        . ($penalty > 0 ? "• ប្រាក់ពិន័យយឺតយ៉ាវ៖ *\${$penAmt}*\n" : "")
        . "\nសូមអតិថិជនមេត្តាធ្វើការទូទាត់ប្រាក់ឱ្យបានឆាប់រហ័ស។ ប្រសិនបើលោកអ្នកបានទូទាត់រួចរាល់ សូមមេត្តាទាក់ទងមកកាន់ហាងដើម្បីផ្ទៀងផ្ទាត់។ សូមអរគុណ! 🙏";
@endphp
<div id="remind-modal-{{ $installment->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-gray-100">
        <div class="px-6 py-4 bg-slate-50 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-base">
                    <i class="fab fa-telegram-plane"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900" lang="km">ផ្ញើសាររំលឹកតាម Telegram</h3>
                    <p class="text-xs text-gray-500">Send Payment Reminder via Telegram</p>
                </div>
            </div>
            <button type="button" 
                    onclick="document.getElementById('remind-modal-{{ $installment->id }}').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('late-payments.remind', $installment) }}" class="p-6">
            @csrf
            <div class="bg-blue-50/60 rounded-xl p-3.5 border border-blue-100 mb-4 text-xs space-y-1.5">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">អតិថិជន:</span>
                    <span class="font-bold text-gray-900">{{ $custName }} ({{ $installment->customer?->phone ?? 'គ្មានលេខទូរស័ព្ទ' }})</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">ទំនិញ:</span>
                    <span class="font-bold text-gray-800">{{ $prodName }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">យឺតយ៉ាវ:</span>
                    <span class="font-bold text-amber-600">{{ $daysLate }} ថ្ងៃ</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">ទឹកប្រាក់នៅសល់:</span>
                    <span class="font-bold text-rose-600">${{ $remAmt }}</span>
                </div>
                @if($penalty > 0)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">ប្រាក់ពិន័យ:</span>
                    <span class="font-bold text-red-600">${{ $penAmt }}</span>
                </div>
                @endif
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" lang="km">
                    ខ្លឹមសារសារជាភាសាខ្មែរ (អាចកែប្រែបាន)
                </label>
                <textarea name="message" rows="7" 
                          class="w-full px-3.5 py-2.5 text-xs font-normal border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent leading-relaxed text-gray-800 bg-slate-50/50 resize-y"
                >{{ $defaultKhmerMessage }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" 
                        onclick="document.getElementById('remind-modal-{{ $installment->id }}').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition border-0 cursor-pointer"
                >
                    {{ __('app.cancel') }}
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition flex items-center gap-2 shadow-sm border-0 cursor-pointer"
                >
                    <i class="fab fa-telegram-plane"></i>
                    <span>ផ្ញើសារឥឡូវនេះ (Send Now)</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
    const installmentData = {
        @foreach($lateInstallments as $ins)
        {{ $ins->id }}: {
            days: {{ (int) $ins->daysLate() }},
            monthlyDue: {{ (float) ($ins->monthly_payment ?: $ins->remaining_balance) }},
            remaining: {{ (float) $ins->remaining_balance }},
            graceDays: {{ (int) ($settings['late_grace_period_days'] ?? 0) }},
            defaultMethod: '{{ $settings['late_fee_method'] ?? 'fixed' }}',
            defaultRate: {{ (float) ($settings['late_fee_rate'] ?? 1.00) }}
        },
        @endforeach
    };

    function openPenaltyModal(id) {
        const modal = document.getElementById('penalty-modal-' + id);
        if (modal) {
            modal.classList.remove('hidden');
            recalcPenalty(id);
        }
    }

    function closePenaltyModal(id) {
        const modal = document.getElementById('penalty-modal-' + id);
        if (modal) modal.classList.add('hidden');
    }

    function switchPenaltyMethod(id, method) {
        const methodInput = document.getElementById('penalty_method_' + id);
        if (methodInput) methodInput.value = method;

        const cards = document.querySelectorAll('.penalty-method-card-' + id);
        cards.forEach(c => {
            if (c.getAttribute('data-method') === method) {
                c.className = 'penalty-method-card-' + id + ' flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold cursor-pointer transition bg-white shadow-xs text-blue-700';
            } else {
                c.className = 'penalty-method-card-' + id + ' flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold cursor-pointer transition text-slate-600 hover:text-slate-900';
            }
        });

        const label = document.getElementById('penalty_rate_label_' + id);
        const unit = document.getElementById('penalty_rate_unit_' + id);

        if (method === 'percentage') {
            if (label) label.textContent = 'អត្រាការប្រាក់ (%)';
            if (unit) unit.textContent = '%';
        } else {
            if (label) label.textContent = 'ចំនួនដុល្លារក្នុងមួយថ្ងៃ';
            if (unit) unit.textContent = '$';
        }

        recalcPenalty(id);
    }

    function recalcPenalty(id) {
        const data = installmentData[id];
        if (!data) return;

        const methodInput = document.getElementById('penalty_method_' + id);
        const daysInput = document.getElementById('penalty_days_' + id);
        const rateInput = document.getElementById('penalty_rate_' + id);
        const totalInput = document.getElementById('penalty_total_' + id);
        const noteInput = document.getElementById('penalty_note_' + id);

        const method = methodInput ? methodInput.value : 'fixed';
        const days = parseInt(daysInput ? daysInput.value : 0) || 0;
        const rate = parseFloat(rateInput ? rateInput.value : 0) || 0;

        const effectiveDays = Math.max(0, days - data.graceDays);
        let totalFee = 0;

        if (effectiveDays > 0) {
            if (method === 'percentage') {
                totalFee = (data.monthlyDue * (rate / 100)) * effectiveDays;
            } else {
                totalFee = effectiveDays * rate;
            }
        }

        if (totalInput) {
            totalInput.value = totalFee.toFixed(2);
        }

        if (noteInput && days > 0) {
            noteInput.value = 'ធ្លាក់ពិន័យបង់យឺត ' + days + ' ថ្ងៃ';
        }
    }
</script>
@endsection