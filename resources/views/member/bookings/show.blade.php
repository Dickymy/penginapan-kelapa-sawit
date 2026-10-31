@extends('layouts.member')

@section('title', 'Detail Booking ' . $booking->booking_code)

@section('content')
@php
    use App\Enums\BookingStatus;
    use App\Enums\PaymentStatus;

    $statusConfig = match($booking->status) {
        BookingStatus::PendingPayment => [
            'label' => 'Menunggu Pembayaran',
            'description' => 'Selesaikan pembayaran sebelum batas waktu agar booking Anda tetap aktif.',
            'color' => 'yellow',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        BookingStatus::Confirmed => [
            'label' => 'Booking Dikonfirmasi',
            'description' => 'Pembayaran berhasil dan kamar Anda telah dikonfirmasi.',
            'color' => 'green',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        BookingStatus::CheckedIn => [
            'label' => 'Sedang Menginap',
            'description' => 'Selamat menikmati waktu menginap di Penginapan Kelapa Sawit.',
            'color' => 'blue',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],
        BookingStatus::CheckedOut => [
            'label' => 'Check-out Selesai',
            'description' => 'Terima kasih telah menginap bersama kami.',
            'color' => 'indigo',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        ],
        BookingStatus::Completed => [
            'label' => 'Selesai',
            'description' => 'Terima kasih telah menginap di Penginapan Kelapa Sawit. Semoga berkesan!',
            'color' => 'green',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        BookingStatus::Cancelled => [
            'label' => 'Booking Dibatalkan',
            'description' => 'Booking ini telah dibatalkan.',
            'color' => 'red',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        BookingStatus::Expired => [
            'label' => 'Waktu Pembayaran Berakhir',
            'description' => 'Booking ini tidak lagi aktif karena pembayaran tidak diselesaikan dalam batas waktu.',
            'color' => 'red',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        BookingStatus::NoShow => [
            'label' => 'Tidak Hadir',
            'description' => 'Tamu tidak melakukan check-in pada tanggal yang ditentukan.',
            'color' => 'red',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
        ],
        default => [
            'label' => $booking->status->label(),
            'description' => '',
            'color' => 'gray',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
    };

    $colorClasses = match($statusConfig['color']) {
        'yellow' => ['bg' => 'bg-amber-50', 'bg_strong' => 'bg-amber-500', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'text_strong' => 'text-amber-900', 'icon' => 'text-amber-600'],
        'green' => ['bg' => 'bg-emerald-50', 'bg_strong' => 'bg-emerald-500', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'text_strong' => 'text-emerald-900', 'icon' => 'text-emerald-600'],
        'blue' => ['bg' => 'bg-blue-50', 'bg_strong' => 'bg-blue-500', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'text_strong' => 'text-blue-900', 'icon' => 'text-blue-600'],
        'indigo' => ['bg' => 'bg-indigo-50', 'bg_strong' => 'bg-indigo-500', 'border' => 'border-indigo-200', 'text' => 'text-indigo-800', 'text_strong' => 'text-indigo-900', 'icon' => 'text-indigo-600'],
        'red' => ['bg' => 'bg-rose-50', 'bg_strong' => 'bg-rose-500', 'border' => 'border-rose-200', 'text' => 'text-rose-800', 'text_strong' => 'text-rose-900', 'icon' => 'text-rose-600'],
        default => ['bg' => 'bg-gray-50', 'bg_strong' => 'bg-gray-500', 'border' => 'border-gray-200', 'text' => 'text-gray-800', 'text_strong' => 'text-gray-900', 'icon' => 'text-gray-600'],
    };
@endphp

<div class="space-y-6 pb-12">
    {{-- Navigation --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('member.bookings.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-800 hover:bg-gray-50 hover:shadow-sm transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Detail Perjalanan</h1>
    </div>

    {{-- Premium Status Header Card --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-1.5 {{ $colorClasses['bg_strong'] }}"></div>
        
        <div class="p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                
                {{-- Status Info --}}
                <div class="flex items-start gap-5">
                    <div class="flex-shrink-0 w-16 h-16 rounded-2xl {{ $colorClasses['bg'] }} flex items-center justify-center shadow-inner">
                        <svg class="w-8 h-8 {{ $colorClasses['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $statusConfig['icon'] !!}</svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold font-mono tracking-wider">{{ $booking->booking_code }}</span>
                            @if($booking->payment_status === PaymentStatus::Paid)
                                <span class="px-3 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-bold tracking-wider flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Lunas
                                </span>
                            @endif
                        </div>
                        <h2 class="text-2xl font-black {{ $colorClasses['text_strong'] }}">{{ $statusConfig['label'] }}</h2>
                        <p class="text-gray-500 mt-1 max-w-xl text-sm sm:text-base">{{ $statusConfig['description'] }}</p>
                    </div>
                </div>

                {{-- Primary Actions --}}
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto shrink-0">
                    @if($booking->status === BookingStatus::PendingPayment && $booking->is_hold_active)
                        <div class="text-right w-full sm:w-auto mb-2 sm:mb-0 mr-0 sm:mr-4">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Bayar Sebelum</p>
                            <p class="text-sm font-bold text-red-600">{{ $booking->payment_expires_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <a href="{{ route('booking.pay', $booking->booking_code) }}"
                           class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-500 hover:-translate-y-0.5 shadow-lg shadow-primary-600/30 transition-all active:scale-95">
                            Selesaikan Pembayaran
                        </a>
                    @endif

                    @if($booking->status === BookingStatus::Confirmed || $booking->status === BookingStatus::Completed || $booking->payment_status === PaymentStatus::Paid)
                        <a href="{{ route('location') }}"
                           class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-3 border border-gray-200 text-gray-700 bg-white font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Peta Lokasi
                        </a>
                        <a href="{{ route('booking.invoice', $booking->booking_code) }}"
                           download="invoice-{{ $booking->booking_code }}.pdf"
                           class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black hover:shadow-lg hover:-translate-y-0.5 transition-all active:scale-95">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh Invoice
                        </a>
                    @endif

                    @if($booking->status === BookingStatus::Expired || $booking->status === BookingStatus::Cancelled)
                        <a href="{{ route('home') }}#cari-kamar"
                           class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-500 hover:-translate-y-0.5 shadow-lg shadow-primary-600/30 transition-all active:scale-95">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Cari Kamar Lagi
                        </a>
                    @endif
                </div>
            </div>

            {{-- Secondary Actions (Booking Changes/Cancellations) --}}
            @if($booking->status === BookingStatus::Confirmed)
                <div class="mt-6 pt-6 border-t border-gray-100 flex flex-wrap gap-3" x-data>
                    @if($booking->changeRequests->where('status', 'pending')->isNotEmpty())
                        <button disabled class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 text-slate-400 bg-slate-50 rounded-xl cursor-not-allowed font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Menunggu Konfirmasi Admin untuk Perubahan
                        </button>
                    @else
                        <a href="{{ route('member.booking-changes.create', $booking) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl transition-all font-semibold text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Ajukan Perubahan Jadwal
                        </a>
                        
                        @if(now()->startOfDay()->diffInDays($booking->check_in->startOfDay(), false) >= 1)
                            <a href="{{ route('member.booking-cancellations.create', $booking) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 text-red-700 hover:bg-red-100 rounded-xl transition-all font-semibold text-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batalkan Pesanan
                            </a>
                        @endif
                    @endif
                </div>
            @endif

            @if($booking->status === BookingStatus::PendingPayment)
                <div class="mt-6 pt-6 border-t border-gray-100" x-data>
                    <button type="button" @click="$dispatch('open-confirm', { id: 'cancel-booking-{{ $booking->id }}' })"
                            class="inline-flex items-center gap-2 px-5 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 rounded-xl transition-all font-semibold text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
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
                </div>
            @endif
        </div>
    </div>

    {{-- Change Request History --}}
    @if($booking->changeRequests->isNotEmpty())
        <div class="bg-white rounded-3xl border border-blue-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden">
            <div class="px-6 sm:px-8 py-5 border-b border-blue-50 bg-blue-50/50 flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="font-bold text-blue-900">Riwayat Pengajuan Perubahan</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($booking->changeRequests as $req)
                <div class="px-6 sm:px-8 py-5 hover:bg-gray-50 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $req->type)) }}</div>
                            <div class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Diajukan: {{ $req->created_at->format('d M Y, H:i') }}
                            </div>
                            @if($req->admin_notes)
                                <div class="mt-3 bg-white border border-gray-200 rounded-lg p-3 text-sm flex gap-3">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                    <div>
                                        <span class="font-semibold text-gray-700 block mb-0.5">Tanggapan Admin:</span>
                                        <span class="text-gray-600">{{ $req->admin_notes }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider
                                @if($req->status === 'approved') bg-emerald-100 text-emerald-800
                                @elseif($req->status === 'rejected') bg-red-100 text-red-800
                                @elseif($req->status === 'cancelled') bg-gray-100 text-gray-800
                                @else bg-amber-100 text-amber-800
                                @endif
                            ">
                                @if($req->status === 'approved') <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @elseif($req->status === 'rejected') <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @elseif($req->status === 'cancelled') <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @else <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                                {{ $req->status === 'pending' ? 'Diproses' : ($req->status === 'approved' ? 'Disetujui' : ($req->status === 'rejected' ? 'Ditolak' : 'Dibatalkan')) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Booking Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Detail Pesanan</h2>
                </div>
                
                <div class="p-6 sm:p-8">
                    {{-- Room Info --}}
                    <div class="flex items-start gap-4 mb-8">
                        <div class="w-16 h-16 rounded-2xl bg-primary-50 flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $booking->room_type_name_snapshot }}</h3>
                            @if($booking->room_name_snapshot)
                                <p class="text-primary-600 font-medium mt-1">Kamar: {{ $booking->room_name_snapshot }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Dates Grid --}}
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-gray-50 rounded-2xl p-4 sm:p-5 border border-gray-100 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10">
                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Check-in</p>
                            <p class="text-lg sm:text-xl font-black text-gray-900">{{ $booking->check_in->translatedFormat('d M Y') }}</p>
                            <p class="text-sm font-medium text-gray-500 mt-1">Mulai 14:00 WITA</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 sm:p-5 border border-gray-100 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10">
                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Check-out</p>
                            <p class="text-lg sm:text-xl font-black text-gray-900">{{ $booking->check_out->translatedFormat('d M Y') }}</p>
                            <p class="text-sm font-medium text-gray-500 mt-1">Hingga 12:00 WITA</p>
                        </div>
                    </div>

                    {{-- Other Details --}}
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Durasi</p>
                                <p class="font-bold text-gray-900">{{ $booking->nights }} Malam</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tamu</p>
                                <p class="font-bold text-gray-900">{{ $booking->guest_count }} Orang</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dibuat Pada</p>
                                <p class="font-bold text-gray-900">{{ $booking->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                        @if($booking->arrival_estimate)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Estimasi Tiba</p>
                                <p class="font-bold text-gray-900">{{ $booking->arrival_estimate }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Status History --}}
            @if($booking->statusHistories->count())
            <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Riwayat Perjalanan</h2>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="relative border-l-2 border-gray-100 ml-3 space-y-8">
                        @foreach($booking->statusHistories as $history)
                        <div class="relative pl-6">
                            <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-4 border-white bg-primary-500 shadow-sm"></div>
                            <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-1 mb-1">
                                <h3 class="font-bold text-gray-900">{{ ucwords(str_replace('_', ' ', $history->to_status)) }}</h3>
                                <time class="text-xs font-semibold text-gray-400">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</time>
                            </div>
                            @if($history->reason)
                                <p class="text-sm text-gray-500">{{ $history->reason }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Pricing --}}
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] overflow-hidden sticky top-24">
                <div class="px-6 py-5 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Rincian Pembayaran</h2>
                </div>
                <div class="p-6 space-y-6">
                    
                    {{-- Room Prices --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3 text-sm font-bold text-gray-700">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Biaya Kamar
                        </div>
                        @if($booking->nightPrices->count() > 0)
                            <ul class="space-y-2.5">
                                @foreach($booking->nightPrices as $np)
                                    <li class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500 font-medium flex items-center gap-2">
                                            {{ $np->date->translatedFormat('d M') }}
                                            @if($np->label)
                                                <span class="text-[10px] font-bold bg-primary-50 text-primary-600 px-1.5 py-0.5 rounded-md uppercase tracking-wider">{{ $np->label }}</span>
                                            @endif
                                        </span>
                                        <span class="font-semibold text-gray-900">Rp{{ number_format($np->price, 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="flex justify-between items-center text-sm pt-3 mt-3 border-t border-gray-100 border-dashed">
                                <span class="font-bold text-gray-700">Subtotal ({{ $booking->nights }} malam)</span>
                                <span class="font-bold text-gray-900">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @else
                            <div class="flex justify-between items-center text-sm mb-2">
                                <span class="text-gray-500 font-medium">Harga per malam</span>
                                <span class="font-semibold text-gray-900">Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm pt-3 border-t border-gray-100 border-dashed">
                                <span class="font-bold text-gray-700">{{ $booking->nights }} malam</span>
                                <span class="font-bold text-gray-900">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Addons --}}
                    @if($booking->addons->count() > 0)
                    <div>
                        <div class="flex items-center gap-2 mb-3 text-sm font-bold text-gray-700">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            Layanan Tambahan
                        </div>
                        <ul class="space-y-3">
                            @foreach($booking->addons as $ba)
                                <li class="flex justify-between items-start text-sm">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $ba->addon->name ?? 'Layanan' }} <span class="text-gray-400 font-normal">x{{ $ba->quantity }}</span></p>
                                        @if($ba->quantity > 1 || $ba->addon?->isQuantityBased())
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $ba->formatted_unit_price }} / unit</p>
                                        @endif
                                    </div>
                                    <span class="font-semibold text-gray-900 mt-0.5">{{ $ba->formatted_subtotal }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Discounts --}}
                    @if($booking->promotion_discount > 0 || $booking->points_discount > 0)
                    <div class="bg-green-50 rounded-xl p-4 space-y-2">
                        @if($booking->promotion_discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="font-semibold text-green-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                Promo {{ $booking->promotion_code_snapshot }}
                            </span>
                            <span class="font-bold text-green-700">−Rp{{ number_format($booking->promotion_discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($booking->points_discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="font-semibold text-green-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Tukar {{ number_format($booking->points_redeemed) }} Poin
                            </span>
                            <span class="font-bold text-green-700">−Rp{{ number_format($booking->points_discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Total --}}
                    <div class="pt-4 border-t-2 border-gray-100">
                        <div class="flex justify-between items-end mb-4">
                            <div>
                                <span class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Total Pembayaran</span>
                            </div>
                            <span class="text-3xl font-black text-primary-600 leading-none">{{ $booking->formatted_total }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl p-3">
                            <span class="text-sm font-semibold text-gray-600">Status Pembayaran</span>
                            <x-status-badge :status="$booking->payment_status" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

