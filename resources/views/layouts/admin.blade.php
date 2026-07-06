<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Penginapan Kelapa Sawit')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-primary-800 text-white transform transition-transform duration-200 ease-in-out md:relative md:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-4 border-b border-primary-700">
                <h1 class="text-lg font-bold">Admin Panel</h1>
                <p class="text-xs text-primary-200">Penginapan Kelapa Sawit</p>
            </div>
            <nav class="p-4 space-y-1 text-sm overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-primary-700">Dashboard</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Reservasi</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Kalender</a>
                <a href="{{ route('admin.room-types.index') }}" class="block px-3 py-2 rounded hover:bg-primary-700">Kamar</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Tamu</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Pembayaran</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Promo</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Loyalty</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Room Block</a>
                <a href="{{ route('admin.galleries.index') }}" class="block px-3 py-2 rounded hover:bg-primary-700">Galeri</a>
                <a href="{{ route('admin.policies.index') }}" class="block px-3 py-2 rounded hover:bg-primary-700">Kebijakan</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Pengeluaran</a>
                <a href="#" class="block px-3 py-2 rounded text-primary-300 cursor-not-allowed">Laporan</a>
                <a href="{{ route('admin.settings.edit', 'general') }}" class="block px-3 py-2 rounded hover:bg-primary-700">Pengaturan</a>
            </nav>
        </aside>

        {{-- Overlay mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-20 bg-black/50 md:hidden" x-transition.opacity></div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Topbar --}}
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center space-x-4 ml-auto">
                    <span class="text-sm text-gray-600">{{ auth('admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Keluar</button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif
                @if (session('error'))
                    <x-alert type="error" :message="session('error')" />
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
