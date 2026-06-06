@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Add Supplier</h1>
<form method="POST" action="{{ route('admin.suppliers.store') }}" class="bg-white p-6 rounded shadow">
    @csrf
    <div class="mb-4"><label class="block">Name</label><input name="name" class="w-full border px-2 py-1" required></div>
    <div class="mb-4"><label class="block">Phone</label><input name="phone" class="w-full border px-2 py-1"></div>
    <div class="mb-4"><label class="block">Email</label><input name="email" class="w-full border px-2 py-1"></div>
    <div class="mb-4"><label class="block">Address</label><textarea name="address" class="w-full border px-2 py-1"></textarea></div>
    <button class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
</form>
@endsection
