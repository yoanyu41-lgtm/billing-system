@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    
    <!-- Top Action & Header Bar -->
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-xl font-bold shadow-sm flex-shrink-0">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                    {{ app()->getLocale() === 'km' ? 'កែប្រែព័ត៌មានអ្នកផ្គត់ផ្គង់' : 'Edit Supplier' }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    {{ app()->getLocale() === 'km' ? 'បច្ចុប្បន្នភាពព័ត៌មានអ្នកផ្គត់ផ្គង់' : 'Update details for' }} "<span class="font-bold text-slate-800">{{ $supplier->name }}</span>"
                </p>
            </div>
        </div>

        <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 px-4 py-2.5 rounded-xl transition duration-150 shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
            {{ __('app.back') }}
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden">
        <div class="h-1.5 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600"></div>

        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="p-6 sm:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <!-- Supplier Name -->
                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        {{ app()->getLocale() === 'km' ? 'ឈ្មោះអ្នកផ្គត់ផ្គង់ / ក្រុមហ៊ុន' : 'Supplier / Company Name' }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-building text-sm"></i>
                        </div>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $supplier->name) }}" 
                            required 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border text-sm font-medium transition duration-150 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent {{ $errors->has('name') ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-slate-50/50 hover:bg-white' }}" 
                            placeholder="{{ app()->getLocale() === 'km' ? 'ឧទាហរណ៍៖ ក្រុមហ៊ុន អាណាចក្រ កុំព្យូទ័រ' : 'Supplier Company or Agent Name' }}"
                        >
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs font-semibold mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        {{ app()->getLocale() === 'km' ? 'លេខទូរស័ព្ទទំនាក់ទំនង' : 'Phone Number' }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-phone text-sm"></i>
                        </div>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', $supplier->phone) }}" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border text-sm font-medium transition duration-150 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent {{ $errors->has('phone') ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-slate-50/50 hover:bg-white' }}" 
                            placeholder="{{ app()->getLocale() === 'km' ? 'ឧទាហរណ៍៖ 012 345 678' : 'Phone Number' }}"
                        >
                    </div>
                    @error('phone')
                        <p class="text-red-500 text-xs font-semibold mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        {{ app()->getLocale() === 'km' ? 'អាសយដ្ឋានអ៊ីមែល' : 'Email Address' }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $supplier->email) }}" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border text-sm font-medium transition duration-150 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent {{ $errors->has('email') ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-slate-50/50 hover:bg-white' }}" 
                            placeholder="supplier@company.com"
                        >
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs font-semibold mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Business Address -->
                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        {{ app()->getLocale() === 'km' ? 'អាសយដ្ឋានទីតាំងក្រុមហ៊ុន' : 'Business Address' }}
                    </label>
                    <div class="relative">
                        <div class="absolute top-3.5 left-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-location-dot text-sm"></i>
                        </div>
                        <textarea 
                            name="address" 
                            rows="3" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border text-sm font-medium transition duration-150 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent {{ $errors->has('address') ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-slate-50/50 hover:bg-white' }}" 
                            placeholder="{{ app()->getLocale() === 'km' ? 'ឧទាហរណ៍៖ ផ្ទះលេខ..., ផ្លូវ..., សង្កាត់..., រាជធានីភ្នំពេញ' : 'Business Address' }}"
                        >{{ old('address', $supplier->address) }}</textarea>
                    </div>
                    @error('address')
                        <p class="text-red-500 text-xs font-semibold mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.suppliers.index') }}" class="px-6 py-2.5 text-xs font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition duration-150 shadow-sm">
                    {{ __('app.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-md transition duration-150">
                    <i class="fas fa-sync-alt mr-2"></i>
                    {{ __('app.update') }}
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
