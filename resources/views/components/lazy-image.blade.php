@props([
    'src',
    'srcset' => null,
    'sizes' => null,
    'alt' => 'Foto Penginapan Kelapa Sawit',
    'class' => 'w-full h-full object-cover',
    'containerClass' => '',
    'aspect' => null,
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'decoding' => 'async',
    'fallbackText' => 'Foto Tidak Tersedia',
    'fallbackIcon' => 'room',
])

<div x-data="{ loaded: false, hasError: false }"
     class="relative overflow-hidden bg-gray-100 {{ $aspect ?? '' }} {{ $containerClass }}">

    {{-- Shimmer Skeleton - hilang saat gambar selesai dimuat --}}
    <div x-show="!loaded && !hasError"
         x-transition:leave="transition-opacity ease-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 skeleton-shimmer flex items-center justify-center pointer-events-none select-none">
        <div class="w-9 h-9 rounded-full bg-white/50 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 animate-pulse text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
    </div>

    {{-- Error Fallback --}}
    <div x-show="hasError" x-cloak
         class="absolute inset-0 bg-gray-50 flex flex-col items-center justify-center text-gray-400 p-3 text-center pointer-events-none"
         style="display:none;">
        @if($fallbackIcon === 'room')
            <svg class="w-8 h-8 text-primary-300 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        @else
            <svg class="w-8 h-8 text-gray-300 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        @endif
        <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">{{ $fallbackText }}</span>
    </div>

    {{-- Image: blur-up via :style inline (menghindari konflik CSS class) --}}
    <img src="{{ $src }}"
         @if($srcset) srcset="{{ $srcset }}" @endif
         @if($sizes) sizes="{{ $sizes }}" @endif
         alt="{{ $alt }}"
         loading="{{ $loading }}"
         decoding="{{ $decoding }}"
         @if($width) width="{{ $width }}" @endif
         @if($height) height="{{ $height }}" @endif
         x-init="$nextTick(() => {
             if ($el.complete) {
                 if ($el.naturalWidth > 0) { loaded = true; }
                 else { hasError = true; loaded = true; }
             }
         })"
         x-on:load="loaded = true"
         x-on:error="hasError = true; loaded = true"
         :style="{
             filter: loaded ? 'blur(0px)' : 'blur(10px)',
             transform: loaded ? 'scale(1)' : 'scale(1.03)',
             opacity: loaded ? (hasError ? 0 : 1) : 0,
             transition: 'filter 0.5s ease, transform 0.5s ease, opacity 0.5s ease'
         }"
         class="{{ $class }}"
         {{ $attributes }}
    />
</div>
