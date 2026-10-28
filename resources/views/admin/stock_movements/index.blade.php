@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.stock_movements') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.stock_movements_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.products.stock') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('app.back_to_manage_stock') }}
        </a>
    </div>

    <!-- Search Bar -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.stock-movements.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        id="search-input"
                        value="{{ request('search') }}" 
                        autocomplete="off"
                        class="block w-full pl-10 pr-9 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm transition duration-150" 
                        placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកតាមឈ្មោះ, លេខកូដ, ឬអ្នកផ្គត់ផ្គង់...' : 'Search by product name, code, or supplier...' }}"
                    >

                    @if(request('search'))
                    <button type="button" onclick="clearSearchInput(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" title="Clear">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif

                    <div id="suggestions-box" class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"></div>
                </div>
            </div>
            <div class="flex gap-2">
                <select name="type" class="block py-2.5 px-4 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm transition duration-150">
                    <option value="">{{ __('app.all_types') }}</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>{{ __('app.in_stock_in') }}</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>{{ __('app.out_stock_out') }}</option>
                </select>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    {{ __('app.search') }}
                </button>
                @if(request('search') || request('type'))
                    <a href="{{ route('admin.stock-movements.index') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        {{ __('app.clear') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.date') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.product') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.type') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.quantity') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.supplier') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.note_reason') }}</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.actions') ?? 'សកម្មភាព' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $movement->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $movement->product->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $movement->product->code ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($movement->type === 'in')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    IN
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    OUT
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $movement->supplier->name ?? '—' }}
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $movement->note }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- View Detail Button (For both Admin & Staff) --}}
                                <button type="button" 
                                    onclick="showMovementDetail({
                                        date: '{{ $movement->created_at->format('d M Y, H:i') }}',
                                        product: '{{ addslashes($movement->product->name ?? 'N/A') }}',
                                        code: '{{ addslashes($movement->product->code ?? '—') }}',
                                        type: '{{ strtoupper($movement->type) }}',
                                        qty: '{{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}',
                                        supplier: '{{ addslashes($movement->supplier->name ?? '—') }}',
                                        note: '{{ addslashes($movement->note ?? '—') }}'
                                    })"
                                    class="p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-800 rounded-lg transition" 
                                    title="{{ __('app.view') ?? 'មើលលម្អិត' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                @php
                                    $currentUser = auth()->user();
                                    $isAdmin = $currentUser && (
                                        strtolower($currentUser->role ?? '') === 'admin' 
                                        || (method_exists($currentUser, 'hasRole') && ($currentUser->hasRole('Admin') || $currentUser->hasRole('admin')))
                                    );
                                @endphp

                                {{-- Delete Button (Admin Only) --}}
                                @if($isAdmin)
                                    <form action="{{ route('admin.stock-movements.destroy', $movement->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកពិតជាចង់លុបប្រវត្តិនេះចេញមែនទេ?' : 'Are you sure you want to delete this movement record?' }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-700 rounded-lg transition" title="{{ __('app.delete') ?? 'លុប' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">{{ __('app.no_movements') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $movements->links() }}
    </div>
</div>

<script>
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

    function showMovementDetail(data) {
        document.getElementById('modal-date').textContent = data.date;
        document.getElementById('modal-product').textContent = data.product;
        document.getElementById('modal-code').textContent = data.code;
        document.getElementById('modal-type').textContent = data.type;
        document.getElementById('modal-qty').textContent = data.qty;
        document.getElementById('modal-supplier').textContent = data.supplier;
        document.getElementById('modal-note').textContent = data.note;

        const typeBadge = document.getElementById('modal-type');
        if (data.type === 'IN') {
            typeBadge.className = 'px-3 py-1 inline-flex text-xs font-bold rounded-full bg-emerald-100 text-emerald-800';
            document.getElementById('modal-qty').className = 'text-base font-bold text-emerald-600';
        } else {
            typeBadge.className = 'px-3 py-1 inline-flex text-xs font-bold rounded-full bg-rose-100 text-rose-800';
            document.getElementById('modal-qty').className = 'text-base font-bold text-rose-600';
        }

        document.getElementById('movementDetailModal').classList.remove('hidden');
    }

    function closeMovementDetail() {
        document.getElementById('movementDetailModal').classList.add('hidden');
    }
</script>

<!-- Movement Detail Modal -->
<div id="movementDetailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 animate-fadeIn">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/60">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-indigo-600"></i>
                <span>{{ app()->getLocale() === 'km' ? 'ព័ត៌មានលម្អិតចលនាស្តុក' : 'Stock Movement Detail' }}</span>
            </h3>
            <button type="button" onclick="closeMovementDetail()" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('app.date') }}</span>
                <span id="modal-date" class="text-sm font-medium text-gray-800"></span>
            </div>
            <div class="flex justify-between items-start py-2 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('app.product') }}</span>
                <div class="text-right">
                    <div id="modal-product" class="text-sm font-bold text-gray-900"></div>
                    <div id="modal-code" class="text-xs text-gray-500"></div>
                </div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('app.type') }}</span>
                <span id="modal-type" class="px-2.5 py-1 text-xs font-bold rounded-full"></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('app.quantity') }}</span>
                <span id="modal-qty" class="text-base font-bold"></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('app.supplier') }}</span>
                <span id="modal-supplier" class="text-sm font-medium text-gray-800"></span>
            </div>
            <div class="py-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">{{ __('app.note_reason') }}</span>
                <div id="modal-note" class="text-sm text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100 whitespace-pre-line"></div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeMovementDetail()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold rounded-xl transition">
                {{ __('app.close') ?? 'បិទ' }}
            </button>
        </div>
    </div>
</div>
@endsection