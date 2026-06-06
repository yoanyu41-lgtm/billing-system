@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Category Management</h1>

<div class="flex justify-between items-center mb-4">
    <div></div>
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded shadow">Add Category</a>
</div>

<div class="bg-white p-6 rounded shadow">
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="p-3 border">Name</th>
                <th class="p-3 border w-48">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr class="border-t">
                    <td class="p-3">{{ $category->name }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="p-3 text-gray-500">No categories yet. Add one to make it available for products.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
