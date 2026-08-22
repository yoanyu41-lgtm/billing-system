@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold shadow-2xs border border-indigo-100">
                🛡️
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                    {{ app()->getLocale() === 'km' ? 'បញ្ជីសិទ្ធិទាំងអស់ក្នុងប្រព័ន្ធ' : 'System Permissions Management' }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ app()->getLocale() === 'km' ? 'មើល និងបន្ថែមសិទ្ធិថ្មីតាម Module នីមួយៗក្នុងប្រព័ន្ធ' : 'View and add custom permission action keys categorized by module' }}
                </p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all cursor-pointer no-underline border border-slate-200">
                ⬅️ {{ app()->getLocale() === 'km' ? 'ត្រឡប់ទៅតួនាទី (Back to Roles)' : 'Back to Roles' }}
            </a>
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

    <!-- Add Custom Permission Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6 mb-6">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
            ➕ {{ app()->getLocale() === 'km' ? 'បន្ថែមសិទ្ធិថ្មី (Add New Permission)' : 'Add New Permission' }}
        </h2>
        <form action="{{ route('admin.permissions.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label for="module" class="block text-xs font-bold text-slate-600 mb-1">
                    {{ app()->getLocale() === 'km' ? 'ឈ្មោះ Module' : 'Module Name' }}
                </label>
                <input type="text" name="module" id="module" placeholder="e.g. sales, customers, reports..." required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
            </div>

            <div>
                <label for="action" class="block text-xs font-bold text-slate-600 mb-1">
                    {{ app()->getLocale() === 'km' ? 'ឈ្មោះសកម្មភាព (Action)' : 'Action Name' }}
                </label>
                <input type="text" name="action" id="action" placeholder="e.g. view, create, edit, delete, export..." required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-all border-none cursor-pointer">
                    ➕ {{ app()->getLocale() === 'km' ? 'បន្ថែមសិទ្ធិ' : 'Add Permission' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Permissions List Grouped -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($groupedPermissions as $module => $permissions)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <span class="text-xs font-extrabold uppercase text-indigo-700 tracking-wider flex items-center gap-2">
                        📁 {{ strtoupper($module) }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 font-mono">
                        {{ count($permissions) }} permissions
                    </span>
                </div>

                <div class="space-y-2">
                    @foreach($permissions as $permission)
                        <div class="flex items-center justify-between py-1.5 px-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-xs font-semibold text-slate-700 font-mono">
                                🔑 {{ $permission->name }}
                            </span>
                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបសិទ្ធិនេះមែនទេ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-400 hover:text-rose-600 text-[11px] font-bold border-none bg-transparent cursor-pointer">
                                    ❌
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
