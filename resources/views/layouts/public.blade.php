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
<body class="min-h-screen bg-white text-gray-800 font-sans">
    {{-- Header --}}
    <header class="bg-white shadow-sm border-b border-gray-100" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="text-xl font-bold text-primary-700">
                    Penginapan Kelapa Sawit
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center space-x-6 text-sm">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-primary-600 font-medium' : 'text-gray-600 hover:text-primary-600' }}">Beranda</a>
                    <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'text-primary-600 font-medium' : 'text-gray-600 hover:text-primary-600' }}">Kamar</a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-primary-600 font-medium' : 'text-gray-600 hover:text-primary-600' }}">Tentang</a>
                    <a href="{{ route('location') }}" class="{{ request()->routeIs('location') ? 'text-primary-600 font-medium' : 'text-gray-600 hover:text-primary-600' }}">Lokasi</a>
                    <a href="{{ route('policy') }}" class="{{ request()->routeIs('policy') ? 'text-primary-600 font-medium' : 'text-gray-600 hover:text-primary-600' }}">Kebijakan</a>
                    <a href="{{ route('booking.verify.form') }}" class="{{ request()->routeIs('booking.verify*') ? 'text-primary-600 font-medium' : 'text-gray-600 hover:text-primary-600' }}">Cek Booking</a>
                    @auth
                        <a href="{{ route('member.dashboard') }}" class="text-primary-600 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-1.5 bg-primary-600 text-white rounded-md font-medium hover:bg-primary-700 transition">Masuk</a>
                    @endauth
                </nav>

                {{-- Mobile Hamburger --}}
                <button @click="open = !open" class="md:hidden p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open" x-transition class="md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('home') }}" class="block text-gray-600 hover:text-primary-600">Beranda</a>
                <a href="{{ route('rooms.index') }}" class="block text-gray-600 hover:text-primary-600">Kamar</a>
                <a href="{{ route('about') }}" class="block text-gray-600 hover:text-primary-600">Tentang</a>
                <a href="{{ route('location') }}" class="block text-gray-600 hover:text-primary-600">Lokasi</a>
                <a href="{{ route('policy') }}" class="block text-gray-600 hover:text-primary-600">Kebijakan</a>
                <a href="{{ route('booking.verify.form') }}" class="block text-gray-600 hover:text-primary-600">Cek Booking</a>
                @auth
                    <a href="{{ route('member.dashboard') }}" class="block text-primary-600 font-medium">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block text-primary-600 font-medium">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Toast Component --}}
    <x-toast />

    {{-- Content --}}
    <main>
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
    <footer class="bg-gray-800 text-gray-300 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-3">Penginapan Kelapa Sawit</h3>
                    <p class="text-sm text-gray-400">Kota Bangun, Kalimantan Timur</p>
                </div>
                <div>
                    <h4 class="text-white font-medium text-sm mb-3">Navigasi</h4>
                    <nav class="space-y-2 text-sm">
                        <a href="{{ route('rooms.index') }}" class="block text-gray-400 hover:text-white transition">Kamar</a>
                        <a href="{{ route('about') }}" class="block text-gray-400 hover:text-white transition">Tentang</a>
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
