@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold shadow-2xs border border-indigo-100">
                🔐
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                        {{ app()->getLocale() === 'km' ? 'គ្រប់គ្រងតួនាទី និងសិទ្ធិ' : 'Roles & Permissions Management' }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100 font-mono">
                        {{ count($roles) }} {{ app()->getLocale() === 'km' ? 'តួនាទី' : 'Roles' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ app()->getLocale() === 'km' ? 'កំណត់តួនាទី និងបែងចែកសិទ្ធិប្រើប្រាស់មុខងារប្រព័ន្ធជូនបុគ្គលិក' : 'Define user roles and assign access permissions across system modules' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all cursor-pointer no-underline border border-slate-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>{{ app()->getLocale() === 'km' ? 'បញ្ជីសិទ្ធិទាំងអស់ (Permissions)' : 'All Permissions' }}</span>
            </a>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs hover:shadow-md transition-all cursor-pointer no-underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>{{ app()->getLocale() === 'km' ? 'បន្ថែមតួនាទីថ្មី' : 'Add New Role' }}</span>
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

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($roles as $role)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 tracking-tight">
                                {{ $role->name }}
                            </h3>
                            <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                {{ app()->getLocale() === 'km' ? 'ប្រព័ន្ធវិបសាយ' : 'Web System' }}
                            </span>
                        </div>

                        @if($role->name === 'Admin')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                System Core
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 my-4 py-3 px-3.5 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">
                                {{ app()->getLocale() === 'km' ? 'ចំនួនបុគ្គលិក' : 'Users' }}
                            </span>
                            <span class="text-sm font-extrabold text-slate-700 font-mono">
                                {{ $role->users_count }} {{ app()->getLocale() === 'km' ? 'នាក់' : 'users' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">
                                {{ app()->getLocale() === 'km' ? 'សិទ្ធិអនុញ្ញាត' : 'Permissions' }}
                            </span>
                            <span class="text-sm font-extrabold text-indigo-600 font-mono">
                                {{ $role->permissions_count }} {{ app()->getLocale() === 'km' ? 'សិទ្ធិ' : 'active' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-5 py-3.5 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors no-underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>{{ app()->getLocale() === 'km' ? 'កែប្រែសិទ្ធិ (Edit Permissions)' : 'Edit Permissions' }}</span>
                    </a>

                    @if($role->name !== 'Admin')
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបតួនាទីនេះមែនទេ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-bold transition-colors cursor-pointer border-none bg-transparent">
                                {{ app()->getLocale() === 'km' ? 'លុប' : 'Delete' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
