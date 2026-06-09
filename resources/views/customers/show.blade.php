@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('customers.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $customer->name }}</h1>
                    @if($latestCredit)
                        @php $rc = $latestCredit->risk_color; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold
                            {{ $rc === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($rc === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                            {{ ucfirst($latestCredit->risk_level) }} Risk
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('app.customer_id') }}: #{{ $customer->id }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('customers.edit', $customer) }}"
               class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('app.edit') }}
            </a>
            <a href="{{ route('installments.create', ['customer_id' => $customer->id]) }}"
               class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('app.new_installment') }}
            </a>
        </div>
    </div>

    {{-- ── Stats Row ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ __('app.total_installments') }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $installments->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ __('app.total_paid') }}</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($totalPaid, 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ __('app.balance_due') }}</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">${{ number_format($totalBalance, 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ __('app.overdue') }}</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $totalLate }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left: Profile Card ── --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                @if($customer->photo)
                    <img src="{{ asset('storage/' . $customer->photo) }}" alt="{{ $customer->name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md mx-auto">
                @else
                    <div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center mx-auto shadow-md">
                        <span class="text-white text-3xl font-bold">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                    </div>
                @endif
                <h2 class="text-lg font-bold text-gray-800 mt-4">{{ $customer->name }}</h2>
                @if($customer->gender)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                        {{ $customer->gender === 'male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                        {{ __('app.' . $customer->gender) }}
                    </span>
                @endif

                <div class="mt-4 space-y-2.5 text-left border-t border-gray-50 pt-4">
                    @if($customer->phone)
                    <div class="flex items-center gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        {{ $customer->phone }}
                    </div>
                    @endif
                    @if($customer->dob)
                    <div class="flex items-center gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{ $customer->dob->format('d M Y') }} ({{ $customer->age }}y)
                    </div>
                    @endif
                    @if($customer->id_card)
                    <div class="flex items-center gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                            </svg>
                        </div>
                        <span class="font-mono text-xs">{{ $customer->id_card }}</span>
                    </div>
                    @endif
                    @if($customer->address)
                    <div class="flex items-start gap-2.5 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        {{ $customer->address }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Documents --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">{{ __('app.documents') }}</h3>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['key' => 'id_card_photo', 'label' => __('app.id_card_photo'), 'emoji' => '🪪'],
                        ['key' => 'income_proof',  'label' => __('app.income_proof'),  'emoji' => '💰'],
                    ] as $doc)
                    <div>
                        <p class="text-xs text-gray-400 mb-1.5">{{ $doc['emoji'] }} {{ $doc['label'] }}</p>
                        @if($customer->{$doc['key']})
                            @php $ext = pathinfo($customer->{$doc['key']}, PATHINFO_EXTENSION); @endphp
                            @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ asset('storage/' . $customer->{$doc['key']}) }}" target="_blank"
                                   onclick="openLightbox(this.href); return false;">
                                    <img src="{{ asset('storage/' . $customer->{$doc['key']}) }}"
                                         alt="{{ $doc['label'] }}"
                                         class="w-full h-20 object-cover rounded-lg border border-gray-200 hover:opacity-90 transition-opacity cursor-zoom-in">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $customer->{$doc['key']}) }}" target="_blank"
                                   class="flex flex-col items-center justify-center h-20 rounded-lg border border-dashed border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors gap-1">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-xs text-blue-600 font-medium">{{ __('app.view') }} PDF</span>
                                </a>
                            @endif
                        @else
                            <div class="flex items-center justify-center h-20 rounded-lg border border-dashed border-gray-100 bg-gray-50">
                                <span class="text-xs text-gray-300">{{ __('app.not_uploaded') }}</span>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Right: Tabs ── --}}

        <div class="lg:col-span-2">

            {{-- Tab Nav --}}
            <div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-5" id="tab-nav">
                @php
                $activeTab = session('activeTab', 'installments');
                if(request()->has('tab')) $activeTab = request('tab');
                @endphp
                @foreach([
                    ['id' => 'installments', 'label' => __('app.installment_plans'), 'count' => $installments->count()],
                    ['id' => 'payments',     'label' => __('app.payments'),           'count' => $payments->count()],
                    ['id' => 'guarantors',   'label' => __('app.guarantors'),         'count' => $guarantors->count()],
                    ['id' => 'credit',       'label' => __('app.credit_check'),       'count' => $creditChecks->count()],
                ] as $tab)
                <button onclick="switchTab('{{ $tab['id'] }}')"
                        id="tab-btn-{{ $tab['id'] }}"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-sm font-medium transition-all
                            {{ $activeTab === $tab['id'] ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $tab['label'] }}
                    @if($tab['count'] > 0)
                        <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                            {{ $activeTab === $tab['id'] ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-500' }}">
                            {{ $tab['count'] }}
                        </span>
                    @endif
                </button>
                @endforeach
            </div>

            {{-- ── TAB: Installments ── --}}
            <div id="tab-installments" class="{{ $activeTab !== 'installments' ? 'hidden' : '' }} space-y-3">
                @forelse($installments as $inst)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-gray-800">{{ $inst->product->name ?? 'N/A' }}</span>
                                @php $sc = ['active'=>'bg-emerald-100 text-emerald-700','paid'=>'bg-blue-100 text-blue-700','overdue'=>'bg-red-100 text-red-600','cancelled'=>'bg-gray-100 text-gray-500']; @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc[$inst->status] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ __('app.' . $inst->status) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                                <span>💵 ${{ number_format($inst->monthly_payment, 2) }}/{{ __('app.months') }}</span>
                                <span>📅 {{ $inst->duration_months }} {{ __('app.months') }}</span>
                                @if($inst->next_due_date)
                                    <span class="{{ \Carbon\Carbon::parse($inst->next_due_date)->isPast() ? 'text-red-500 font-semibold' : '' }}">
                                        🔔 {{ __('app.next_due_date') }}: {{ \Carbon\Carbon::parse($inst->next_due_date)->format('d M Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-sm font-bold text-gray-800">${{ number_format($inst->remaining_balance, 2) }}</div>
                            <div class="text-xs text-gray-400">{{ __('app.remaining_balance') }}</div>
                        </div>
                    </div>
                    @if($inst->total_price > 0)
                    @php $pct = round((($inst->total_price - $inst->remaining_balance) / $inst->total_price) * 100); @endphp
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span>{{ $pct }}% {{ __('app.paid') }}</span>
                            <span>{{ __('app.total') }}: ${{ number_format($inst->total_price, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $pct >= 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-blue-500' : 'bg-amber-400') }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400 text-sm">
                    {{ __('app.no_installments') }}
                </div>
                @endforelse
            </div>

            {{-- ── TAB: Payment History ── --}}
            <div id="tab-payments" class="{{ $activeTab !== 'payments' ? 'hidden' : '' }}">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    {{-- Payment summary bar --}}
                    <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
                        <div class="p-4 text-center">
                            <div class="text-xs text-gray-400 font-medium">{{ __('app.approved') }}</div>
                            <div class="text-lg font-bold text-emerald-600 mt-0.5">${{ number_format($payments->where('status','approved')->sum('amount'), 2) }}</div>
                        </div>
                        <div class="p-4 text-center">
                            <div class="text-xs text-gray-400 font-medium">{{ __('app.pending') }}</div>
                            <div class="text-lg font-bold text-amber-500 mt-0.5">${{ number_format($totalPending, 2) }}</div>
                        </div>
                        <div class="p-4 text-center">
                            <div class="text-xs text-gray-400 font-medium">{{ __('app.rejected') }}</div>
                            <div class="text-lg font-bold text-red-500 mt-0.5">${{ number_format($payments->where('status','rejected')->sum('amount'), 2) }}</div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.date') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.installment') }}</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.amount') }}</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.payment_method') }}</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($payments as $pay)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3.5 text-gray-600 text-xs">
                                        {{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600 text-xs">
                                        {{ $pay->installment->product->name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-bold text-gray-800">
                                        ${{ number_format($pay->amount, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if($pay->paymentMethod)
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $pay->paymentMethod->name }}</span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @php $sc2 = ['approved'=>'bg-emerald-100 text-emerald-700','pending'=>'bg-amber-100 text-amber-700','rejected'=>'bg-red-100 text-red-600']; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc2[$pay->status] ?? 'bg-gray-100 text-gray-500' }}">
                                            {{ __('app.' . $pay->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">{{ __('app.no_payments') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── TAB: Guarantors ── --}}
            <div id="tab-guarantors" class="{{ $activeTab !== 'guarantors' ? 'hidden' : '' }} space-y-4">

                {{-- Add Guarantor Form --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <button onclick="toggleSection('guarantor-form')"
                            class="w-full flex items-center justify-between px-5 py-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('app.add_guarantor') }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" id="guarantor-form-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="guarantor-form" class="hidden border-t border-gray-100">
                        <form method="POST" action="{{ route('guarantors.store', $customer) }}" enctype="multipart/form-data" class="p-5 space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.full_name') }} <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required placeholder="Guarantor full name"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                                    <input type="text" name="phone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Relationship</label>
                                    <input type="text" name="relationship" placeholder="e.g., Brother, Sister, Friend, etc."
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Gender</label>
                                    <select name="gender" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">—</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.id_card') }}</label>
                                    <input type="text" name="id_card" placeholder="012345678"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.occupation') }}</label>
                                    <input type="text" name="occupation" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.monthly_income') }} ($)</label>
                                    <input type="number" name="monthly_income" step="0.01" min="0"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.address') }}</label>
                                    <textarea name="address" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.photo') }}</label>
                                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-gray-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.id_card_photo') }}</label>
                                    <input type="file" name="id_card_photo" accept="image/*" class="w-full text-xs text-gray-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.notes') }}</label>
                                    <textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    {{ __('app.add_guarantor') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Guarantor List --}}
                @forelse($guarantors as $g)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start gap-4">
                        {{-- Avatar --}}
                        @if($g->photo)
                            <img src="{{ asset('storage/' . $g->photo) }}" class="w-12 h-12 rounded-full object-cover border border-gray-200 flex-shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold">{{ strtoupper(substr($g->name,0,1)) }}</span>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-gray-800">{{ $g->name }}</span>
                                @if($g->relationship)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        {{ ucfirst($g->relationship) }}
                                    </span>
                                @endif
                                @if($g->gender)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $g->gender === 'male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ ucfirst($g->gender) }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1 text-xs text-gray-500">
                                @if($g->phone)<span>📞 {{ $g->phone }}</span>@endif
                                @if($g->id_card)<span>🪪 <span class="font-mono">{{ $g->id_card }}</span></span>@endif
                                @if($g->occupation)<span>💼 {{ $g->occupation }}</span>@endif
                                @if($g->monthly_income)<span>💰 ${{ number_format($g->monthly_income, 0) }}/month</span>@endif
                            </div>
                            @if($g->address)
                                <div class="text-xs text-gray-400 mt-0.5">📍 {{ $g->address }}</div>
                            @endif
                            @if($g->notes)
                                <div class="text-xs text-gray-500 bg-gray-50 rounded p-2 mt-2">{{ $g->notes }}</div>
                            @endif
                            @if($g->id_card_photo)
                                <a href="{{ asset('storage/' . $g->id_card_photo) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-1">
                                    🪪 View ID Card Photo
                                </a>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <form method="POST" action="{{ route('guarantors.destroy', [$customer, $g]) }}"
                                  style="display:inline;" onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Delete" class="text-red-400 hover:text-red-600 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400 text-sm">
                    {{ __('app.no_guarantors') }}
                </div>
                @endforelse
            </div>

            {{-- ── TAB: Credit Check ── --}}
            <div id="tab-credit" class="{{ $activeTab !== 'credit' ? 'hidden' : '' }} space-y-4">

                {{-- Credit Score Summary --}}
                @if($latestCredit)
                @php
                    $score = $latestCredit->credit_score;
                    $rc    = $latestCredit->risk_color;
                    $barColor = $rc === 'emerald' ? '#10b981' : ($rc === 'amber' ? '#f59e0b' : '#ef4444');
                @endphp
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Latest Credit Assessment</h3>
                    <div class="flex items-center gap-6">
                        {{-- Score Circle --}}
                        <div class="relative w-24 h-24 flex-shrink-0">
                            <svg class="w-24 h-24 -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f5f9" stroke-width="3"/>
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $barColor }}" stroke-width="3"
                                        stroke-dasharray="{{ $score }}, 100" stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xl font-bold text-gray-800">{{ $score }}</span>
                                <span class="text-xs text-gray-400">/ 100</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg font-bold text-gray-800">
                                    {{ $score >= 70 ? __('app.credit_good') : ($score >= 40 ? __('app.credit_fair') : __('app.credit_poor')) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                    {{ $rc === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($rc === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                                    {{ __('app.' . $latestCredit->risk_level . '_risk') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                    {{ $latestCredit->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($latestCredit->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700') }}">
                                    {{ __('app.' . $latestCredit->status) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-500">
                                @if($latestCredit->employment_status)
                                    <span>💼 {{ ucfirst(str_replace('-', ' ', $latestCredit->employment_status)) }}</span>
                                @endif
                                @if($latestCredit->monthly_income)
                                    <span>💰 ${{ number_format($latestCredit->monthly_income, 0) }}/{{ __('app.months') }}</span>
                                @endif
                                @if($latestCredit->existing_debt)
                                    <span>💳 ${{ number_format($latestCredit->existing_debt, 0) }} {{ __('app.existing_debt') }}</span>
                                @endif
                                <span>👤 {{ __('app.checked_by') }}: {{ $latestCredit->checker->name ?? '—' }}</span>
                                <span class="col-span-2">📅 {{ $latestCredit->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($latestCredit->notes)
                                <div class="mt-2 text-xs text-gray-500 bg-gray-50 rounded p-2">{{ $latestCredit->notes }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- New Credit Check Form --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <button onclick="toggleSection('credit-form')"
                            class="w-full flex items-center justify-between px-5 py-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ __('app.new_assessment') }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400" id="credit-form-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="credit-form" class="hidden border-t border-gray-100">
                        <form method="POST" action="{{ route('credit-checks.store', $customer) }}" class="p-5 space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.employment_status') }}</label>
                                    <input type="text" name="employment_status" placeholder="e.g., Employed, Self-employed, Student, etc."
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           oninput="calcScore()">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.monthly_income') }} ($)</label>
                                    <input type="number" name="monthly_income" step="0.01" min="0" placeholder="0.00"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           oninput="calcScore()">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.existing_debt') }} ($)</label>
                                    <input type="number" name="existing_debt" step="0.01" min="0" value="0" placeholder="0.00"
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           oninput="calcScore()">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.decision') }}</label>
                                    <input type="text" name="status" required placeholder="e.g., Pending, Approved, Rejected, etc."
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.notes') }}</label>
                                    <textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                </div>
                            </div>

                            {{-- Live Score Preview --}}
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-gray-600">{{ __('app.est_credit_score') }}</span>
                                    <span id="score-preview" class="text-lg font-bold text-gray-800">—</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="score-bar" class="h-2 rounded-full bg-gray-300 transition-all duration-500" style="width:0%"></div>
                                </div>
                                <div id="score-label" class="text-xs text-gray-400 mt-1">{{ __('app.score_hint') }}</div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    {{ __('app.save_assessment') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Credit History --}}
                @if($creditChecks->count() > 0)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-700">{{ __('app.assessment_history') }}</h4>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($creditChecks as $cc)
                        <div class="px-5 py-3.5 flex items-center gap-4">
                            @php $ccRc = $cc->risk_color; @endphp
                            <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $ccRc === 'emerald' ? 'bg-emerald-100' : ($ccRc === 'amber' ? 'bg-amber-100' : 'bg-red-100') }}">
                                <span class="text-sm font-bold {{ $ccRc === 'emerald' ? 'text-emerald-700' : ($ccRc === 'amber' ? 'text-amber-700' : 'text-red-600') }}">
                                    {{ $cc->credit_score }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-700">{{ __('app.credit_score') }}: {{ $cc->credit_score }}/100</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $cc->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($cc->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700') }}">
                                        {{ __('app.' . $cc->status) }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $cc->created_at->format('d M Y') }} · {{ __('app.checked_by') }}: {{ $cc->checker->name ?? '—' }}
                                    @if($cc->monthly_income) · ${{ number_format($cc->monthly_income,0) }}/{{ __('app.months') }} @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

        </div>{{-- end right --}}
    </div>{{-- end grid --}}
</div>

<script>
function switchTab(id) {
    ['installments','payments','guarantors','credit'].forEach(t => {
        document.getElementById('tab-' + t).classList.add('hidden');
        document.getElementById('tab-btn-' + t).classList.remove('bg-white','text-gray-800','shadow-sm');
        document.getElementById('tab-btn-' + t).classList.add('text-gray-500');
    });
    document.getElementById('tab-' + id).classList.remove('hidden');
    document.getElementById('tab-btn-' + id).classList.add('bg-white','text-gray-800','shadow-sm');
    document.getElementById('tab-btn-' + id).classList.remove('text-gray-500');
}

function toggleSection(id) {
    const el = document.getElementById(id);
    const chevron = document.getElementById(id + '-chevron');
    el.classList.toggle('hidden');
    if (chevron) chevron.style.transform = el.classList.contains('hidden') ? '' : 'rotate(180deg)';
}

function calcScore() {
    const income = parseFloat(document.querySelector('[name=monthly_income]')?.value) || 0;
    const debt   = parseFloat(document.querySelector('[name=existing_debt]')?.value) || 0;
    const employ = document.querySelector('[name=employment_status]')?.value;

    let score = 50;
    if (employ === 'employed')      score += 25;
    else if (employ === 'self-employed') score += 15;
    else if (employ === 'student')  score += 5;

    if (income > 0) {
        const ratio = debt / income;
        if (ratio < 0.3)      score += 25;
        else if (ratio < 0.5) score += 10;
        else if (ratio < 0.8) score -= 10;
        else                  score -= 25;
    }
    score = Math.max(0, Math.min(100, score));

    const preview = document.getElementById('score-preview');
    const bar     = document.getElementById('score-bar');
    const label   = document.getElementById('score-label');
    if (!preview) return;

    preview.textContent = score;
    bar.style.width = score + '%';

    if (score >= 70) {
        bar.className = 'h-2 rounded-full bg-emerald-500 transition-all duration-500';
        label.textContent = '✅ {{ __("app.credit_good") }} — {{ __("app.low_risk") }}';
        label.className = 'text-xs text-emerald-600 mt-1 font-medium';
    } else if (score >= 40) {
        bar.className = 'h-2 rounded-full bg-amber-400 transition-all duration-500';
        label.textContent = '⚠️ {{ __("app.credit_fair") }} — {{ __("app.medium_risk") }}';
        label.className = 'text-xs text-amber-600 mt-1 font-medium';
    } else {
        bar.className = 'h-2 rounded-full bg-red-500 transition-all duration-500';
        label.textContent = '❌ {{ __("app.credit_poor") }} — {{ __("app.high_risk") }}';
        label.className = 'text-xs text-red-600 mt-1 font-medium';
    }
}

// Auto-open tab from URL fragment
document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace('#tab-', '');
    if (['installments','payments','guarantors','credit'].includes(hash)) {
        switchTab(hash);
    }
});

// ── Lightbox ──
function openLightbox(src) {
    const lb = document.getElementById('lightbox');
    document.getElementById('lightbox-img').src = src;
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    const lb = document.getElementById('lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>

{{-- Lightbox Modal --}}
<div id="lightbox" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/80 p-4"
     onclick="closeLightbox()">
    <div class="relative max-w-3xl w-full" onclick="event.stopPropagation()">
        <button onclick="closeLightbox()"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors flex items-center gap-1 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Close
        </button>
        <img id="lightbox-img" src="" alt="Document Preview"
             class="w-full max-h-[80vh] object-contain rounded-xl shadow-2xl">
    </div>
</div>

@endsection
