@extends('layouts.public')

@section('title', 'Galeri - Penginapan Kelapa Sawit')

@section('meta')
<meta name="description" content="Galeri foto Penginapan Kelapa Sawit di Kota Bangun II, Kutai Kartanegara, Kalimantan Timur.">
@endsection

@if($galleries->isNotEmpty())
@section('head')
{{-- Preload gambar pertama agar LCP instan --}}
@foreach($galleries->take(3) as $i => $g)
<link rel="preload" as="image" href="{{ $g->medium_url }}" fetchpriority="{{ $i === 0 ? 'high' : 'auto' }}">
@endforeach
@endsection
@endif

@section('content')

{{-- ===== HERO ===== --}}
<section class="relative text-white py-12 md:py-16 bg-primary-950 overflow-hidden"
         x-data="{ scrollY: 0 }"
         @scroll.window.passive="scrollY = window.scrollY">
    <x-hero-background />
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 text-center will-change-transform"
         :style="`transform: translateY(${scrollY * 0.12}px); opacity: ${Math.max(0, 1 - scrollY / 350)}`">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm text-xs font-semibold text-primary-200 border border-white/15 mb-3 shadow-xs">
            <svg class="w-3.5 h-3.5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Dokumentasi Visual
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight drop-shadow-sm">Galeri Penginapan</h1>
        <p class="mt-2.5 text-sm sm:text-base text-gray-200 max-w-md mx-auto leading-relaxed">
            Suasana, fasilitas, dan setiap sudut penginapan kami yang nyaman dan asri.
        </p>
        @if($galleries->isNotEmpty())
        <div class="mt-4 inline-flex items-center gap-2 text-xs text-white/70 bg-white/10 backdrop-blur-xs px-3 py-1 rounded-full border border-white/10">
            <svg class="w-3.5 h-3.5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span><strong>{{ $galleries->count() }}</strong> Foto Tersedia</span>
            <span class="text-white/40">•</span>
            <span>Ketuk foto untuk memperbesar</span>
        </div>
        @endif
    </div>
</section>

