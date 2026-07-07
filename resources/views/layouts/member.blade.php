<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member - Penginapan Kelapa Sawit')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-gray-50 font-sans">
    {{-- Toast --}}
    <x-toast />

    <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="text-lg font-bold text-primary-700 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="hidden sm:inline">Penginapan Kelapa Sawit</span>
                    <span class="sm:hidden">Member</span>
                </a>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600 hidden sm:inline">{{ auth()->user()->name }}</span>
                    <button type="button" @click="$dispatch('open-confirm', { id: 'member-logout' })"
                            class="text-sm text-red-600 hover:text-red-800 font-medium">Keluar</button>
                </div>
            </div>
        </div>
    </header>

    <div class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{-- Member Nav --}}
            <nav class="mb-6 flex flex-wrap gap-1 sm:gap-2 text-sm border-b border-gray-200 pb-3 overflow-x-auto">
                <a href="{{ route('member.dashboard') }}"
                   class="flex-shrink-0 px-3 py-1.5 rounded-md transition {{ request()->routeIs('member.dashboard') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('member.bookings.index') }}"
                   class="flex-shrink-0 px-3 py-1.5 rounded-md transition {{ request()->routeIs('member.bookings.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                    Booking Saya
                </a>
                <a href="{{ route('member.points.index') }}"
                   class="flex-shrink-0 px-3 py-1.5 rounded-md transition {{ request()->routeIs('member.points.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                    Poin Saya
                </a>
                <a href="{{ route('member.claim.index') }}"
                   class="flex-shrink-0 px-3 py-1.5 rounded-md transition {{ request()->routeIs('member.claim.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                    Klaim Booking
                </a>
                <a href="{{ route('member.profile.edit') }}"
                   class="flex-shrink-0 px-3 py-1.5 rounded-md transition {{ request()->routeIs('member.profile.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                    Profil
                </a>
            </nav>

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

    {{-- Simple footer --}}
    <footer class="bg-white border-t border-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Penginapan Kelapa Sawit
        </div>
    </footer>

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
