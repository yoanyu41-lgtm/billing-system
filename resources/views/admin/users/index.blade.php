@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold shadow-2xs border border-indigo-100">
                👥
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                        {{ app()->getLocale() === 'km' ? 'គ្រប់គ្រងអ្នកប្រើប្រាស់' : 'User Management' }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100 font-mono">
                        {{ count($users) }} នាក់
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ app()->getLocale() === 'km' ? 'គ្រប់គ្រងគណនីបុគ្គលិក អ្នកគ្រប់គ្រង និងសិទ្ធិប្រើប្រាស់ប្រព័ន្ធ' : 'Manage system users, roles and permissions' }}
                </p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs hover:shadow-md transition-all cursor-pointer no-underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>{{ app()->getLocale() === 'km' ? 'បន្ថែមអ្នកប្រើប្រាស់' : 'Add User' }}</span>
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

    <!-- Action Bar: Real-Time Search & Role Filter -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
        <div class="relative w-full sm:w-80">
            <input type="text" id="userSearchInput" onkeyup="filterUsers()" placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកឈ្មោះ ឬអ៉ីមែល...' : 'Search name or email...' }}" class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs">
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <div class="flex items-center gap-2.5">
            <label for="roleFilter" class="text-xs font-bold text-slate-500 whitespace-nowrap hidden sm:inline-block">តម្រៀបតាមតួនាទី៖</label>
            <select id="roleFilter" onchange="filterUsers()" class="py-2 px-3 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs">
                <option value="all">តួនាទីទាំងអស់ (All Roles)</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
            </select>
        </div>
    </div>

    <!-- Users Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse" id="usersTable">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">#</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'km' ? 'អ្នកប្រើប្រាស់' : 'User' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'km' ? 'អ៉ីមែល' : 'Email' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'km' ? 'តួនាទី' : 'Role' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'km' ? 'សកម្មភាព' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($users as $index => $user)
                    @php
                        $assignedRoleName = $user->roles->first()?->name ?? ucfirst($user->role);
                        $roleLower = strtolower($assignedRoleName);
                    @endphp
                    <tr class="user-row hover:bg-slate-50/80 transition-colors" data-role="{{ $roleLower }}">
                        <td class="px-5 py-3.5 font-bold text-slate-400 font-mono">{{ $index + 1 }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm shadow-2xs overflow-hidden flex-shrink-0 border border-indigo-200/60">
                                    @if($user->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_image))
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-xs">{{ $user->name }}</div>
                                    <div class="text-[10px] font-mono text-slate-400">ID: #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-medium text-slate-600 font-mono">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ $user->email }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($roleLower === 'admin')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs">
                                    {{ $assignedRoleName }}
                                </span>
                            @elseif($roleLower === 'manager')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs">
                                    {{ $assignedRoleName }}
                                </span>
                            @elseif($roleLower === 'cashier')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                    {{ $assignedRoleName }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80 shadow-2xs">
                                    {{ $assignedRoleName }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Edit -->
                                <a href="{{ route('admin.users.edit', $user) }}" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs border border-emerald-200/80 transition-all flex items-center gap-1 cursor-pointer no-underline" title="{{ __('app.edit') }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>កែប្រែ</span>
                                </a>

                                <!-- Reset Password -->
                                <button type="button" onclick="confirmResetPassword('{{ $user->id }}', '{{ $user->name }}')" class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs border border-amber-200/80 transition-all flex items-center gap-1 cursor-pointer" title="Reset Password">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    <span>Reset Password</span>
                                </button>

                                <!-- Delete -->
                                <button type="button" onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200/80 transition-all flex items-center gap-1 cursor-pointer" title="Delete">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>លុប</span>
                                </button>
                            </div>

                            <!-- Hidden forms -->
                            <form id="reset-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="hidden">
                                @csrf
                            </form>

                            <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center z-50 transition-opacity" onclick="closeModal(event)">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl transform transition-all border border-slate-100 animate-in fade-in zoom-in duration-200" onclick="event.stopPropagation()">
        <div class="text-center mb-5">
            <div id="modalIcon" class="w-14 h-14 rounded-2xl mx-auto mb-3 flex items-center justify-center text-2xl font-bold shadow-2xs"></div>
            <h3 id="modalTitle" class="text-base font-bold text-slate-800 mb-1"></h3>
            <p id="modalMessage" class="text-xs text-slate-500 leading-relaxed"></p>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="closeModal()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition-colors cursor-pointer">
                {{ app()->getLocale() === 'km' ? 'បោះបង់' : 'Cancel' }}
            </button>
            <button type="button" id="modalConfirm" onclick="confirmAction()" class="flex-1 py-2.5 text-white rounded-xl font-bold text-xs shadow-xs transition-all cursor-pointer">
                {{ app()->getLocale() === 'km' ? 'យល់ព្រម' : 'Confirm' }}
            </button>
        </div>
    </div>
</div>

<script>
function filterUsers() {
    const query = document.getElementById('userSearchInput')?.value.toLowerCase().trim() || '';
    const role = document.getElementById('roleFilter')?.value || 'all';
    const rows = document.querySelectorAll('.user-row');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowRole = row.getAttribute('data-role');
        
        const matchesQuery = (query === '' || text.includes(query));
        const matchesRole = (role === 'all' || rowRole === role);

        if (matchesQuery && matchesRole) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

let currentAction = null;
let currentFormId = null;

function confirmResetPassword(userId, userName) {
    currentAction = 'reset';
    currentFormId = 'reset-form-' + userId;

    const modalIcon = document.getElementById('modalIcon');
    modalIcon.className = 'w-14 h-14 rounded-2xl mx-auto mb-3 flex items-center justify-center text-2xl font-bold shadow-2xs bg-amber-50 text-amber-600 border border-amber-200/80';
    modalIcon.innerHTML = '🔑';

    document.getElementById('modalTitle').textContent = 'កំណត់ពាក្យសម្ងាត់ឡើងវិញ?';
    document.getElementById('modalMessage').textContent = 'តើអ្នកប្រាកដជាចង់កំណត់ពាក្យសម្ងាត់របស់អ្នកប្រើប្រាស់ "' + userName + '" ទៅជា "password" មែនទេ?';

    const modalConfirm = document.getElementById('modalConfirm');
    modalConfirm.className = 'flex-1 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs shadow-xs transition-all cursor-pointer';

    const modal = document.getElementById('confirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function confirmDelete(userId, userName) {
    currentAction = 'delete';
    currentFormId = 'delete-form-' + userId;

    const modalIcon = document.getElementById('modalIcon');
    modalIcon.className = 'w-14 h-14 rounded-2xl mx-auto mb-3 flex items-center justify-center text-2xl font-bold shadow-2xs bg-rose-50 text-rose-600 border border-rose-200/80';
    modalIcon.innerHTML = '⚠️';

    document.getElementById('modalTitle').textContent = 'លុបអ្នកប្រើប្រាស់?';
    document.getElementById('modalMessage').textContent = 'តើអ្នកប្រាកដជាចង់លុបអ្នកប្រើប្រាស់ "' + userName + '" នេះមែនទេ? ទិន្នន័យនឹងត្រូវផ្លាស់ទីទៅធុងសំរាម។';

    const modalConfirm = document.getElementById('modalConfirm');
    modalConfirm.className = 'flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs shadow-xs transition-all cursor-pointer';

    const modal = document.getElementById('confirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function confirmAction() {
    if (currentFormId) {
        document.getElementById(currentFormId).submit();
    }
}

function closeModal(event) {
    if (!event || event.target.id === 'confirmModal') {
        const modal = document.getElementById('confirmModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        currentAction = null;
        currentFormId = null;
    }
}
</script>
@endsection