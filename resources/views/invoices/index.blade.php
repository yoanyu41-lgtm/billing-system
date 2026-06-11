@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">{{ __('app.invoices') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('app.manage_your_business_easily') }}</p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.total_invoices') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalInvoices) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.total_amount') }}</p>
                <p class="text-2xl font-bold text-emerald-600">${{ number_format($totalAmount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="{{ __('app.search') }}..."
                        class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                </div>
            </div>

            <!-- Date Filter -->
            <div class="w-full md:w-48">
                <input 
                    type="date" 
                    name="date" 
                    value="{{ request('date') }}"
                    class="px-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
            </div>

            <!-- Search Button -->
            <button 
                type="submit" 
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 flex items-center justify-center gap-2 text-sm"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>{{ __('app.search') }}</span>
            </button>

            <!-- Clear Button -->
            @if(request('search') || request('date'))
            <a 
                href="{{ route('invoices.index') }}" 
                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-150 flex items-center justify-center gap-2 text-sm"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span>{{ __('app.clear') }}</span>
            </a>
            @endif
        </form>

        <!-- Search Results Info -->
        @if(request('search') || request('date'))
        <div class="mt-3 pt-3 border-t border-gray-200 text-sm text-gray-600">
            <span class="font-medium">លទ្ធផលស្វែងរក / Search Results:</span>
            <span class="text-gray-900 font-semibold">{{ $invoices->total() }}</span> វិក្កយបត្រ / invoices found
            @if(request('search'))
                <span class="ml-2 px-2 py-1 bg-blue-50 text-blue-700 rounded">
                    "{{ request('search') }}"
                </span>
            @endif
            @if(request('date'))
                <span class="ml-2 px-2 py-1 bg-purple-50 text-purple-700 rounded">
                    📅 {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                </span>
            @endif
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Invoices Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.invoice_number') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.customer_name') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.amount') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.date') }}</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600">
                            {{ $invoice->invoice_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $invoice->payment?->installment?->customer?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            ${{ number_format($invoice->payment?->amount ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $pstatus = $invoice->payment?->status ?? 'approved'; @endphp
                            @php $statusColors = ['approved' => 'bg-emerald-100 text-emerald-700', 'pending' => 'bg-amber-100 text-amber-700', 'rejected' => 'bg-red-100 text-red-600']; @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$pstatus] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ __('app.'.$pstatus) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $invoice->created_at?->format('Y-m-d') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-1.5">
                                <a href="{{ route('invoices.show', $invoice) }}" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-900 rounded-lg transition duration-150" title="{{ __('app.view') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('invoices.download', $invoice) }}" class="p-2 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-900 rounded-lg transition duration-150" title="{{ __('app.download') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                                <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="p-2 text-purple-600 bg-purple-50 hover:bg-purple-100 hover:text-purple-900 rounded-lg transition duration-150" title="{{ __('app.print') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                            {{ __('app.no_invoices') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection