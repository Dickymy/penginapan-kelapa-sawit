<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Penginapan Kelapa Sawit')</title>
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/png">
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans overflow-x-hidden" x-data="{ sidebarOpen: false, moreOpen: false }">
    {{-- Toast --}}
    <x-toast />

    {{-- Desktop: fixed sidebar layout --}}
    {{-- Mobile: normal document flow (body scrolls naturally) --}}
    <div class="md:flex md:h-screen md:overflow-hidden">
        {{-- Desktop Sidebar --}}
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="w-60 bg-white border-r border-gray-100 flex flex-col h-full shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
                {{-- Brand --}}
                <div class="px-4 py-4 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-6 w-auto object-contain">
                        <h1 class="text-base font-bold text-primary-700">Kelapa Sawit</h1>
                    </div>
                    <p class="text-xs text-gray-400">Panel Admin</p>
                </div>
                {{-- Nav --}}
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
                    @include('layouts.partials.admin-nav')
                </nav>
            </div>
        </aside>

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/50 md:hidden" x-transition.opacity x-cloak></div>

        {{-- Mobile Sidebar Drawer --}}
        <aside x-show="sidebarOpen" x-cloak
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col md:hidden shadow-2xl"
               x-effect="document.body.style.overflow = sidebarOpen ? 'hidden' : ''">
            <div class="px-4 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-6 w-auto object-contain">
                        <h1 class="text-base font-bold text-primary-700">Kelapa Sawit</h1>
                    </div>
                    <p class="text-xs text-gray-400">Panel Admin</p>
                </div>
                <button @click="sidebarOpen = false" class="p-1 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
                @include('layouts.partials.admin-nav')
            </nav>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col md:overflow-hidden">
            {{-- Topbar --}}
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-[0_4px_24px_rgba(0,0,0,0.02)] px-4 py-3 flex items-center justify-between flex-shrink-0 sticky top-0 z-20 md:static">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-1.5 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="text-sm font-semibold text-gray-700 hidden sm:block">@yield('page-title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Account dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition">
                            <span class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}
                            </span>
                            <span class="text-sm text-gray-700 hidden sm:inline">{{ auth('admin')->user()->name }}</span>
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] py-1.5 z-50">
                            <a href="/" target="_blank" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Lihat Website
                            </a>
                            <a href="{{ route('admin.booking-changes.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Perubahan Booking
                            </a>
                            <a href="{{ route('admin.settings.edit', 'general') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Pengaturan
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <button type="button" @click="$dispatch('open-confirm', { id: 'admin-logout' })"
                                    class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            {{-- Mobile: normal flow (body scrolls), Desktop: overflow-y-auto on main --}}
            <main class="flex-1 md:overflow-y-auto p-4 md:p-6 pb-20 md:pb-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    <nav class="fixed bottom-0 inset-x-0 z-30 bg-white/90 backdrop-blur-md border-t border-gray-100 shadow-[0_-4px_24px_rgba(0,0,0,0.04)] md:hidden safe-area-bottom">
        <div class="flex items-center justify-around h-14">
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('admin.dashboard') ? 'text-primary-600' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] mt-0.5">Beranda</span>
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('admin.bookings.*') ? 'text-primary-600' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-[10px] mt-0.5">Reservasi</span>
            </a>
            {{-- Quick Add --}}
            <div x-data="{ open: false }" class="relative flex flex-col items-center justify-center w-full h-full">
                <button @click="open = !open" class="flex flex-col items-center justify-center text-primary-600">
                    <span class="w-9 h-9 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-md -mt-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    <span class="text-[10px] mt-0.5">Tambah</span>
                </button>
                {{-- Quick Add Menu --}}
                <div x-show="open" x-cloak @click.away="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                     class="absolute bottom-16 bg-white border border-gray-100 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] py-2 w-48 transform origin-bottom">
                    <a href="{{ route('admin.bookings.create') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium">
                        <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Booking Manual
                    </a>
                    <a href="{{ route('admin.room-blocks.create') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition font-medium">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Blokir Kamar
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.room-blocks.index') }}" class="flex flex-col items-center justify-center w-full h-full {{ request()->routeIs('admin.room-blocks.*') ? 'text-primary-600' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-[10px] mt-0.5">Blokir</span>
            </a>
            <button @click="sidebarOpen = true" class="flex flex-col items-center justify-center w-full h-full text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span class="text-[10px] mt-0.5">Menu</span>
            </button>
        </div>
    </nav>

    {{-- Admin Logout Confirmation Modal --}}
    <x-confirm-modal
        id="admin-logout"
        title="Keluar dari Admin?"
        message="Sesi admin akan diakhiri dan Anda perlu masuk kembali untuk mengelola penginapan."
        confirm-text="Ya, Keluar"
        cancel-text="Batal"
        variant="danger"
        form-action="{{ route('admin.logout') }}"
        method="POST"
    />

    @stack('scripts')
</body>
</html>
