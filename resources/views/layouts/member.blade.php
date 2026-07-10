<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'Member - Penginapan Kelapa Sawit')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Safe area for bottom navigation on mobile */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50 font-sans" x-data>
    {{-- Toast --}}
    <x-toast />

    {{-- Header --}}
    <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                {{-- Left: Logo --}}
                <a href="{{ route('home') }}" class="text-lg font-bold text-primary-700 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="hidden sm:inline">Penginapan Kelapa Sawit</span>
                    <span class="sm:hidden text-base">Kelapa Sawit</span>
                </a>

                {{-- Right: Desktop nav + Account --}}
                <div class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('member.dashboard') }}"
                       class="px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('member.dashboard') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        Ringkasan
                    </a>
                    <a href="{{ route('member.bookings.index') }}"
                       class="px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('member.bookings.*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        Booking
                    </a>
                    <a href="{{ route('member.points.index') }}"
                       class="px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('member.points.*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        Poin
                    </a>
                    <a href="{{ route('member.profile.edit') }}"
                       class="px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('member.profile.*') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                        Profil
                    </a>
                </div>

                {{-- Right: Account dropdown (desktop) --}}
                <div class="hidden lg:block" x-data="{ accountOpen: false }">
                    <button @click="accountOpen = !accountOpen" @click.outside="accountOpen = false"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-sm font-bold text-primary-700">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="accountOpen" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-4 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Kembali ke Website
                        </a>
                        <a href="{{ route('member.claim.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Klaim Booking
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <button type="button" @click="$dispatch('open-confirm', { id: 'member-logout' })"
                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </div>
                </div>

                {{-- Mobile: Avatar (no hamburger needed, we use bottom nav) --}}
                <div class="lg:hidden flex items-center gap-2">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                            <span class="text-sm font-bold text-primary-700">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <div class="flex-1 pb-20 lg:pb-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6">
            {{-- Alerts --}}
            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif
            @if (session('warning'))
                <x-alert type="warning" :message="session('warning')" />
            @endif
            @if (session('info'))
                <x-alert type="info" :message="session('info')" />
            @endif
            @if (session('status'))
                <x-alert type="success" :message="session('status')" />
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Desktop Footer --}}
    <footer class="hidden lg:block bg-white border-t border-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Penginapan Kelapa Sawit
        </div>
    </footer>

    {{-- Mobile Bottom Navigation --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 z-40 pb-safe"
         aria-label="Navigasi utama mobile">
        <div class="grid grid-cols-5 h-16 max-w-md mx-auto">
            {{-- Beranda --}}
            <a href="{{ route('member.dashboard') }}"
               class="flex flex-col items-center justify-center gap-0.5 text-xs transition
                      {{ request()->routeIs('member.dashboard') ? 'text-primary-600 font-medium' : 'text-gray-500' }}"
               aria-label="Ringkasan"
               @if(request()->routeIs('member.dashboard')) aria-current="page" @endif>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('member.dashboard') ? '2.5' : '2' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Beranda</span>
                @if(request()->routeIs('member.dashboard'))
                <span class="absolute top-1 w-1 h-1 rounded-full bg-primary-600"></span>
                @endif
            </a>

            {{-- Booking --}}
            <a href="{{ route('member.bookings.index') }}"
               class="flex flex-col items-center justify-center gap-0.5 text-xs transition
                      {{ request()->routeIs('member.bookings.*') ? 'text-primary-600 font-medium' : 'text-gray-500' }}"
               aria-label="Booking Saya"
               @if(request()->routeIs('member.bookings.*')) aria-current="page" @endif>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('member.bookings.*') ? '2.5' : '2' }}" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Booking</span>
            </a>

            {{-- Pesan (CTA - search rooms) --}}
            <a href="{{ route('home') }}#cari-kamar"
               class="flex flex-col items-center justify-center gap-0.5 text-xs text-gray-500"
               aria-label="Pesan Kamar">
                <div class="w-10 h-10 -mt-3 rounded-full bg-primary-600 flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <span class="mt-0.5 text-primary-600 font-medium">Pesan</span>
            </a>

            {{-- Poin --}}
            <a href="{{ route('member.points.index') }}"
               class="flex flex-col items-center justify-center gap-0.5 text-xs transition
                      {{ request()->routeIs('member.points.*') ? 'text-primary-600 font-medium' : 'text-gray-500' }}"
               aria-label="Poin Saya"
               @if(request()->routeIs('member.points.*')) aria-current="page" @endif>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('member.points.*') ? '2.5' : '2' }}" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Poin</span>
            </a>

            {{-- Akun --}}
            <a href="{{ route('member.profile.edit') }}"
               class="flex flex-col items-center justify-center gap-0.5 text-xs transition
                      {{ request()->routeIs('member.profile.*') || request()->routeIs('member.claim.*') ? 'text-primary-600 font-medium' : 'text-gray-500' }}"
               aria-label="Akun Saya"
               @if(request()->routeIs('member.profile.*')) aria-current="page" @endif>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('member.profile.*') ? '2.5' : '2' }}" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Akun</span>
            </a>
        </div>
    </nav>

    {{-- Member Logout Confirmation Modal --}}
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
</body>
</html>
