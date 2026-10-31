@extends('layouts.member')

@section('title', 'Dashboard Member')

@section('content')
<div class="space-y-8 pb-8">

    {{-- Onboarding: WhatsApp Completion (for new Google users) --}}
    @if($needsWhatsapp)
    <div class="bg-white rounded-3xl border border-primary-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden relative" id="onboarding-whatsapp">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary-500 to-emerald-500"></div>
        <div class="p-6 sm:p-8">
            <div class="flex items-start gap-5">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center shadow-inner">
                    <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-black text-gray-900">Selamat datang di Penginapan Kelapa Sawit 👋</h2>
                    <p class="text-gray-500 mt-1">Lengkapi nomor WhatsApp agar proses booking dan komunikasi lebih mudah.</p>

                    <form method="POST" action="{{ route('member.profile.update-whatsapp') }}" class="mt-5 flex flex-col sm:flex-row gap-3"
                          x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf
                        @method('PUT')
                        <div class="flex-1">
                            <input type="tel" name="whatsapp" inputmode="tel" placeholder="08xxxxxxxxxx"
                                   autocomplete="tel"
                                   class="w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-3 @error('whatsapp') border-red-300 @enderror"
                                   value="{{ old('whatsapp') }}">
                            <p class="text-xs text-gray-400 mt-1.5">Format: 08xx, 628xx, atau +628xx</p>
                            @error('whatsapp')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex-shrink-0">
                            <button type="submit" :disabled="submitting"
                                    class="w-full sm:w-auto px-6 py-3 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-500 hover:-translate-y-0.5 shadow-lg shadow-primary-600/30 transition-all active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center">
                                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-show="!submitting">Simpan Nomor</span>
                                <span x-show="submitting" x-cloak>Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-10 w-32 h-32 bg-primary-500 opacity-20 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <p class="text-gray-400 text-sm font-medium mb-1">Selamat datang kembali,</p>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">{{ Str::before(auth()->user()->name, ' ') }} 👋</h1>
                <p class="text-gray-300 text-sm sm:text-base mt-2">Pantau semua aktivitas dan perjalanan Anda dari sini.</p>
            </div>
            
            <a href="{{ route('home') }}#cari-kamar" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold shadow-lg shadow-primary-600/30 hover:bg-primary-500 hover:scale-105 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari Kamar
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        {{-- Points Balance --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] p-6 relative overflow-hidden group hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-primary-50 rounded-full opacity-60 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Saldo Poin</span>
                </div>
                <p class="text-3xl font-black text-gray-900">{{ number_format($pointBalance) }}</p>
                @if($pointBalance > 0)
                    <p class="text-sm font-medium text-primary-600 mt-1">≈ Rp{{ number_format($pointValue, 0, ',', '.') }}</p>
                @else
                    <p class="text-sm text-gray-400 mt-1">Kumpulkan setelah menginap</p>
                @endif
            </div>
        </div>

        {{-- Active Bookings --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] p-6 relative overflow-hidden group hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-blue-50 rounded-full opacity-60 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Booking Aktif</span>
                </div>
                <p class="text-3xl font-black text-gray-900">{{ $activeBookings }}</p>
                <p class="text-sm text-gray-400 mt-1">Menunggu / Dikonfirmasi</p>
            </div>
        </div>

        {{-- Total Bookings --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] p-6 relative overflow-hidden group hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-emerald-50 rounded-full opacity-60 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Booking</span>
                </div>
                <p class="text-3xl font-black text-gray-900">{{ $totalBookings }}</p>
                <p class="text-sm text-gray-400 mt-1">Sepanjang waktu</p>
            </div>
        </div>
    </div>

    {{-- PRIORITY 1: Pending Payment (most urgent) --}}
    @if($pendingPaymentBookings->isNotEmpty())
    <div class="bg-white rounded-3xl border border-amber-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden">
        <div class="px-6 sm:px-8 py-5 border-b border-amber-50 bg-amber-50/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="font-bold text-amber-900">Menunggu Pembayaran</h2>
                <p class="text-xs text-amber-600">Segera selesaikan pembayaran sebelum batas waktu</p>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($pendingPaymentBookings as $booking)
            <div class="px-6 sm:px-8 py-5 hover:bg-gray-50/50 transition-colors">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold font-mono tracking-wider">{{ $booking->booking_code }}</span>
                            <x-status-badge :status="$booking->status" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $booking->room_type_name_snapshot }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $booking->check_in->translatedFormat('d M Y') }} → {{ $booking->check_out->translatedFormat('d M Y') }} ({{ $booking->nights }} malam)
                        </p>
                        @if($booking->payment_expires_at)
                        <p class="text-xs font-bold text-red-600 mt-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            Bayar sebelum {{ $booking->payment_expires_at->translatedFormat('d M Y, H:i') }} WITA
                        </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 shrink-0" x-data>
                        <p class="text-xl font-black text-gray-900 mr-2">{{ $booking->formatted_total }}</p>
                        <button type="button" @click="$dispatch('open-confirm', { id: 'cancel-dashboard-booking-{{ $booking->id }}' })"
                                class="inline-flex items-center justify-center px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100 transition-all active:scale-95">
                            Batalkan
                        </button>
                        
                        <x-confirm-modal 
                            id="cancel-dashboard-booking-{{ $booking->id }}"
                            title="Batalkan Pesanan" 
                            message="Apakah Anda yakin ingin membatalkan pesanan kamar {{ $booking->room_type_name_snapshot }}? Pesanan yang dibatalkan tidak dapat dikembalikan."
                            confirm-text="Ya, Batalkan"
                            cancel-text="Kembali"
                            variant="danger"
                            form-action="{{ route('member.bookings.cancel', $booking) }}"
                            method="PATCH"
                        />
                        <a href="{{ route('member.bookings.show', $booking) }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-bold hover:bg-amber-600 hover:-translate-y-0.5 shadow-lg shadow-amber-500/30 transition-all active:scale-95">
                            Bayar Sekarang
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- PRIORITY 2: Currently Checked In --}}
    @if($checkedInBookings->isNotEmpty())
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-10 -mt-10 w-48 h-48 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-10 w-24 h-24 bg-cyan-400 opacity-20 rounded-full blur-2xl"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black">Selamat Menginap! 🏨</h2>
                    <p class="text-blue-200 text-sm">Semoga Anda menikmati waktu menginap bersama kami</p>
                </div>
            </div>

            @foreach($checkedInBookings as $booking)
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 {{ !$loop->last ? 'mb-4' : '' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold">{{ $booking->room_type_name_snapshot }}</h3>
                        <p class="text-blue-200 text-sm">{{ $booking->room_name_snapshot ?? '' }}</p>
                        <p class="text-blue-100 text-sm mt-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Check-out: {{ $booking->check_out->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <a href="{{ route('member.bookings.show', $booking) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-blue-700 rounded-xl font-bold text-sm hover:bg-blue-50 hover:-translate-y-0.5 transition-all active:scale-95">
                        Lihat Detail
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- PRIORITY 3: Upcoming Confirmed Bookings --}}
    @if($upcomingBookings->isNotEmpty())
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden">
        <div class="px-6 sm:px-8 py-5 border-b border-emerald-50 bg-emerald-50/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="font-bold text-emerald-900">Booking Akan Datang</h2>
                <p class="text-xs text-emerald-600">Perjalanan yang sudah dikonfirmasi dan menunggu Anda</p>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($upcomingBookings as $booking)
            <a href="{{ route('member.bookings.show', $booking) }}" class="block px-6 sm:px-8 py-5 hover:bg-gray-50/50 transition-colors group">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold font-mono tracking-wider">{{ $booking->booking_code }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary-600 transition-colors">{{ $booking->room_type_name_snapshot }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $booking->check_in->translatedFormat('d M Y') }} → {{ $booking->check_out->translatedFormat('d M Y') }} ({{ $booking->nights }} malam)
                        </p>
                        <p class="text-xs font-bold text-emerald-600 mt-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Check-in {{ $booking->check_in->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold group-hover:bg-primary-50 group-hover:text-primary-700 group-hover:border-primary-100 transition-all">
                            Lihat Detail
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- PRIORITY 4: Past Bookings ready for review --}}
    @if(isset($reviewableBookings) && $reviewableBookings->isNotEmpty())
    <div class="bg-white rounded-3xl border border-purple-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden">
        <div class="px-6 sm:px-8 py-5 border-b border-purple-50 bg-purple-50/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <div>
                <h2 class="font-bold text-purple-900">Bagikan Pengalaman Anda</h2>
                <p class="text-xs text-purple-600">Ulasan Anda sangat berharga untuk meningkatkan layanan kami</p>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($reviewableBookings as $booking)
            <div class="px-6 sm:px-8 py-5 hover:bg-gray-50/50 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900">{{ $booking->room_type_name_snapshot }}</h3>
                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Menginap pada {{ $booking->check_in->translatedFormat('d M') }} - {{ $booking->check_out->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('member.reviews.create', $booking) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 text-white rounded-xl text-sm font-bold hover:bg-purple-500 hover:-translate-y-0.5 shadow-lg shadow-purple-600/30 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            Tulis Ulasan
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- PRIORITY 5: No Active Bookings — Show Hero CTA --}}
    @if($pendingPaymentBookings->isEmpty() && $upcomingBookings->isEmpty() && $checkedInBookings->isEmpty() && (empty($reviewableBookings) || $reviewableBookings->isEmpty()))
    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] p-10 sm:p-14 text-center">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-50 flex items-center justify-center relative">
            <div class="absolute inset-0 border-4 border-white shadow-sm rounded-full"></div>
            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <h2 class="text-2xl font-black text-gray-900 mb-2">
            @if($totalBookings === 0)
                Mulai Perjalanan Anda
            @else
                Siap untuk Perjalanan Berikutnya?
            @endif
        </h2>
        <p class="text-gray-500 max-w-md mx-auto mb-8">
            @if($totalBookings === 0)
                Temukan kamar terbaik dan mulai booking pertama Anda. Semua riwayat perjalanan akan tersimpan di akun Anda.
            @else
                Tidak ada booking aktif saat ini. Rencanakan perjalanan berikutnya dan nikmati pengalaman menginap terbaik.
            @endif
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('home') }}#cari-kamar" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gray-900 text-white rounded-xl font-bold shadow-lg hover:bg-black hover:-translate-y-0.5 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari Kamar
            </a>
            <a href="{{ route('rooms.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-gray-700 border border-gray-200 rounded-xl font-bold hover:bg-gray-50 hover:text-gray-900 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Lihat Tipe Kamar
            </a>
        </div>
    </div>
    @endif

    {{-- Quick Tips for New Users --}}
    @if($showOnboarding && $totalBookings === 0)
    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] p-6 sm:p-8">
        <h3 class="text-lg font-bold text-gray-900 mb-5">Cara Menggunakan Akun Member</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-primary-50 text-primary-700 text-sm font-black flex items-center justify-center">1</div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">Cari & Pesan</p>
                    <p class="text-gray-500 text-sm mt-0.5">Cari kamar sesuai tanggal perjalanan dan lakukan pemesanan.</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-50 text-blue-700 text-sm font-black flex items-center justify-center">2</div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">Pantau & Kelola</p>
                    <p class="text-gray-500 text-sm mt-0.5">Pantau status booking dan pembayaran dari dashboard ini.</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-black flex items-center justify-center">3</div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">Kumpulkan Poin</p>
                    <p class="text-gray-500 text-sm mt-0.5">Dapatkan poin setiap menginap untuk potongan harga berikutnya.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
