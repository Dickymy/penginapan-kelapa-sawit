@extends('layouts.admin')

@section('title', 'Galeri - Admin')
@section('page-title', 'Galeri')

@section('content')
<div class="space-y-6" x-data="galleryManager()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Galeri Penginapan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola foto yang ditampilkan kepada calon tamu di website.</p>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 font-medium">{{ $activeCount }} aktif</span>
            @if($inactiveCount > 0)
            <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">{{ $inactiveCount }} nonaktif</span>
            @endif
            <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">{{ $galleries->count() }} total</span>
        </div>
    </div>

    {{-- Upload Area --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-1">Unggah Foto Baru</h2>
        <p class="text-xs text-gray-500 mb-4">Anda dapat mengunggah foto beresolusi tinggi. Sistem akan mengoptimalkan ukuran file secara otomatis tanpa mengurangi kualitas visual secara berlebihan.</p>

        <form action="{{ route('admin.galleries.store') }}" method="POST" class="space-y-4"
              x-data="{ submitting: false }"
              @submit="if(submitting) event.preventDefault(); else submitting = true;">
            @csrf

            <x-image-uploader 
                name="images" 
                directory="galleries" 
                :multiple="true" 
                :variants="true" 
                :max-files="10" 
                :max-size-mb="15" 
                label="Pilih Foto"
                hint="Anda dapat memilih beberapa foto sekaligus. Maks 10 file, 15MB per file." />

            {{-- Title & Alt --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="title" class="block text-xs font-medium text-gray-600 mb-1">Judul (opsional)</label>
                    <input type="text" name="title" id="title" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Contoh: Tampak Depan Penginapan">
                </div>
                <div>
                    <label for="alt_text" class="block text-xs font-medium text-gray-600 mb-1">Teks alternatif / deskripsi (opsional)</label>
                    <input type="text" name="alt_text" id="alt_text" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Deskripsi gambar untuk aksesibilitas">
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" :disabled="submitting"
                        class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan ke Galeri'"></span>
                </button>
            </div>
        </form>

        <x-form-error field="images" />
        <x-form-error field="images.*" />
    </div>

    {{-- Gallery Grid --}}
    @if($galleries->isEmpty())
        <x-empty-state message="Belum ada foto di galeri." description="Unggah foto pertama untuk menampilkan galeri di website publik." />
    @else
    <div class="space-y-3">
        {{-- Sort Controls --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600 font-medium">Semua Foto</p>
            <div class="flex items-center gap-2">
                <button type="button" @click="toggleReorder()"
                        :class="reordering ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition">
                    <span x-text="reordering ? '✓ Selesai Atur Urutan' : '↕ Atur Urutan'"></span>
                </button>
            </div>
        </div>

        {{-- Gallery Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" x-ref="galleryGrid">
            @foreach($galleries as $gallery)
            <div class="relative bg-white border border-gray-200 rounded-xl overflow-hidden group"
                 data-id="{{ $gallery->id }}">
                {{-- Image --}}
                <div class="aspect-[4/3] overflow-hidden cursor-pointer relative" @click="openLightbox('{{ $gallery->large_url }}', '{{ $gallery->title ?? '' }}')">
                    <img src="{{ $gallery->thumb_url }}"
                         alt="{{ $gallery->alt_text ?? $gallery->title ?? 'Foto galeri' }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition duration-300 group-hover:scale-105">

                    {{-- Overlay on hover (desktop) --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </div>

                    {{-- Inactive overlay --}}
                    @if(!$gallery->is_active)
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                        <span class="px-2 py-1 bg-black/60 text-white text-xs font-medium rounded">Nonaktif</span>
                    </div>
                    @endif
                </div>

                {{-- Info & Actions --}}
                <div class="p-3">
                    @if($gallery->title)
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $gallery->title }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">Tanpa judul</p>
                    @endif

                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            {{-- Status badge --}}
                            @if($gallery->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                            <span class="text-[10px] text-gray-400">#{{ $gallery->sort_order }}</span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1">
                            {{-- Toggle --}}
                            <form action="{{ route('admin.galleries.toggle', $gallery) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="p-1.5 rounded-md text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition"
                                        title="{{ $gallery->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        aria-label="{{ $gallery->is_active ? 'Nonaktifkan foto' : 'Aktifkan foto' }}">
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
                                    title="Hapus foto"
                                    aria-label="Hapus foto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Reorder buttons (shown when reordering) --}}
                    <div x-show="reordering" x-cloak class="mt-2 flex gap-1">
                        @if(!$loop->first)
                        <form action="{{ route('admin.galleries.reorder') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="order[]" value="{{ $galleries[$loop->index - 1]->id ?? '' }}">
                            @foreach($galleries as $g)
                                @if($g->id !== $gallery->id && $g->id !== ($galleries[$loop->index - 1]->id ?? null))
                                <input type="hidden" name="order[]" value="{{ $g->id }}">
                                @endif
                            @endforeach
                            <button type="button" @click="moveUp({{ $gallery->id }})" class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600 hover:bg-gray-200">↑ Naik</button>
                        </form>
                        @endif
                        @if(!$loop->last)
                        <button type="button" @click="moveDown({{ $gallery->id }})" class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600 hover:bg-gray-200">↓ Turun</button>
                        @endif
                    </div>
                </div>

                {{-- Delete Confirmation Modal --}}
                <x-confirm-modal
                    id="delete-gallery-{{ $gallery->id }}"
                    title="Hapus Foto?"
                    message="Foto beserta seluruh file varian (thumbnail, medium, large) akan dihapus secara permanen dari galeri dan storage."
                    confirm-text="Ya, Hapus"
                    cancel-text="Batal"
                    variant="danger"
                    :form-action="route('admin.galleries.destroy', $gallery)"
                    method="DELETE"
                />
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Lightbox --}}
    <div x-show="lightboxOpen" x-cloak
         @keydown.escape.window="lightboxOpen = false"
         @keydown.left.window="prevImage()"
         @keydown.right.window="nextImage()"
         class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
         role="dialog" aria-modal="true" aria-label="Preview gambar">
        {{-- Close --}}
        <button @click="lightboxOpen = false"
                class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white transition"
                aria-label="Tutup preview">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Image --}}
        <img :src="lightboxSrc" :alt="lightboxTitle" class="max-h-[85vh] max-w-[90vw] object-contain rounded-lg shadow-2xl">

        {{-- Caption --}}
        <div x-show="lightboxTitle" class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-black/60 text-white px-4 py-2 rounded-lg text-sm max-w-md text-center" x-text="lightboxTitle"></div>
    </div>
</div>

<script>
function galleryManager() {
    return {
        reordering: false,
        lightboxOpen: false,
        lightboxSrc: '',
        lightboxTitle: '',

        toggleReorder() {
            this.reordering = !this.reordering;
        },

        async moveUp(id) {
            await this.swapOrder(id, 'up');
        },

        async moveDown(id) {
            await this.swapOrder(id, 'down');
        },

        async swapOrder(id, direction) {
            const cards = [...this.$refs.galleryGrid.querySelectorAll('[data-id]')];
            const ids = cards.map(c => parseInt(c.dataset.id));
            const idx = ids.indexOf(id);

            if (direction === 'up' && idx > 0) {
                [ids[idx], ids[idx - 1]] = [ids[idx - 1], ids[idx]];
            } else if (direction === 'down' && idx < ids.length - 1) {
                [ids[idx], ids[idx + 1]] = [ids[idx + 1], ids[idx]];
            }

            // Submit reorder
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.galleries.reorder") }}';
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
            ids.forEach(id => {
                form.innerHTML += `<input type="hidden" name="order[]" value="${id}">`;
            });
            document.body.appendChild(form);
            form.submit();
        },

        openLightbox(src, title) {
            if (this.reordering) return;
            this.lightboxSrc = src;
            this.lightboxTitle = title;
            this.lightboxOpen = true;
        },
    }
}
</script>
@endsection
