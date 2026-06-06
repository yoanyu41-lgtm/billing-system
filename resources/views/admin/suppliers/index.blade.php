@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Suppliers</h1>
<a href="{{ route('admin.suppliers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Supplier</a>
<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200"><th class="p-2">Name</th><th class="p-2">Phone</th><th class="p-2">Email</th></tr>
    </thead>
    <tbody>
        @foreach($suppliers as $s)
        <tr>
            <td class="p-2">{{ $s->name }}</td>
            <td class="p-2">{{ $s->phone }}</td>
            <td class="p-2">{{ $s->email }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">{{ $suppliers->links() }}</div>
@endsection
