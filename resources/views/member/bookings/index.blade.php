@extends('layouts.member')

@section('title', 'Booking Saya')

@section('content')
<div class="space-y-5">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Booking Saya</h1>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 overflow-x-auto">
        <nav class="flex gap-4 min-w-max">
            <a href="{{ route('member.bookings.index', ['tab' => 'active']) }}"
               class="pb-2 border-b-2 text-sm font-medium whitespace-nowrap {{ $tab === 'active' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Aktif
            </a>
            <a href="{{ route('member.bookings.index', ['tab' => 'completed']) }}"
               class="pb-2 border-b-2 text-sm font-medium whitespace-nowrap {{ $tab === 'completed' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Selesai
            </a>
            <a href="{{ route('member.bookings.index', ['tab' => 'cancelled']) }}"
               class="pb-2 border-b-2 text-sm font-medium whitespace-nowrap {{ $tab === 'cancelled' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Batal / Expired
            </a>
        </nav>
    </div>

    {{-- Booking Cards --}}
    <div class="space-y-3">
        @forelse($bookings as $booking)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-5 hover:border-primary-300 hover:shadow-md transition cursor-pointer"
             x-data
             @click="window.location.href = '{{ route('member.bookings.show', $booking) }}'">
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <x-status-badge :status="$booking->status" />
                    <span class="text-xs font-mono text-gray-400">{{ $booking->booking_code }}</span>
                </div>
            </div>
            <p class="font-semibold text-gray-800">{{ $booking->room_type_name_snapshot }}</p>
            @if($booking->room_name_snapshot)
                <p class="text-xs text-gray-500">{{ $booking->room_name_snapshot }}</p>
            @endif
            <p class="text-sm text-gray-600 mt-1">
                <svg class="inline w-4 h-4 text-gray-400 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ $booking->check_in->format('d M Y') }} → {{ $booking->check_out->format('d M Y') }} ({{ $booking->nights }} malam)
            </p>

            <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <span class="text-base font-bold text-gray-800">{{ $booking->formatted_total }}</span>
                <div class="flex items-center gap-2">
                    @if($booking->status->value === 'pending_payment')
                        <button type="button" @click.stop="$dispatch('open-confirm', { id: 'cancel-booking-{{ $booking->id }}' })"
                                class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition relative z-10">
                            Batalkan
                        </button>
                        
                        <x-confirm-modal 
                            id="cancel-booking-{{ $booking->id }}"
                            title="Batalkan Pesanan" 
                            message="Apakah Anda yakin ingin membatalkan pesanan kamar {{ $booking->room_type_name_snapshot }}? Pesanan yang dibatalkan tidak dapat dikembalikan."
                            confirm-text="Ya, Batalkan"
                            cancel-text="Tutup"
                            variant="danger"
                            form-action="{{ route('member.bookings.cancel', $booking) }}"
                            method="PATCH"
                        />
                        <a href="{{ route('booking.pay', $booking->booking_code) }}" @click.stop
                           class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition relative z-10">
                            Bayar Sekarang
                        </a>
                    @else
                        <a href="{{ route('member.bookings.show', $booking) }}" @click.stop
                           class="text-sm text-primary-600 hover:text-primary-800 font-medium relative z-10">
                            Lihat Detail &rarr;
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        {{-- Enhanced Empty State --}}
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            @if($tab === 'active')
                <p class="mt-4 text-sm text-gray-600 font-medium">Belum ada booking aktif</p>
                <p class="mt-1 text-xs text-gray-500">Cari kamar sesuai tanggal perjalanan Anda. Booking yang dibuat menggunakan akun ini akan muncul di sini.</p>
                <div class="mt-5 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('home') }}#cari-kamar" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari Kamar
                    </a>
                    <a href="{{ route('member.claim.index') }}" class="text-sm text-gray-600 hover:text-primary-600">
                        Punya kode booking? Klaim di sini
                    </a>
                </div>
            @elseif($tab === 'completed')
                <p class="mt-4 text-sm text-gray-600">Belum ada booking yang selesai.</p>
                <p class="mt-1 text-xs text-gray-500">Booking yang telah Anda selesaikan akan muncul di sini.</p>
            @else
                <p class="mt-4 text-sm text-gray-600">Tidak ada booking yang dibatalkan.</p>
            @endif
        </div>
        @endforelse
    </div>

    {{ $bookings->withQueryString()->links() }}
</div>
@endsection
