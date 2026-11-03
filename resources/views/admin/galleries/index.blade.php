@extends('layouts.admin')

@section('title', 'Manajemen Galeri - Admin')
@section('page-title', 'Galeri')

@section('content')
<div class="space-y-6" x-data="galleryManager()">
    {{-- Header & Quick Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Galeri Foto Penginapan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola, beri keterangan, ganti gambar, dan atur urutan tampilan foto di website publik.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="uploadOpen = !uploadOpen"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-semibold shadow-xs transition active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span x-text="uploadOpen ? 'Tutup Form Unggah' : '+ Unggah Foto Baru'"></span>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Foto</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $galleries->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Foto Tampil (Aktif)</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeCount }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Disembunyikan</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $inactiveCount }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanpa Judul</p>
                <p class="text-2xl font-bold text-gray-700 mt-1">{{ $galleries->whereNull('title')->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
        </div>
    </div>

    {{-- Collapsible Upload Section --}}
    <div x-show="uploadOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="bg-white rounded-2xl border-2 border-primary-100 p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-base font-bold text-gray-900">Unggah Foto Baru ke Galeri</h2>
                <p class="text-xs text-gray-500 mt-0.5">Maksimal 20 foto sekaligus. Format: JPG, PNG, WEBP (otomatis dikompres & dibuat versi WebP).</p>
            </div>
            <button @click="uploadOpen = false" class="text-gray-400 hover:text-gray-600 text-sm">✕ Tutup</button>
        </div>

        <form action="{{ route('admin.galleries.store') }}" method="POST" class="space-y-4"
              x-data="{ submitting: false }"
              @submit="if(submitting) event.preventDefault(); else submitting = true;">
            @csrf

            <x-image-uploader 
                name="images" 
                directory="galleries" 
                :multiple="true" 
                :variants="true" 
                :max-files="20" 
                :max-size-mb="15" 
                label="Pilih atau Seret Foto ke Sini"
                hint="Dapat memilih banyak foto sekaligus. Resolusi tinggi otomatis dioptimalkan." />

            {{-- Optional Default Title & Alt --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <label for="upload_title" class="block text-xs font-medium text-gray-700 mb-1">
                        Judul Default (opsional)
                    </label>
                    <input type="text" name="title" id="upload_title"
                           class="w-full rounded-xl border-gray-200 text-sm focus:ring-primary-500 focus:border-primary-500 placeholder-gray-400"
                           placeholder="Contoh: Kamar Deluxe, Area Parkir, Lobi">
                    <p class="text-[11px] text-gray-400 mt-1">Dapat diedit lagi per-foto setelah diunggah.</p>
                </div>
                <div>
                    <label for="upload_alt_text" class="block text-xs font-medium text-gray-700 mb-1">
                        Keterangan / Teks Aksesibilitas (opsional)
                    </label>
                    <input type="text" name="alt_text" id="upload_alt_text"
                           class="w-full rounded-xl border-gray-200 text-sm focus:ring-primary-500 focus:border-primary-500 placeholder-gray-400"
                           placeholder="Deskripsi singkat isi gambar">
                </div>
            </div>

            {{-- Submit button --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="uploadOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" :disabled="submitting"
                        class="inline-flex items-center px-6 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50 shadow-xs">
                    <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="submitting ? 'Menyimpan ke Galeri...' : 'Simpan Foto ke Galeri'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- Gallery List & Controls --}}
    @if($galleries->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Belum ada foto di galeri</h3>
            <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">Mulai tambahkan foto fasilitas dan suasana penginapan agar calon tamu dapat melihatnya.</p>
            <button @click="uploadOpen = true" class="mt-4 px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition">
                + Unggah Foto Pertama
            </button>
        </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        {{-- Toolbar: Filter tabs & Search --}}
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            {{-- Filter Pills --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs font-medium">
                <button type="button" @click="filter = 'all'"
                        :class="filter === 'all' ? 'bg-primary-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                    Semua ({{ $galleries->count() }})
                </button>
                <button type="button" @click="filter = 'active'"
                        :class="filter === 'active' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                    Tampil ({{ $activeCount }})
                </button>
                <button type="button" @click="filter = 'inactive'"
                        :class="filter === 'inactive' ? 'bg-amber-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                    Disembunyikan ({{ $inactiveCount }})
                </button>
                <button type="button" @click="filter = 'untitled'"
                        :class="filter === 'untitled' ? 'bg-gray-800 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                    Belum Diberi Teks ({{ $galleries->whereNull('title')->count() }})
                </button>
            </div>

            {{-- Search Box --}}
            <div class="relative w-full sm:w-64">
                <input type="text" x-model="searchQuery"
                       placeholder="Cari judul/keterangan..."
                       class="w-full pl-9 pr-4 py-1.5 text-xs rounded-xl border-gray-200 focus:ring-primary-500 focus:border-primary-500 placeholder-gray-400">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" x-ref="galleryGrid">
                @foreach($galleries as $index => $gallery)
                <div class="bg-white border border-gray-200 hover:border-primary-300 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 flex flex-col group relative"
                     data-id="{{ $gallery->id }}"
                     data-index="{{ $index }}"
                     x-show="matchesFilter({{ $gallery->id }}, {{ $gallery->is_active ? 'true' : 'false' }}, '{{ addslashes($gallery->title ?? '') }}', '{{ addslashes($gallery->alt_text ?? '') }}')">

                    {{-- Image Thumbnail Container --}}
                    <div class="aspect-[4/3] bg-gray-100 overflow-hidden relative cursor-pointer"
                         @click="previewImage('{{ $gallery->large_url }}', '{{ addslashes($gallery->title ?? 'Foto ' . ($index + 1)) }}')">
                        <img src="{{ $gallery->thumb_url }}"
                             alt="{{ $gallery->alt_text ?? $gallery->title ?? 'Foto Galeri' }}"
                             loading="lazy"
                             class="w-full h-full object-cover transition duration-300 group-hover:scale-105">

                        {{-- Position Badge --}}
                        <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                            @if($index === 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-400 text-amber-950 shadow-sm border border-amber-300">
                                <svg class="w-3 h-3 text-amber-950 fill-amber-950" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>#1 Utama</span>
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-black/60 backdrop-blur-xs text-white">
                                #{{ $index + 1 }}
                            </span>
                            @endif
                        </div>

                        {{-- Status Pill --}}
                        <div class="absolute top-2.5 right-2.5">
                            @if($gallery->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white shadow-xs">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-700 text-white shadow-xs">Nonaktif</span>
                            @endif
                        </div>

                        {{-- Click to zoom hint overlay --}}
                        <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/60 text-white text-xs font-medium backdrop-blur-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                Lihat
                            </span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-3.5 flex-1 flex flex-col justify-between space-y-3">
                        <div>
                            @if($gallery->title)
                                <h3 class="text-sm font-bold text-gray-900 line-clamp-1" title="{{ $gallery->title }}">{{ $gallery->title }}</h3>
                            @else
                                <button type="button" @click="openEditModal({{ $gallery->id }}, '{{ addslashes($gallery->title ?? '') }}', '{{ addslashes($gallery->alt_text ?? '') }}', '{{ $gallery->thumb_url }}')"
                                        class="text-xs text-primary-600 hover:text-primary-800 font-semibold inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    + Beri Judul Foto
                                </button>
                            @endif

                            @if($gallery->alt_text)
                                <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5" title="{{ $gallery->alt_text }}">{{ $gallery->alt_text }}</p>
                            @endif
                        </div>

                        {{-- Action Buttons Toolbar --}}
                        <div class="pt-2 border-t border-gray-100 space-y-2">
                            {{-- Primary Quick Action Buttons --}}
                            <div class="grid grid-cols-2 gap-1.5">
                                <button type="button"
                                        @click="openEditModal({{ $gallery->id }}, '{{ addslashes($gallery->title ?? '') }}', '{{ addslashes($gallery->alt_text ?? '') }}', '{{ $gallery->thumb_url }}')"
                                        class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 bg-gray-50 hover:bg-primary-50 text-gray-700 hover:text-primary-700 border border-gray-200 hover:border-primary-200 rounded-lg text-xs font-semibold transition">
                                    <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit Teks
                                </button>

                                <button type="button"
                                        @click="openReplaceModal({{ $gallery->id }}, '{{ addslashes($gallery->title ?? '') }}', '{{ $gallery->thumb_url }}')"
                                        class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 bg-gray-50 hover:bg-blue-50 text-gray-700 hover:text-blue-700 border border-gray-200 hover:border-blue-200 rounded-lg text-xs font-semibold transition">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Ganti Foto
                                </button>
                            </div>

                            {{-- Reorder & State Actions --}}
                            <div class="flex items-center justify-between gap-1 text-xs">
                                <div class="flex items-center gap-1">
                                    {{-- Move Up --}}
                                    @if($index > 0)
                                    <button type="button" @click="moveUp({{ $gallery->id }})"
                                            title="Geser ke kiri / atas"
                                            class="p-1 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    @endif

                                    {{-- Move Down --}}
                                    @if($index < $galleries->count() - 1)
                                    <button type="button" @click="moveDown({{ $gallery->id }})"
                                            title="Geser ke kanan / bawah"
                                            class="p-1 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    @endif

                                    {{-- Make Primary (#1) --}}
                                    @if($index !== 0)
                                    <button type="button" @click="moveToTop({{ $gallery->id }})"
                                            title="Jadikan Foto Utama (#1)"
                                            class="px-1.5 py-1 rounded-md bg-amber-50 hover:bg-amber-100 text-amber-700 text-[10px] font-bold transition flex items-center gap-0.5">
                                        ⭐ Utama
                                    </button>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1">
                                    {{-- Toggle Visibility --}}
                                    <form action="{{ route('admin.galleries.toggle', $gallery) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="p-1.5 rounded-md transition {{ $gallery->is_active ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}"
                                                title="{{ $gallery->is_active ? 'Sembunyikan dari website publik' : 'Tampilkan di website publik' }}">
                                            @if($gallery->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                            @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <button type="button"
                                            @click="$dispatch('open-confirm', { id: 'delete-gallery-{{ $gallery->id }}' })"
                                            class="p-1.5 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                            title="Hapus foto dari galeri">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Modal for this item --}}
                    <x-confirm-modal
                        id="delete-gallery-{{ $gallery->id }}"
                        title="Hapus Foto Galeri?"
                        message="Foto ini beserta varian thumbnail dan gambarnya akan dihapus permanen. Tindakan ini tidak dapat dibatalkan."
                        confirm-text="Ya, Hapus Foto"
                        cancel-text="Batal"
                        variant="danger"
                        :form-action="route('admin.galleries.destroy', $gallery)"
                        method="DELETE"
                    />
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ===== MODAL 1: EDIT TEKS & JUDUL FOTO ===== --}}
    <div x-show="editModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity" @click="closeEditModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden" @click.stop>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Edit Judul & Keterangan Foto</h3>
                        <p class="text-xs text-gray-500">Teks ini akan tampil saat pengunjung melihat galeri foto.</p>
                    </div>
                    <button @click="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                </div>

                <form :action="editActionUrl" method="POST" class="p-6 space-y-4">
                    @csrf @method('PATCH')

                    {{-- Preview thumbnail --}}
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <img :src="editThumbUrl" alt="" class="w-16 h-12 object-cover rounded-lg border border-gray-200">
                        <div>
                            <p class="text-xs font-semibold text-gray-800">Foto Terpilih</p>
                            <p class="text-[11px] text-gray-500">Keterangan yang jelas membantu calon tamu mengenali fasilitas Anda.</p>
                        </div>
                    </div>

                    {{-- Judul Input --}}
                    <div>
                        <label for="edit_title" class="block text-xs font-bold text-gray-700 mb-1">Judul Foto</label>
                        <input type="text" name="title" id="edit_title" x-model="editTitle"
                               class="w-full rounded-xl border-gray-200 text-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Contoh: Tampak Depan Penginapan, Kamar Twin Bed, dll.">

                        {{-- Quick Preset Suggestions --}}
                        <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                            <span class="text-[10px] text-gray-400">Rekomendasi Cepat:</span>
                            <template x-for="preset in ['Tampak Depan', 'Lobi Resepsionis', 'Kamar Twin Bed', 'Kamar Double Bed', 'Kamar Mandi Bersih', 'Area Parkir Luas', 'Fasilitas Mushola', 'Lorong & Kamar']" :key="preset">
                                <button type="button" @click="editTitle = preset"
                                        class="px-2 py-0.5 rounded-full bg-gray-100 hover:bg-primary-100 hover:text-primary-700 text-gray-600 text-[10px] transition">
                                    + <span x-text="preset"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Alt Text / Deskripsi --}}
                    <div>
                        <label for="edit_alt" class="block text-xs font-bold text-gray-700 mb-1">Keterangan Tambahan / Deskripsi (Opsional)</label>
                        <input type="text" name="alt_text" id="edit_alt" x-model="editAlt"
                               class="w-full rounded-xl border-gray-200 text-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Deskripsi detail sudut atau keunggulan foto ini">
                    </div>

                    {{-- Buttons --}}
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button type="button" @click="closeEditModal()"
                                class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl text-sm font-bold bg-primary-600 text-white hover:bg-primary-700 shadow-xs transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== MODAL 2: GANTI FOTO (REPLACE IMAGE) ===== --}}
    <div x-show="replaceModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity" @click="closeReplaceModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden" @click.stop>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Ganti File Foto</h3>
                        <p class="text-xs text-gray-500">Ganti file gambar ini dengan foto baru tanpa menghapus posisi dan judulnya.</p>
                    </div>
                    <button @click="closeReplaceModal()" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                </div>

                <form :action="replaceActionUrl" method="POST" enctype="multipart/form-data" class="p-6 space-y-4"
                      x-data="{ uploading: false }"
                      @submit="uploading = true">
                    @csrf @method('PATCH')

                    {{-- Compare: Old Photo vs New Preview --}}
                    <div class="grid grid-cols-2 gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 mb-1">Foto Lama</p>
                            <img :src="replaceOldThumb" alt="Foto Lama" class="w-full h-24 object-cover rounded-lg border border-gray-200">
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 mb-1">Foto Baru (Preview)</p>
                            <div class="w-full h-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-white">
                                <template x-if="newPhotoPreview">
                                    <img :src="newPhotoPreview" alt="Foto Baru" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!newPhotoPreview">
                                    <span class="text-[11px] text-gray-400 text-center px-2">Pilih file baru di bawah</span>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- File Input --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Pilih Foto Pengganti <span class="text-red-500">*</span></label>
                        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" required
                               @change="handleFileChange($event)"
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer border border-gray-200 rounded-xl p-1.5">
                        <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maks 15MB. Otomatis dikompres & dioptimalkan.</p>
                    </div>

                    {{-- Title (Optional update) --}}
                    <div>
                        <label for="replace_title" class="block text-xs font-bold text-gray-700 mb-1">Judul Foto (bisa disesuaikan)</label>
                        <input type="text" name="title" id="replace_title" x-model="replaceTitle"
                               class="w-full rounded-xl border-gray-200 text-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    {{-- Buttons --}}
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button type="button" @click="closeReplaceModal()"
                                class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="uploading || !hasNewFile"
                                class="px-5 py-2 rounded-xl text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 shadow-xs transition flex items-center gap-1.5">
                            <span x-text="uploading ? 'Mengganti Foto...' : 'Ganti Foto Sekarang'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== LIGHTBOX PREVIEW ===== --}}
    <div x-show="previewOpen" x-cloak
         @keydown.escape.window="previewOpen = false"
         class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center p-4"
         role="dialog" aria-modal="true">
        <button @click="previewOpen = false"
                class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white transition"
                aria-label="Tutup Preview">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img :src="previewSrc" :alt="previewTitle" class="max-h-[85vh] max-w-[92vw] object-contain rounded-xl shadow-2xl">
        <div x-show="previewTitle" class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-black/70 backdrop-blur-xs text-white px-4 py-2 rounded-xl text-sm font-medium max-w-md text-center" x-text="previewTitle"></div>
    </div>
</div>

<script>
function galleryManager() {
    return {
        uploadOpen: false,
        filter: 'all',
        searchQuery: '',

        // Preview Lightbox
        previewOpen: false,
        previewSrc: '',
        previewTitle: '',

        // Edit Modal
        editModalOpen: false,
        editGalleryId: null,
        editTitle: '',
        editAlt: '',
        editThumbUrl: '',
        editActionUrl: '',

        // Replace Modal
        replaceModalOpen: false,
        replaceGalleryId: null,
        replaceTitle: '',
        replaceOldThumb: '',
        replaceActionUrl: '',
        newPhotoPreview: null,
        hasNewFile: false,

        previewImage(src, title) {
            this.previewSrc = src;
            this.previewTitle = title;
            this.previewOpen = true;
        },

        openEditModal(id, title, alt, thumb) {
            this.editGalleryId = id;
            this.editTitle = title || '';
            this.editAlt = alt || '';
            this.editThumbUrl = thumb;
            this.editActionUrl = `/admin/galleries/${id}`;
            this.editModalOpen = true;
        },

        closeEditModal() {
            this.editModalOpen = false;
        },

        openReplaceModal(id, title, thumb) {
            this.replaceGalleryId = id;
            this.replaceTitle = title || '';
            this.replaceOldThumb = thumb;
            this.replaceActionUrl = `/admin/galleries/${id}/replace`;
            this.newPhotoPreview = null;
            this.hasNewFile = false;
            this.replaceModalOpen = true;
        },

        closeReplaceModal() {
            this.replaceModalOpen = false;
            this.newPhotoPreview = null;
            this.hasNewFile = false;
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.hasNewFile = true;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.newPhotoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.hasNewFile = false;
                this.newPhotoPreview = null;
            }
        },

        matchesFilter(id, isActive, title, alt) {
            // Check status tab
            if (this.filter === 'active' && !isActive) return false;
            if (this.filter === 'inactive' && isActive) return false;
            if (this.filter === 'untitled' && (title && title.trim() !== '')) return false;

            // Check search text
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                const t = (title || '').toLowerCase();
                const a = (alt || '').toLowerCase();
                return t.includes(q) || a.includes(q);
            }

            return true;
        },

        moveUp(id) {
            this.swapOrder(id, 'up');
        },

        moveDown(id) {
            this.swapOrder(id, 'down');
        },

        moveToTop(id) {
            const cards = [...this.$refs.galleryGrid.querySelectorAll('[data-id]')];
            const ids = cards.map(c => parseInt(c.dataset.id));
            const idx = ids.indexOf(id);
            if (idx > 0) {
                ids.splice(idx, 1);
                ids.unshift(id);
                this.submitOrder(ids);
            }
        },

        swapOrder(id, direction) {
            const cards = [...this.$refs.galleryGrid.querySelectorAll('[data-id]')];
            const ids = cards.map(c => parseInt(c.dataset.id));
            const idx = ids.indexOf(id);

            if (direction === 'up' && idx > 0) {
                [ids[idx], ids[idx - 1]] = [ids[idx - 1], ids[idx]];
                this.submitOrder(ids);
            } else if (direction === 'down' && idx < ids.length - 1) {
                [ids[idx], ids[idx + 1]] = [ids[idx + 1], ids[idx]];
                this.submitOrder(ids);
            }
        },

        submitOrder(ids) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.galleries.reorder") }}';
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
            ids.forEach(id => {
                form.innerHTML += `<input type="hidden" name="order[]" value="${id}">`;
            });
            document.body.appendChild(form);
            form.submit();
        }
    };
}
</script>
@endsection
