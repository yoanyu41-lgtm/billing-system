@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Customers</h1>

<div class="mb-4">
    <a href="{{ route('customers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add Customer</a>
    <form method="GET" class="inline ml-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search" class="border px-2 py-1">
        <button type="submit" class="bg-gray-500 text-white px-4 py-1">Search</button>
    </form>
</div>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Name</th>
            <th class="p-2">Phone</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td class="p-2">{{ $customer->name }}</td>
            <td class="p-2">{{ $customer->phone }}</td>
            <td class="p-2">
                <a href="{{ route('customers.show', $customer) }}" class="text-blue-500">View</a>
                <a href="{{ route('customers.edit', $customer) }}" class="text-green-500 ml-2">Edit</a>
                @if(auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline ml-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500">Delete</button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $customers->links() }}
@endsection