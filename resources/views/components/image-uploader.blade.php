@props([
    'name' => 'image',
    'directory' => 'uploads',
    'multiple' => false,
    'variants' => false,
    'maxFiles' => 1,
    'maxSizeMb' => 15,
    'existingImages' => [],
    'label' => 'Unggah Foto',
    'hint' => 'Maksimal 15MB. Format: JPG, PNG, WEBP. Otomatis dikompres & dioptimalkan.',
    'compress' => true,
    'maxWidth' => 1920,
    'quality' => 0.85,
])

<div x-data="imageUploader({
        name: '{{ $name }}',
        directory: '{{ $directory }}',
        multiple: {{ $multiple ? 'true' : 'false' }},
        variants: {{ $variants ? 'true' : 'false' }},
        maxFiles: {{ $maxFiles }},
        maxSizeMb: {{ $maxSizeMb }},
        compress: {{ $compress ? 'true' : 'false' }},
        maxWidth: {{ $maxWidth }},
        quality: {{ $quality }},
        uploadUrl: '{{ route('admin.upload.image') }}',
        csrfToken: '{{ csrf_token() }}'
    })"
    class="w-full">
    
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-semibold text-gray-800">{{ $label }}</label>
        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Auto Kompresi & WebP
        </span>
    </div>

    {{-- Drag & Drop Zone --}}
    <div 
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop($event)"
        :class="{ 
            'border-primary-500 bg-primary-50/70 ring-4 ring-primary-100 scale-[1.01] shadow-md': isDragging, 
            'border-gray-300 bg-white hover:bg-gray-50/80 hover:border-primary-400': !isDragging 
        }"
        class="relative border-2 border-dashed rounded-2xl p-6 sm:p-8 text-center transition-all duration-200 cursor-pointer flex flex-col items-center justify-center min-h-[170px] shadow-sm group"
        @click="$refs.fileInput.click()">
        
        <input type="file" x-ref="fileInput" class="hidden" 
               accept="image/jpeg,image/png,image/webp" 
               {{ $multiple ? 'multiple' : '' }}
               @change="handleFileSelect($event)">

        {{-- Icon with subtle pulse on hover --}}
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100/80 border border-primary-200/60 flex items-center justify-center text-primary-600 mb-3 group-hover:scale-110 group-hover:shadow-md transition-all duration-300 pointer-events-none">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
        </div>
        
        <p class="text-sm font-semibold text-gray-800 pointer-events-none">
            <span class="text-primary-600 hover:underline">Pilih file foto</span> atau tarik & lepaskan ke sini
        </p>
        <p class="text-xs text-gray-500 mt-1 pointer-events-none">{{ $hint }}</p>

        {{-- Feature Highlights --}}
        <div class="mt-4 flex flex-wrap items-center justify-center gap-2 pointer-events-none">
            <span class="inline-flex items-center text-[11px] font-medium text-gray-600 bg-gray-100 px-2.5 py-0.5 rounded-full">
                ⚡ Cepat & Ringan
            </span>
            <span class="inline-flex items-center text-[11px] font-medium text-gray-600 bg-gray-100 px-2.5 py-0.5 rounded-full">
                📊 Progres Realtime
            </span>
            <span class="inline-flex items-center text-[11px] font-medium text-gray-600 bg-gray-100 px-2.5 py-0.5 rounded-full">
                🎯 Kualitas HD Terjaga
            </span>
        </div>
    </div>

    {{-- File List & Upload Cards --}}
    <div class="mt-4 space-y-3" x-show="files.length > 0" x-cloak>
        <template x-for="(file, index) in files" :key="file.id">
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm relative overflow-hidden transition-all duration-200 hover:border-gray-300">
                
                {{-- Main Card Content --}}
                <div class="flex items-start gap-4">
                    {{-- Thumbnail Preview --}}
                    <div class="w-20 h-20 rounded-lg bg-gray-100 flex-shrink-0 overflow-hidden relative border border-gray-200 shadow-inner group/thumb">
                        <img :src="file.preview" class="w-full h-full object-cover">
                        
                        {{-- Spinner Overlay while compressing/uploading --}}
                        <div x-show="file.status === 'compressing' || file.status === 'uploading' || file.status === 'processing'" 
                             class="absolute inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center">
                            <svg class="animate-spin w-6 h-6 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        {{-- Success Checkmark on thumbnail --}}
                        <div x-show="file.status === 'success'" class="absolute bottom-1 right-1 bg-green-500 text-white rounded-full p-0.5 shadow">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>

                    {{-- Info, Metrics & Actions --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate" x-text="file.name"></p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Ukuran asli: <span class="font-medium text-gray-700" x-text="formatBytes(file.originalSize)"></span>
                                </p>
                            </div>
                            
                            {{-- Remove / Action Button --}}
                            <div class="flex items-center gap-1">
                                <button type="button" @click.stop="removeFile(index)" 
                                        class="text-gray-400 hover:text-red-500 hover:bg-red-50 transition p-1.5 rounded-lg focus:outline-none" 
                                        title="Hapus foto ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Progress Bar (Realtime during upload) --}}
                        <div x-show="file.status === 'uploading' || file.status === 'processing'" class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span class="font-medium text-primary-700" x-show="file.status === 'uploading'">Mengunggah ke server...</span>
                                <span class="font-medium text-amber-700 animate-pulse" x-show="file.status === 'processing'">Memproses & mengoptimalkan...</span>
                                <span class="font-bold text-primary-700" x-text="`${file.progress}%`"></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-primary-500 to-emerald-500 h-2 rounded-full transition-all duration-150 ease-out relative" 
                                     :style="`width: ${file.progress}%`">
                                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-gray-500 mt-1">
                                <span x-text="`${formatBytes(file.loadedBytes)} / ${formatBytes(file.totalBytes)}`"></span>
                                <span class="text-primary-600 font-medium">Realtime Upload</span>
                            </div>
                        </div>

                        {{-- Status: Compressing --}}
                        <div x-show="file.status === 'compressing'" class="mt-2.5 flex items-center gap-2 text-xs font-medium text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-100">
                            <svg class="animate-spin w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Mengompresi gambar otomatis di browser...</span>
                        </div>

                        {{-- Status: Success with compression stats --}}
                        <div x-show="file.status === 'success'" class="mt-2.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Optimal & Siap Disimpan
                            </span>
                            <span class="inline-flex items-center text-xs font-medium text-gray-700 bg-gray-100 px-2.5 py-1 rounded-md">
                                Hasil: <strong class="ml-1 text-gray-900" x-text="file.sizeFormatted || formatBytes(file.compressedSize)"></strong>
                            </span>
                            <template x-if="file.savingsPercent > 0">
                                <span class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-100/60 px-2 py-0.5 rounded">
                                    Hemat <span x-text="file.savingsPercent"></span>%
                                </span>
                            </template>
                        </div>

                        {{-- Status: Error --}}
                        <div x-show="file.status === 'error'" class="mt-2.5 p-2 bg-red-50 border border-red-100 rounded-md text-xs text-red-700 flex items-start justify-between gap-2">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="truncate" x-text="file.error || 'Gagal mengunggah foto.'"></span>
                            </div>
                            <button type="button" @click.stop="uploadFile(file)" class="text-xs font-bold text-red-800 underline hover:text-red-900 flex-shrink-0">
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Hidden input for form submission --}}
                <template x-if="file.status === 'success' && file.path">
                    <input type="hidden" :name="multiple ? `${name}[]` : name" :value="file.path">
                </template>
            </div>
        </template>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    // Avoid double registration
    if (Alpine.data('imageUploader')) return;

    Alpine.data('imageUploader', (config) => ({
        isDragging: false,
        files: [],
        name: config.name,
        multiple: config.multiple,
        
        formatBytes(bytes, decimals = 1) {
            if (!bytes || bytes <= 0) return '0 B';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        },

        handleFileSelect(event) {
            this.processFiles(event.target.files);
            event.target.value = '';
        },
        
        handleDrop(event) {
            this.isDragging = false;
            this.processFiles(event.dataTransfer.files);
        },
        
        async processFiles(fileList) {
            if (!fileList || fileList.length === 0) return;
            
            const newFiles = Array.from(fileList);
            
            if (!config.multiple && newFiles.length > 1) {
                alert('Hanya bisa memilih satu file foto.');
                return;
            }
            
            if (config.multiple && config.maxFiles > 0) {
                if (this.files.length + newFiles.length > config.maxFiles) {
                    alert(`Maksimal ${config.maxFiles} foto diperbolehkan.`);
                    return;
                }
            }

            if (!config.multiple) {
                // Revoke old previews
                this.files.forEach(f => {
                    if (f.preview) URL.revokeObjectURL(f.preview);
                });
                this.files = [];
            }

            for (const file of newFiles) {
                if (file.size > config.maxSizeMb * 1024 * 1024) {
                    alert(`Foto ${file.name} melebihi batas ${config.maxSizeMb}MB.`);
                    continue;
                }
                
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    alert(`Format file ${file.name} tidak didukung. Gunakan JPG, PNG, atau WEBP.`);
                    continue;
                }

                const id = Math.random().toString(36).substring(2, 9);
                const preview = URL.createObjectURL(file);
                
                const fileObj = {
                    id,
                    file,
                    name: file.name,
                    preview,
                    status: 'compressing', // compressing, uploading, processing, success, error
                    progress: 0,
                    loadedBytes: 0,
                    totalBytes: file.size,
                    originalSize: file.size,
                    compressedSize: file.size,
                    savingsPercent: 0,
                    sizeFormatted: null,
                    path: null,
                    error: null
                };
                
                this.files.push(fileObj);
                
                // Compress client-side and upload
                this.handleCompressionAndUpload(this.files[this.files.length - 1]);
            }
        },

        async handleCompressionAndUpload(fileObj) {
            let uploadTarget = fileObj.file;
            
            if (config.compress) {
                try {
                    const compressed = await this.clientSideCompress(fileObj.file, config.maxWidth || 1920, config.quality || 0.85);
                    if (compressed && compressed.blob && compressed.blob.size < fileObj.originalSize) {
                        const newExt = compressed.mime === 'image/webp' ? '.webp' : '.jpg';
                        const newFileName = fileObj.name.replace(/\.[^.]+$/, '') + newExt;
                        uploadTarget = new File([compressed.blob], newFileName, {
                            type: compressed.mime,
                            lastModified: Date.now()
                        });
                        fileObj.compressedSize = uploadTarget.size;
                        fileObj.totalBytes = uploadTarget.size;
                        fileObj.savingsPercent = Math.round(((fileObj.originalSize - uploadTarget.size) / fileObj.originalSize) * 100);
                    }
                } catch (e) {
                    console.warn('Client-side compression skipped or failed:', e);
                }
            }

            fileObj.processedFile = uploadTarget;
            this.uploadFile(fileObj);
        },

        clientSideCompress(file, maxWidth, quality) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                const reader = new FileReader();

                reader.onload = (e) => {
                    img.onload = () => {
                        try {
                            let width = img.width;
                            let height = img.height;

                            if (width > maxWidth || height > maxWidth) {
                                if (width > height) {
                                    height = Math.round((height * maxWidth) / width);
                                    width = maxWidth;
                                } else {
                                    width = Math.round((width * maxWidth) / height);
                                    height = maxWidth;
                                }
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            // Prefer webp, fallback to jpeg
                            const mime = 'image/webp';
                            canvas.toBlob((blob) => {
                                if (blob) {
                                    resolve({ blob, mime, width, height });
                                } else {
                                    // Fallback to jpeg
                                    canvas.toBlob((jpegBlob) => {
                                        resolve({ blob: jpegBlob, mime: 'image/jpeg', width, height });
                                    }, 'image/jpeg', quality);
                                }
                            }, mime, quality);
                        } catch (err) {
                            reject(err);
                        }
                    };
                    img.onerror = (err) => reject(err);
                    img.src = e.target.result;
                };
                reader.onerror = (err) => reject(err);
                reader.readAsDataURL(file);
            });
        },
        
        removeFile(index) {
            const file = this.files[index];
            if (file && file.preview) {
                URL.revokeObjectURL(file.preview);
            }
            this.files.splice(index, 1);
        },
        
        uploadFile(fileObj) {
            fileObj.status = 'uploading';
            fileObj.progress = 0;
            fileObj.error = null;
            
            const fileToSend = fileObj.processedFile || fileObj.file;
            const formData = new FormData();
            formData.append('file', fileToSend);
            formData.append('directory', config.directory);
            if (config.variants) {
                formData.append('variants', '1');
            }
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', config.uploadUrl, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', config.csrfToken);
            xhr.setRequestHeader('Accept', 'application/json');
            
            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                    fileObj.loadedBytes = event.loaded;
                    fileObj.totalBytes = event.total;
                    fileObj.progress = Math.round((event.loaded / event.total) * 100);
                    if (fileObj.progress >= 100) {
                        fileObj.status = 'processing';
                    }
                }
            };
            
            xhr.onload = () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            fileObj.status = 'success';
                            fileObj.progress = 100;
                            fileObj.path = config.variants ? JSON.stringify(response.paths) : response.path;
                            if (response.size_formatted) {
                                fileObj.sizeFormatted = response.size_formatted;
                            }
                            if (response.savings_percent !== undefined) {
                                fileObj.savingsPercent = response.savings_percent;
                            }
                        } else {
                            fileObj.status = 'error';
                            fileObj.error = response.message || 'Gagal mengunggah';
                        }
                    } catch(e) {
                        fileObj.status = 'error';
                        fileObj.error = 'Respons tidak valid dari server';
                    }
                } else {
                    fileObj.status = 'error';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        fileObj.error = response.message || (response.errors?.file ? response.errors.file[0] : 'Gagal mengunggah');
                    } catch(e) {
                        fileObj.status = 'error';
                        fileObj.error = `Error ${xhr.status}`;
                    }
                }
            };
            
            xhr.onerror = () => {
                fileObj.status = 'error';
                fileObj.error = 'Terjadi kesalahan jaringan atau koneksi terputus';
            };
            
            xhr.send(formData);
        }
    }));
});
</script>
