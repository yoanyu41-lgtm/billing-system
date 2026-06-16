@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">📢 ផ្សព្វផ្សាយសារ Telegram (Telegram Broadcast Center)</h1>
    <p class="text-xs text-slate-500 mt-1">ផ្ញើសារផ្សព្វផ្សាយ ប្រូម៉ូសិន ឬព័ត៌មានផ្សេងៗទៅកាន់អតិថិជនទាំងអស់ដែលមានភ្ជាប់ Telegram ID ក្នុងពេលតែមួយ។</p>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-emerald-100 p-4 text-emerald-800">
    ✓ {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 rounded-lg bg-rose-100 p-4 text-rose-800">
    ✗ {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 rounded-lg bg-rose-100 p-4 text-rose-800">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Stats Cards -->
<div class="grid gap-4 md:grid-cols-2 mb-6">
    <div class="rounded-xl bg-white p-5 shadow border border-slate-100 flex items-center justify-between">
        <div>
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">អតិថិជនបានភ្ជាប់ Telegram</span>
            <span class="block text-2xl font-bold text-slate-950 mt-1">{{ $totalLinked }} / {{ $totalCustomers }} នាក់</span>
        </div>
        <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
            <i class="fab fa-telegram-plane text-2xl"></i>
        </div>
    </div>
    
    <div class="rounded-xl bg-white p-5 shadow border border-slate-100 flex items-center justify-between">
        <div>
            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">អត្រាការភ្ជាប់គណនី (Linked Rate)</span>
            <span class="block text-2xl font-bold text-slate-950 mt-1">
                {{ $totalCustomers > 0 ? round(($totalLinked / $totalCustomers) * 100, 1) : 0 }}%
            </span>
        </div>
        <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600">
            <i class="fas fa-chart-pie text-2xl"></i>
        </div>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Message Composer Card -->
    <div class="rounded-xl bg-white p-6 shadow border border-slate-100 lg:col-span-2">
        <h2 class="text-base font-semibold text-slate-900 mb-4">✍️ សរសេរសារផ្សព្វផ្សាយ (Compose Message)</h2>
        
        <form method="POST" action="{{ route('admin.broadcast.send') }}">
            @csrf
            
            <div class="mb-4">
                <label for="message" class="mb-2 block text-sm font-medium text-slate-700">ខ្លឹមសារសារផ្សព្វផ្សាយ (Message Content)</label>
                <textarea 
                    name="message" 
                    id="message" 
                    rows="10" 
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" 
                    placeholder="វាយខ្លឹមសារសារផ្សព្វផ្សាយនៅទីនេះ..."
                    required>{{ old('message') }}</textarea>
                <p class="mt-2 text-xs text-slate-400">សារផ្សព្វផ្សាយនេះនឹងត្រូវផ្ញើទៅកាន់អតិថិជនចំនួន <b>{{ $totalLinked }}</b> នាក់ភ្លាមៗ។</p>
            </div>
            
            <div class="flex items-center justify-end gap-3">
                <button 
                    type="reset" 
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    សម្អាត (Clear)
                </button>
                <button 
                    type="submit" 
                    class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow-sm transition duration-150 flex items-center gap-2"
                    onclick="return confirm('តើលោកអ្នកពិតជាចង់ផ្ញើសារផ្សព្វផ្សាយនេះទៅកាន់អតិថិជនទាំងអស់មែនទេ?')">
                    <i class="fas fa-paper-plane"></i> ផ្ញើសារឥឡូវនេះ (Send Now)
                </button>
            </div>
        </form>
    </div>

    <!-- Formatting Guide Card -->
    <div class="rounded-xl bg-white p-6 shadow border border-slate-100">
        <h2 class="text-base font-semibold text-slate-900 mb-4">💡 ការប្រើប្រាស់ Markdown</h2>
        <p class="text-xs text-slate-600 mb-4">លោកអ្នកអាចប្រើប្រាស់កូដ Markdown ខាងក្រោមដើម្បីទម្រង់អក្សរនៅក្នុងសារ Telegram៖</p>
        
        <div class="space-y-4">
            <div class="rounded-lg bg-slate-50 p-3">
                <span class="block text-xs font-bold text-slate-700">អក្សរដិត (Bold)</span>
                <code class="text-xs text-blue-600">*ខ្លឹមសារដិត*</code>
                <span class="block text-xs text-slate-500 mt-1">លទ្ធផល: <b>ខ្លឹមសារដិត</b></span>
            </div>
            
            <div class="rounded-lg bg-slate-50 p-3">
                <span class="block text-xs font-bold text-slate-700">អក្សរទ្រេត (Italic)</span>
                <code class="text-xs text-blue-600">_ខ្លឹមសារទ្រេត_</code>
                <span class="block text-xs text-slate-500 mt-1">លទ្ធផល: <i>ខ្លឹមសារទ្រេត</i></span>
            </div>
            
            <div class="rounded-lg bg-slate-50 p-3">
                <span class="block text-xs font-bold text-slate-700">តំណភ្ជាប់ Link</span>
                <code class="text-xs text-blue-600">[ឈ្មោះលីង](https://example.com)</code>
                <span class="block text-xs text-slate-500 mt-1">លទ្ធផល: <a href="#" class="text-blue-500 underline">ឈ្មោះលីង</a></span>
            </div>
            
            <div class="rounded-lg bg-slate-50 p-3">
                <span class="block text-xs font-bold text-slate-700">កូដកុំព្យូទ័រ (Monospace)</span>
                <code class="text-xs text-blue-600">`កូដម៉ូដែល`</code>
                <span class="block text-xs text-slate-500 mt-1">លទ្ធផល: <code class="bg-slate-200 px-1 rounded">កូដម៉ូដែល</code></span>
            </div>
        </div>
    </div>
</div>
@endsection
