@extends('layouts.app')

@section('content')

<div style="margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h1 style="font-size:28px;font-weight:700;color:#0f172a;margin-bottom:4px;">User Management</h1>
        <p style="font-size:14px;color:#64748b;">Manage system users and their permissions</p>
    </div>
    <a href="{{ route('admin.users.create') }}" style="background:#6366f1;color:white;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(99,102,241,0.3);">
        <i class="fas fa-plus"></i> Add User
    </a>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;">
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">User</th>
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Email</th>
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Role</th>
                    <th style="padding:14px 16px;text-align:right;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr style="border-bottom:1px solid #f8fafc;transition:background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    <td style="padding:14px 16px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:40px;height:40px;border-radius:50%;background:#6366f1;color:white;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;overflow:hidden;flex-shrink:0;">
                                @if($user->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_image))
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.style.display='none'">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:600;color:#0f172a;">{{ $user->name }}</div>
                                <div style="font-size:12px;color:#64748b;">ID: {{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="font-size:14px;color:#334155;">{{ $user->email }}</div>
                    </td>
                    <td style="padding:14px 16px;">
                        @if($user->role === 'admin')
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#eff6ff;color:#3b82f6;border-radius:6px;font-size:12px;font-weight:600;">
                                <i class="fas fa-shield-alt"></i> Admin
                            </span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f0fdf4;color:#22c55e;border-radius:6px;font-size:12px;font-weight:600;">
                                <i class="fas fa-user"></i> User
                            </span>
                        @endif
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="display:flex;gap:8px;justify-content:flex-end;">
                            <a href="{{ route('admin.users.edit', $user) }}" style="padding:8px 14px;background:#f0fdf4;color:#22c55e;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;transition:all 0.2s;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <button onclick="confirmResetPassword('{{ $user->id }}', '{{ $user->name }}')" style="padding:8px 14px;background:#fef3c7;color:#f59e0b;border-radius:8px;border:none;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                                <i class="fas fa-key"></i> Reset Password
                            </button>
                            
                            <button onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')" style="padding:8px 14px;background:#fee2e2;color:#ef4444;border-radius:8px;border:none;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                        
                        {{-- Hidden forms --}}
                        <form id="reset-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.reset-password', $user) }}" style="display:none;">
                            @csrf
                        </form>
                        
                        <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:none;">
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

{{-- Confirmation Modal Styles --}}
<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.2s;
}
.modal-overlay.active {
    display: flex;
}
.modal-content {
    background: white;
    border-radius: 16px;
    padding: 24px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: slideUp 0.3s;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>

{{-- Confirmation Modal --}}
<div id="confirmModal" class="modal-overlay" onclick="closeModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div style="text-align:center;margin-bottom:20px;">
            <div id="modalIcon" style="width:60px;height:60px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:28px;"></div>
            <h3 id="modalTitle" style="font-size:20px;font-weight:700;color:#0f172a;margin-bottom:8px;"></h3>
            <p id="modalMessage" style="font-size:14px;color:#64748b;"></p>
        </div>
        <div style="display:flex;gap:12px;">
            <button onclick="closeModal()" style="flex:1;padding:12px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-weight:600;cursor:pointer;font-size:14px;">
                Cancel
            </button>
            <button id="modalConfirm" onclick="confirmAction()" style="flex:1;padding:12px;border:none;border-radius:10px;color:white;font-weight:600;cursor:pointer;font-size:14px;">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
let currentAction = null;
let currentFormId = null;

function confirmResetPassword(userId, userName) {
    currentAction = 'reset';
    currentFormId = 'reset-form-' + userId;
    
    document.getElementById('modalIcon').style.background = '#fef3c7';
    document.getElementById('modalIcon').style.color = '#f59e0b';
    document.getElementById('modalIcon').innerHTML = '<i class="fas fa-key"></i>';
    document.getElementById('modalTitle').textContent = 'Reset Password?';
    document.getElementById('modalMessage').textContent = 'Reset password for "' + userName + '" to default "password"?';
    document.getElementById('modalConfirm').style.background = '#f59e0b';
    
    document.getElementById('confirmModal').classList.add('active');
}

function confirmDelete(userId, userName) {
    currentAction = 'delete';
    currentFormId = 'delete-form-' + userId;
    
    document.getElementById('modalIcon').style.background = '#fee2e2';
    document.getElementById('modalIcon').style.color = '#ef4444';
    document.getElementById('modalIcon').innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
    document.getElementById('modalTitle').textContent = 'Delete User?';
    document.getElementById('modalMessage').textContent = 'Are you sure you want to delete "' + userName + '"? This action cannot be undone.';
    document.getElementById('modalConfirm').style.background = '#ef4444';
    
    document.getElementById('confirmModal').classList.add('active');
}

function confirmAction() {
    if (currentFormId) {
        document.getElementById(currentFormId).submit();
    }
}

function closeModal(event) {
    if (!event || event.target.id === 'confirmModal') {
        document.getElementById('confirmModal').classList.remove('active');
        currentAction = null;
        currentFormId = null;
    }
}
</script>

@endsection