{{-- ===== GALLERY GRID + LIGHTBOX ===== --}}
<div class="bg-gray-50 min-h-screen"
     x-data="publicGallery()"
     @keydown.escape.window="if(open) closeLightbox()"
     @keydown.arrow-left.window="if(open) prev()"
     @keydown.arrow-right.window="if(open) next()">

    @if($galleries->isEmpty())
    {{-- Empty state --}}
    <div class="max-w-7xl mx-auto px-4 py-24 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-gray-700 font-semibold text-base">Galeri sedang dipersiapkan.</p>
        <p class="text-sm text-gray-500 mt-1">Silakan kunjungi kembali nanti.</p>
    </div>

    @else

    {{-- ===== PHOTO GRID ===== --}}
    <div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-8 pt-8 pb-4 sm:pt-10">

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-3 lg:gap-3.5
                    auto-rows-[160px] sm:auto-rows-[185px] lg:auto-rows-[210px]">

            @foreach($galleries as $index => $gallery)
            @php
                $featured = ($index === 0);
                $cellClass = $featured ? 'col-span-2 row-span-2' : '';
            @endphp

            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-gray-200 cursor-zoom-in
                        shadow-xs hover:shadow-md hover:scale-[1.01] transition-all duration-200 {{ $cellClass }}"
                 @click="openLightbox({{ $index }})">

                {{-- Image: Langsung dirender browser tanpa blok opacity 0 inline agar instan --}}
                <img src="{{ $gallery->medium_url }}"
                     srcset="{{ $gallery->thumb_url }} 480w, {{ $gallery->medium_url }} 960w"
                     sizes="{{ $featured ? '(max-width:640px) 100vw, (max-width:1024px) 67vw, 50vw' : '(max-width:640px) 50vw, (max-width:1024px) 33vw, 25vw' }}"
                     alt="{{ $gallery->alt_text ?? $gallery->title ?? 'Foto Penginapan Kelapa Sawit ' . ($index + 1) }}"
                     loading="{{ $index < 4 ? 'eager' : 'lazy' }}"
                     decoding="{{ $index < 2 ? 'sync' : 'async' }}"
                     fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

                {{-- Hover overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-transparent to-transparent
                            opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-end p-3 sm:p-4 pointer-events-none">
                    @if($gallery->title)
                    <p class="text-white text-xs sm:text-sm font-semibold line-clamp-1 drop-shadow">{{ $gallery->title }}</p>
                    @endif
                    <div class="flex items-center gap-1 mt-0.5 text-white/80 text-[11px] font-medium">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        <span>Ketuk untuk perbesar</span>
                    </div>
                </div>

                {{-- Featured badge --}}
                @if($featured)
                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-black/40 backdrop-blur-md text-white text-[11px] font-semibold border border-white/15 flex items-center gap-1 shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Foto Utama</span>
                </div>
                @endif
            </div>
            @endforeach

        </div>

        {{-- Photo count footer --}}
        <p class="text-center text-xs text-gray-500 mt-5 mb-2 font-medium">
            Menampilkan {{ $galleries->count() }} dokumentasi foto
        </p>
    </div>

    {{-- ===== CTA ===== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm px-6 py-8 sm:py-10 text-center max-w-2xl mx-auto">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">Tertarik Menginap di Penginapan Kelapa Sawit?</h2>
            <p class="text-gray-600 text-sm mt-1.5 mb-5 leading-relaxed">Pesan kamar pilihan Anda sekarang dan nikmati suasana menginap yang bersih, tenang, dan strategis.</p>
            <a href="{{ route('rooms.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-xl font-bold text-sm
                      hover:bg-primary-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary-600/25
                      active:scale-95 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Lihat Pilihan Kamar
            </a>
        </div>
    </div>

    {{-- ===== LIGHTBOX INSTAN (DUAL-LAYER LQIP) ===== --}}
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] bg-black/98 flex flex-col"
         role="dialog"
         aria-modal="true"
         aria-label="Lightbox galeri foto"
         x-on:touchstart.passive="touchStart($event)"
         x-on:touchend.passive="touchEnd($event)">

        {{-- Top bar: counter + caption + close --}}
        <div class="flex-none flex items-center justify-between px-4 py-3 z-20
                    bg-gradient-to-b from-black/80 via-black/40 to-transparent absolute top-0 inset-x-0 pointer-events-none">
            <div class="pointer-events-auto flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full border border-white/15">
                    <span x-text="currentIndex + 1"></span>
                    <span class="opacity-40">/</span>
                    <span>{{ $galleries->count() }}</span>
                </span>
                <div x-show="images[currentIndex]?.title"
                     class="text-white text-xs sm:text-sm font-medium max-w-[200px] sm:max-w-md truncate drop-shadow"
                     x-text="images[currentIndex]?.title"></div>
            </div>
            <button @click="closeLightbox()"
                    class="pointer-events-auto w-10 h-10 bg-white/15 hover:bg-white/30 active:bg-white/40
                           backdrop-blur-md rounded-full flex items-center justify-center text-white
                           transition-all duration-150 border border-white/20 shadow-md"
                    aria-label="Tutup Galeri">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Main image viewer: Dual layer (Instant Medium + Smooth High-Res Large) --}}
        <div class="flex-1 flex items-center justify-center relative min-h-0 px-2 sm:px-14 overflow-hidden"
             @click.self="closeLightbox()">

            <div class="relative max-h-full max-w-full flex items-center justify-center">
                {{-- Layer 1: Medium image (Instant 0ms from browser cache) --}}
                <img :src="images[currentIndex]?.medium || images[currentIndex]?.thumb"
                     alt=""
                     class="max-h-[78vh] sm:max-h-[82vh] max-w-[94vw] sm:max-w-[88vw] object-contain select-none rounded-xl shadow-2xl"
                     draggable="false">

                {{-- Layer 2: Large image (Smooth fade-in on top when loaded) --}}
                <img :src="images[currentIndex]?.large"
                     :alt="images[currentIndex]?.title || 'Foto penginapan'"
                     :key="currentIndex"
                     class="absolute inset-0 m-auto max-h-[78vh] sm:max-h-[82vh] max-w-[94vw] sm:max-w-[88vw] object-contain select-none rounded-xl transition-opacity duration-300"
                     :class="largeLoaded ? 'opacity-100' : 'opacity-0'"
                     x-on:load="largeLoaded = true"
                     x-on:error="largeLoaded = true"
                     @click.stop
                     draggable="false">
            </div>

            {{-- Prev button --}}
            <button @click.stop="prev()"
                    class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20
                           w-10 h-10 sm:w-12 sm:h-12 bg-black/40 hover:bg-black/70 active:scale-95
                           backdrop-blur-md rounded-full flex items-center justify-center text-white
                           transition-all duration-150 border border-white/15 shadow-lg"
                    aria-label="Foto Sebelumnya">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>

            {{-- Next button --}}
            <button @click.stop="next()"
                    class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20
                           w-10 h-10 sm:w-12 sm:h-12 bg-black/40 hover:bg-black/70 active:scale-95
                           backdrop-blur-md rounded-full flex items-center justify-center text-white
                           transition-all duration-150 border border-white/15 shadow-lg"
                    aria-label="Foto Berikutnya">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        {{-- Filmstrip thumbnails (navigasi bawah) --}}
        @if($galleries->count() > 1)
        <div class="flex-none pb-4 sm:pb-6 pt-2 px-4
                    bg-gradient-to-t from-black via-black/80 to-transparent">
            <div class="flex gap-2 overflow-x-auto justify-center py-1"
                 style="scrollbar-width: none;"
                 x-ref="thumbsRow">
                @foreach($galleries as $i => $gallery)
                <button @click.stop="goTo({{ $i }})"
                        class="flex-none w-14 h-10 sm:w-16 sm:h-11 rounded-lg overflow-hidden border-2
                               transition-all duration-200 focus:outline-none"
                        :class="currentIndex === {{ $i }}
                            ? 'border-emerald-400 opacity-100 scale-105 shadow-md shadow-emerald-500/30'
                            : 'border-transparent opacity-40 hover:opacity-75'"
                        aria-label="Foto {{ $i + 1 }}">
                    <img src="{{ $gallery->thumb_url }}"
                         alt="{{ $gallery->alt_text ?? 'Foto ' . ($i + 1) }}"
                         loading="lazy"
                         class="w-full h-full object-cover pointer-events-none">
                </button>
                @endforeach
            </div>
        </div>
        @endif

    </div>
    @endif
</div>

@if($galleries->isNotEmpty())
<script>
function publicGallery() {
    return {
        open: false,
        currentIndex: 0,
        largeLoaded: false,
        touchStartX: 0,
        touchStartY: 0,
        preloaded: new Set(),

        images: {{ Js::from($galleries->map(function($g) {
            return [
                'large'  => $g->large_url,
                'medium' => $g->medium_url,
                'thumb'  => $g->thumb_url,
                'title'  => $g->title ?? '',
            ];
        })->values()) }},

        openLightbox(index) {
            this.currentIndex = index;
            this.largeLoaded = false;
            this.open = true;
            this._lockScroll();
            this._preload(index);
            this._syncThumb();
        },

        closeLightbox() {
            this.open = false;
            this._unlockScroll();
        },

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
            this.largeLoaded = false;
            this._preload(this.currentIndex);
            this._syncThumb();
        },

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            this.largeLoaded = false;
            this._preload(this.currentIndex);
            this._syncThumb();
        },

        goTo(index) {
            if (index === this.currentIndex) return;
            this.currentIndex = index;
            this.largeLoaded = false;
            this._preload(index);
            this._syncThumb();
        },

        touchStart(e) {
            this.touchStartX = e.touches[0].clientX;
            this.touchStartY = e.touches[0].clientY;
        },

        touchEnd(e) {
            const dx = this.touchStartX - e.changedTouches[0].clientX;
            const dy = Math.abs(this.touchStartY - e.changedTouches[0].clientY);
            if (Math.abs(dx) > 45 && Math.abs(dx) > dy) {
                dx > 0 ? this.next() : this.prev();
            }
        },

        _lockScroll() {
            const y = window.scrollY;
            document.body.dataset.scrollY = y;
            document.documentElement.classList.add('overflow-hidden');
            document.body.classList.add('overflow-hidden', 'touch-none');
        },

        _unlockScroll() {
            const y = parseInt(document.body.dataset.scrollY || '0', 10);
            document.documentElement.classList.remove('overflow-hidden');
            document.body.classList.remove('overflow-hidden', 'touch-none');
            window.scrollTo({ top: y, behavior: 'instant' });
        },

        _preload(index) {
            const n = this.images.length;
            [index, (index + 1) % n, (index - 1 + n) % n].forEach(i => {
                const url = this.images[i]?.large;
                if (url && !this.preloaded.has(url)) {
                    this.preloaded.add(url);
                    const img = new Image();
                    img.src = url;
                }
            });
        },

        _syncThumb() {
            this.$nextTick(() => {
                const row = this.$refs.thumbsRow;
                if (!row) return;
                const btn = row.children[this.currentIndex];
                if (btn) btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            });
        },
    };
}
</script>
@endif

@endsection
