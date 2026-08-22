@php
    $existingImg = isset($product) && $product->image ? asset('storage/' . $product->image) : null;
    $existingFileName = isset($product) && $product->image ? basename($product->image) : '';
@endphp

<div class="mb-6">
    <div class="flex items-center justify-between mb-2">
        <label class="block text-gray-700 text-sm font-bold flex items-center gap-2">
            <i class="fas fa-image text-indigo-600"></i>
            <span>{{ __('app.product_image') }}</span>
        </label>
    </div>

    <!-- Dropzone Area -->
    <div id="image_dropzone" 
         class="relative border-2 border-dashed border-indigo-200 hover:border-indigo-500 bg-indigo-50/20 hover:bg-indigo-50/50 rounded-2xl p-6 text-center transition-all duration-200 cursor-pointer group">
        
        <input type="file" name="image" id="product_image_input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

        <!-- Empty State -->
        <div id="image_dropzone_empty" class="{{ $existingImg ? 'hidden' : '' }} flex flex-col items-center justify-center space-y-3 py-3">
            <div class="w-16 h-16 rounded-2xl bg-indigo-100/80 group-hover:scale-110 group-hover:bg-indigo-600 text-indigo-600 group-hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm">
                <i class="fas fa-cloud-upload-alt text-3xl"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800">
                    {{ app()->getLocale() === 'km' ? 'ចុចជ្រើសរើស ឬ អូសរូបភាពមកទម្លាក់ទីនេះ' : 'Click to upload or drag & drop image here' }}
                </p>
                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                    {{ app()->getLocale() === 'km' ? 'ឬ Copy រូបភាពពីប្រភពផ្សេង (ឬ Screenshot) រួចចុច' : 'Or copy an image from anywhere and press' }} 
                    <kbd class="px-2 py-0.5 bg-white border border-gray-300 rounded text-xs font-mono font-bold text-indigo-700 shadow-2xs">Ctrl + V</kbd> 
                    {{ app()->getLocale() === 'km' ? 'ដើម្បី Paste រូបភាពដោយផ្ទាល់' : 'to paste directly' }}
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs text-indigo-600 font-semibold bg-white px-3 py-1.5 rounded-lg border border-indigo-100 shadow-2xs">
                <i class="fas fa-file-image"></i>
                <span>Support PNG, JPG, WEBP (Max 20MB)</span>
            </div>
        </div>

        <!-- Preview State -->
        <div id="image_preview_container" class="{{ $existingImg ? '' : 'hidden' }} flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-xl border border-indigo-100 shadow-sm relative z-20">
            <div class="flex items-center gap-4">
                <div class="relative group/preview shrink-0">
                    <img id="image_preview_img" 
                         src="{{ $existingImg ?? '' }}" 
                         alt="Product Preview" 
                         class="w-20 h-20 object-cover rounded-xl border border-gray-200 shadow-sm bg-gray-50">
                </div>
                <div class="text-left space-y-1 overflow-hidden">
                    <p id="file_info_text" class="text-sm font-bold text-gray-800 truncate max-w-xs">
                        {{ $existingFileName ?: 'selected_image.png' }}
                    </p>
                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md font-semibold border border-emerald-100">
                        <i class="fas fa-check-circle"></i>
                        <span id="file_status_label">{{ $existingImg ? (app()->getLocale() === 'km' ? 'រូបភាពបច្ចុប្បន្ន' : 'Current Image') : (app()->getLocale() === 'km' ? 'រូបភាពត្រូវបានជ្រើសរើស' : 'Image Selected') }}</span>
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap shrink-0 z-30">
                <!-- Copy Image Button -->
                <button type="button" 
                        onclick="copyProductImageToClipboard()" 
                        class="px-3.5 py-2 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition flex items-center gap-1.5 shadow-2xs cursor-pointer"
                        title="{{ app()->getLocale() === 'km' ? 'Copy រូបភាពទៅ Clipboard សម្រាប់ Paste ចូល Telegram/Facebook' : 'Copy image to clipboard' }}">
                    <i class="fas fa-copy"></i>
                    <span>{{ app()->getLocale() === 'km' ? 'Copy រូបភាព' : 'Copy Image' }}</span>
                </button>

                <!-- Change Image Button -->
                <button type="button" 
                        onclick="document.getElementById('product_image_input').click()" 
                        class="px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg border border-gray-200 transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-sync-alt"></i>
                    <span>{{ app()->getLocale() === 'km' ? 'ផ្លាស់ប្តូរ' : 'Change' }}</span>
                </button>

                <!-- Clear Image Button -->
                <button type="button" 
                        onclick="clearSelectedImage()" 
                        class="px-3 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-trash-alt"></i>
                    <span>{{ app()->getLocale() === 'km' ? 'លុបចេញ' : 'Remove' }}</span>
                </button>
            </div>
        </div>
    </div>
    
    @error('image')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Floating Toast Container -->
