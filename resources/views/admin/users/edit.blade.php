@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit User</h1>

<form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white p-6 rounded shadow">
    @csrf @method('PUT')
    <div class="mb-4">
        <label class="block">Name</label>
        <input type="text" name="name" value="{{ $user->name }}" required class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">Email</label>
        <input type="email" name="email" value="{{ $user->email }}" required class="w-full border px-2 py-1">
    </div>
    <div class="mb-4">
        <label class="block">Role</label>
        <select name="role" required class="w-full border px-2 py-1">
            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
</form>
@endsection