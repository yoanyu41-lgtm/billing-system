@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.import_products') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('app.import_products_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-3.5 py-2 rounded-lg hover:bg-gray-50 transition shadow-2xs font-medium">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>{{ __('app.back') }}</span>
        </a>
    </div>

    @if(session('error'))
    <div class="mb-5 p-3.5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl flex items-center gap-2.5">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-5 p-3.5 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center gap-2.5">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Main Simple Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6">
        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="space-y-5 m-0">
            @csrf

            <!-- Upload Area -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ app()->getLocale() === 'km' ? 'ឯកសារ Excel ឬ CSV' : 'Excel or CSV File' }} <span class="text-red-500">*</span>
                </label>
                <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-indigo-500 rounded-xl p-6 text-center cursor-pointer bg-gray-50/50 hover:bg-indigo-50/20 transition">
                    <input type="file" name="csv_file" id="csv_file" class="hidden" accept=".xlsx,.xls,.csv,text/csv,text/plain,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required>
                    
                    <div id="upload-prompt" class="space-y-2">
                        <i class="fas fa-cloud-upload-alt text-3xl text-indigo-500"></i>
                        <p class="text-sm font-medium text-gray-700">
                            <span class="text-indigo-600 font-semibold underline">{{ __('app.click_to_upload') }}</span> {{ __('app.or_drag_and_drop') }}
                        </p>
                        <p class="text-xs text-gray-400">.xlsx, .xls, .csv (អតិបរមា 10MB)</p>
                    </div>

                    <div id="file-info" class="hidden items-center justify-center gap-2 text-sm text-gray-800 font-medium bg-white border border-gray-200 rounded-lg py-2 px-3">
                        <i class="fas fa-file-excel text-emerald-600 text-lg"></i>
                        <span id="file-name" class="truncate max-w-xs"></span>
                        <span id="file-size" class="text-xs text-gray-400"></span>
                        <button type="button" id="remove-file-btn" class="ml-2 text-gray-400 hover:text-red-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @error('csv_file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>


            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    {{ __('app.cancel') }}
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-file-import text-xs"></i>
                    <span>{{ __('app.import') }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Simple Instructions & Sample Template Card -->
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 text-sm space-y-3">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <span class="font-bold text-gray-800 flex items-center gap-1.5">
                <span>💡</span> {{ __('app.instructions') }}
            </span>
            <a href="{{ route('admin.products.import-template') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition" style="text-decoration: none;">
                <i class="fas fa-download text-[11px]"></i>
                <span>{{ __('app.download_template') }}</span>
            </a>
        </div>

        <ul class="text-xs text-gray-600 space-y-1.5 pl-4 list-disc">
            <li>{{ __('app.instruction_1') }}</li>
            <li>{{ __('app.instruction_2') }}</li>
            <li>{{ __('app.instruction_3') }}</li>
            <li>{{ __('app.instruction_4') }}</li>
        </ul>

        <div class="pt-3 border-t border-gray-200 flex justify-end">
            <form action="{{ route('admin.products.clear-imported') }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'km' ? 'តើអ្នកពិតជាចង់លុបទិន្នន័យទំនិញដែលបាននាំចូលទាំងអស់មែនទេ?' : 'Are you sure you want to delete all imported products?' }}');" class="m-0">
                @csrf
                <button type="submit" class="text-xs text-red-600 hover:text-red-700 hover:underline bg-transparent border-0 cursor-pointer p-0 font-medium">
                    <i class="fas fa-trash-alt text-[10px] mr-1"></i> {{ app()->getLocale() === 'km' ? 'លុបទិន្នន័យនាំចូលទាំងអស់' : 'Clear Imported Products' }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('csv_file');
    const uploadPrompt = document.getElementById('upload-prompt');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeBtn = document.getElementById('remove-file-btn');

    dropZone.addEventListener('click', (e) => {
        if (e.target.closest('#remove-file-btn')) return;
        fileInput.click();
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-indigo-500', 'bg-indigo-50/30');
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/30');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files;
            displayFile(files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            displayFile(fileInput.files[0]);
        }
    });

    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        uploadPrompt.classList.remove('hidden');
        fileInfo.classList.add('hidden');
        fileInfo.classList.remove('flex');
    });

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function displayFile(file) {
        fileName.textContent = file.name;
        fileSize.textContent = '(' + formatBytes(file.size) + ')';
        uploadPrompt.classList.add('hidden');
        fileInfo.classList.remove('hidden');
        fileInfo.classList.add('flex');
    }
</script>
@endsection
