@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Product Details</h1>
            <p class="text-sm text-gray-500 mt-1">View comprehensive information about this product.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg transition duration-150 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to List
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg shadow-sm transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Product
            </a>
            @endif
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="md:flex">
            <!-- Left: Image & Badges -->
            <div class="md:w-1/3 bg-gray-50 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-48 h-48 object-cover rounded-xl shadow-md border border-gray-200 mb-6">
                @else
                    <div class="w-48 h-48 bg-white rounded-xl shadow-sm flex flex-col items-center justify-center text-gray-400 mb-6 border border-gray-200">
                        <svg class="w-16 h-16 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-sm font-medium">No Image</span>
                    </div>
                @endif
                <span class="px-4 py-1.5 inline-flex text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                    {{ $product->category ?? 'Uncategorized' }}
                </span>
            </div>

            <!-- Right: Product Information -->
            <div class="md:w-2/3 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $product->name }}</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 mb-8">
                    <div>
                        <div class="text-sm font-medium text-gray-500 mb-1">Item Code</div>
                        <div class="text-base text-gray-900 font-semibold">{{ $product->code }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 mb-1">Stock Status</div>
                        <div class="text-base text-gray-900">
                            @if($product->stock <= 0)
                                <span class="text-red-600 font-bold">Out of Stock</span>
                            @elseif($product->stock <= ($product->low_stock_threshold ?? 5))
                                <span class="text-yellow-600 font-bold">{{ $product->stock }} (Low Stock)</span>
                            @else
                                <span class="text-green-600 font-bold">{{ $product->stock }} In Stock</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 mb-1">Selling Price</div>
                        <div class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 mb-1">Cost Price</div>
                        <div class="text-lg text-gray-700 font-medium">${{ $product->cost_price ? number_format($product->cost_price, 2) : '—' }}</div>
                    </div>
                </div>

                @if($product->cpu || $product->ram || $product->storage || $product->graphics_card)
                <div class="border-t border-gray-100 pt-6 mt-4">
                    <div class="text-sm font-medium text-gray-500 mb-3">Computer Specifications</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 bg-indigo-50/20 p-5 rounded-xl border border-indigo-100/50">
                        @if($product->cpu)
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">CPU</div>
                            <div class="text-base text-gray-900 font-semibold">{{ $product->cpu }}</div>
                        </div>
                        @endif
                        @if($product->ram)
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">RAM</div>
                            <div class="text-base text-gray-900 font-semibold">{{ $product->ram }}</div>
                        </div>
                        @endif
                        @if($product->storage)
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Storage</div>
                            <div class="text-base text-gray-900 font-semibold">{{ $product->storage }}</div>
                        </div>
                        @endif
                        @if($product->graphics_card)
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Graphics Card</div>
                            <div class="text-base text-gray-900 font-semibold">{{ $product->graphics_card }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="border-t border-gray-100 pt-6 mt-4">
                    <div class="text-sm font-medium text-gray-500 mb-3">Description</div>
                    <div class="text-base text-gray-800 leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">
                        @if($product->description)
                            {!! nl2br(e($product->description)) !!}
                        @else
                            <span class="text-gray-400 italic">No description provided for this product.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
