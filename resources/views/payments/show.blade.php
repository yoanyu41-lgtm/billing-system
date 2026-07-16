@extends('layouts.app')

@section('content')
@php
    $pstatus = $payment->status ?? 'pending';
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
    
    $statusConfig = [
        'approved' => [
            'bg' => 'bg-emerald-50 border-emerald-100',
            'text' => 'text-emerald-700',
            'badge' => 'bg-emerald-500 text-white',
            'icon' => 'fa-check-circle',
            'label' => $L('បានអនុម័ត', 'Approved'),
            'gradient' => 'from-emerald-500 to-teal-600',
        ],
        'pending' => [
            'bg' => 'bg-amber-50 border-amber-100',
            'text' => 'text-amber-700',
            'badge' => 'bg-amber-500 text-white',
            'icon' => 'fa-clock',
            'label' => $L('កំពុងរង់ចាំ', 'Pending'),
            'gradient' => 'from-amber-500 to-orange-600',
        ],
        'rejected' => [
            'bg' => 'bg-rose-50 border-rose-100',
            'text' => 'text-rose-700',
            'badge' => 'bg-rose-500 text-white',
            'icon' => 'fa-times-circle',
            'label' => $L('បានបដិសេធ', 'Rejected'),
            'gradient' => 'from-rose-500 to-red-600',
        ]
    ];
    $cfg = $statusConfig[$pstatus] ?? $statusConfig['pending'];
    
    $methodKey = strtolower(str_replace(' ', '_', $payment->paymentMethod->name ?? ''));
    $methodIcon = [
        'cash' => 'fa-money-bill-wave text-emerald-500',
        'qr_code' => 'fa-qrcode text-blue-500',
        'credit_card' => 'fa-credit-card text-indigo-500'
    ][$methodKey] ?? 'fa-credit-card text-gray-500';
@endphp

