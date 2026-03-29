@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Products</h1>

<div class="mb-4">
    <a href="{{ route('admin.products.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add Product</a>
</div>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Name</th>
            <th class="p-2">Price</th>
            <th class="p-2">Stock</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td class="p-2">{{ $product->name }}</td>
            <td class="p-2">${{ $product->price }}</td>
            <td class="p-2">{{ $product->stock }}</td>
            <td class="p-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="text-green-500">Edit</a>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline ml-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection