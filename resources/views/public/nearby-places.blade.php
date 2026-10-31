@extends('layouts.public')

@section('title', 'Toko Sembako')

@section('content')
{{-- Hero Section --}}
<div class="relative bg-primary-900 py-24 overflow-hidden">
    {{-- Decorative Background --}}
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-800 to-primary-900 opacity-90"></div>
        <svg class="absolute left-0 bottom-0 text-primary-800 w-full h-auto opacity-50 transform translate-y-1/2" viewBox="0 0 1440 320" fill="currentColor">
            <path fill-opacity="1" d="M0,128L48,138.7C96,149,192,171,288,165.3C384,160,480,128,576,128C672,128,768,160,864,170.7C960,181,1056,171,1152,149.3C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <div class="inline-flex items-center justify-center px-4 py-2 bg-white/10 rounded-full backdrop-blur-md mb-6 border border-white/20 shadow-lg gap-2">
            <svg class="w-5 h-5 text-primary-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-white font-bold tracking-widest uppercase text-xs">Sekitar Penginapan</span>
        </div>
        <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl mb-6">
            Toko Sembako Arkhan
        </h1>
        <p class="mt-4 text-xl text-primary-100 max-w-3xl mx-auto font-medium leading-relaxed">
            Penginapan Kelapa Sawit berdampingan langsung dengan toko sembako milik kami. Penuhi segala kebutuhan harian, cemilan, minuman, hingga perlengkapan mandi tanpa harus pergi jauh!
        </p>
        
        <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="https://maps.app.goo.gl/Q5M349iKRqpmDMQn9" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-primary-900 bg-white rounded-xl hover:bg-gray-50 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                <svg class="w-5 h-5 mr-2.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Lihat Lokasi di Google Maps
            </a>
        </div>
    </div>
</div>

{{-- Content Grid --}}
<div class="py-20 bg-gray-50 relative -mt-6 rounded-t-[3rem] z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Katalog & Galeri Toko</h2>
            <div class="w-24 h-1.5 bg-primary-500 mx-auto mt-4 rounded-full"></div>
        </div>

        @if($placesByCategory->isEmpty())
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center max-w-2xl mx-auto mt-8">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-primary-50 mb-6">
                    <svg class="w-12 h-12 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-4">Katalog Belum Tersedia</h3>
                <p class="text-gray-500 text-lg leading-relaxed">
                    Foto dan informasi produk toko sembako kami sedang dalam proses pembaruan. Silakan kunjungi toko kami secara langsung di sebelah penginapan!
                </p>
            </div>
        @else
            <div x-data="{ selectedCategory: 'Semua' }">
                {{-- Category Filters --}}
                <div class="flex flex-wrap gap-2 sm:gap-3 justify-center mb-8 sm:mb-12">
                    <button @click="selectedCategory = 'Semua'" 
                            :class="selectedCategory === 'Semua' ? 'bg-primary-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-primary-50 hover:text-primary-600'"
                            class="px-4 py-2 sm:px-6 sm:py-2.5 rounded-full text-xs sm:text-sm font-semibold border border-gray-200 transition-all duration-300 active:scale-95">
                        Semua Produk
                    </button>
                    @foreach($placesByCategory->keys() as $cat)
                    <button @click="selectedCategory = '{{ $cat }}'" 
                            :class="selectedCategory === '{{ $cat }}' ? 'bg-primary-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-primary-50 hover:text-primary-600'"
                            class="px-4 py-2 sm:px-6 sm:py-2.5 rounded-full text-xs sm:text-sm font-semibold border border-gray-200 transition-all duration-300 active:scale-95">
                        {{ $cat }}
                    </button>
                    @endforeach
                </div>

                {{-- Product Grid: 2 Columns on mobile, 3 Columns on lg --}}
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6 lg:gap-8">
                    @foreach($placesByCategory as $category => $places)
                        @foreach($places as $place)
                            <div x-show="selectedCategory === 'Semua' || selectedCategory === '{{ $category }}'"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="bg-white rounded-2xl sm:rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_16px_40px_rgba(8,_112,_184,_0.1)] hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col group cursor-default relative">
                                
                                {{-- Kategori Badge --}}
                                <div class="absolute top-2.5 left-2.5 sm:top-4 sm:left-4 z-10">
                                    <span class="bg-primary-600/90 backdrop-blur-md text-white text-[10px] sm:text-xs font-bold px-2 py-0.5 sm:px-3 sm:py-1 rounded-full shadow-md">
                                        {{ $category }}
                                    </span>
                                </div>

                                {{-- Image Area --}}
                                <div class="relative overflow-hidden aspect-square sm:aspect-[4/3] bg-gradient-to-br from-primary-50 to-gray-100 flex items-center justify-center p-4">
                                    @if($place->image)
                                        <img src="{{ Storage::url($place->image) }}" alt="{{ $place->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" loading="lazy" decoding="async">
                                    @else
                                        {{-- Placeholder SVG --}}
                                        <div class="text-center z-10 group-hover:scale-105 transition-transform duration-300">
                                            <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-white shadow-sm mb-2 sm:mb-3">
                                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            </div>
                                            <p class="text-primary-500 font-medium text-[11px] sm:text-xs">Produk Toko</p>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Card Content --}}
                                <div class="p-3 sm:p-5 md:p-6 flex flex-col flex-grow bg-white">
                                    <h4 class="text-xs sm:text-base md:text-lg font-bold text-gray-900 leading-snug mb-1 sm:mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">
                                        {{ $place->name }}
                                    </h4>
                                    
                                    @if($place->description)
                                        <p class="text-gray-500 text-[11px] sm:text-xs md:text-sm leading-relaxed mb-3 flex-grow line-clamp-2">
                                            {{ $place->description }}
                                        </p>
                                    @endif

                                    @if($place->distance)
                                    <div class="mt-auto pt-2 sm:pt-3 border-t border-gray-100 flex items-center justify-between">
                                        <div class="inline-flex items-center text-primary-700 font-bold text-[11px] sm:text-xs bg-primary-50 px-2 py-1 rounded-lg">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            {{ $place->distance }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
