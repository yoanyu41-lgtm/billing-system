<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backupFiles = Storage::files('backups');
        
        $backups = [];
        $totalSize = 0;
        $lastBackupTime = null;
        
        foreach ($backupFiles as $file) {
            $name = basename($file);
            try {
                $size = Storage::size($file);
                $modified = Storage::lastModified($file);
            } catch (\Throwable $e) {
                $size = 0;
                $modified = time();
            }
            
            $totalSize += $size;
            if (is_null($lastBackupTime) || $modified > $lastBackupTime) {
                $lastBackupTime = $modified;
            }
            
            $backups[] = [
                'name' => $name,
                'size' => $size,
                'date' => \Carbon\Carbon::createFromTimestamp($modified)->setTimezone('Asia/Phnom_Penh'),
            ];
        }
        
        // Sort backups by date descending
        usort($backups, function($a, $b) {
            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        return view('admin.backups.index', compact('backups', 'totalSize', 'lastBackupTime'));
    }

    public function create()
    {
        try {
            Artisan::call('db:backup');
            return redirect()->back()->with('success', __('app.backup_success'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __('app.backup_failed') . ': ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        return Storage::download('backups/' . $filename);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);

        $file = $request->file('file');
        
        try {
            $sql = file_get_contents($file->getRealPath());

            // Disable foreign keys and execute raw SQL statements
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            \Illuminate\Support\Facades\DB::unprepared($sql);
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', __('app.restore_success'));
        } catch (\Throwable $e) {
            try {
                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Throwable $ex) {}

            return redirect()->back()->with('error', __('app.restore_failed') . ': ' . $e->getMessage());
        }
    }

    public function restoreFromFile($filename)
    {
        // Prevent directory traversal
        $filename = basename($filename);
        $filePath = 'backups/' . $filename;

        if (!Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        try {
            $sql = Storage::get($filePath);

            // Disable foreign keys and execute raw SQL statements
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            \Illuminate\Support\Facades\DB::unprepared($sql);
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', __('app.restore_success'));
        } catch (\Throwable $e) {
            try {
                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Throwable $ex) {}

            return redirect()->back()->with('error', __('app.restore_failed') . ': ' . $e->getMessage());
        }
    }

    public function destroy($filename)
    {
        // Prevent directory traversal
        $filename = basename($filename);
        $filePath = 'backups/' . $filename;

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return redirect()->back()->with('success', __('app.delete_success'));
        }

        return redirect()->back()->with('error', 'Backup file not found.');
    }
}
