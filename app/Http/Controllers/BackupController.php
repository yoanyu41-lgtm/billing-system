<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Storage::files('backups');
        return view('admin.backups.index', compact('backups'));
    }

    public function create()
    {
        Artisan::call('db:backup');
        return redirect()->back()->with('success', 'Backup created.');
    }

    public function download($filename)
    {
        return Storage::download('backups/' . $filename);
    }

    public function restore(Request $request)
    {
        $request->validate(['file' => 'required|file']);
        $file = $request->file('file');
        $path = $file->storeAs('backups', 'restore.sql');

        // For restore, it's complex, perhaps just upload and note.
        return redirect()->back()->with('success', 'File uploaded for restore.');
    }
}
