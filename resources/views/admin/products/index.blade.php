@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.product_list') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your products, pricing, and view current stock levels.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('manage-product')
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                {{ __('app.add_product') }}
            </a>
            @endcan
        </div>
    </div>

    <!-- Filter and Search Form -->
    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-8 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Search -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('app.search_product') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.category') }}</label>
                <select name="category" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    <option value="">{{ __('app.all') }} {{ __('app.categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Supplier Filter -->
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">{{ __('app.supplier') }}</label>
                <select name="supplier_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    <option value="">{{ __('app.all') }} {{ __('app.suppliers') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort -->
            <div class="flex space-x-2">
                <div class="w-3/5">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Sort By</label>
                    <select name="sort" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('app.name') }}</option>
                        <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>{{ __('app.price') }}</option>
                        <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>{{ __('app.stock') }}</option>
                    </select>
                </div>
                <div class="w-2/5">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Direction</label>
                    <select name="direction" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Asc</option>
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Desc</option>
                    </select>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="md:col-span-4 flex justify-end items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150">{{ __('app.clear') }}</a>
                
                <button type="submit" name="export" value="excel" class="inline-flex items-center bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 font-medium px-5 py-2.5 rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                    {{ __('app.export') }} Excel
                </button>
                
                <button type="submit" class="inline-flex items-center bg-gray-800 hover:bg-gray-900 text-white font-medium px-6 py-2.5 rounded-lg shadow-sm transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    {{ __('app.filter') }}
                </button>
            </div>
        </div>
    </form>

    <!-- Product Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.image') }}</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.name') }}</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.category') }}</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.price') }}</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.stock') }}</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-sm">
                        @else
                            <div class="w-12 h-12 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 text-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $product->name }}</div>
                        <div class="text-sm text-gray-500">{{ $product->code }}</div>
                        @if($product->cpu || $product->ram || $product->storage || $product->graphics_card)
                            <div class="text-xs mt-1.5 flex flex-wrap gap-1.5 items-center">
                                @if($product->cpu)
                                    <span class="bg-indigo-50/50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100/40 font-medium">CPU: {{ $product->cpu }}</span>
                                @endif
                                @if($product->ram)
                                    <span class="bg-indigo-50/50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100/40 font-medium">RAM: {{ $product->ram }}</span>
                                @endif
                                @if($product->storage)
                                    <span class="bg-indigo-50/50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100/40 font-medium">Storage: {{ $product->storage }}</span>
                                @endif
                                @if($product->graphics_card)
                                    <span class="bg-indigo-50/50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100/40 font-medium">GPU: {{ $product->graphics_card }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $product->category }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</td>
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
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.products.show', $product) }}" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-900 rounded-lg transition duration-150" title="{{ __('app.view') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            @can('manage-product')
                            <a href="{{ route('admin.products.edit', $product) }}" class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 hover:text-yellow-900 rounded-lg transition duration-150" title="{{ __('app.edit') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-900 rounded-lg transition duration-150" title="{{ __('app.delete') }}" onclick="return confirm('{{ __('app.confirm_delete') }}')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">{{ __('app.no_products') }}</td>
                </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection