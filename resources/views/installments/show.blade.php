@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm">{{ session('error') }}</div>
    @endif
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-gray-400 mb-1 flex items-center gap-1.5">
                <a href="{{ route('installments.index') }}" class="hover:text-indigo-600 transition">{{ __('app.installment_plans') }}</a>
                <span>/</span>
                <span class="text-gray-600">#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.installment_plan_details') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.installment_plan_details_sub') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('installments.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.back') }}
            </a>
            @if($installment->status !== 'pending')
            <a href="{{ route('installments.contract', $installment) }}" target="_blank" class="inline-flex items-center bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition duration-150">
                <i class="fas fa-file-contract mr-2 text-indigo-400"></i>
                {{ __('app.print_contract') }}
            </a>
            @endif

            @if($installment->status === 'completed' || $installment->remaining_balance <= 0)
            <a href="{{ route('installments.clearance', $installment) }}" target="_blank" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition duration-150">
                <i class="fas fa-certificate mr-2"></i>
                {{ __('app.print_clearance') }}
            </a>
            @endif

            @if(auth()->user()->role === 'admin')
            <a href="{{ route('installments.edit', $installment) }}" class="inline-flex items-center bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold px-4 py-2.5 rounded-xl shadow-xs transition duration-150">
                <i class="fas fa-edit mr-2 text-slate-500"></i>
                {{ __('app.edit_plan') }}
            </a>
            @endif
        </div>
    </div>

    @if($installment->status === 'pending')
    <!-- Modern Elegant Pending Approval Card -->
    <div class="mb-8 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-200/80 p-5 shadow-sm backdrop-blur-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-500/20 text-xl">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-slate-900 text-base">
                            {{ app()->getLocale() === 'km' ? 'គម្រោងបង់រំលស់កំពុងរង់ចាំការឯកភាពពី Admin' : 'Installment Plan Awaiting Admin Approval' }}
                        </h4>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300/60">
                            {{ app()->getLocale() === 'km' ? 'រង់ចាំពិនិត្យ' : 'Pending' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ app()->getLocale() === 'km' ? 'គម្រោងនេះទើបតែបង្កើតថ្មី និងតម្រូវឱ្យ Admin ពិនិត្យឯកភាពជាផ្លូវការ មុនពេលចាប់ផ្តើមកិច្ចសន្យា និងទូទាត់ប្រាក់។' : 'This contract is awaiting admin review before becoming active for monthly payment collections.' }}
                    </p>
                </div>
            </div>

            @if(auth()->user()->role === 'admin')
            <div class="flex items-center gap-2.5 shrink-0 self-end md:self-center">
                {{-- Approve Button --}}
                <form method="POST" action="{{ route('installments.approve', $installment) }}" class="inline-block" onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ឯកភាពគម្រោងបង់រំលស់នេះមែនទេ?' : 'Are you sure you want to approve this installment plan?' }}')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-sm hover:shadow-md transition cursor-pointer">
                        <i class="fas fa-check-circle text-sm"></i>
                        <span>{{ app()->getLocale() === 'km' ? 'ឯកភាពគម្រោង (Approve)' : 'Approve Plan' }}</span>
                    </button>
                </form>

                {{-- Reject Button --}}
                <form method="POST" action="{{ route('installments.reject', $installment) }}" class="inline-block" onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់បដិសេធគម្រោងបង់រំលស់នេះ ហើយបញ្ចូលទំនិញចូលក្នុងស្តុកវិញមែនទេ?' : 'Are you sure you want to reject this plan and return item to stock?' }}')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-bold rounded-xl text-xs shadow-xs hover:shadow-sm transition cursor-pointer">
                        <i class="fas fa-times-circle text-sm"></i>
                        <span>{{ app()->getLocale() === 'km' ? 'បដិសេធ (Reject)' : 'Reject Plan' }}</span>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Customer & Product Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Customer Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">{{ __('app.customer_profile') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('app.contract_holder') }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.name') }}</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->customer?->name ?? 'N/A' }}</div>
                    </div>
                    @if($installment->customer?->phone)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.phone') }}</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->customer?->phone }}</div>
                    </div>
                    @endif
                    @if($installment->customer?->email)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.email') }}</div>
                        <div class="text-sm text-gray-800">{{ $installment->customer?->email }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-50 pb-3">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">{{ __('app.product_details') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('app.financed_asset') }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.model_name') }}</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $installment->product?->name ?? 'N/A' }}</div>
                    </div>
                    @if($installment->product?->code)
                    <div>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-0.5">{{ __('app.item_code') }}</div>
                        <div class="text-sm font-semibold text-indigo-600">{{ $installment->product?->code }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Financial Breakdown -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Metrics -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">{{ __('app.financial_schedule') }}</h3>

                <div class="overflow-x-auto rounded-xl border border-gray-100 mb-6">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @if($installment->tax_amount > 0)
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.subtotal') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">${{ number_format($installment->subtotal_before_tax ?? $installment->total_price, 2) }}</td>
                            </tr>
                            @php
                                $taxLabel = \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT';
                            @endphp
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.tax') }} {{ $taxLabel }} ({{ $installment->tax_rate }}%)</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">${{ number_format($installment->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.total_price') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">${{ number_format($installment->total_price, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.down_payment') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">${{ number_format($installment->down_payment, 2) }}</td>
                            </tr>
                            @php
                                $types = \App\Services\InstallmentCalculationService::getTypes();
                                $typeKey = $installment->interest_type ?? 'flat';
                                $isKm = app()->getLocale() === 'km';
                                $typeName = $isKm ? ($types[$typeKey]['name_km'] ?? 'ការប្រាក់ថេរ (Flat Rate)') : ($types[$typeKey]['name_en'] ?? 'Flat Rate');
                            @endphp
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ $isKm ? 'ប្រភេទរំលស់' : 'Installment Plan Type' }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-indigo-700">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $typeName }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.interest_rate') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-gray-900">{{ $installment->interest_rate }}%</td>
                            </tr>
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-3.5 text-gray-500 font-medium">{{ __('app.duration') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-bold text-gray-900">{{ $installment->duration_months }} {{ __('app.duration_unit') }}</span>
                                    <span class="text-xs block font-semibold {{ ($installment->remaining_balance <= 0 || $installment->remaining_months <= 0) ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ app()->getLocale() === 'km' ? ($installment->remaining_months <= 0 ? 'នៅសល់ ០ ខែ' : 'នៅសល់ ' . $installment->remaining_months . ' ខែ') : ($installment->remaining_months <= 0 ? '0 months left' : $installment->remaining_months . ' ' . __('app.months') . ' left') }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="bg-indigo-50/60 hover:bg-indigo-50">
                                <td class="px-5 py-3.5 text-indigo-700 font-semibold">{{ __('app.monthly_payment') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-extrabold text-indigo-700 text-base block">${{ number_format($installment->monthly_payment, 2) }}</span>
                                </td>
                            </tr>
                            <tr class="bg-amber-50/60 hover:bg-amber-50">
                                <td class="px-5 py-3.5 text-amber-800 font-semibold">{{ __('app.remaining_balance') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-extrabold text-amber-800 text-base block">${{ number_format($installment->remaining_balance, 2) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Metadata details -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('app.plan_status') }}</span>
                        <div class="mt-1">
                            @if($installment->status === 'active' || $installment->status === 'ongoing')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-1 text-green-600"></i> {{ __('app.active') }}
                                </span>
                            @elseif($installment->status === 'pending')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                    <i class="fas fa-clock mr-1 text-amber-600"></i> {{ __('app.pending') }}
                                </span>
                            @elseif($installment->status === 'completed' || $installment->status === 'paid')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fas fa-check-double mr-1 text-blue-600"></i> {{ __('app.completed') }}
                                </span>
                            @elseif($installment->status === 'returned')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                    <i class="fas fa-undo-alt mr-1"></i> {{ app()->getLocale() === 'km' ? 'បានយកអីវ៉ាន់វិញ' : 'Returned' }}
                                </span>
                            @elseif($installment->status === 'cancelled')
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-times-circle mr-1 text-red-600"></i> {{ __('app.cancelled') }}
                                </span>
                            @else
                                <span class="px-3.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200">{{ $installment->status }}</span>
                            @endif
                        </div>
                    </div>
                    @if($installment->next_due_date)
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('app.next_due_date') }}</span>
                        <div class="mt-1 text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($installment->next_due_date)->format('d M Y') }}
                        </div>
                    </div>
                    @endif
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

                    <!-- Amount, Penalty, and Date -->
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
                                oninput="updateModalTotal()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                {{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }} (USD)
                            </label>
                            <input 
                                type="number" 
                                name="penalty_amount" 
                                id="modalPenaltyInput" 
                                step="0.01" 
                                min="0" 
                                value="0.00"
                                oninput="updateModalTotal()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-semibold text-red-600"
                            >
                            <span class="text-[11px] text-gray-400 mt-0.5 block" id="modalPenaltyDaysHint"></span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('app.payment_date') }}</label>
                            <input 
                                type="date" 
                                name="payment_date" 
                                id="modalPaymentDateInput"
                                value="{{ now()->toDateString() }}" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                        </div>
                    </div>

                    <!-- Total payment summary in modal -->
                    <div class="flex justify-between items-center px-3.5 py-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold">
                        <span class="text-slate-600">{{ app()->getLocale() === 'km' ? 'ទឹកប្រាក់សរុបរួមទាំងប្រាក់ពិន័យ:' : 'Total with Penalty:' }}</span>
                        <span class="text-sm font-bold text-indigo-700" id="modalTotalWithPenalty">$0.00</span>
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

<script>
    function updateModalTotal() {
        const amt = parseFloat(document.getElementById('modalAmountInput').value) || 0;
        const pen = parseFloat(document.getElementById('modalPenaltyInput').value) || 0;
        const total = amt + pen;
        document.getElementById('modalTotalWithPenalty').innerText = '$' + total.toFixed(2);
    }

    function openRecordPaymentModal(month, amount, dueDateStr) {
        document.getElementById('modalMonthVal').innerText = month;
        document.getElementById('modalMonthLabel').innerText = "{{ __('app.installment_month') }} " + month;
        document.getElementById('modalAmountInput').value = parseFloat(amount).toFixed(2);
        
        // Calculate penalty based on due date vs today
        const dueDate = new Date(dueDateStr);
        const today = new Date();
        today.setHours(0,0,0,0);
        dueDate.setHours(0,0,0,0);

        let penalty = 0;
        let daysLate = 0;

        if (today > dueDate) {
            const diffTime = Math.abs(today - dueDate);
            daysLate = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            // Standard system penalty rule:
            // <= 5 days: $0 (Grace period)
            // 6-15 days: $5/day
            // > 15 days: $10/day
            if (daysLate > 15) {
                penalty = daysLate * 10.00;
            } else if (daysLate > 5) {
                penalty = daysLate * 5.00;
            }
        }

        const penaltyInput = document.getElementById('modalPenaltyInput');
        penaltyInput.value = penalty.toFixed(2);

        const hint = document.getElementById('modalPenaltyDaysHint');
        if (daysLate > 0) {
            hint.innerText = (daysLate > 5) 
                ? 'យឺតយ៉ាវ ' + daysLate + ' ថ្ងៃ (ពិន័យ: $' + penalty.toFixed(2) + ')' 
                : 'យឺតយ៉ាវ ' + daysLate + ' ថ្ងៃ (នៅក្នុងអំឡុងពេលអនុគ្រោះ ៥ ថ្ងៃ គ្មានការពិន័យ)';
            hint.className = (daysLate > 5) ? 'text-[11px] text-red-500 font-semibold mt-0.5 block' : 'text-[11px] text-amber-600 mt-0.5 block';
        } else {
            hint.innerText = '';
        }

        updateModalTotal();
        
        const modal = document.getElementById('recordPaymentModal');
        modal.classList.remove('hidden');
    }

    function closeRecordPaymentModal() {
        const modal = document.getElementById('recordPaymentModal');
        modal.classList.add('hidden');
    }
</script>

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
        // Deselect all
        document.querySelectorAll('.tg-qr-card').forEach(function(c) {
            c.classList.remove('border-purple-500', 'bg-purple-50', 'shadow-sm', 'ring-2', 'ring-purple-200');
            c.classList.add('border-slate-200', 'bg-white');
        });
        document.querySelectorAll('.tg-check-badge').forEach(function(b) {
            b.classList.add('hidden');
        });
        // Select clicked
        el.classList.remove('border-slate-200', 'bg-white');
        el.classList.add('border-purple-500', 'bg-purple-50', 'shadow-sm', 'ring-2', 'ring-purple-200');
        const badge = el.querySelector('.tg-check-badge');
        if (badge) badge.classList.remove('hidden');
        document.getElementById('tgSelectedQrKey').value = key;
    }

    // Close on backdrop click
    document.getElementById('telegramQrModal').addEventListener('click', function(e) {
        if (e.target === this) closeTelegramQrModal();
    });
</script>
@endsection
