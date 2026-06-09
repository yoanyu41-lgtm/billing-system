@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.pay_off') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.pay_off_note') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm">{{ session('error') }}</div>
    @endif

    <!-- Plans Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.customer') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.product') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.remaining_balance') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.outstanding_principal') }}</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $installment->product?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">${{ number_format($installment->remaining_balance, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-amber-700">${{ number_format($installment->payoff_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($installment->payoff_amount > 0)
                            <button type="button" onclick="document.getElementById('payoff-modal-{{ $installment->id }}').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-medium px-4 py-2 rounded-lg transition duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ __('app.pay_off') }}
                            </button>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
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

<!-- Pay Off Modals -->
@foreach($installments as $installment)
    @if($installment->payoff_amount > 0)
    <div id="payoff-modal-{{ $installment->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">{{ __('app.pay_off') }} — {{ $installment->customer?->name }}</h3>
                <button type="button" onclick="document.getElementById('payoff-modal-{{ $installment->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5">
                <p class="text-sm text-amber-800">{{ __('app.pay_off_note') }}</p>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-sm text-gray-600">{{ __('app.outstanding_principal') }}</span>
                    <span class="text-2xl font-extrabold text-amber-700">${{ number_format($installment->payoff_amount, 2) }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('installments.pay-off', $installment) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.payment_method') }}</label>
                        <select name="payment_method_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.payment_date') }}</label>
                        <input type="date" name="payment_date" value="{{ now()->toDateString() }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('payoff-modal-{{ $installment->id }}').classList.add('hidden')" class="px-5 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">{{ __('app.cancel') }}</button>
                    <button type="submit" onclick="return confirm('{{ __('app.confirm_pay_off') }}')" class="px-5 py-2.5 font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-sm transition">{{ __('app.confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach
@endsection
