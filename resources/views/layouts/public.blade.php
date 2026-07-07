<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Penginapan Kelapa Sawit')</title>
    @yield('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-white text-gray-800 font-sans">
    {{-- Header --}}
    <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-40" x-data="{ open: false }" @keydown.escape.window="open = false">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="text-xl font-bold text-primary-700 flex items-center gap-2">
                    <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="hidden sm:inline">Penginapan Kelapa Sawit</span>
                    <span class="sm:hidden">Kelapa Sawit</span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden lg:flex items-center space-x-1 text-sm">
                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('home') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">Beranda</a>
                    <a href="{{ route('rooms.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('rooms.*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">Kamar</a>
                    <a href="{{ route('about') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('about') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">Tentang</a>
                    <a href="{{ route('location') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('location') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">Lokasi</a>
                    <a href="{{ route('policy') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('policy') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">Kebijakan</a>
                    <a href="{{ route('booking.verify.form') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('booking.verify*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">Cek Booking</a>
                </nav>

                {{-- Desktop Account --}}
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <a href="{{ route('member.dashboard') }}" class="px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 rounded-lg hover:bg-primary-100 transition">
                            Dashboard
                        </a>
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
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <span class="text-lg font-bold text-primary-700">Menu</span>
                    <button @click="open = false" class="p-2 rounded-md text-gray-500 hover:bg-gray-100" aria-label="Tutup menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Drawer Navigation --}}
                <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                    <p class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Utama</p>
                    <a href="{{ route('home') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('home') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Beranda</a>
                    <a href="{{ route('rooms.index') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('rooms.*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Kamar</a>
                    <a href="{{ route('home') }}#cari-kamar" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('availability.*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Cek Ketersediaan</a>

                    <p class="px-3 py-1 pt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Informasi</p>
                    <a href="{{ route('about') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('about') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Tentang</a>
                    <a href="{{ route('location') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('location') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Lokasi</a>
                    <a href="{{ route('policy') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('policy') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Kebijakan</a>
                    <a href="{{ route('booking.verify.form') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('booking.verify*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Cek Booking</a>
                </nav>

                {{-- Drawer Footer (Account) --}}
                <div class="border-t border-gray-100 px-4 py-4 space-y-2">
                    @auth
                        <a href="{{ route('member.dashboard') }}" @click="open = false" class="block w-full px-4 py-2.5 text-center text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                            Dashboard Saya
                        </a>
                    @else
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
        {{-- Flash Alerts --}}
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <x-alert type="success" :message="session('success')" />
            </div>
        @endif
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
        @if (session('info'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <x-alert type="info" :message="session('info')" />
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-3">Penginapan Kelapa Sawit</h3>
                    <p class="text-sm text-gray-400">Kota Bangun, Kalimantan Timur</p>
                </div>
                <div>
                    <h4 class="text-white font-medium text-sm mb-3">Navigasi</h4>
                    <nav class="space-y-2 text-sm">
                        <a href="{{ route('rooms.index') }}" class="block text-gray-400 hover:text-white transition">Kamar</a>
                        <a href="{{ route('about') }}" class="block text-gray-400 hover:text-white transition">Tentang</a>
                        <a href="{{ route('location') }}" class="block text-gray-400 hover:text-white transition">Lokasi</a>
                        <a href="{{ route('policy') }}" class="block text-gray-400 hover:text-white transition">Kebijakan</a>
                        <a href="{{ route('booking.verify.form') }}" class="block text-gray-400 hover:text-white transition">Cek Booking</a>
                    </nav>
                </div>
                <div>
                    <h4 class="text-white font-medium text-sm mb-3">Akun</h4>
                    <nav class="space-y-2 text-sm">
                        @auth
                            <a href="{{ route('member.dashboard') }}" class="block text-gray-400 hover:text-white transition">Dashboard</a>
                            <a href="{{ route('member.bookings.index') }}" class="block text-gray-400 hover:text-white transition">Booking Saya</a>
                        @else
                            <a href="{{ route('login') }}" class="block text-gray-400 hover:text-white transition">Masuk</a>
                            <a href="{{ route('register') }}" class="block text-gray-400 hover:text-white transition">Daftar</a>
                        @endauth
                    </nav>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-700 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Penginapan Kelapa Sawit. Seluruh hak dilindungi.
            </div>
        </div>
    </footer>
</body>
</html>
