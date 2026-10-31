@extends('layouts.member')

@section('title', 'Booking Saya')

@section('content')
<div class="space-y-8 pb-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-10 w-32 h-32 bg-primary-500 opacity-20 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-2">Riwayat Perjalanan</h1>
                <p class="text-gray-300 text-sm sm:text-base">Kelola dan pantau semua pemesanan kamar Anda di satu tempat.</p>
            </div>
            
            @if($tab === 'active')
            <a href="{{ route('home') }}#cari-kamar" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold shadow-lg shadow-primary-600/30 hover:bg-primary-500 hover:scale-105 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Pesan Kamar
            </a>
            @endif
        </div>
    </div>

    {{-- Custom Tabs --}}
    <div class="bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100 flex overflow-x-auto hide-scrollbar">
        <a href="{{ route('member.bookings.index', ['tab' => 'active']) }}"
           class="flex-1 text-center py-3 px-4 rounded-xl text-sm font-semibold transition-all whitespace-nowrap {{ $tab === 'active' ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
            <div class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 {{ $tab === 'active' ? 'text-primary-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Booking Aktif
            </div>
        </a>
        <a href="{{ route('member.bookings.index', ['tab' => 'completed']) }}"
           class="flex-1 text-center py-3 px-4 rounded-xl text-sm font-semibold transition-all whitespace-nowrap {{ $tab === 'completed' ? 'bg-green-50 text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
            <div class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 {{ $tab === 'completed' ? 'text-green-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Selesai
            </div>
        </a>
        <a href="{{ route('member.bookings.index', ['tab' => 'cancelled']) }}"
           class="flex-1 text-center py-3 px-4 rounded-xl text-sm font-semibold transition-all whitespace-nowrap {{ $tab === 'cancelled' ? 'bg-red-50 text-red-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
            <div class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 {{ $tab === 'cancelled' ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Batal / Kedaluwarsa
            </div>
        </a>
    </div>

    {{-- Booking Cards --}}
    <div class="space-y-5">
        @forelse($bookings as $booking)
        <div class="group bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 p-6 sm:p-8 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden relative"
             x-data
             @click="window.location.href = '{{ route('member.bookings.show', $booking) }}'">
             
            <!-- Accent Line -->
            <div class="absolute top-0 left-0 w-1.5 h-full {{ $booking->status->value === 'pending_payment' ? 'bg-yellow-400' : ($booking->status->value === 'confirmed' ? 'bg-green-500' : 'bg-gray-200') }}"></div>

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Main Info -->
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold font-mono tracking-wider">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                {{ $booking->booking_code }}
                            </span>
                            <x-status-badge :status="$booking->status" />
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors">{{ $booking->room_type_name_snapshot }}</h3>
                    @if($booking->room_name_snapshot)
                        <p class="text-sm font-medium text-gray-500 mt-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Kamar: {{ $booking->room_name_snapshot }}
                        </p>
                    @endif

                    <div class="mt-6 flex flex-wrap gap-4 sm:gap-8">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Check-in</p>
                            <p class="text-sm sm:text-base font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                </span>
                                {{ $booking->check_in->translatedFormat('d M Y') }}
                            </p>
                        </div>
                        <div class="hidden sm:flex items-center justify-center">
                            <div class="w-12 h-[1px] bg-gray-200 relative">
                                <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-2 text-xs font-semibold text-gray-400">{{ $booking->nights }}M</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Check-out</p>
                            <p class="text-sm sm:text-base font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </span>
                                {{ $booking->check_out->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action & Price -->
                <div class="lg:w-64 flex flex-col justify-between pt-6 lg:pt-0 border-t lg:border-t-0 lg:border-l border-gray-100 lg:pl-6 relative z-10">
                    <div class="mb-6 lg:mb-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Harga</p>
                        <p class="text-2xl font-black text-gray-900">{{ $booking->formatted_total }}</p>
                    </div>

                    <div class="flex flex-col sm:flex-row lg:flex-col gap-3">
                        @if($booking->status->value === 'pending_payment')
                            <a href="{{ route('booking.pay', $booking->booking_code) }}" @click.stop
                               class="w-full inline-flex justify-center items-center px-4 py-2.5 text-sm font-bold text-white bg-primary-600 rounded-xl hover:bg-primary-700 hover:shadow-lg shadow-primary-600/30 transition-all active:scale-95">
                                Bayar Sekarang
                            </a>
                            <button type="button" @click.stop="$dispatch('open-confirm', { id: 'cancel-booking-{{ $booking->id }}' })"
                                    class="w-full inline-flex justify-center items-center px-4 py-2.5 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all active:scale-95">
                                Batalkan Pesanan
                            </button>
                            
                            <x-confirm-modal 
                                id="cancel-booking-{{ $booking->id }}"
                                title="Batalkan Pesanan" 
                                message="Apakah Anda yakin ingin membatalkan pesanan kamar {{ $booking->room_type_name_snapshot }}? Pesanan yang dibatalkan tidak dapat dikembalikan."
                                confirm-text="Ya, Batalkan"
                                cancel-text="Kembali"
                                variant="danger"
                                form-action="{{ route('member.bookings.cancel', $booking) }}"
                                method="PATCH"
                            />
                        @else
                            <a href="{{ route('member.bookings.show', $booking) }}" @click.stop
                               class="w-full inline-flex justify-center items-center px-4 py-2.5 text-sm font-bold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 hover:text-gray-900 transition-all active:scale-95 group-hover:bg-primary-50 group-hover:text-primary-700 group-hover:border-primary-100">
                                Lihat Detail Perjalanan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        {{-- Empty State Premium --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gray-50 flex items-center justify-center relative">
                <div class="absolute inset-0 border-4 border-white shadow-sm rounded-full"></div>
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($tab === 'active')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    @elseif($tab === 'completed')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @endif
                </svg>
            </div>
            
            @if($tab === 'active')
                <h3 class="text-2xl font-black text-gray-900 mb-2">Belum ada perjalanan</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-8">Waktunya merencanakan liburan Anda! Temukan kamar terbaik untuk menginap bersama kami.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('home') }}#cari-kamar" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gray-900 text-white rounded-xl font-bold shadow-lg hover:bg-black hover:-translate-y-0.5 active:scale-95 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari Kamar Sekarang
                    </a>
                    <a href="{{ route('member.claim.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-gray-700 border border-gray-200 rounded-xl font-bold hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Klaim Booking
                    </a>
                </div>
            @elseif($tab === 'completed')
                <h3 class="text-2xl font-black text-gray-900 mb-2">Belum ada riwayat selesai</h3>
                <p class="text-gray-500 max-w-sm mx-auto">Setiap kenangan menginap Anda yang telah berlalu akan tersimpan dengan rapi di sini.</p>
            @else
                <h3 class="text-2xl font-black text-gray-900 mb-2">Tidak ada pembatalan</h3>
                <p class="text-gray-500 max-w-sm mx-auto">Sempurna! Anda belum pernah memiliki riwayat pemesanan yang dibatalkan.</p>
            @endif
        </div>
        @endforelse
    </div>

    @if($bookings->hasPages())
        <div class="mt-8">
            {{ $bookings->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
