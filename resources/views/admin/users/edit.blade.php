@extends('layouts.app')

@section('content')

<style>
.edit-user-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 0 16px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #6366f1;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 16px;
    transition: all 0.2s;
}

.back-link:hover {
    color: #4f46e5;
    transform: translateX(-4px);
}

.page-header {
    margin-bottom: 28px;
}

.page-title {
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
    letter-spacing: -0.5px;
}

.page-subtitle {
    font-size: 15px;
    color: #64748b;
}

.form-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 10px 40px rgba(0,0,0,0.03);
}

.profile-section {
    padding-bottom: 28px;
    border-bottom: 2px solid #f1f5f9;
    margin-bottom: 28px;
}

.section-label {
    display: block;
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 16px;
}

.profile-upload-wrapper {
    display: flex;
    align-items: center;
    gap: 24px;
}

.profile-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 800;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(99,102,241,0.25);
    border: 4px solid white;
}

.profile-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-input-wrapper {
    flex: 1;
}

.file-input {
    font-size: 14px;
    padding: 10px 14px;
    border: 2px dashed #e2e8f0;
    border-radius: 10px;
    width: 100%;
    cursor: pointer;
    transition: all 0.2s;
    background: #f8fafc;
}

.file-input:hover {
    border-color: #6366f1;
    background: #f1f5f9;
}

.file-hint {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 8px;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 10px;
}

.form-label i {
    color: #6366f1;
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

.form-input {
    width: 100%;
    padding: 13px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.2s;
    background: white;
}

.form-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.1);
}

.form-input:hover {
    border-color: #cbd5e1;
}

.form-select {
    width: 100%;
    padding: 13px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.form-select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.1);
}

.form-select:hover {
    border-color: #cbd5e1;
}

.button-group {
    display: flex;
    gap: 12px;
    padding-top: 28px;
    border-top: 2px solid #f1f5f9;
}

.btn-primary {
    flex: 1;
    padding: 14px 28px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 14px rgba(99,102,241,0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99,102,241,0.5);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-secondary {
    flex: 1;
    padding: 14px 28px;
    background: #f1f5f9;
    color: #64748b;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: #e2e8f0;
    color: #475569;
}

@media (max-width: 640px) {
    .form-card {
        padding: 24px 20px;
    }
    
    .profile-upload-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .button-group {
        flex-direction: column;
    }
}
</style>

<div class="edit-user-container">
    {{-- Header --}}
    <div class="page-header">
        <a href="{{ route('admin.users.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
        <h1 class="page-title">Edit User</h1>
        <p class="page-subtitle">Update user information and profile picture</p>
    </div>

    {{-- Form Card --}}
    <div class="form-card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
            @csrf 
            @method('PUT')
            
            {{-- Profile Picture Section --}}
            <div class="profile-section">
                <label class="section-label">Profile Picture</label>
                <div class="profile-upload-wrapper">
                    <div id="user-preview" class="profile-preview">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" id="preview-img">
                        @else
                            <span id="preview-initial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="upload-input-wrapper">
                        <div style="display:flex;gap:12px;margin-bottom:12px;">
                            <label for="user_profile_image" style="flex:1;padding:10px 20px;background:#6366f1;color:white;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s;text-align:center;" onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                                <i class="fas fa-upload"></i> Upload New
                            </label>
                            <input type="file" name="profile_image" id="user_profile_image" accept="image/*" onchange="previewUserImage(event)" style="display:none;">
                            
                            @if($user->profile_image)
                            <button type="button" onclick="removeProfileImage()" style="flex:1;padding:10px 20px;background:#fee2e2;color:#ef4444;border:none;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                            <input type="hidden" name="remove_image" id="remove_image" value="0">
                            @endif
                        </div>
                        <p class="file-hint">Allowed: JPG, PNG, WEBP. Max size: 2MB</p>
                    </div>
                </div>
            </div>

            {{-- Name Field --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user"></i> Full Name
                </label>
                <input type="text" name="name" value="{{ $user->name }}" required class="form-input" placeholder="Enter full name">
            </div>

            {{-- Email Field --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input type="email" name="email" value="{{ $user->email }}" required class="form-input" placeholder="Enter email address">
            </div>

            {{-- Role Field --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-shield-alt"></i> User Role
                </label>
                <select name="role" required class="form-select">
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="button-group">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Update User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewUserImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('user-preview');
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" id="preview-img" style="width:100%;height:100%;object-fit:cover;">';
        }
        
        reader.readAsDataURL(file);
        
        // Reset remove flag when new image is selected
        document.getElementById('remove_image').value = '0';
    }
}

function removeProfileImage() {
    const preview = document.getElementById('user-preview');
    const userName = '{{ $user->name }}';
    const initial = userName.charAt(0).toUpperCase();
    
    // Show initial instead of image
    preview.innerHTML = '<span id="preview-initial" style="font-size:36px;font-weight:800;">' + initial + '</span>';
    
    // Clear file input
    document.getElementById('user_profile_image').value = '';
    
    // Set remove flag
    document.getElementById('remove_image').value = '1';
}
</script>

@endsection