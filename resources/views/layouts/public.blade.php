<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Penginapan Kelapa Sawit — Penginapan di Kota Bangun II')</title>
    @yield('meta')
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
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
                    <a href="{{ route('location') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('location') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">Lokasi</a>
                    <a href="{{ route('booking.my') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('booking.my') || request()->routeIs('booking.verify*') || request()->routeIs('booking.guest.detail') || request()->routeIs('member.bookings.*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">Booking Saya</a>
                    <a href="{{ route('home') }}#cari-kamar" class="ml-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">Cari Kamar</a>
                </nav>

                {{-- Desktop Account --}}
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <div x-data="{ accountOpen: false }" class="relative">
                            <button @click="accountOpen = !accountOpen" @click.outside="accountOpen = false"
                                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-sm font-bold text-primary-700">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="hidden xl:inline max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="accountOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
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
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <span class="text-lg font-bold text-primary-700">Menu</span>
                    <button @click="open = false" class="p-2 rounded-md text-gray-500 hover:bg-gray-100" aria-label="Tutup menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Drawer Navigation --}}
                <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                    <p class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pesan</p>
                    <a href="{{ route('home') }}#cari-kamar" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-primary-700 bg-primary-50 font-medium">Cari Kamar</a>
                    <a href="{{ route('rooms.index') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('rooms.*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Kamar</a>

                    <p class="px-3 py-1 pt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Booking</p>
                    <a href="{{ route('booking.my') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('booking.my') || request()->routeIs('booking.verify*') || request()->routeIs('booking.guest.detail') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Booking Saya</a>

                    <p class="px-3 py-1 pt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Informasi</p>
                    <a href="{{ route('home') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('home') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Beranda</a>
                    <a href="{{ route('location') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('location') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Lokasi</a>
                    <a href="{{ route('about') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('about') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Tentang</a>
                    <a href="{{ route('policy') }}" @click="open = false" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('policy') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">Kebijakan</a>
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
    <footer class="bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
                {{-- Property --}}
                <div>
                    <p class="font-semibold text-gray-800">Penginapan Kelapa Sawit</p>
                    <p class="text-gray-500 mt-1">Kota Bangun II, Kutai Kartanegara, Kalimantan Timur</p>
                </div>
                {{-- Quick Links --}}
                <div>
                    <p class="font-medium text-gray-700 mb-2">Tautan Cepat</p>
                    <nav class="space-y-1 text-gray-500">
                        <a href="{{ route('rooms.index') }}" class="block hover:text-primary-600 transition">Kamar</a>
                        <a href="{{ route('location') }}" class="block hover:text-primary-600 transition">Lokasi</a>
                        <a href="{{ route('policy') }}" class="block hover:text-primary-600 transition">Kebijakan</a>
                        <a href="{{ route('booking.my') }}" class="block hover:text-primary-600 transition">Booking Saya</a>
                    </nav>
                </div>
                {{-- Contact --}}
                <div>
                    <p class="font-medium text-gray-700 mb-2">Hubungi</p>
                    <nav class="space-y-1 text-gray-500">
                        @php $footerWa = \App\Support\WhatsApp::url(\App\Models\Setting::get('contact', 'whatsapp', '')); @endphp
                        @if($footerWa)
                        <a href="{{ $footerWa }}" target="_blank" rel="noopener" class="block hover:text-green-600 transition">WhatsApp</a>
                        @endif
                        @php $footerEmail = \App\Models\Setting::get('contact', 'email', ''); @endphp
                        @if($footerEmail)
                        <a href="mailto:{{ $footerEmail }}" class="block hover:text-primary-600 transition">{{ $footerEmail }}</a>
                        @endif
                        @php $footerMapLink = \App\Models\Setting::get('contact', 'map_link', '') ?: \App\Models\Setting::get('contact', 'map_url', ''); @endphp
                        @if($footerMapLink)
                        <a href="{{ $footerMapLink }}" target="_blank" rel="noopener" class="block hover:text-primary-600 transition">Google Maps</a>
                        @endif
                        @auth
                        <a href="{{ route('member.dashboard') }}" class="block hover:text-primary-600 transition">Akun Saya</a>
                        @else
                        <a href="{{ route('login') }}" class="block hover:text-primary-600 transition">Masuk / Daftar</a>
                        @endauth
                    </nav>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-200 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Penginapan Kelapa Sawit
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
</body>
</html>
