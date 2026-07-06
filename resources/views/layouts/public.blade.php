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
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary-600">Beranda</a>
                    <a href="{{ route('rooms.index') }}" class="text-gray-600 hover:text-primary-600">Kamar</a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-primary-600">Tentang</a>
                    <a href="{{ route('location') }}" class="text-gray-600 hover:text-primary-600">Lokasi</a>
                    <a href="{{ route('policy') }}" class="text-gray-600 hover:text-primary-600">Kebijakan</a>
                    <a href="#" class="text-gray-600 hover:text-primary-600">Cek Booking</a>
                    @auth
                        <a href="{{ route('member.dashboard') }}" class="text-primary-600 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-primary-600 font-medium">Masuk</a>
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
                <a href="#" class="block text-gray-600 hover:text-primary-600">Cek Booking</a>
                @auth
                    <a href="{{ route('member.dashboard') }}" class="block text-primary-600 font-medium">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block text-primary-600 font-medium">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-50 border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Penginapan Kelapa Sawit. Kota Bangun, Kalimantan Timur.
            </div>
        </div>
    </footer>
</body>
</html>
