@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Backups</h1>

<div class="mb-4">
    <form method="POST" action="{{ route('admin.backups.create') }}">
        @csrf
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create Backup</button>
    </form>
</div>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">File</th>
            <th class="p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($backups as $backup)
        <tr>
            <td class="p-2">{{ basename($backup) }}</td>
            <td class="p-2">
                <a href="{{ route('admin.backups.download', basename($backup)) }}" class="text-blue-500">Download</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection