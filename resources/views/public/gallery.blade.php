@extends('layouts.public')

@section('title', 'Galeri - Penginapan Kelapa Sawit')

@section('meta')
<meta name="description" content="Galeri foto Penginapan Kelapa Sawit di Kota Bangun II, Kutai Kartanegara, Kalimantan Timur.">
@endsection

@section('content')
{{-- Page Hero --}}
<section class="bg-primary-700 text-white py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-2xl md:text-3xl font-bold">Galeri Penginapan</h1>
        <p class="mt-2 text-primary-100 text-sm">Lihat suasana dan kondisi penginapan kami</p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="publicGallery()">
    @if($galleries->isEmpty())
        <div class="text-center py-16">
            <svg class="mx-auto w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-600 font-medium">Galeri foto sedang dipersiapkan.</p>
            <p class="text-sm text-gray-500 mt-1">Silakan kunjungi kembali nanti.</p>
        </div>
    @else
        {{-- Gallery Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
            @foreach($galleries as $index => $gallery)
            <div class="group cursor-pointer" @click="openLightbox({{ $index }})">
                <div class="aspect-[4/3] overflow-hidden rounded-xl bg-gray-100 relative">
                    <img src="{{ $gallery->medium_url }}"
                         srcset="{{ $gallery->thumb_url }} 480w, {{ $gallery->medium_url }} 960w"
                         sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                         alt="{{ $gallery->alt_text ?? $gallery->title ?? 'Foto Penginapan Kelapa Sawit' }}"
                         loading="{{ $index < 8 ? 'eager' : 'lazy' }}"
                         decoding="{{ $index < 4 ? 'sync' : 'async' }}"
                         width="480" height="360"
                         class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors rounded-xl"></div>
                </div>
                @if($gallery->title)
                <p class="mt-2 text-sm text-gray-700 font-medium truncate px-0.5">{{ $gallery->title }}</p>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Lightbox --}}
        <div x-show="open" x-cloak
             @keydown.escape.window="open = false"
             @keydown.left.window="prev()"
             @keydown.right.window="next()"
             class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center"
             role="dialog" aria-modal="true" aria-label="Galeri foto">

            {{-- Close button --}}
            <button @click="open = false"
                    class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition"
                    aria-label="Tutup galeri">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            {{-- Counter --}}
            <div class="absolute top-4 left-4 text-white/70 text-sm">
                <span x-text="currentIndex + 1"></span> / <span>{{ $galleries->count() }}</span>
            </div>

            {{-- Previous --}}
            <button @click="prev()"
                    class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition"
                    aria-label="Foto sebelumnya">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>

            {{-- Image --}}
            <img :src="images[currentIndex]?.large" :alt="images[currentIndex]?.title || 'Foto penginapan'"
                 class="max-h-[80vh] max-w-[85vw] sm:max-w-[90vw] object-contain rounded-lg select-none"
                 @click.stop>

            {{-- Next --}}
            <button @click="next()"
                    class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition"
                    aria-label="Foto berikutnya">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>

            {{-- Caption --}}
            <div x-show="images[currentIndex]?.title" class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-black/60 text-white px-4 py-2 rounded-lg text-sm max-w-md text-center" x-text="images[currentIndex]?.title"></div>

            {{-- Swipe support (mobile) --}}
            <div class="absolute inset-0" @click="open = false"
                 x-on:touchstart.passive="touchStart($event)"
                 x-on:touchend.passive="touchEnd($event)"></div>
        </div>
    @endif

    {{-- CTA --}}
    <div class="mt-12 text-center">
        <a href="{{ route('home') }}#cari-kamar" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
            Pesan Kamar Sekarang
        </a>
    </div>
</div>

@if($galleries->isNotEmpty())
<script>
function publicGallery() {
    return {
        open: false,
        currentIndex: 0,
        touchStartX: 0,
        images: @json($galleries->map(fn($g) => [
            'large' => $g->large_url,
            'title' => $g->title ?? '',
        ])->values()),

        openLightbox(index) {
            this.currentIndex = index;
            this.open = true;
        },

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        },

        touchStart(e) {
            this.touchStartX = e.touches[0].clientX;
        },

        touchEnd(e) {
            const diff = this.touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) {
                diff > 0 ? this.next() : this.prev();
            }
        }
    }
}
</script>
@endif
@endsection
