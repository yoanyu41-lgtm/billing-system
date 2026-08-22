@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold shadow-2xs border border-indigo-100">
                ➕
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                    {{ app()->getLocale() === 'km' ? 'បន្ថែមតួនាទីថ្មី' : 'Create New Role' }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ app()->getLocale() === 'km' ? 'បញ្ចូលឈ្មោះតួនាទី និងជ្រើសរើសសិទ្ធិប្រើប្រាស់មុខងារប្រព័ន្ធ' : 'Enter role name and select access permissions across modules' }}
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
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6 mb-6">
            <div class="max-w-md">
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wider">
                    {{ app()->getLocale() === 'km' ? 'ឈ្មោះតួនាទី (Role Name)' : 'Role Name' }} <span class="text-rose-500">*</span>
                </label>
                <div class="space-y-3">
                    <select name="name_select" id="role_select" onchange="handleRoleSelectChange(this)"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs cursor-pointer">
                        <option value="" disabled selected>{{ app()->getLocale() === 'km' ? '--- សូមជ្រើសរើសឈ្មោះតួនាទីពីអ្នកប្រើប្រាស់ (Select Role from Users) ---' : '--- Please Select Role from Users ---' }}</option>
                        
                        @forelse($userRoles as $uRole)
                            <option value="{{ $uRole }}" {{ old('name') === $uRole ? 'selected' : '' }}>
                                👤 {{ $uRole }}
                            </option>
                        @empty
                            <option value="" disabled>{{ app()->getLocale() === 'km' ? 'មិនទាន់មានតួនាទីក្នុង Users នៅឡើយទេ' : 'No roles found in Users' }}</option>
                        @endforelse

                        <option value="__custom__" {{ old('name_custom') ? 'selected' : '' }}>➕ {{ app()->getLocale() === 'km' ? 'វាយឈ្មោះតួនាទីថ្មីផ្សេងទៀត (Type Custom Role)...' : 'Type Custom Role...' }}</option>
                    </select>

                    <div id="custom_role_wrapper" class="{{ old('name_custom') ? '' : 'hidden' }}">
                        <input type="text" name="name_custom" id="name_custom" value="{{ old('name_custom') }}"
                               placeholder="{{ app()->getLocale() === 'km' ? 'បញ្ចូលឈ្មោះតួនាទីថ្មីផ្ទាល់ខ្លួន...' : 'Type custom role name...' }}" 
                               class="w-full px-4 py-2.5 rounded-xl border border-indigo-300 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-indigo-50/30 shadow-2xs">
                    </div>

                    <!-- Hidden input to submit the real role name -->
                    <input type="hidden" name="name" id="real_role_name" value="{{ old('name') }}">
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">
                    {{ app()->getLocale() === 'km' ? '💡 ជ្រើសរើសតួនាទីពីបញ្ជី ឬជ្រើសរើស "បង្កើតឈ្មោះផ្សេងទៀត" បើចង់កំណត់ឈ្មោះថ្មី' : '💡 Select a predefined role or pick "Other" to enter a custom role name' }}
                </p>
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
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" data-module="{{ $module }}" class="permission-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
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
                💾 {{ app()->getLocale() === 'km' ? 'រក្សាទុកតួនាទី' : 'Save Role' }}
            </button>
        </div>
    </form>
</div>

<script>
    function handleRoleSelectChange(select) {
        const customWrapper = document.getElementById('custom_role_wrapper');
        const customInput = document.getElementById('name_custom');
        const realInput = document.getElementById('real_role_name');

        if (select.value === '__custom__') {
            customWrapper.classList.remove('hidden');
            customInput.focus();
            customInput.required = true;
            realInput.value = customInput.value.trim();
        } else {
            customWrapper.classList.add('hidden');
            customInput.required = false;
            realInput.value = select.value;
        }
    }

    document.getElementById('name_custom')?.addEventListener('input', function() {
        document.getElementById('real_role_name').value = this.value.trim();
    });

    // Form submit validation
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const select = document.getElementById('role_select');
        const customInput = document.getElementById('name_custom');
        const realInput = document.getElementById('real_role_name');

        if (select.value === '__custom__') {
            realInput.value = customInput.value.trim();
        } else {
            realInput.value = select.value;
        }

        if (!realInput.value) {
            e.preventDefault();
            alert('{{ app()->getLocale() === "km" ? "សូមជ្រើសរើស ឬបញ្ចូលឈ្មោះតួនាទី!" : "Please select or type a role name!" }}');
            select.focus();
        }
    });

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
