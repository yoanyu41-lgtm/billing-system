@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold shadow-2xs border border-indigo-100">
                💾
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                    {{ __('app.backup_restore') }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    គ្រប់គ្រងការបម្រុងទុកមូលដ្ឋានទិន្នន័យ (Database) ស្ដារទិន្នន័យឡើងវិញ និងទាញយកឯកសារចាស់ៗដើម្បីសុវត្ថិភាព។
                </p>
            </div>
        </div>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="mb-6 px-4 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-2xs flex items-center gap-2.5">
            <span class="text-base">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 px-4 py-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold shadow-2xs flex items-center gap-2.5">
            <span class="text-base">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <!-- Total Backups Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs relative overflow-hidden transition-all duration-200 hover:border-slate-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('app.total_backups') }}</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    📊
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-800 font-mono mb-1">{{ count($backups) }}</div>
            <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                <span>📁 {{ __('app.backup_history') }}</span>
            </div>
        </div>

        <!-- Total Size Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs relative overflow-hidden transition-all duration-200 hover:border-slate-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('app.total_size') }}</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    💾
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-800 font-mono mb-1">
                @if($totalSize >= 1048576)
                    {{ number_format($totalSize / 1048576, 2) }} MB
                @else
                    {{ number_format($totalSize / 1024, 2) }} KB
                @endif
            </div>
            <div class="text-[11px] text-emerald-600 font-medium flex items-center gap-1">
                <span>💽 ទំហំសរុបនៅលើ server</span>
            </div>
        </div>

        <!-- Last Backup Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs relative overflow-hidden transition-all duration-200 hover:border-slate-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('app.last_backup') }}</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    ⏱️
                </div>
            </div>
            <div class="text-lg font-bold text-slate-800 font-mono mb-1 truncate">
                @if($lastBackupTime)
                    {{ \Carbon\Carbon::createFromTimestamp($lastBackupTime)->setTimezone('Asia/Phnom_Penh')->format('d M Y, h:i A') }}
                @else
                    —
                @endif
            </div>
            <div class="text-[11px] text-amber-600 font-medium flex items-center gap-1">
                <span>📅 កាលបរិច្ឆេទចុងក្រោយ</span>
            </div>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Create Backup Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">
                        📥
                    </div>
                    <h2 class="text-base font-bold text-slate-800">{{ __('app.create_backup') }}</h2>
                </div>
                <p class="text-slate-600 text-xs leading-relaxed mb-6">
                    បង្កើតឯកសារបម្រុងទុកនៃមូលដ្ឋានទិន្នន័យ (Database SQL Dump) ដែលមានផ្ទុកនូវរាល់ទិន្នន័យអតិថិជន ផលិតផល គម្រោងបង់រំលស់ និងការទូទាត់ទាំងអស់។ ឯកសារនឹងត្រូវរក្សាទុកនៅលើម៉ាស៊ីនមេ ហើយអាចទាញយកបានគ្រប់ពេល។
                </p>
            </div>
            <div>
                <form method="POST" action="{{ route('admin.backups.create') }}">
                    @csrf
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-center gap-2 text-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ __('app.create_backup') }}</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Upload & Restore Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold">
                    📤
                </div>
                <h2 class="text-base font-bold text-slate-800">{{ __('app.upload_backup') }}</h2>
            </div>

            <form method="POST" action="{{ route('admin.backups.restore') }}" enctype="multipart/form-data" id="upload-restore-form" class="space-y-4">
                @csrf

                <div class="border-2 border-dashed border-slate-200 hover:border-amber-500 rounded-2xl p-5 transition-colors duration-200 cursor-pointer text-center relative group bg-slate-50/50 hover:bg-amber-50/20" id="drop-zone">
                    <input type="file" name="file" id="file-input" accept=".sql" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required onchange="updateFileLabel(this)">
                    <div class="space-y-1.5">
                        <div class="w-10 h-10 rounded-full bg-white text-slate-400 group-hover:text-amber-600 transition-colors mx-auto flex items-center justify-center text-xl shadow-2xs">
                            ☁️
                        </div>
                        <p class="text-xs font-bold text-slate-700 group-hover:text-amber-700 transition-colors" id="file-label">
                            {{ __('app.select_sql_file') }}
                        </p>
                        <p class="text-[10px] text-slate-400 font-medium">{{ app()->getLocale() === 'km' ? 'ទំហំឯកសារ SQL ធំបំផុត 20MB' : 'SQL file maximum size 20MB' }}</p>
                    </div>
                </div>

                <!-- Warning Box -->
                <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 flex items-start gap-2.5">
                    <span class="text-amber-600 font-bold text-sm">⚠️</span>
                    <div>
                        <span class="font-bold text-amber-900">ការព្រមាន៖</span> ការស្ដារទិន្នន័យឡើងវិញ នឹងសរសេរជាន់លើ (Overwrite) ទិន្នន័យបច្ចុប្បន្នទាំងអស់! សូមប្រាកដថាបានបម្រុងទុកទិន្នន័យសិន។
                    </div>
                </div>

                <button type="submit" onclick="return confirmRestoreUploaded(event);" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-center gap-2 text-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>{{ __('app.upload_and_restore') }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Backup History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span>📁</span>
                <span>{{ __('app.backup_history') }}</span>
            </h2>
            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-extrabold border border-indigo-100">
                {{ count($backups) }} ឯកសារ
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 text-[11px] uppercase font-bold tracking-wider border-b border-slate-100">
                        <th class="py-3.5 px-5 w-16">#</th>
                        <th class="py-3.5 px-5">{{ __('app.backup_file') }}</th>
                        <th class="py-3.5 px-5">{{ __('app.generated_date') }}</th>
                        <th class="py-3.5 px-5">{{ __('app.file_size') }}</th>
                        <th class="py-3.5 px-5 text-center w-48">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($backups as $index => $backup)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 px-5 font-bold text-slate-400 font-mono">{{ $index + 1 }}</td>
                        <td class="py-3 px-5 font-semibold text-slate-800">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-base">📄</span>
                                <span class="font-mono text-slate-700">{{ $backup['name'] }}</span>
                            </span>
                        </td>
                        <td class="py-3 px-5 text-slate-600 whitespace-nowrap">
                            <div class="font-semibold text-slate-700 font-mono">{{ $backup['date']->format('d M Y, h:i A') }}</div>
                            <span class="text-[10px] text-slate-400 block">{{ $backup['date']->diffForHumans() }}</span>
                        </td>
                        <td class="py-3 px-5 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 font-mono">
                                @if($backup['size'] >= 1048576)
                                    {{ number_format($backup['size'] / 1048576, 2) }} MB
                                @else
                                    {{ number_format($backup['size'] / 1024, 2) }} KB
                                @endif
                            </span>
                        </td>
                        <td class="py-3 px-5 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Download -->
                                <a href="{{ route('admin.backups.download', $backup['name']) }}" class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200/80 transition-all flex items-center gap-1 cursor-pointer no-underline" title="{{ __('app.download') }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>ទាញយក</span>
                                </a>

                                <!-- Direct Restore -->
                                <form method="POST" action="{{ route('admin.backups.restore-file', $backup['name']) }}" class="inline" id="restore-form-{{ $index }}">
                                    @csrf
                                    <button type="button" onclick="confirmRestoreFile('{{ $backup['name'] }}', 'restore-form-{{ $index }}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs border border-emerald-200/80 transition-all flex items-center gap-1 cursor-pointer" title="{{ __('app.restore') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>ស្ដារ</span>
                                    </button>
                                </form>

                                <!-- Delete -->
                                <form method="POST" action="{{ route('admin.backups.destroy', $backup['name']) }}" class="inline" id="delete-form-{{ $index }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDeleteBackup('{{ $backup['name'] }}', 'delete-form-{{ $index }}')" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200/80 transition-all cursor-pointer" title="{{ __('app.delete') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center text-slate-400">
                            <div class="text-4xl mb-2">📁</div>
                            <p class="font-medium text-xs">{{ __('app.no_backups') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function updateFileLabel(input) {
    const label = document.getElementById('file-label');
    if (input.files && input.files[0]) {
        label.textContent = "📄 " + input.files[0].name;
    }
}

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
</script>
@endsection