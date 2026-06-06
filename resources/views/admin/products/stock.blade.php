@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.manage_stock') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.current_stock') }}</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm px-4 py-2 rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('app.add_product') }}
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    {{ __('app.suppliers') }}
                </a>
                <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    {{ __('app.purchase') }}
                </a>
                <a href="{{ route('admin.stock-movements.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    {{ __('app.stock_movements') }}
                </a>
                <a href="{{ route('admin.purchases.create') }}" class="inline-flex items-center text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm px-4 py-2 rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                    {{ __('app.stock_in') }}
                </a>
                <a href="{{ route('admin.sales.create') }}" class="inline-flex items-center text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 shadow-sm px-4 py-2 rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path></svg>
                    {{ __('app.stock_out') }}
                </a>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stock Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Image</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Price / Cost</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 object-cover rounded-lg border border-gray-200 shadow-sm">
                            @else
                                <div class="w-12 h-12 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 text-xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $product->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $product->category ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</div>
                            <div class="text-xs text-gray-500">Cost: ${{ $product->cost_price ? number_format($product->cost_price, 2) : '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->stock <= 0)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">{{ __('app.out_of_stock') }}</span>
                            @elseif($product->stock <= ($product->low_stock_threshold ?? 5))
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">{{ $product->stock }} ({{ __('app.low_stock') }})</span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">{{ $product->stock }} {{ __('app.in_stock') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if(auth()->user()->role === 'admin')
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 stock-in-btn" data-id="{{ $product->id }}" data-name="{{ $product->name }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Quick In
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 ml-1 border border-gray-300 text-xs font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 adjust-stock-btn" data-name="{{ $product->name }}" data-url="{{ route('admin.products.stock.update', $product) }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Adjust
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No products found in inventory.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<div class="mt-4">
    {{ $products->links() }}
</div>

<!-- Inventory Stock In Modal -->
<div id="stockInModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Quick Stock In</h3>
            <button id="closeStockIn" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.purchases.store') }}" id="stockInForm" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Product</label>
                <input type="text" id="stockProductName" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-600 focus:outline-none" readonly>
                <input type="hidden" name="items[0][product_id]" id="stockProductId">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Supplier <span class="text-red-500">*</span></label>
                <select name="supplier_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    <option value="">-- Select supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Purchase Date</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="items[0][quantity]" value="1" min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">Unit Cost Price</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" step="0.01" name="items[0][cost_price]" class="w-full border border-gray-300 rounded-lg pl-8 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                </div>
                <p class="text-xs text-gray-500 mt-1">Leave blank to use the product's current cost price.</p>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" id="cancelStockIn" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition duration-150">Record Purchase</button>
            </div>
        </form>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjustStockModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Adjust Stock (Manual)</h3>
            <button type="button" id="closeAdjustStock" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form method="POST" action="" id="adjustStockForm" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Product Name</label>
                <input type="text" id="adjustProductName" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-600 focus:outline-none" readonly>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Adjustment Type <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        <option value="in">Add to Stock (In)</option>
                        <option value="out">Remove from Stock (Out)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" value="1" min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">Note / Reason</label>
                <input type="text" name="note" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Damaged item, Counted incorrectly...">
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" id="cancelAdjustStock" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-150">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition duration-150">Update Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal handlers
    document.querySelectorAll('.stock-in-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            document.getElementById('stockProductId').value = id;
            document.getElementById('stockProductName').value = name;
            const modal = document.getElementById('stockInModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // focus on supplier
            const sup = modal.querySelector('select[name="supplier_id"]');
            if (sup) sup.focus();
        });
    });
    document.getElementById('closeStockIn').addEventListener('click', ()=>{
        const modal = document.getElementById('stockInModal'); modal.classList.add('hidden'); modal.classList.remove('flex');
    });
    document.getElementById('cancelStockIn').addEventListener('click', ()=>{
        const modal = document.getElementById('stockInModal'); modal.classList.add('hidden'); modal.classList.remove('flex');
    });

    // Adjust Stock Modal handlers
    document.querySelectorAll('.adjust-stock-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const name = btn.getAttribute('data-name');
            const url = btn.getAttribute('data-url');
            
            document.getElementById('adjustProductName').value = name;
            document.getElementById('adjustStockForm').action = url;
            
            const modal = document.getElementById('adjustStockModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            const qty = modal.querySelector('input[name="quantity"]');
            if (qty) qty.focus();
        });
    });
    
    const closeAdjustModal = () => {
        const modal = document.getElementById('adjustStockModal'); modal.classList.add('hidden'); modal.classList.remove('flex');
    };
    document.getElementById('closeAdjustStock').addEventListener('click', closeAdjustModal);
    document.getElementById('cancelAdjustStock').addEventListener('click', closeAdjustModal);
</script>
@endsection
