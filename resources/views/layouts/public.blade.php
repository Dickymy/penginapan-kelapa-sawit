<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Penginapan Kelapa Sawit — Penginapan di Kota Bangun II')</title>
    @yield('meta')
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="preload" as="image" href="{{ asset('images/logo.webp') }}">
    <meta name="theme-color" content="#0284c7">
    {{-- x-cloak harus aktif sebelum Alpine init agar tidak ada flash of unstyled Alpine elements --}}
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-white text-gray-800 font-sans">
    {{-- Header --}}
    <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-40" x-data="{ open: false }" @keydown.escape.window="open = false">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="text-xl font-bold text-primary-700 flex items-center gap-2">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-8 w-auto object-contain">
                    <span class="hidden sm:inline">Penginapan Kelapa Sawit</span>
                    <span class="sm:hidden">Kelapa Sawit</span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden lg:flex items-center space-x-2 text-sm font-medium">
                    <a href="{{ route('home') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-md transition border-b-2 {{ request()->routeIs('home') ? 'border-primary-600 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Beranda
                    </a>
                    <a href="{{ route('rooms.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-md transition border-b-2 {{ request()->routeIs('rooms.*') ? 'border-primary-600 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        Kamar
                    </a>
                    <a href="{{ route('gallery') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-md transition border-b-2 {{ request()->routeIs('gallery') ? 'border-primary-600 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Galeri
                    </a>
                    <a href="{{ route('booking.my') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-md transition border-b-2 {{ request()->routeIs('booking.my') || request()->routeIs('booking.verify*') || request()->routeIs('booking.guest.detail') || request()->routeIs('member.bookings.*') ? 'border-primary-600 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Booking Saya
                    </a>

                    {{-- Dropdown Lainnya --}}
                    @php
                        $isLainnyaActive = request()->routeIs('nearby-places') || request()->routeIs('faq') || request()->routeIs('location') || request()->routeIs('contact.*') || request()->routeIs('about') || request()->routeIs('policy');
                    @endphp
                    <div x-data="{ dropdownOpen: false, closeTimeout: null }" 
                         class="relative h-full flex items-center" 
                         @mouseenter="clearTimeout(closeTimeout); dropdownOpen = true" 
                         @mouseleave="closeTimeout = setTimeout(() => dropdownOpen = false, 200)">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-1 px-3 py-2 rounded-md transition border-b-2 {{ $isLainnyaActive ? 'border-primary-600 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                            Lainnya
                            <svg class="w-3.5 h-3.5 opacity-70 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="dropdownOpen" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 top-full pt-1 w-48 z-50">
                            <div class="bg-white rounded-xl shadow-lg border border-gray-100 py-2">
                            
                            <a href="{{ route('nearby-places') }}" class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('nearby-places') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Sekitar
                            </a>
                            <a href="{{ route('location') }}" class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('location') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Lokasi
                            </a>
                            <a href="{{ route('faq') }}" class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('faq') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                FAQ
                            </a>
                            <a href="{{ route('contact.create') }}" class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('contact.*') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Hubungi Kami
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('about') }}" class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('about') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Tentang
                            </a>
                            <a href="{{ route('policy') }}" class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('policy') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Kebijakan
                            </a>
                        </div>
                        </div>
                    </div>

                    <a href="{{ route('home') }}#cari-kamar" class="ml-2 flex items-center gap-1.5 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm hover:bg-primary-700 transition shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari Kamar
                    </a>
                </nav>

                {{-- Desktop Account --}}
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <div x-data="{ accountOpen: false, closeTimeout: null }" 
                             class="relative h-full flex items-center"
                             @mouseenter="clearTimeout(closeTimeout); accountOpen = true" 
                             @mouseleave="closeTimeout = setTimeout(() => accountOpen = false, 200)">
                            <button @click="accountOpen = !accountOpen"
                                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-sm font-bold text-primary-700">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="hidden xl:inline max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <svg :class="{'rotate-180': accountOpen}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="accountOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 origin-top-right">
                                <a href="{{ route('member.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('member.bookings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Booking Saya
                                </a>
                                <a href="{{ route('member.points.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Poin Saya
                                </a>
                                <a href="{{ route('member.profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profil
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button type="button" @click="$dispatch('open-confirm', { id: 'member-logout' })"
                                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                            Masuk
                        </a>
                    @endauth
                </div>

                {{-- Mobile Hamburger --}}
                <button @click="open = !open" class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100 transition" aria-label="Buka menu navigasi">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Drawer Overlay --}}
        <div x-show="open" x-cloak class="fixed inset-0 z-40 lg:hidden">
            {{-- Backdrop --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false"
                 class="fixed inset-0 bg-black/40"></div>

            {{-- Drawer Panel --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="fixed inset-y-0 right-0 w-72 max-w-[85vw] bg-white shadow-xl flex flex-col"
                 x-effect="document.body.style.overflow = open ? 'hidden' : ''">
                {{-- Drawer Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-white">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-bold text-primary-700">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-6 w-auto object-contain">
                        <span>Penginapan Kelapa Sawit</span>
                    </a>
                    <button @click="open = false" class="p-2 -mr-2 rounded-md text-gray-500 hover:bg-gray-100" aria-label="Tutup menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Drawer Navigation --}}
                <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1 bg-gray-50/50">
                    {{-- CTA Utama --}}
                    <div class="mb-6 px-1">
                        <a href="{{ route('home') }}#cari-kamar" @click="open = false" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Cari Kamar Tersedia
                        </a>
                    </div>

                    <p class="px-3 py-1 text-xs font-bold text-primary-600/80 uppercase tracking-wider">Jelajahi</p>
                    <a href="{{ route('home') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('home') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Beranda
                    </a>
                    <a href="{{ route('rooms.index') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('rooms.*') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        Kamar
                    </a>
                    <a href="{{ route('gallery') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('gallery') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Galeri
                    </a>
                    <a href="{{ route('nearby-places') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('nearby-places') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Sekitar
                    </a>
                    <a href="{{ route('location') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('location') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Lokasi
                    </a>

                    <p class="px-3 py-1 pt-6 text-xs font-bold text-primary-600/80 uppercase tracking-wider">Akun Saya</p>
                    <a href="{{ route('booking.my') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('booking.my') || request()->routeIs('booking.verify*') || request()->routeIs('booking.guest.detail') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Booking Saya
                    </a>
                    
                    <p class="px-3 py-1 pt-6 text-xs font-bold text-primary-600/80 uppercase tracking-wider">Bantuan & Info</p>
                    <a href="{{ route('faq') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('faq') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        FAQ
                    </a>
                    <a href="{{ route('contact.create') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('contact.*') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        Hubungi Kami
                    </a>
                    <a href="{{ route('about') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('about') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tentang
                    </a>
                    <a href="{{ route('policy') }}" @click="open = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('policy') ? 'text-primary-700 bg-primary-100/50' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Kebijakan
                    </a>
                </nav>

                {{-- Drawer Footer (Account) --}}
                <div class="border-t border-gray-100 px-4 py-4 space-y-2">
                    @auth
                        {{-- User Identity --}}
                        <div class="flex items-center gap-3 px-3 py-2 mb-2">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-9 h-9 rounded-full object-cover">
                            @else
                                <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-sm font-bold text-primary-700">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('member.dashboard') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Dashboard</a>
                        <a href="{{ route('member.bookings.index') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Booking Saya</a>
                        <a href="{{ route('member.points.index') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Poin Saya</a>
                        <a href="{{ route('member.profile.edit') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Profil</a>
                        <button type="button" @click="open = false; $dispatch('open-confirm', { id: 'member-logout' })"
                                class="flex items-center w-full px-3 py-2.5 rounded-lg text-sm text-red-600 hover:bg-red-50">
                            Keluar
                        </button>
                    @else
                        <p class="text-xs text-gray-500 px-3 mb-1">Simpan booking dan kumpulkan poin.</p>
                        <a href="{{ route('login') }}" @click="open = false" class="block w-full px-4 py-2.5 text-center text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" @click="open = false" class="block w-full px-4 py-2.5 text-center text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Toast Component --}}
    <x-toast />

    {{-- Content --}}
    <main class="flex-1">
        {{-- Flash Alerts (for warnings and errors that need to stay visible) --}}
        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <x-alert type="error" :message="session('error')" />
            </div>
        @endif
        @if (session('warning'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <x-alert type="warning" :message="session('warning')" />
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 border-t border-gray-800 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 text-sm">
                {{-- Property Branding --}}
                <div class="lg:col-span-1">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-white flex items-center gap-2 mb-4">
                        <div class="bg-white p-1 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-8 w-auto object-contain">
                        </div>
                        <span>{{ \App\Models\Setting::get('general', 'property_name', 'Penginapan Kelapa Sawit') }}</span>
                    </a>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        Menghadirkan kenyamanan beristirahat layaknya di rumah sendiri, berlokasi strategis di Kota Bangun II.
                    </p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary-600 hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary-600 hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary-600 hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Tautan Cepat</h3>
                    <nav class="space-y-3">
                        <a href="{{ route('rooms.index') }}" class="block text-gray-400 hover:text-white hover:translate-x-1 transition-all">Kamar Tersedia</a>
                        <a href="{{ route('gallery') }}" class="block text-gray-400 hover:text-white hover:translate-x-1 transition-all">Galeri Penginapan</a>
                        <a href="{{ route('faq') }}" class="block text-gray-400 hover:text-white hover:translate-x-1 transition-all">Tanya Jawab (FAQ)</a>
                        <a href="{{ route('nearby-places') }}" class="block text-gray-400 hover:text-white hover:translate-x-1 transition-all">Destinasi Sekitar</a>
                        <a href="{{ route('policy') }}" class="block text-gray-400 hover:text-white hover:translate-x-1 transition-all">Kebijakan & Syarat</a>
                    </nav>
                </div>

                {{-- Contact Info --}}
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Hubungi Kami</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-gray-400 leading-relaxed">{{ \App\Models\Setting::get('contact', 'address', 'Gunung Kelambu, SP 2, Kota Bangun Darat, Kalimantan Timur.') }}</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-primary-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:{{ \App\Models\Setting::get('contact', 'phone', '') }}" class="text-gray-400 hover:text-white transition-colors">{{ \App\Models\Setting::get('contact', 'phone', '') ?: \App\Models\Setting::get('contact', 'whatsapp', '') }}</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-primary-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:{{ \App\Models\Setting::get('contact', 'email', 'penginapankelapasawit@gmail.com') }}" class="text-gray-400 hover:text-white transition-colors">{{ \App\Models\Setting::get('contact', 'email', 'penginapankelapasawit@gmail.com') }}</a>
                        </li>
                    </ul>
                </div>
                
                {{-- Payments & Support --}}
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Pembayaran & Dukungan</h3>
                    <p class="text-gray-400 mb-4">Mendukung berbagai metode pembayaran untuk kenyamanan Anda.</p>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <div class="bg-gray-800 px-3 py-1.5 rounded border border-gray-700 text-xs font-semibold text-gray-300">Bank Transfer</div>
                        <div class="bg-gray-800 px-3 py-1.5 rounded border border-gray-700 text-xs font-semibold text-gray-300">QRIS</div>
                        <div class="bg-gray-800 px-3 py-1.5 rounded border border-gray-700 text-xs font-semibold text-gray-300">E-Wallet</div>
                    </div>
                    <div class="p-4 bg-gray-800 rounded-xl border border-gray-700 flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h4 class="text-white font-medium text-sm">Resepsionis 24 Jam</h4>
                            <p class="text-xs text-gray-400 mt-1">Kami siap melayani kebutuhan Anda kapanpun.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ \App\Models\Setting::get('general', 'property_name', 'Penginapan Kelapa Sawit') }}. Seluruh hak cipta dilindungi.
                </div>
                <div class="flex items-center gap-4 text-sm font-medium text-gray-500">
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg> Pembayaran Aman</span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Konfirmasi Instan</span>
                </div>
            </div>
        </div>
    </footer>
    {{-- Member Logout Confirmation Modal --}}
    @auth
    <x-confirm-modal
        id="member-logout"
        title="Keluar dari akun?"
        message="Anda perlu masuk kembali untuk melihat booking dan poin member."
        confirm-text="Ya, Keluar"
        cancel-text="Batal"
        variant="danger"
        form-action="{{ route('logout') }}"
        method="POST"
    />
    @endauth

    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    for(let registration of registrations) {
                        registration.unregister();
                    }
                });
            });
        }
    </script>
</body>
</html>
