@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ app()->getLocale() === 'km' ? 'ធុងសំរាមរួម' : 'Recycle Bin' }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.auto_delete_note') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 shadow-sm">{{ session('error') }}</div>
    @endif

    <!-- Tabs Header (Horizontal Scrollable on Mobile) -->
    <div class="flex border-b border-gray-200 mb-6 overflow-x-auto whitespace-nowrap scrollbar-thin">
        <a href="{{ route('customers.trash', ['tab' => 'customers']) }}" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 {{ $tab === 'customers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" style="text-decoration: none;">
            <i class="fas fa-users"></i>
            {{ __('app.customers') }} ({{ $customers->total() }})
        </a>
        <a href="{{ route('customers.trash', ['tab' => 'installments']) }}" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 {{ $tab === 'installments' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" style="text-decoration: none;">
            <i class="fas fa-file-contract"></i>
            {{ __('app.installment_plans') }} ({{ $installments->total() }})
        </a>
        <a href="{{ route('customers.trash', ['tab' => 'products']) }}" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 {{ $tab === 'products' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" style="text-decoration: none;">
            <i class="fas fa-box"></i>
            {{ __('app.products') }} ({{ $products->total() }})
        </a>
        <a href="{{ route('customers.trash', ['tab' => 'payments']) }}" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 {{ $tab === 'payments' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" style="text-decoration: none;">
            <i class="fas fa-hand-holding-usd"></i>
            {{ app()->getLocale() === 'km' ? 'ការទូទាត់ប្រាក់' : 'Payments' }} ({{ $payments->total() }})
        </a>
        <a href="{{ route('customers.trash', ['tab' => 'sales']) }}" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 {{ $tab === 'sales' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" style="text-decoration: none;">
            <i class="fas fa-shopping-cart"></i>
            {{ app()->getLocale() === 'km' ? 'ការលក់ដាច់' : 'Sales' }} ({{ $sales->total() }})
        </a>
        <a href="{{ route('customers.trash', ['tab' => 'users']) }}" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 {{ $tab === 'users' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" style="text-decoration: none;">
            <i class="fas fa-users-cog"></i>
            {{ app()->getLocale() === 'km' ? 'បុគ្គលិក' : 'Users' }} ({{ $users->total() }})
        </a>
        <a href="{{ route('customers.trash', ['tab' => 'suppliers']) }}" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 {{ $tab === 'suppliers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" style="text-decoration: none;">
            <i class="fas fa-truck"></i>
            {{ app()->getLocale() === 'km' ? 'អ្នកផ្គត់ផ្គង់' : 'Suppliers' }} ({{ $suppliers->total() }})
        </a>
        <a href="{{ route('customers.trash', ['tab' => 'categories']) }}" 
           class="py-3 px-4 sm:px-6 text-sm font-semibold border-b-2 transition-all inline-flex items-center gap-2 {{ $tab === 'categories' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}" style="text-decoration: none;">
            <i class="fas fa-tags"></i>
            {{ app()->getLocale() === 'km' ? 'ប្រភេទផលិតផល' : 'Categories' }} ({{ $categories->total() }})
        </a>
    </div>

    @php
        $hasItems = false;
        if ($tab === 'customers' && $customers->total() > 0) $hasItems = true;
        if ($tab === 'installments' && $installments->total() > 0) $hasItems = true;
        if ($tab === 'products' && $products->total() > 0) $hasItems = true;
        if ($tab === 'users' && $users->total() > 0) $hasItems = true;
        if ($tab === 'payments' && $payments->total() > 0) $hasItems = true;
        if ($tab === 'suppliers' && $suppliers->total() > 0) $hasItems = true;
        if ($tab === 'categories' && $categories->total() > 0) $hasItems = true;
        if ($tab === 'sales' && $sales->total() > 0) $hasItems = true;
    @endphp

    @if($hasItems)
        <div class="flex items-center justify-end gap-3 mb-4">
            <form action="{{ route('trash.restore-all', ['tab' => $tab]) }}" method="POST"
                  onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារទិន្នន័យទាំងអស់ក្នុងផ្នែកនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore all items in this section?' }}')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-undo"></i>
                    {{ app()->getLocale() === 'km' ? 'ស្តារឡើងវិញទាំងអស់' : 'Restore All' }}
                </button>
            </form>
            <form action="{{ route('trash.empty', ['tab' => $tab]) }}" method="POST"
                  onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបចោលទិន្នន័យទាំងអស់ក្នុងផ្នែកនេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to empty the trash for this section? This action is permanent and cannot be undone!' }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-trash-alt"></i>
                    {{ app()->getLocale() === 'km' ? 'សម្អាតធុងសំរាម' : 'Empty Trash' }}
                </button>
            </form>
        </div>
    @endif

    @if($tab === 'customers')
        {{-- Customers Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.id') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.customers') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.phone') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប' }}</th>
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
                                        <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
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
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs">{{ $customer->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('customers.restore', $customer->id) }}" method="POST"
                                          onsubmit="return confirm('{{ __('app.confirm_restore_customer') }}')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            {{ __('app.restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('customers.force-delete', $customer->id) }}" method="POST"
                                          onsubmit="return confirm('{{ __('app.confirm_force_delete_customer') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ __('app.force_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span>{{ __('app.trash_empty') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
            @endif
        </div>

    @elseif($tab === 'installments')
        {{-- Installments Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.id') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.customer') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.product') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.total_price') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប' }}</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($installments as $installment)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800">{{ $installment->customer?->name ?? 'N/A' }}</div>
                                @if($installment->customer?->phone)
                                    <div class="text-xs text-gray-400">{{ $installment->customer?->phone }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-gray-600">{{ $installment->product?->name ?? 'N/A' }}</td>
                            <td class="px-5 py-2.5 text-gray-900 font-bold">{{ format_currency($installment->total_price) }}</td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs">{{ $installment->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('installments.restore', $installment->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារគម្រោងបង់រំលស់នេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this installment plan?' }}')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            {{ __('app.restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('installments.force-delete', $installment->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបគម្រោងបង់រំលស់នេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this installment plan permanently? This action cannot be undone!' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ __('app.force_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span>{{ __('app.trash_empty') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($installments->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $installments->links() }}
            </div>
            @endif
        </div>

    @elseif($tab === 'products')
        {{-- Products Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.item_code') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.products') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.price') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.stock') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប' }}</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">{{ $product->code ?? '—' }}</td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800">{{ $product->name }}</div>
                                @if($product->category)
                                    <div class="text-xs text-gray-400">{{ $product->category }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-gray-900 font-bold">{{ format_currency($product->price) }}</td>
                            <td class="px-5 py-2.5 text-gray-600 font-semibold">{{ $product->stock }}</td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs">{{ $product->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('products.restore', $product->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារផលិតផលនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this product?' }}')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            {{ __('app.restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('products.force-delete', $product->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបផលិតផលនេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this product permanently? This action cannot be undone!' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ __('app.force_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span>{{ __('app.trash_empty') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
            @endif
        </div>

    @elseif($tab === 'payments')
        {{-- Payments Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.id') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.customer') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.amount') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទ & វិធីបង់ប្រាក់' : 'Date & Method' }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប' }}</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">#PAY-{{ str_pad($payment->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800">{{ $payment->installment?->customer?->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">#INS-{{ str_pad($payment->installment_id, 3, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-5 py-2.5 text-gray-900 font-bold">
                                {{ format_currency($payment->amount) }}
                                @if($payment->penalty_amount > 0)
                                    <div class="text-xs text-red-600 font-semibold">+{{ format_currency($payment->penalty_amount) }} {{ app()->getLocale() === 'km' ? 'ពិន័យ' : 'Penalty' }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-2.5">
                                <div class="text-xs text-gray-700 font-medium">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : '—' }}</div>
                                <div class="text-xs text-gray-400 font-medium">{{ $payment->paymentMethod?->name ?? '—' }}</div>
                            </td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs">{{ $payment->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('payments.restore', $payment->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារការទូទាត់ប្រាក់នេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this payment?' }}')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            {{ __('app.restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('payments.force-delete', $payment->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបការទូទាត់ប្រាក់នេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this payment permanently? This action cannot be undone!' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ __('app.force_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span>{{ __('app.trash_empty') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
            @endif
        </div>

    @elseif($tab === 'sales')
        {{-- Sales Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.invoice_no') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.customer') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.total_price') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ app()->getLocale() === 'km' ? 'កាលបរិច្ឆេទលក់' : 'Sale Date' }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប' }}</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono font-semibold">{{ $sale->invoice_no ?? '#SAL-' . str_pad($sale->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800">{{ $sale->customer_name ?? 'N/A' }}</div>
                                @if($sale->customer_phone)
                                    <div class="text-xs text-gray-400">{{ $sale->customer_phone }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-gray-900 font-bold">{{ format_currency($sale->total) }}</td>
                            <td class="px-5 py-2.5 text-gray-600 text-xs font-medium">{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') : '—' }}</td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs">{{ $sale->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('sales.restore', $sale->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារការលក់នេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this sale?' }}')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            {{ __('app.restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('sales.force-delete', $sale->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបការលក់នេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this sale permanently? This action cannot be undone!' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ __('app.force_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span>{{ __('app.trash_empty') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($sales->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $sales->links() }}
            </div>
            @endif
        </div>

    @elseif($tab === 'users')
        {{-- Users Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.id') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ app()->getLocale() === 'km' ? 'ឈ្មោះ & អ៊ីមែល' : 'Name & Email' }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ app()->getLocale() === 'km' ? 'តួនាទី' : 'Role' }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប' }}</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">{{ $user->id }}</td>
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-3">
                                    @if($user->profile_image)
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}"
                                             class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-xs font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-400 font-medium">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-2.5">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs">{{ $user->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារគណនីបុគ្គលិកនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this user?' }}')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            {{ __('app.restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('users.force-delete', $user->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបគណនីបុគ្គលិកនេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this user permanently? This action cannot be undone!' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ __('app.force_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span>{{ __('app.trash_empty') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
            @endif
        </div>

    @elseif($tab === 'suppliers')
        {{-- Suppliers Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.id') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ app()->getLocale() === 'km' ? 'អ្នកផ្គត់ផ្គង់' : 'Supplier Name' }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.phone') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.email') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប' }}</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">{{ $supplier->id }}</td>
                            <td class="px-5 py-2.5">
                                <div class="font-semibold text-gray-800">{{ $supplier->name }}</div>
                                @if($supplier->address)
                                    <div class="text-xs text-gray-400">{{ $supplier->address }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-gray-600 font-medium">{{ $supplier->phone ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-gray-600 font-medium">{{ $supplier->email ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs">{{ $supplier->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('suppliers.restore', $supplier->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារអ្នកផ្គត់ផ្គង់នេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this supplier?' }}')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            {{ __('app.restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('suppliers.force-delete', $supplier->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបអ្នកផ្គត់ផ្គង់នេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this supplier permanently? This action cannot be undone!' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ __('app.force_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span>{{ __('app.trash_empty') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($suppliers->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $suppliers->links() }}
            </div>
            @endif
        </div>

    @elseif($tab === 'categories')
        {{-- Categories Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.id') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ app()->getLocale() === 'km' ? 'ឈ្មោះប្រភេទផលិតផល' : 'Category Name' }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.brand') }}</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.deleted_at') ?? 'កាលបរិច្ឆេទលុប' }}</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-5 py-2.5 text-xs text-gray-400 font-mono">{{ $category->id }}</td>
                            <td class="px-5 py-2.5 text-gray-800 font-semibold">{{ $category->name }}</td>
                            <td class="px-5 py-2.5 text-gray-600 font-medium">{{ $category->brand ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-gray-600 font-mono text-xs">{{ $category->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('categories.restore', $category->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់ស្តារប្រភេទផលិតផលនេះឡើងវិញមែនទេ?' : 'Are you sure you want to restore this category?' }}')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-undo"></i>
                                            {{ __('app.restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('categories.force-delete', $category->id) }}" method="POST"
                                          onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកប្រាកដជាចង់លុបប្រភេទផលិតផលនេះជាស្ថាពរមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ!' : 'Are you sure you want to delete this category permanently? This action cannot be undone!' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded transition duration-150">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ __('app.force_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt text-4xl text-gray-200"></i>
                                    <span>{{ __('app.trash_empty') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    @endif

</div>
@endsection
