@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800" lang="km">{{ __('app.contracts') }}</h1>
            <p class="text-sm text-gray-500 mt-1" lang="km">{{ __('app.contracts_sub') }}</p>
        </div>

        <div class="flex flex-nowrap items-center gap-1.5 sm:gap-2 shrink-0 overflow-x-auto max-w-full">
            {{-- 1. Payment Schedule --}}
            <a href="{{ route('installments.schedule-index') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-calendar-alt text-indigo-600"></i>
                <span>{{ __('app.payment_schedule') }}</span>
            </a>

            {{-- 2. Contracts --}}
            <a href="{{ route('installments.contract-index') }}" class="inline-flex items-center gap-1.5 bg-purple-50 hover:bg-purple-100/80 text-purple-700 font-semibold text-xs px-3 py-2 rounded-xl border border-purple-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-file-signature text-purple-600"></i>
                <span>{{ __('app.contracts') }}</span>
            </a>

            {{-- 3. Pay Off --}}
            <a href="{{ route('installments.pay-off-index') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-hand-holding-usd text-emerald-600"></i>
                <span>{{ __('app.pay_off') }}</span>
            </a>

            {{-- 4. Clearance Certificates --}}
            <a href="{{ route('installments.clearance-index') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-certificate text-amber-600"></i>
                <span>{{ __('app.clearance_certificates') }}</span>
            </a>

            {{-- 5. Return Customer --}}
            <a href="{{ route('installments.return-index') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl border border-slate-200 shadow-sm transition whitespace-nowrap shrink-0">
                <i class="fas fa-undo-alt text-rose-600"></i>
                <span>{{ app()->getLocale() === 'km' ? 'ដកហូត / យកអីវ៉ាន់វិញ' : 'Returns' }}</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm">
            <ul class="list-disc pl-5 space-y-1 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Search Bar & Add Button Row --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <form method="GET" action="{{ route('installments.contract-index') }}" class="flex flex-col sm:flex-row gap-3 flex-1 max-w-xl">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}" autocomplete="off"
                       placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកកិច្ចសន្យា (ឈ្មោះអតិថិជន)...' : 'Search contracts (customer)...' }}"
                       class="w-full pl-10 pr-9 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">

                @if(request('search'))
                <button type="button" onclick="clearSearchInput(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" title="Clear">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif

                <div id="suggestions-box" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"></div>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors shrink-0">
                {{ __('app.search') }}
            </button>
            @if(request('search'))
            <a href="{{ route('installments.contract-index') }}" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors text-center shrink-0">
                {{ __('app.clear') }}
            </a>
            @endif
        </form>

        {{-- Add Contract / Create Installment Button --}}
        <a href="{{ route('installments.create') }}" class="inline-flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md hover:shadow-lg transition shrink-0">
            <i class="fas fa-plus"></i>
            <span lang="km">{{ app()->getLocale() === 'km' ? 'បង្កើតកិច្ចសន្យាថ្មី' : 'Create Contract' }}</span>
        </a>
    </div>

    <!-- Plans Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.customer') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.product') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.total_price') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.contract') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($installments as $installment)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $installment->customer?->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-400">#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <div class="max-w-[150px] lg:max-w-[170px] truncate" title="{{ $installment->product?->name ?? 'N/A' }}">
                                {{ $installment->product?->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ format_currency($installment->total_price) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($installment->signed_contract)
                                <div class="inline-flex flex-col items-start gap-1">
                                    <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ __('app.signed') }}
                                    </span>
                                    <div class="flex items-center gap-2 text-[11px] text-gray-500">
                                        <a href="{{ asset('storage/' . $installment->signed_contract) }}" target="_blank" class="text-blue-600 hover:underline inline-flex items-center gap-0.5">
                                            <i class="fas fa-eye text-[10px]"></i> ឯកសារ
                                        </a>
                                        <span>•</span>
                                        <button type="button" onclick="openUploadModal({{ $installment->id }}, '{{ addslashes($installment->customer?->name ?? '') }}')" class="text-amber-600 hover:underline">
                                            ប្តូរ
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="inline-flex flex-col items-start gap-1">
                                    <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        {{ __('app.not_signed') }}
                                    </span>
                                    <button type="button" onclick="openUploadModal({{ $installment->id }}, '{{ addslashes($installment->customer?->name ?? '') }}')" class="text-[11px] text-purple-600 hover:text-purple-800 font-semibold hover:underline inline-flex items-center gap-1">
                                        <i class="fas fa-upload text-[10px]"></i> បញ្ចូលឯកសារ
                                    </button>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('installments.contract', $installment) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3.5 py-2 rounded-lg transition duration-150 text-xs shadow-sm">
                                <i class="fas fa-print"></i>
                                <span>{{ __('app.view_contract') }}</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-500">{{ __('app.no_installments') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $installments->links() }}
    </div>
