@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Breadcrumbs & Header -->
    <div class="mb-8">
        <nav class="flex mb-3 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition duration-150">{{ __('app.dashboard') ?? 'Dashboard' }}</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('admin.products.index') }}" class="hover:text-indigo-600 transition duration-150">{{ __('app.products') ?? 'Products' }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-gray-400 font-medium">{{ __('app.import_products') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('app.import_products') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('app.import_products_subtitle') }}</p>
            </div>
            <div>
                <form action="{{ route('admin.products.clear-imported') }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកពិតជាចង់លុបទិន្នន័យទំនិញដែលបាននាំចូលទាំងអស់មែនទេ?' : 'Are you sure you want to delete all imported products?' }}');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded-xl shadow-sm transition duration-150">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        {{ app()->getLocale() === 'km' ? 'លុបទិន្នន័យនាំចូលទាំងអស់' : 'Clear Imported Products' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Error/Success Flash Alerts -->
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <h4 class="font-bold">{{ __('app.error') ?? 'Error' }}</h4>
            <p class="text-sm mt-1">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form Column -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                @csrf

                <!-- Excel / CSV File Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">{{ app()->getLocale() === 'km' ? 'ជ្រើសរើសឯកសារ Excel ឬ CSV' : 'Select Excel or CSV File' }}</label>
                    <div id="drop-zone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-500 transition duration-150 ease-in-out cursor-pointer bg-gray-50/50">
                        <input type="file" name="csv_file" id="csv_file" class="hidden" accept=".xlsx,.xls,.csv,text/csv,text/plain,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required>
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <div class="text-sm font-medium text-gray-700">
                                <span class="text-indigo-600 hover:text-indigo-700 underline font-semibold">{{ __('app.click_to_upload') }}</span> {{ __('app.or_drag_and_drop') }}
                            </div>
                            <p class="text-xs text-gray-500">{{ app()->getLocale() === 'km' ? 'អនុញ្ញាតឯកសារប្រភេទ .xlsx, .xls, .csv ទំហំបំផុត 10MB' : 'Supports .xlsx, .xls, .csv up to 10MB' }}</p>
                            <div id="file-info" class="hidden text-sm font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100 mt-2">
                                <span id="file-name"></span>
                            </div>
                        </div>
                    </div>
                    @error('csv_file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Configurations Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <!-- Duplicate Code Handling -->
                    <div>
                        <label for="duplicate_handling" class="block text-sm font-semibold text-gray-700 mb-2">
                            {{ __('app.duplicate_handling') }}
                        </label>
                        <select name="duplicate_handling" id="duplicate_handling" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 text-sm">
                            <option value="skip" selected>{{ __('app.skip_existing') }}</option>
                            <option value="update">{{ __('app.update_existing') }}</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1.5">{{ __('app.duplicate_handling_info') }}</p>
                    </div>

                    <!-- Stock Handling -->
                    <div>
                        <label for="stock_handling" class="block text-sm font-semibold text-gray-700 mb-2">
                            {{ __('app.stock_handling') }}
                        </label>
                        <select name="stock_handling" id="stock_handling" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 text-sm">
                            <option value="add" selected>{{ __('app.add_to_stock') }}</option>
                            <option value="overwrite">{{ __('app.overwrite_stock') }}</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1.5">{{ __('app.stock_handling_info') }}</p>
                    </div>
                </div>

                <!-- Submit / Back Actions -->
                <div class="flex justify-end items-center gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition duration-150">
                        {{ __('app.cancel') ?? 'Cancel' }}
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition duration-150 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        {{ __('app.import') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar / Instructions Column -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>💡</span> {{ __('app.instructions') }}
                </h3>
                
                <ul class="space-y-3 text-xs text-gray-600 list-disc pl-4">
                    <li>{{ __('app.instruction_1') }}</li>
                    <li>{{ __('app.instruction_2') }}</li>
                    <li>{{ __('app.instruction_3') }}</li>
                    <li>{{ __('app.instruction_4') }}</li>
                    <li>{{ __('app.instruction_5') }}</li>
                    <li>{{ __('app.instruction_6') }}</li>
                </ul>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.products.import-template') }}" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-semibold px-4 py-2.5 rounded-lg text-sm transition duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        {{ __('app.download_template') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('csv_file');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-indigo-500', 'bg-indigo-50/10');
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/10');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files;
            updateFileInfo(files[0].name);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            updateFileInfo(fileInput.files[0].name);
        }
    });

    function updateFileInfo(name) {
        fileName.textContent = name;
        fileInfo.classList.remove('hidden');
    }
</script>
@endsection
