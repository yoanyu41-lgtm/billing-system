@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold shadow-2xs border border-indigo-100">
                ✏️
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                    {{ app()->getLocale() === 'km' ? 'កែប្រែតួនាទី និងសិទ្ធិ' : 'Edit Role & Permissions' }} - <span class="text-indigo-600">{{ $role->name }}</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ app()->getLocale() === 'km' ? 'កែប្រែឈ្មោះតួនាទី និងកែប្រែសិទ្ធិប្រើប្រាស់មុខងារប្រព័ន្ធ' : 'Modify role title and tick/untick access permissions' }}
                </p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all cursor-pointer no-underline border border-slate-200">
                ⬅️ {{ app()->getLocale() === 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Roles' }}
            </a>
        </div>
    </div>

    <!-- Role Form Card -->
    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6 mb-6">
            <div class="max-w-md">
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wider">
                    {{ app()->getLocale() === 'km' ? 'ឈ្មោះតួនាទី (Role Name)' : 'Role Name' }} <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" @if($role->name === 'Admin') readonly @endif required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs @if($role->name === 'Admin') bg-slate-100 text-slate-500 cursor-not-allowed @endif">
                @if($role->name === 'Admin')
                    <p class="text-[11px] text-amber-600 mt-1 font-semibold">🔒 ឈ្មោះតួនាទី Admin មិនអាចកែប្រែបានទេ ប៉ុន្តែអាចកែប្រែសិទ្ធិបាន។</p>
                @endif
                @error('name')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Permission Matrix (Horizontal Row Layout) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6 mb-6">
            <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        🔑 {{ app()->getLocale() === 'km' ? 'កំណត់សិទ្ធិប្រើប្រាស់ (Permission Assignment)' : 'Permission Assignment Matrix' }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ app()->getLocale() === 'km' ? 'គ្រូសជ្រើសរើស (Tick) សិទ្ធិដែលអនុញ្ញាតឱ្យតួនាទីនេះប្រើប្រាស់តាមជួរមុខងារនីមួយៗ' : 'Check all permissions you want to grant to this role organized line by line' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleAllPermissions(true)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 border-none bg-transparent cursor-pointer">
                        ✅ {{ app()->getLocale() === 'km' ? 'ជ្រើសរើសទាំងអស់ (Select All)' : 'Select All' }}
                    </button>
                    <span class="text-slate-300">|</span>
                    <button type="button" onclick="toggleAllPermissions(false)" class="text-xs font-bold text-slate-500 hover:text-slate-700 border-none bg-transparent cursor-pointer">
                        ❌ {{ app()->getLocale() === 'km' ? 'ដោះចេញទាំងអស់ (Deselect All)' : 'Deselect All' }}
                    </button>
                </div>
            </div>

            <!-- Stacked Row List -->
            <div class="space-y-4">
                @foreach($groupedPermissions as $module => $permissions)
                    <div class="border border-slate-200/80 rounded-2xl p-4 bg-slate-50/50 hover:border-indigo-200 transition-all">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200/80 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm border border-indigo-100">
                                    📁
                                </span>
                                <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wider">
                                    {{ strtoupper($module) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200/70 text-slate-600 font-mono">
                                    {{ count($permissions) }} permissions
                                </span>
                            </div>
                            <label class="inline-flex items-center cursor-pointer bg-white px-3 py-1 rounded-xl border border-slate-200 shadow-2xs hover:bg-slate-50 transition-colors">
                                <input type="checkbox" onchange="toggleModulePermissions('{{ $module }}', this.checked)" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                <span class="ml-1.5 text-xs font-bold text-slate-600">Select All ({{ strtoupper($module) }})</span>
                            </label>
                        </div>

                        <!-- Checkboxes Row -->
                        <div class="flex flex-wrap gap-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition-all cursor-pointer shadow-2xs">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" data-module="{{ $module }}" @if(in_array($permission->name, $rolePermissions)) checked @endif class="permission-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                    <span class="text-xs font-semibold text-slate-700 font-mono">
                                        {{ $permission->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-colors no-underline">
                {{ app()->getLocale() === 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs hover:shadow-md transition-all border-none cursor-pointer">
                💾 {{ app()->getLocale() === 'km' ? 'រក្សាទុកការកែប្រែ' : 'Update Role' }}
            </button>
        </div>
    </form>
</div>

<script>
    function toggleAllPermissions(checked) {
        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            cb.checked = checked;
        });
    }

    function toggleModulePermissions(module, checked) {
        document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(cb => {
            cb.checked = checked;
        });
    }
</script>
@endsection
