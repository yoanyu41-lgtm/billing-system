@extends('layouts.app')

@section('content')

<div style="max-width:800px;margin:0 auto;">
    <h1 style="font-size:24px;font-weight:700;color:#0f172a;margin-bottom:24px;">Profile Settings</h1>

    {{-- Profile Picture Card --}}
    <div class="card" style="margin-bottom:20px;">
        <h2 style="font-size:18px;font-weight:600;color:#0f172a;margin-bottom:16px;">Profile Picture</h2>
        
        <form method="POST" action="{{ route('profile.update-picture') }}" enctype="multipart/form-data" id="profile-picture-form">
            @csrf
            <div style="display:flex;align-items:center;gap:24px;margin-bottom:20px;">
                <div id="profile-preview" style="width:100px;height:100px;border-radius:50%;background:#6366f1;color:white;display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:700;overflow:hidden;flex-shrink:0;">
                    @if(auth()->user()->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->profile_image))
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div style="flex:1;">
                    <input type="file" name="profile_image" id="profile_image" accept="image/*" onchange="previewImage(event)" style="margin-bottom:12px;font-size:14px;">
                    <p style="font-size:12px;color:#64748b;">Allowed: JPG, PNG, GIF. Max size: 2MB</p>
                </div>
            </div>
            <div style="display:flex;gap:12px;">
                <button type="submit" style="background:#6366f1;color:white;padding:10px 20px;border-radius:8px;border:none;font-weight:600;cursor:pointer;font-size:14px;">
                    Upload Picture
                </button>
                @if(auth()->user()->profile_image)
                <button type="button" onclick="document.getElementById('remove-picture-form').submit();" style="background:#ef4444;color:white;padding:10px 20px;border-radius:8px;border:none;font-weight:600;cursor:pointer;font-size:14px;">
                    Remove Picture
                </button>
                @endif
            </div>
        </form>

        @if(auth()->user()->profile_image)
        <form id="remove-picture-form" method="POST" action="{{ route('profile.remove-picture') }}" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        @endif
    </div>

    {{-- Profile Information Card --}}
    <div class="card" style="margin-bottom:20px;">
        <h2 style="font-size:18px;font-weight:600;color:#0f172a;margin-bottom:16px;">Profile Information</h2>
        
        <form method="POST" action="{{ route('profile.update-info') }}">
            @csrf
            @method('PATCH')
            
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:6px;">Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" required style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:6px;">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" required style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:6px;">Role</label>
                <input type="text" value="{{ ucfirst(auth()->user()->role) }}" disabled style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;background:#f8fafc;color:#64748b;">
            </div>

            <button type="submit" style="background:#6366f1;color:white;padding:10px 20px;border-radius:8px;border:none;font-weight:600;cursor:pointer;font-size:14px;">
                Update Information
            </button>
        </form>
    </div>

    {{-- Change Password Card --}}
    <div class="card">
        <h2 style="font-size:18px;font-weight:600;color:#0f172a;margin-bottom:16px;">Change Password</h2>
        
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:6px;">Current Password</label>
                <input type="password" name="current_password" required style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:6px;">New Password</label>
                <input type="password" name="password" required style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:6px;">Confirm New Password</label>
                <input type="password" name="password_confirmation" required style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>

            <button type="submit" style="background:#6366f1;color:white;padding:10px 20px;border-radius:8px;border:none;font-weight:600;cursor:pointer;font-size:14px;">
                Change Password
            </button>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('profile-preview');
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="width:100%;height:100%;object-fit:cover;">';
        }
        
        reader.readAsDataURL(file);
    }
}
</script>

@endsection
