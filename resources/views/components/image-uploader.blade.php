@props([
    'name' => 'image',
    'directory' => 'uploads',
    'multiple' => false,
    'variants' => false,
    'maxFiles' => 1,
    'maxSizeMb' => 15,
    'existingImages' => [],
    'label' => 'Unggah Foto',
    'hint' => 'Maksimal 15MB. Format: JPG, PNG, WEBP.'
])

<div x-data="imageUploader({
        name: '{{ $name }}',
        directory: '{{ $directory }}',
        multiple: {{ $multiple ? 'true' : 'false' }},
        variants: {{ $variants ? 'true' : 'false' }},
        maxFiles: {{ $maxFiles }},
        maxSizeMb: {{ $maxSizeMb }},
        uploadUrl: '{{ route('admin.upload.image') }}',
        csrfToken: '{{ csrf_token() }}'
    })"
    class="w-full">
    
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>

    {{-- Drag & Drop Zone --}}
    <div 
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop($event)"
        :class="{ 'border-primary-500 bg-primary-50 ring-4 ring-primary-50': isDragging, 'border-gray-300 bg-gray-50 hover:bg-gray-100': !isDragging }"
        class="relative border-2 border-dashed rounded-xl p-8 text-center transition-all duration-200 cursor-pointer flex flex-col items-center justify-center min-h-[160px]"
        @click="$refs.fileInput.click()">
        
        <input type="file" x-ref="fileInput" class="hidden" 
               accept="image/jpeg,image/png,image/webp" 
               {{ $multiple ? 'multiple' : '' }}
               @change="handleFileSelect($event)">

        <div class="bg-white p-3 rounded-full shadow-sm mb-3 text-primary-500 pointer-events-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-700 pointer-events-none">Klik atau seret foto ke sini</p>
        <p class="text-xs text-gray-500 mt-1 pointer-events-none">{{ $hint }}</p>
    </div>

    {{-- File List --}}
    <div class="mt-4 space-y-3" x-show="files.length > 0" x-cloak>
        <template x-for="(file, index) in files" :key="file.id">
            <div class="flex items-center gap-4 bg-white border border-gray-200 rounded-lg p-3 shadow-sm relative overflow-hidden group">
                {{-- Preview --}}
                <div class="w-16 h-16 rounded-md bg-gray-100 flex-shrink-0 overflow-hidden relative">
                    <img :src="file.preview" class="w-full h-full object-cover">
                    <div x-show="file.status === 'uploading'" class="absolute inset-0 bg-black/40 flex items-center justify-center">
                        <svg class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>

                {{-- Info & Progress --}}
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-sm font-medium text-gray-800 truncate pr-4" x-text="file.name"></p>
                        <button type="button" @click.stop="removeFile(index)" class="text-gray-400 hover:text-red-500 transition focus:outline-none p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Progress Bar --}}
                    <div x-show="file.status === 'uploading'" class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-primary-500 h-1.5 rounded-full transition-all duration-300" :style="`width: ${file.progress}%`"></div>
                    </div>

                    {{-- Status text --}}
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-xs text-gray-500" x-show="file.status === 'pending'">Menunggu...</p>
                        <p class="text-xs text-primary-600" x-show="file.status === 'uploading'" x-text="`${file.progress}%`"></p>
                        <p class="text-xs text-green-600 flex items-center gap-1" x-show="file.status === 'success'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Selesai
                        </p>
                        <p class="text-xs text-red-600 flex items-center gap-1" x-show="file.status === 'error'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="file.error || 'Gagal mengunggah'"></span>
                            <button type="button" @click.stop="uploadFile(file)" class="underline ml-1 hover:text-red-700">Coba lagi</button>
                        </p>
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
    Alpine.data('imageUploader', (config) => ({
        isDragging: false,
        files: [],
        
        handleFileSelect(event) {
            this.processFiles(event.target.files);
            // Reset input so the same file can be selected again if removed
            event.target.value = '';
        },
        
        handleDrop(event) {
            this.isDragging = false;
            this.processFiles(event.dataTransfer.files);
        },
        
        processFiles(fileList) {
            if (!fileList || fileList.length === 0) return;
            
            const newFiles = Array.from(fileList);
            
            // Check limits
            if (!config.multiple && newFiles.length > 1) {
                alert('Hanya bisa memilih satu file.');
                return;
            }
            
            if (config.multiple && config.maxFiles > 0) {
                if (this.files.length + newFiles.length > config.maxFiles) {
                    alert(`Maksimal ${config.maxFiles} file diperbolehkan.`);
                    return;
                }
            }

            // If not multiple, replace the existing file
            if (!config.multiple) {
                this.files = [];
            }

            newFiles.forEach(file => {
                // Validate size
                if (file.size > config.maxSizeMb * 1024 * 1024) {
                    alert(`File ${file.name} melebihi batas ukuran ${config.maxSizeMb}MB.`);
                    return;
                }
                
                // Validate type
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    alert(`File ${file.name} bukan format yang didukung (JPG/PNG/WEBP).`);
                    return;
                }

                const id = Math.random().toString(36).substring(2, 9);
                const preview = URL.createObjectURL(file);
                
                const fileObj = {
                    id,
                    file,
                    name: file.name,
                    preview,
                    status: 'pending', // pending, uploading, success, error
                    progress: 0,
                    path: null,
                    error: null
                };
                
                this.files.push(fileObj);
                
                // Start upload immediately
                this.uploadFile(fileObj);
            });
        },
        
        removeFile(index) {
            const file = this.files[index];
            if (file.preview) {
                URL.revokeObjectURL(file.preview);
            }
            this.files.splice(index, 1);
        },
        
        uploadFile(fileObj) {
            fileObj.status = 'uploading';
            fileObj.progress = 0;
            fileObj.error = null;
            
            const formData = new FormData();
            formData.append('file', fileObj.file);
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
                    fileObj.progress = Math.round((event.loaded / event.total) * 100);
                }
            };
            
            xhr.onload = () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            fileObj.status = 'success';
                            // Store the path so it can be added to the hidden input
                            // If variants, we might want to store JSON string of paths, 
                            // but usually the form expects a single string or JSON.
                            // Let's store JSON string if variants, or direct path otherwise
                            fileObj.path = config.variants ? JSON.stringify(response.paths) : response.path;
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
                fileObj.error = 'Terjadi kesalahan jaringan';
            };
            
            xhr.send(formData);
        }
    }));
});
</script>