<div id="product_toast_notification" class="fixed top-5 right-5 z-50 transform -translate-y-4 opacity-0 pointer-events-none transition-all duration-300 flex items-center gap-2 bg-slate-900/90 text-white text-sm font-semibold px-4 py-3 rounded-xl shadow-2xl backdrop-blur-sm border border-slate-700">
    <i id="product_toast_icon" class="fas fa-info-circle text-indigo-400"></i>
    <span id="product_toast_message"></span>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('product_image_input');
        const dropzone = document.getElementById('image_dropzone');
        const emptyState = document.getElementById('image_dropzone_empty');
        const previewContainer = document.getElementById('image_preview_container');
        const previewImg = document.getElementById('image_preview_img');
        const fileInfoText = document.getElementById('file_info_text');
        const fileStatusLabel = document.getElementById('file_status_label');

        if (!fileInput || !dropzone) return;

        // File Input Change
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                processImageFile(this.files[0]);
            }
        });

        // Drag & Drop Highlight
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-indigo-500', 'bg-indigo-100/50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-indigo-500', 'bg-indigo-100/50');
            }, false);
        });

        // Handle Drop
        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files[0] && files[0].type.startsWith('image/')) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(files[0]);
                fileInput.files = dataTransfer.files;
                processImageFile(files[0]);
            }
        });

        // Global Paste Listener (Ctrl + V)
        document.addEventListener('paste', function(e) {
            const items = (e.clipboardData || e.originalEvent?.clipboardData)?.items;
            if (!items) return;

            let foundImage = false;
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    const blob = items[i].getAsFile();
                    if (blob) {
                        foundImage = true;
                        const dataTransfer = new DataTransfer();
                        const fileName = 'pasted_image_' + Date.now() + '.png';
                        const imageFile = new File([blob], fileName, { type: blob.type || 'image/png' });
                        dataTransfer.items.add(imageFile);
                        fileInput.files = dataTransfer.files;
                        processImageFile(imageFile, true);
                        e.preventDefault();
                        break;
                    }
                }
            }

            // If text pasted is an image URL
            if (!foundImage) {
                const text = (e.clipboardData || window.clipboardData)?.getData('text')?.trim();
                if (text && (text.match(/\.(jpeg|jpg|gif|png|webp)(\?.*)?$/i) || (text.startsWith('http') && text.includes('image')))) {
                    fetchImageUrl(text);
                    e.preventDefault();
                }
            }
        });

        function processImageFile(file, isPasted = false) {
            if (file.size > 1.5 * 1024 * 1024) {
                showProductToast("⚡ {{ app()->getLocale() === 'km' ? 'កំពុងសម្រួលទំហំរូបភាព...' : 'Optimizing image size...' }}");
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        const maxDim = 1920;

                        if (width > maxDim || height > maxDim) {
                            if (width > height) {
                                height = Math.round((height * maxDim) / width);
                                width = maxDim;
                            } else {
                                width = Math.round((width * maxDim) / height);
                                height = maxDim;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob(function(blob) {
                            if (blob) {
                                const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(compressedFile);
                                fileInput.files = dataTransfer.files;
                                handleImageFile(compressedFile, isPasted);
                            } else {
                                handleImageFile(file, isPasted);
                            }
                        }, 'image/jpeg', 0.85);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                handleImageFile(file, isPasted);
            }
        }

        function handleImageFile(file, isPasted = false) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                emptyState.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                fileInfoText.textContent = file.name + ' (' + formatBytes(file.size) + ')';
                fileStatusLabel.textContent = "{{ app()->getLocale() === 'km' ? 'រូបភាពបានជ្រើសរើស' : 'Image Selected' }}";
                
                if (isPasted) {
                    showProductToast("📋 {{ app()->getLocale() === 'km' ? 'បាន Paste រូបភាពដោយជោគជ័យ!' : 'Image pasted successfully!' }}");
                } else {
                    showProductToast("✅ {{ app()->getLocale() === 'km' ? 'បានជ្រើសរើសរូបភាព!' : 'Image selected!' }}");
                }
            };
            reader.readAsDataURL(file);
        }

        async function fetchImageUrl(url) {
            try {
                showProductToast("⏳ {{ app()->getLocale() === 'km' ? 'កំពុងទាញយករូបភាពពី URL...' : 'Fetching image from URL...' }}");
                const response = await fetch(url);
                const blob = await response.blob();
                const fileName = 'url_image_' + Date.now() + '.png';
                const file = new File([blob], fileName, { type: blob.type || 'image/png' });
                
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                handleImageFile(file, true);
            } catch (err) {
                console.error(err);
                showProductToast("⚠️ {{ app()->getLocale() === 'km' ? 'មិនអាចទាញយករូបភាពពី URL នេះបានទេ' : 'Could not fetch image from URL' }}", 'error');
            }
        }
    });

    function clearSelectedImage() {
        const fileInput = document.getElementById('product_image_input');
        const emptyState = document.getElementById('image_dropzone_empty');
        const previewContainer = document.getElementById('image_preview_container');
        const previewImg = document.getElementById('image_preview_img');
        
        if (fileInput) fileInput.value = '';
        if (previewImg) previewImg.src = '';
        if (previewContainer) previewContainer.classList.add('hidden');
        if (emptyState) emptyState.classList.remove('hidden');
        showProductToast("🗑️ {{ app()->getLocale() === 'km' ? 'បានលុបរូបភាពចេញ' : 'Image cleared' }}");
    }

    async function copyProductImageToClipboard() {
        const previewImg = document.getElementById('image_preview_img');
        if (!previewImg || !previewImg.src) {
            showProductToast("❌ {{ app()->getLocale() === 'km' ? 'មិនមានរូបភាពសម្រាប់ Copy ទេ' : 'No image to copy' }}", 'error');
            return;
        }

        try {
            const imgSrc = previewImg.src;
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.src = imgSrc;
            await new Promise((resolve, reject) => {
                img.onload = resolve;
                img.onerror = () => resolve();
            });

            const canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth || img.width || 300;
            canvas.height = img.naturalHeight || img.height || 300;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            canvas.toBlob(async (blob) => {
                if (!blob) {
                    await fallbackCopyLink(imgSrc);
                    return;
                }
                try {
                    await navigator.clipboard.write([
                        new ClipboardItem({ [blob.type || 'image/png']: blob })
                    ]);
                    showProductToast("📋 {{ app()->getLocale() === 'km' ? 'បាន Copy រូបភាពទៅ Clipboard! អាចយកទៅ Paste ក្នុង Telegram/Facebook បាន' : 'Image copied to clipboard!' }}");
                } catch (err) {
                    await fallbackCopyLink(imgSrc);
                }
            }, 'image/png');
        } catch (e) {
            await fallbackCopyLink(document.getElementById('image_preview_img').src);
        }
    }

    async function fallbackCopyLink(url) {
        try {
            await navigator.clipboard.writeText(url);
            showProductToast("🔗 {{ app()->getLocale() === 'km' ? 'បាន Copy តំណភ្ជាប់រូបភាពទៅ Clipboard!' : 'Image link copied to clipboard!' }}");
        } catch (err) {
            showProductToast("❌ {{ app()->getLocale() === 'km' ? 'មិនអាច Copy បានទេ' : 'Could not copy image' }}", 'error');
        }
    }

    function formatBytes(bytes, decimals = 2) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function showProductToast(message, type = 'info') {
        const toast = document.getElementById('product_toast_notification');
        const msg = document.getElementById('product_toast_message');
        
        if (!toast || !msg) return;

        msg.textContent = message;
        toast.classList.remove('-translate-y-4', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('-translate-y-4', 'opacity-0', 'pointer-events-none');
        }, 3500);
    }
</script>
