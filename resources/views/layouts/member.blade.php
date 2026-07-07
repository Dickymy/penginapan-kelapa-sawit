<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member - Penginapan Kelapa Sawit')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans">
    {{-- Toast --}}
    <x-toast />

    <header class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="text-lg font-bold text-primary-700">
                    Penginapan Kelapa Sawit
                </a>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600 hidden sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Member Nav --}}
        <nav class="mb-6 flex flex-wrap gap-2 sm:gap-4 text-sm border-b border-gray-200 pb-3">
            <a href="{{ route('member.dashboard') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('member.dashboard') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                Dashboard
            </a>
            <a href="{{ route('member.bookings.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('member.bookings.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                Booking Saya
            </a>
            <a href="{{ route('member.points.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('member.points.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
                Poin Saya
            </a>
            <a href="{{ route('member.profile.edit') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('member.profile.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50' }}">
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
</body>
</html>
