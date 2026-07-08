@extends('layouts.member')

@section('title', 'Dashboard Member')

@section('content')
<div class="space-y-6">

    {{-- Onboarding: WhatsApp Completion (for new Google users) --}}
    @if($needsWhatsapp)
    <div class="bg-gradient-to-br from-primary-50 to-white border border-primary-200 rounded-xl p-5 sm:p-6" id="onboarding-whatsapp">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-semibold text-gray-900">Selamat datang di Penginapan Kelapa Sawit 👋</h2>
                <p class="text-sm text-gray-600 mt-1">Lengkapi nomor WhatsApp agar proses booking dan komunikasi dengan penginapan lebih mudah.</p>

                <form method="POST" action="{{ route('member.profile.update-whatsapp') }}" class="mt-4 flex flex-col sm:flex-row gap-3"
                      x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    @method('PUT')
                    <div class="flex-1">
                        <input type="tel" name="whatsapp" inputmode="tel" placeholder="08xxxxxxxxxx"
                               autocomplete="tel"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('whatsapp') border-red-300 @enderror"
                               value="{{ old('whatsapp') }}">
                        <p class="text-xs text-gray-500 mt-1">Format: 08xx, 628xx, atau +628xx</p>
                        @error('whatsapp')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit" :disabled="submitting"
                                class="w-full sm:w-auto px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center">
                            <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!submitting">Simpan</span>
                            <span x-show="submitting" x-cloak>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- PRIORITY 1: Pending Payment (most urgent) --}}
    @if($pendingPaymentBookings->isNotEmpty())
    <div class="space-y-3">
        <h2 class="text-sm font-semibold text-amber-700 uppercase tracking-wide flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Menunggu Pembayaran
        </h2>
        @foreach($pendingPaymentBookings as $booking)
        <div class="bg-white border-l-4 border-amber-400 rounded-lg shadow-sm p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-mono text-gray-500">{{ $booking->booking_code }}</span>
                        <x-status-badge :status="$booking->status" />
                    </div>
                    <p class="text-base font-semibold text-gray-800 mt-1">{{ $booking->room_type_name_snapshot }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->check_in->format('d M Y') }} → {{ $booking->check_out->format('d M Y') }} ({{ $booking->nights }} malam)</p>
                    @if($booking->payment_expires_at)
                    <p class="text-xs text-amber-600 mt-1 font-medium">
                        Batas bayar: {{ $booking->payment_expires_at->format('d M Y, H:i') }} WITA
                    </p>
                    @endif
                </div>
                <div class="flex flex-col items-stretch sm:items-end gap-2">
                    <span class="text-lg font-bold text-gray-800">{{ $booking->formatted_total }}</span>
                    <a href="{{ route('member.bookings.show', $booking) }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600 transition">
                        Bayar Sekarang
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- PRIORITY 2: Upcoming Confirmed Bookings --}}
    @if($upcomingBookings->isNotEmpty())
    <div class="space-y-3">
        <h2 class="text-sm font-semibold text-green-700 uppercase tracking-wide flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Booking Dikonfirmasi
        </h2>
        @foreach($upcomingBookings as $booking)
        <div class="bg-white border-l-4 border-green-400 rounded-lg shadow-sm p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-mono text-gray-500">{{ $booking->booking_code }}</span>
                        <x-status-badge :status="$booking->status" />
                    </div>
                    <p class="text-base font-semibold text-gray-800 mt-1">{{ $booking->room_type_name_snapshot }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->check_in->format('d M Y') }} → {{ $booking->check_out->format('d M Y') }} ({{ $booking->nights }} malam)</p>
                    <p class="text-xs text-green-600 mt-1 font-medium">
                        Check-in {{ $booking->check_in->diffForHumans() }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('member.bookings.show', $booking) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 border border-primary-200 rounded-lg hover:bg-primary-50 transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- PRIORITY 3: Currently Checked In --}}
    @if($checkedInBookings->isNotEmpty())
    <div class="space-y-3">
        <h2 class="text-sm font-semibold text-blue-700 uppercase tracking-wide flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Sedang Menginap
        </h2>
        @foreach($checkedInBookings as $booking)
        <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-200 rounded-xl p-4 sm:p-5">
            <p class="text-base font-semibold text-gray-800">Selamat menginap! 🏨</p>
            <p class="text-sm text-gray-600 mt-1">{{ $booking->room_type_name_snapshot }} — {{ $booking->room_name_snapshot ?? '' }}</p>
            <p class="text-sm text-gray-500">Check-out: {{ $booking->check_out->format('d M Y') }}</p>
            <a href="{{ route('member.bookings.show', $booking) }}" class="text-sm text-primary-600 hover:text-primary-800 mt-2 inline-block">Lihat Detail →</a>
        </div>
        @endforeach
    </div>
    @endif

    {{-- PRIORITY 4: No Active Bookings — Show Hero CTA --}}
    @if($pendingPaymentBookings->isEmpty() && $upcomingBookings->isEmpty() && $checkedInBookings->isEmpty())
    <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-14 h-14 mx-auto rounded-full bg-primary-100 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">
                Halo, {{ Str::before(auth()->user()->name, ' ') }} 👋
            </h2>
            <p class="text-gray-600 mt-2 text-sm">
                @if($totalBookings === 0)
                    Siap merencanakan kunjungan pertama Anda? Booking dari website tersimpan otomatis di akun Anda.
                @else
                    Siap merencanakan kunjungan berikutnya?
                @endif
            </p>
            <div class="mt-5 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('availability.search') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari Kamar
                </a>
                <a href="{{ route('rooms.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 text-primary-600 border border-primary-200 rounded-lg text-sm font-medium hover:bg-primary-50 transition">
                    Lihat Tipe Kamar
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Secondary Stats (compact) --}}
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-primary-700">{{ number_format($pointBalance) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Saldo Poin</p>
            @if($pointBalance > 0)
                <p class="text-xs text-gray-400">≈ Rp{{ number_format($pointValue, 0, ',', '.') }}</p>
            @else
                <p class="text-xs text-gray-400 mt-0.5">Kumpulkan setelah menginap</p>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $activeBookings }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Booking Aktif</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $totalBookings }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Booking</p>
        </div>
    </div>

    {{-- Quick Tips for New Users --}}
    @if($showOnboarding && $totalBookings === 0)
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Cara menggunakan akun member:</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div class="flex items-start gap-2">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">1</span>
                <span class="text-gray-600">Cari dan pesan kamar sesuai tanggal perjalanan.</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">2</span>
                <span class="text-gray-600">Pantau booking dan pembayaran dari dashboard.</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">3</span>
                <span class="text-gray-600">Kumpulkan poin setiap menyelesaikan masa inap.</span>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