<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Breadcrumb & Navigation -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-800 transition duration-150" style="text-decoration: none;">
                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                {{ __('app.payment_details') }}
            </h1>
        </div>
        
        <div class="flex items-center gap-3">
            @if($pstatus === 'approved' && $payment->invoice)
                <a href="{{ route('invoices.show', $payment->invoice) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition duration-150 shadow-sm" style="text-decoration: none;">
                    <i class="fas fa-file-invoice"></i>
                    <span>{{ $L('មើលវិក្កយបត្រ', 'View Invoice') }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Interactive Receipt style card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden relative">
                <!-- Top Accent Gradient -->
                <div class="h-3 bg-gradient-to-r {{ $cfg['gradient'] }}"></div>
                
                <div class="p-6 text-center space-y-6">
                    <div class="flex justify-center">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full {{ $cfg['badge'] }} shadow-sm">
                            <i class="fas {{ $cfg['icon'] }} text-xl animate-pulse"></i>
                        </span>
                    </div>
                    
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                            {{ $cfg['label'] }}
                        </span>
                        <div class="text-xs text-slate-400 mt-2">
                            Transaction ID: #PAY-{{ $payment->id }}
                        </div>
                    </div>
                    
                    <!-- Dash line divider -->
                    <div class="border-t border-dashed border-slate-200 my-4 relative">
                        <div class="absolute -left-8 -top-2 w-4 h-4 bg-slate-50 rounded-full border-r border-slate-100"></div>
                        <div class="absolute -right-8 -top-2 w-4 h-4 bg-slate-50 rounded-full border-l border-slate-100"></div>
                    </div>
                    
                    <!-- Amount Section -->
                    <div class="space-y-1">
                        <span class="text-xs text-slate-400 font-semibold block uppercase tracking-wider">
                            {{ $L('ចំនួនទឹកប្រាក់សរុប', 'Total Paid Amount') }}
                        </span>
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight">
                            ${{ number_format($payment->amount + $payment->penalty_amount, 2) }}
                        </h2>
                        <span class="inline-block px-3 py-1 bg-slate-50 border border-slate-150 text-slate-600 rounded-lg text-xs font-bold mt-2">
                            {{ number_format(round(($payment->amount + $payment->penalty_amount) * $exchangeRate)) }} ៛
                        </span>
                    </div>
                    
                    <!-- Breakdown Section -->
                    <div class="border-t border-slate-100 pt-4 space-y-2.5 text-sm">
                        <div class="flex justify-between text-slate-500">
                            <span>{{ $L('ទឹកប្រាក់បង់រំលស់', 'Installment Amount') }}</span>
                            <span class="font-semibold text-slate-800">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>{{ $L('ប្រាក់ពិន័យ', 'Penalty Fee') }}</span>
                            <span class="font-semibold {{ $payment->penalty_amount > 0 ? 'text-red-600 font-bold' : 'text-slate-800' }}">
                                ${{ number_format($payment->penalty_amount, 2) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Stamp Barcode visual placeholder -->
                    <div class="border-t border-dashed border-slate-200 pt-6 flex flex-col items-center gap-1.5 opacity-60">
                        <div class="w-full h-8 bg-[repeating-linear-gradient(90deg,#000,#000_2px,transparent_2px,transparent_6px)]"></div>
                        <span class="text-[9px] font-mono text-slate-400">PAYMENT RECEIPT STAMP</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions for Admins (Approve / Reject) -->
            @if($pstatus === 'pending')
                @can('approve-payment', $payment)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-3">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        {{ $L('ការអនុម័តការទូទាត់', 'Payment Approvals') }}
                    </h4>
                    <div class="flex flex-col gap-2.5">
                        <form method="POST" action="{{ route('payments.approve', $payment) }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition duration-150 flex items-center justify-center gap-2 border-0 cursor-pointer shadow-sm">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ $L('អនុម័តការបង់ប្រាក់', 'Approve Payment') }}</span>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('payments.reject', $payment) }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-xl text-sm transition duration-150 flex items-center justify-center gap-2 border-0 cursor-pointer">
                                <i class="fas fa-times-circle"></i>
                                <span>{{ $L('បដិសេធការបង់ប្រាក់', 'Reject Payment') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            @endif
        </div>
        
        <!-- Right Side: Details list -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Details Grid Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-8">
                <!-- Customer Details section -->
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-slate-400 text-lg"></i>
                        <span>{{ $L('ព័ត៌មានអតិថិជន', 'Customer Information') }}</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-xs text-slate-400 font-medium block">{{ $L('ឈ្មោះអតិថិជន', 'Customer Name') }}</span>
                            <a href="{{ route('customers.show', $payment->installment->customer_id ?? 0) }}" class="text-base font-bold text-slate-800 hover:text-blue-600 transition">
                                {{ $payment->installment?->customer?->name ?? 'N/A' }}
                            </a>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-medium block">{{ $L('លេខទូរស័ព្ទ', 'Phone Number') }}</span>
                            <span class="text-base font-semibold text-slate-800">
                                {{ $payment->installment?->customer?->phone ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Transaction detail section -->
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-receipt text-slate-400 text-lg"></i>
                        <span>{{ $L('ព័ត៌មានប្រតិបត្តិការ', 'Transaction Details') }}</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100">
                                <i class="fas {{ $methodIcon }} text-base"></i>
                            </span>
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">{{ __('app.payment_method') }}</span>
                                <span class="text-base font-bold text-slate-800">
                                    {{ __('app.' . $methodKey) ?: ($payment->paymentMethod->name ?? 'N/A') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100">
                                <i class="fas fa-calendar-alt text-blue-500 text-base"></i>
                            </span>
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">{{ __('app.date') }}</span>
                                <span class="text-base font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        @if($payment->approved_by)
                        <div class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100">
                                <i class="fas fa-user-check text-emerald-500 text-base"></i>
                            </span>
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">{{ $L('អនុម័តដោយ', 'Approved By') }}</span>
                                <span class="text-base font-bold text-slate-800">
                                    {{ $payment->user?->name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- CC Details if CC payment -->
                @if($payment->title && $methodKey === 'credit_card')
                <div class="pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-shield-halved text-indigo-500 text-lg"></i>
                        <span>{{ $L('ព័ត៌មានកាតឥណទានសុវត្ថិភាព', 'Secure Credit Card Details') }}</span>
                    </h3>
                    <div class="text-sm font-semibold text-slate-800 bg-indigo-50/20 border border-indigo-100 p-4 rounded-xl flex items-center gap-3">
                        <i class="fas fa-credit-card text-indigo-600 text-xl"></i>
                        <div class="leading-relaxed text-slate-700">
                            {{ $payment->title }}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Payment proof / Receipt Image attachment -->
            @if($payment->qr_image)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-file-image text-slate-400 text-lg"></i>
                    <span>{{ $L('រូបភាពបង្កាន់ដៃបង់ប្រាក់ / ភស្តុតាង', 'Payment Slip / Receipt Image') }}</span>
                </h3>
                <div class="relative group max-w-xs overflow-hidden rounded-xl border border-slate-150 shadow-sm transition duration-300 hover:shadow-md">
                    <img 
                        src="{{ asset('storage/' . $payment->qr_image) }}" 
                        alt="Receipt / QR proof" 
                        class="w-full h-auto object-cover transition duration-300 group-hover:scale-105 cursor-zoom-in"
                        onclick="openImageModal(this.src)"
                    >
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition duration-300 cursor-zoom-in" onclick="openImageModal(this.previousElementSibling.src)">
                        <i class="fas fa-search-plus text-white text-xl"></i>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
    </div>
</div>

<!-- Image Lightbox Modal -->
<div id="imageModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4" onclick="closeImageModal()">
    <button class="absolute top-6 right-6 text-white text-3xl font-bold bg-transparent border-0 cursor-pointer hover:text-slate-350 transition">&times;</button>
    <img id="modalImg" src="" alt="Proof Enlarged" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl" onclick="event.stopPropagation()">
</div>

<script>
function openImageModal(src) {
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('modalImg');
    img.src = src;
    modal.classList.remove('hidden');
}
function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>
@endsection