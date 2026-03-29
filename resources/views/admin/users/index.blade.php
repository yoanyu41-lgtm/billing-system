@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Users</h1>

<div class="mb-4">
    <a href="{{ route('admin.users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add User</a>
</div>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Name</th>
            <th class="p-2">Email</th>
            <th class="p-2">Role</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td class="p-2">{{ $user->name }}</td>
            <td class="p-2">{{ $user->email }}</td>
            <td class="p-2">{{ $user->role }}</td>
            <td class="p-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="text-green-500">Edit</a>
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="inline ml-2">
                    @csrf
                    <button type="submit" class="text-yellow-500">Reset Password</button>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline ml-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection