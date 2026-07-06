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
    <header class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="text-lg font-bold text-primary-700">
                    Penginapan Kelapa Sawit
                </a>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
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
        <nav class="mb-6 flex flex-wrap gap-4 text-sm">
            <a href="{{ route('member.dashboard') }}" class="text-primary-600 font-medium hover:text-primary-800">Dashboard</a>
            <a href="#" class="text-gray-500 hover:text-primary-600">Booking Saya</a>
            <a href="#" class="text-gray-500 hover:text-primary-600">Poin Saya</a>
            <a href="#" class="text-gray-500 hover:text-primary-600">Profil</a>
        </nav>

        {{-- Alerts --}}
        @if (session('status'))
            <x-alert type="success" :message="session('status')" />
        @endif

        @yield('content')
    </div>
</body>
</html>
