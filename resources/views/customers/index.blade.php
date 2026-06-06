@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.customer_management') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.manage_your_business_easily') ?? 'Manage all customers and their installment records' }}</p>
        </div>
        <a href="{{ route('customers.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('app.add_customer') }}
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('app.search_customer') }}"
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                {{ __('app.search') }}
            </button>
            @if(request('search'))
            <a href="{{ route('customers.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors text-center">
                {{ __('app.clear') }}
            </a>
            @endif
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ __('app.total_customers') }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $customers->total() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ __('app.showing') }}</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $customers->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 col-span-2 sm:col-span-1">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ __('app.search') }}</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ request('search') ? $customers->total() : __('app.all') }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.id') }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.customers') }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.phone') }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.id_card') }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.gender') }}</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.installments_count') }}</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                        <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">{{ $customer->id }}</td>
                        <td class="px-5 py-2.5">
                            <div class="flex items-center gap-3">
                                @if($customer->photo)
                                    <img src="{{ asset('storage/' . $customer->photo) }}" alt="{{ $customer->name }}"
                                         class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-sm font-bold">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $customer->name }}</div>
                                    @if($customer->address)
                                        <div class="text-xs text-gray-400 truncate max-w-[160px]">{{ $customer->address }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-2.5 text-gray-600">{{ $customer->phone ?? '—' }}</td>
                        <td class="px-5 py-2.5">
                            @if($customer->id_card)
                                <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $customer->id_card }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5">
                            @if($customer->gender === 'male')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                                    {{ __('app.male') }}
                                </span>
                            @elseif($customer->gender === 'female')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-700">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                                    {{ __('app.female') }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5">
                            @php $count = $customer->installments->count(); @endphp
                            @if($count > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    {{ $count }} {{ __('app.plans') }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">{{ __('app.none') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('customers.show', $customer) }}"
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ __('app.view') }}
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}"
                                   class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-800 text-xs font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    {{ __('app.edit') }}
                                </a>
                                @if(auth()->user()->role === 'admin')
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                      onsubmit="return confirm('{{ __('app.confirm_delete') }} {{ addslashes($customer->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-medium transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        {{ __('app.delete') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-gray-400 font-medium">{{ __('app.no_customers') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
