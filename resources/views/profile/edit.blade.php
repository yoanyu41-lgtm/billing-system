@extends('layouts.app')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px 20px;
    }
    
    .profile-header {
        text-align: center;
        margin-bottom: 35px;
    }
    
    .profile-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    .profile-header p {
        font-size: 14px;
        color: #64748b;
    }
    
    .profile-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }
    
    .profile-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    .profile-card-icon {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .profile-card-title {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    
    .profile-avatar-section {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-bottom: 25px;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
    }
    
    .profile-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }
    
    .profile-avatar-wrapper {
        position: relative;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 700;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25);
        border: 4px solid white;
    }
    
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-badge {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 28px;
        height: 28px;
        background: #10b981;
        border: 3px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .profile-avatar-info {
        flex: 1;
    }
    
    .profile-avatar-info h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 10px;
    }
    
    .profile-file-input {
        position: relative;
        display: inline-block;
    }
    
    .profile-file-input input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .profile-file-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .profile-file-label:hover {
        background: #f8fafc;
        border-color: #6366f1;
        color: #6366f1;
    }
    
    .profile-hint {
        font-size: 12px;
        color: #64748b;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }
    
    .form-label i {
        color: #6366f1;
        font-size: 14px;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .form-input:disabled {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }
    
    .btn-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    @media (max-width: 768px) {
        .profile-avatar-section {
            flex-direction: column;
            text-align: center;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
        
        .profile-container {
            padding: 20px 15px;
        }
    }
</style>

<div class="profile-container">
    {{-- Profile Header --}}
    <div class="profile-header">
        <h1>⚙️ {{ __('កែសម្រួលប្រវត្តិរូប') }}</h1>
        <p>{{ __('គ្រប់គ្រងព័ត៌មានគណនី និងសុវត្ថិភាពរបស់អ្នក') }}</p>
    </div>

    {{-- Profile Information & Picture Card (Merged) --}}
    <div class="profile-card">
        <div class="profile-card-header">
            <div class="profile-card-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2 class="profile-card-title">{{ __('ព័ត៌មានប្រវត្តិរូប') }}</h2>
        </div>
        
        <form method="POST" action="{{ route('profile.update-info') }}" enctype="multipart/form-data" id="profile-info-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="remove_profile_image" id="remove_profile_image" value="0">
            
            {{-- Profile Avatar Section inside Info Card --}}
            <div class="profile-avatar-section">
                <div class="profile-avatar-wrapper">
                    <div id="profile-preview" class="profile-avatar">
                        @if(auth()->user()->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->profile_image))
                            <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="avatar-badge">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                
                <div class="profile-avatar-info">
                    <h3>{{ __('រូបភាពប្រវត្តិរូប') }}</h3>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="profile-file-input">
                            <input type="file" name="profile_image" id="profile_image" accept="image/*" onchange="previewImage(event)">
                            <label for="profile_image" class="profile-file-label">
                                <i class="fas fa-upload"></i>
                                <span id="file-label-text">{{ __('ជ្រើសរើសរូបភាពថ្មី') }}</span>
                            </label>
                        </div>
                        @if(auth()->user()->profile_image)
                        <button type="button" onclick="markDeletePhoto()" id="btn-remove-photo" class="btn btn-danger" style="padding: 10px 16px; font-size: 13px;">
                            <i class="fas fa-trash-alt"></i>
                            {{ __('លុបរូបភាព') }}
                        </button>
                        @endif
                    </div>
                    <p class="profile-hint">
                        <i class="fas fa-info-circle"></i>
                        {{ __('អនុញ្ញាត: JPG, PNG, GIF • ទំហំអតិបរមា: 2MB') }}
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user"></i>
                    {{ __('ឈ្មោះ') }}
                </label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" required class="form-input" placeholder="បញ្ចូលឈ្មោះរបស់អ្នក">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-envelope"></i>
                    {{ __('អ៊ីមែល') }}
                </label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" required class="form-input" placeholder="បញ្ចូលអ៊ីមែលរបស់អ្នក">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-shield-alt"></i>
                    {{ __('តួនាទី') }}
                </label>
                <input type="text" value="{{ ucfirst(auth()->user()->role) }}" disabled class="form-input">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-circle"></i>
                    {{ __('រក្សាទុកការផ្លាស់ប្តូរ') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Change Password Card --}}
    <div class="profile-card">
        <div class="profile-card-header">
            <div class="profile-card-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2 class="profile-card-title">{{ __('ផ្លាស់ប្តូរពាក្យសម្ងាត់') }}</h2>
        </div>
        
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-key"></i>
                    {{ __('ពាក្យសម្ងាត់បច្ចុប្បន្ន') }}
                </label>
                <input type="password" name="current_password" required class="form-input" placeholder="បញ្ចូលពាក្យសម្ងាត់បច្ចុប្បន្ន">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-lock"></i>
                    {{ __('ពាក្យសម្ងាត់ថ្មី') }}
                </label>
                <input type="password" name="password" required class="form-input" placeholder="បញ្ចូលពាក្យសម្ងាត់ថ្មី">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-check-circle"></i>
                    {{ __('បញ្ជាក់ពាក្យសម្ងាត់ថ្មី') }}
                </label>
                <input type="password" name="password_confirmation" required class="form-input" placeholder="បញ្ចូលពាក្យសម្ងាត់ថ្មីម្តងទៀត">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-shield-alt"></i>
                    {{ __('ដំឡើងពាក្យសម្ងាត់') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('profile-preview');
    const removeInput = document.getElementById('remove_profile_image');
    
    if (file) {
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('⚠️ ទំហំឯកសារធំពេក! សូមជ្រើសរើសឯកសារតូចជាង 2MB');
            event.target.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.match('image.*')) {
            alert('⚠️ សូមជ្រើសរើសឯកសាររូបភាពតែប៉ុណ្ណោះ!');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
            
            // Update label text
            const labelText = document.getElementById('file-label-text');
            if (labelText) {
                labelText.textContent = file.name;
            }
            if (removeInput) {
                removeInput.value = '0';
            }
        }
        
        reader.readAsDataURL(file);
    }
}

function markDeletePhoto() {
    if (confirm('⚠️ តើអ្នកប្រាកដថាចង់លុបរូបភាពនេះ?\n\nរូបភាពនឹងត្រូវលុបពេលអ្នកចុច "រក្សាទុកការផ្លាស់ប្តូរ" នៅខាងក្រោម។')) {
        const removeInput = document.getElementById('remove_profile_image');
        const fileInput = document.getElementById('profile_image');
        const preview = document.getElementById('profile-preview');
        const btnRemove = document.getElementById('btn-remove-photo');
        const labelText = document.getElementById('file-label-text');

        if (removeInput) removeInput.value = '1';
        if (fileInput) fileInput.value = '';
        if (preview) preview.innerHTML = '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}';
        if (btnRemove) btnRemove.style.display = 'none';
        if (labelText) labelText.textContent = '{{ __('ជ្រើសរើសរូបភាពថ្មី') }}';
    }
}
</script>

@endsection
