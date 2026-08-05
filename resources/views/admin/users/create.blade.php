@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6 max-w-3xl mx-auto">
    <!-- Back Link -->
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-indigo-600 hover:text-indigo-700 mb-5 transition-all no-underline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>{{ app()->getLocale() === 'km' ? 'ត្រឡប់ទៅកាន់បញ្ជីអ្នកប្រើប្រាស់' : 'Back to User List' }}</span>
    </a>

    <!-- Page Header -->
    <div class="mb-6 flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold shadow-2xs border border-indigo-100">
            👤
        </div>
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'បន្ថែមអ្នកប្រើប្រាស់ថ្មី' : 'Add New User' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() === 'km' ? 'បញ្ចូលព័ត៌មានលម្អិតដើម្បីបង្កើតគណនីប្រើប្រាស់ថ្មីក្នុងប្រព័ន្ធ' : 'Enter details to create a new user account' }}
            </p>
        </div>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 px-4 py-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold shadow-2xs">
            <div class="flex items-center gap-2 font-bold mb-1.5 text-rose-900">
                <span>⚠️</span>
                <span>{{ app()->getLocale() === 'km' ? 'សូមពិនិត្យមើលព័ត៌មានដែលបានបញ្ចូល ៖' : 'Please check the input errors:' }}</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-6 md:p-8">
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Profile Picture Section -->
            <div class="pb-6 border-b border-slate-100">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                    {{ app()->getLocale() === 'km' ? 'រូបថតប្រវត្តិរូប (Profile Picture)' : 'Profile Picture' }}
                </label>
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-full bg-indigo-50 text-indigo-600 font-extrabold text-2xl flex items-center justify-center border-2 border-indigo-100 shadow-2xs overflow-hidden flex-shrink-0" id="avatarPreview">
                        <span>👤</span>
                    </div>
                    <div class="flex-1">
                        <label for="profile_image" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer border border-slate-200/80">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ app()->getLocale() === 'km' ? 'ជ្រើសរើសរូបថត' : 'Choose Photo' }}</span>
                        </label>
                        <input type="file" name="profile_image" id="profile_image" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden" onchange="previewImage(this)">
                        <p class="text-[11px] text-slate-400 mt-2">
                            {{ app()->getLocale() === 'km' ? 'អនុញ្ញាត ៖ JPG, PNG, WEBP (ទំហំអតិបរមា 2MB)' : 'Allowed: JPG, PNG, WEBP (Max 2MB)' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <span class="text-indigo-600">👤</span>
                        <span>{{ app()->getLocale() === 'km' ? 'ឈ្មោះពេញ' : 'Full Name' }} <span class="text-rose-500">*</span></span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="{{ app()->getLocale() === 'km' ? 'បញ្ចូលឈ្មោះពេញ...' : 'Enter full name...' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs">
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <span class="text-indigo-600">✉️</span>
                        <span>{{ app()->getLocale() === 'km' ? 'អាសយដ្ឋានអ៉ីមែល' : 'Email Address' }} <span class="text-rose-500">*</span></span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="example@domain.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs font-mono">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <span class="text-indigo-600">🔒</span>
                        <span>{{ app()->getLocale() === 'km' ? 'ពាក្យសម្ងាត់' : 'Password' }} <span class="text-rose-500">*</span></span>
                    </label>
                    <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs">
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <span class="text-indigo-600">🛡️</span>
                        <span>{{ app()->getLocale() === 'km' ? 'តួនាទីក្នុងប្រព័ន្ធ' : 'User Role' }} <span class="text-rose-500">*</span></span>
                    </label>
                    <select name="role" id="role" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-2xs cursor-pointer">
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>👤 បុគ្គលិក (Staff)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>🛡️ អ្នកគ្រប់គ្រង (Admin)</option>
                    </select>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all no-underline">
                    {{ app()->getLocale() === 'km' ? 'បោះបង់' : 'Cancel' }}
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs hover:shadow-md transition-all cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ app()->getLocale() === 'km' ? 'រក្សាទុកអ្នកប្រើប្រាស់' : 'Save User' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const previewContainer = document.getElementById('avatarPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewContainer.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection