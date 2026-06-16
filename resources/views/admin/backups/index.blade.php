@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight flex items-center gap-3">
                <i class="fas fa-database text-blue-600"></i> {{ __('app.backup_restore') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                គ្រប់គ្រងការបម្រុងទុកមូលដ្ឋានទិន្នន័យ ស្ដារ និងលុបឯកសារចាស់ៗដើម្បីសុវត្ថិភាពទិន្នន័យ។
            </p>
        </div>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in">
            <div class="text-emerald-500 text-xl"><i class="fas fa-check-circle"></i></div>
            <div class="text-emerald-800 font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in">
            <div class="text-rose-500 text-xl"><i class="fas fa-exclamation-circle"></i></div>
            <div class="text-rose-800 font-medium">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Backups Card -->
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl p-6 shadow-md relative overflow-hidden transition-all duration-300 hover:scale-[1.02]">
            <div class="absolute right-4 top-4 text-blue-200/20 text-6xl"><i class="fas fa-database"></i></div>
            <div class="text-sm font-medium text-blue-100 uppercase tracking-wider mb-2">{{ __('app.total_backups') }}</div>
            <div class="text-3xl font-bold mb-1">{{ count($backups) }}</div>
            <div class="text-xs text-blue-200 flex items-center gap-1">
                <i class="fas fa-history"></i> {{ __('app.backup_history') }}
            </div>
        </div>
        
        <!-- Total Size Card -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-2xl p-6 shadow-md relative overflow-hidden transition-all duration-300 hover:scale-[1.02]">
            <div class="absolute right-4 top-4 text-emerald-200/20 text-6xl"><i class="fas fa-hdd"></i></div>
            <div class="text-sm font-medium text-emerald-100 uppercase tracking-wider mb-2">{{ __('app.total_size') }}</div>
            <div class="text-3xl font-bold mb-1">
                @if($totalSize >= 1048576)
                    {{ number_format($totalSize / 1048576, 2) }} MB
                @else
                    {{ number_format($totalSize / 1024, 2) }} KB
                @endif
            </div>
            <div class="text-xs text-emerald-200 flex items-center gap-1">
                <i class="fas fa-chart-pie"></i> ទំហំសរុបនៅលើ server
            </div>
        </div>
        
        <!-- Last Backup Card -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl p-6 shadow-md relative overflow-hidden transition-all duration-300 hover:scale-[1.02]">
            <div class="absolute right-4 top-4 text-amber-200/20 text-6xl"><i class="fas fa-clock"></i></div>
            <div class="text-sm font-medium text-amber-100 uppercase tracking-wider mb-2">{{ __('app.last_backup') }}</div>
            <div class="text-2xl font-bold mb-1 truncate">
                @if($lastBackupTime)
                    {{ \Carbon\Carbon::createFromTimestamp($lastBackupTime)->setTimezone('Asia/Phnom_Penh')->format('d M Y, h:i A') }}
                @else
                    —
                @endif
            </div>
            <div class="text-xs text-amber-200 flex items-center gap-1">
                <i class="fas fa-info-circle"></i> កាលបរិច្ឆេទចុងក្រោយ
            </div>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Create Backup -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg"><i class="fas fa-download"></i></div>
                    <h2 class="text-lg font-bold text-gray-800">{{ __('app.create_backup') }}</h2>
                </div>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">
                    បង្កើតឯកសារបម្រុងទុកនៃមូលដ្ឋានទិន្នន័យ (Database SQL Dump) ដែលមានផ្ទុកនូវរាល់ទិន្នន័យអតិថិជន ផលិតផល គម្រោងបង់រំលស់ និងការទូទាត់ទាំងអស់។ ឯកសារនឹងត្រូវរក្សាទុកនៅលើម៉ាស៊ីនមេ ហើយអាចទាញយកបានគ្រប់ពេល។
                </p>
            </div>
            <div>
                <form method="POST" action="{{ route('admin.backups.create') }}">
                    @csrf
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-xl shadow transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-plus-circle"></i> {{ __('app.create_backup') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Upload & Restore -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-lg"><i class="fas fa-upload"></i></div>
                <h2 class="text-lg font-bold text-gray-800">{{ __('app.upload_backup') }}</h2>
            </div>
            
            <form method="POST" action="{{ route('admin.backups.restore') }}" enctype="multipart/form-data" id="upload-restore-form" class="space-y-4">
                @csrf
                
                <div class="border-2 border-dashed border-gray-200 hover:border-blue-500 rounded-xl p-6 transition-colors duration-200 cursor-pointer text-center relative group" id="drop-zone">
                    <input type="file" name="file" id="file-input" accept=".sql" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                    <div class="space-y-2">
                        <div class="text-gray-400 group-hover:text-blue-500 transition-colors duration-200 text-4xl"><i class="fas fa-cloud-upload-alt"></i></div>
                        <p class="text-sm font-semibold text-gray-700 group-hover:text-blue-600 transition-colors duration-200" id="file-label">
                            {{ __('app.select_sql_file') }}
                        </p>
                        <p class="text-xs text-gray-400">SQL file maximum size 20MB</p>
                    </div>
                </div>
                
                <!-- Warning Box -->
                <div class="bg-amber-50 border-l-4 border-amber-500 p-3 rounded-r-lg text-xs text-amber-800 flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle mt-0.5 text-amber-600"></i>
                    <div>
                        <span class="font-bold text-amber-900">ការព្រមាន៖</span> ការស្ដារមូលដ្ឋានទិន្នន័យឡើងវិញ នឹងលុប និងសរសេរជាន់លើរាល់ទិន្នន័យបច្ចុប្បន្នទាំងអស់! សូមប្រាកដថាអ្នកបានបង្កើតការបម្រុងទុកបច្ចុប្បន្នសិន មុនពេលស្ដារទិន្នន័យថ្មី។
                    </div>
                </div>

                <button type="submit" onclick="return confirmRestoreUploaded(event);" class="w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-3 px-4 rounded-xl shadow transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-history"></i> {{ __('app.upload_and_restore') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Backup History Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list-alt text-blue-500"></i> {{ __('app.backup_history') }}
            </h2>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                {{ count($backups) }} ឯកសារ
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold border-b border-gray-100">
                        <th class="py-4 px-6 w-16">#</th>
                        <th class="py-4 px-6">{{ __('app.backup_file') }}</th>
                        <th class="py-4 px-6">Generated Date</th>
                        <th class="py-4 px-6">File Size</th>
                        <th class="py-4 px-6 text-center w-48">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($backups as $index => $backup)
                    <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                        <td class="py-4 px-6 font-medium text-gray-500">{{ $index + 1 }}</td>
                        <td class="py-4 px-6 font-semibold text-gray-800">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-file-code text-blue-400 text-lg"></i>
                                {{ $backup['name'] }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-gray-600">
                            {{ $backup['date']->format('d M Y, h:i A') }}
                            <span class="text-xs text-gray-400 block">{{ $backup['date']->diffForHumans() }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700">
                                @if($backup['size'] >= 1048576)
                                    {{ number_format($backup['size'] / 1048576, 2) }} MB
                                @else
                                    {{ number_format($backup['size'] / 1024, 2) }} KB
                                @endif
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Download -->
                                <a href="{{ route('admin.backups.download', $backup['name']) }}" class="h-9 w-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors duration-150" title="{{ __('app.download') }}">
                                    <i class="fas fa-download"></i>
                                </a>
                                
                                <!-- Direct Restore -->
                                <form method="POST" action="{{ route('admin.backups.restore-file', $backup['name']) }}" class="inline" id="restore-form-{{ $index }}">
                                    @csrf
                                    <button type="button" onclick="confirmRestoreFile('{{ $backup['name'] }}', 'restore-form-{{ $index }}')" class="h-9 w-9 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors duration-150" title="{{ __('app.restore') }}">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </form>
                                
                                <!-- Delete -->
                                <form method="POST" action="{{ route('admin.backups.destroy', $backup['name']) }}" class="inline" id="delete-form-{{ $index }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDeleteBackup('{{ $backup['name'] }}', 'delete-form-{{ $index }}')" class="h-9 w-9 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors duration-150" title="{{ __('app.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center text-gray-400">
                            <div class="text-gray-200 text-5xl mb-3"><i class="fas fa-folder-open"></i></div>
                            <p class="font-medium text-base">{{ __('app.no_backups') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmRestoreUploaded(event) {
    event.preventDefault();
    const fileInput = document.getElementById('file-input');
    if (!fileInput.value) {
        alert("{{ __('app.select_sql_file') }}");
        return false;
    }
    
    if (confirm("{{ __('app.confirm_restore') }}")) {
        document.getElementById('upload-restore-form').submit();
        return true;
    }
    return false;
}

function confirmRestoreFile(filename, formId) {
    if (confirm("{{ __('app.confirm_restore') }}\n\nឯកសារ៖ " + filename)) {
        document.getElementById(formId).submit();
    }
}

function confirmDeleteBackup(filename, formId) {
    if (confirm("{{ __('app.confirm_delete_backup') }}\n\nឯកសារ៖ " + filename)) {
        document.getElementById(formId).submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input');
    const fileLabel = document.getElementById('file-label');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileLabel.textContent = fileInput.files[0].name;
                fileLabel.classList.add('text-blue-600', 'font-bold');
            } else {
                fileLabel.textContent = "{{ __('app.select_sql_file') }}";
                fileLabel.classList.remove('text-blue-600', 'font-bold');
            }
        });
    }
});
</script>
@endsection