</div>

<!-- Upload Contract Modal -->
<div id="uploadContractModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 animate-fadeIn">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/60">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-file-signature text-purple-600"></i>
                <span id="modalTitle">{{ app()->getLocale() === 'km' ? 'បញ្ចូលឯកសារកិច្ចសន្យា' : 'Upload Contract Document' }}</span>
            </h3>
            <button type="button" onclick="closeUploadModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="uploadContractForm" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <p class="text-xs text-slate-500 mb-2">
                    {{ app()->getLocale() === 'km' ? 'អតិថិជន:' : 'Customer:' }}
                    <strong id="modalCustomerName" class="text-slate-800 text-sm"></strong>
                </p>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ app()->getLocale() === 'km' ? 'ជ្រើសរើសឯកសារកិច្ចសន្យា (បានចុះហត្ថលេខារួច)' : 'Select Signed Contract File' }}
                </label>
                <input type="file" name="contract_file" id="modalContractFileInput" accept=".pdf,.jpg,.jpeg,.png" required
                       class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 border border-slate-200 rounded-lg cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">PDF, JPG, JPEG, PNG (អតិបរមា 5MB)</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeUploadModal()" class="px-4 py-2 font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition text-xs">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit" class="px-5 py-2 font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow-md transition text-xs flex items-center gap-1.5">
                    <i class="fas fa-upload"></i>
                    <span>{{ app()->getLocale() === 'km' ? 'រក្សាទុកឯកសារ' : 'Upload' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUploadModal(installmentId, customerName) {
        const modal = document.getElementById('uploadContractModal');
        const form = document.getElementById('uploadContractForm');
        const customerNameEl = document.getElementById('modalCustomerName');
        const fileInput = document.getElementById('modalContractFileInput');

        if (form) {
            form.action = `/installments/${installmentId}/upload-contract`;
        }
        if (customerNameEl) {
            customerNameEl.textContent = customerName || '';
        }
        if (fileInput) {
            fileInput.value = '';
        }
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeUploadModal() {
        const modal = document.getElementById('uploadContractModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function clearSearchInput(btn) {
        const input = document.getElementById('search-input');
        if (input) {
            input.value = '';
            input.closest('form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const suggestions = @json($suggestions ?? []);
        const input = document.getElementById('search-input');
        const box = document.getElementById('suggestions-box');

        if (!input || !box) return;

        function filterSuggestions(val) {
            if (!val || val.trim().length < 1) {
                box.innerHTML = '';
                box.classList.add('hidden');
                return;
            }

            const query = val.toLowerCase();
            const matches = suggestions.filter(item => 
                item.label.toLowerCase().includes(query) || 
                item.value.toLowerCase().includes(query)
            ).slice(0, 8);

            if (matches.length === 0) {
                box.innerHTML = '';
                box.classList.add('hidden');
                return;
            }

            box.innerHTML = matches.map(match => {
                return `
                    <div class="suggestion-item px-4 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700 transition duration-150 font-medium border-b border-gray-50 last:border-0" data-value="${escapeHtml(match.value)}">
                        ${escapeHtml(match.label)}
                    </div>
                `;
            }).join('');

            box.classList.remove('hidden');
        }

        function escapeHtml(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        input.addEventListener('input', function() {
            filterSuggestions(this.value);
            const urlParams = new URLSearchParams(window.location.search);
            if (this.value.trim() === '' && urlParams.has('search') && urlParams.get('search') !== '') {
                this.closest('form').submit();
            }
        });

        input.addEventListener('focus', function() {
            filterSuggestions(this.value);
        });

        box.addEventListener('click', function(e) {
            const item = e.target.closest('.suggestion-item');
            if (item) {
                input.value = item.getAttribute('data-value');
                box.classList.add('hidden');
                input.closest('form').submit();
            }
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !box.contains(e.target)) {
                box.classList.add('hidden');
            }
        });
    });
</script>
@endsection
