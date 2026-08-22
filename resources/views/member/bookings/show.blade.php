@extends('layouts.member')

@section('title', 'Detail Booking')

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
        'yellow' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'icon' => 'text-amber-600'],
        'green' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => 'text-green-600'],
        'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'icon' => 'text-blue-600'],
        'indigo' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-800', 'icon' => 'text-indigo-600'],
        'red' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'icon' => 'text-red-600'],
        default => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-800', 'icon' => 'text-gray-600'],
    };
@endphp

<div class="max-w-2xl space-y-6">
    {{-- Back --}}
    <a href="{{ route('member.bookings.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Booking Saya
    </a>

    {{-- Status Header --}}
    <div class="rounded-2xl {{ $colorClasses['bg'] }} {{ $colorClasses['border'] }} border p-5 sm:p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full {{ $colorClasses['bg'] }} flex items-center justify-center">
                <svg class="w-6 h-6 {{ $colorClasses['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $statusConfig['icon'] !!}</svg>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-lg sm:text-xl font-bold {{ $colorClasses['text'] }}">{{ $statusConfig['label'] }}</h1>
                <p class="text-sm {{ $colorClasses['text'] }} opacity-80 mt-1">{{ $statusConfig['description'] }}</p>
                <p class="text-xs text-gray-500 mt-2 font-mono">{{ $booking->booking_code }}</p>
            </div>
        </div>

        @if($booking->status === \App\Enums\BookingStatus::Confirmed)
            <div class="mt-6 flex gap-4">
                <a href="{{ route('member.booking-changes.create', $booking) }}" class="inline-flex items-center gap-2 px-6 py-2.5 border border-primary-600 text-primary-600 rounded-xl hover:bg-primary-50 transition-all font-medium">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Ajukan Perubahan
                </a>
            </div>
            
            @if($booking->changeRequests->isNotEmpty())
            <div class="mt-6 border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 font-semibold text-slate-700">Riwayat Pengajuan Perubahan</div>
                <div class="divide-y divide-slate-100">
                    @foreach($booking->changeRequests as $req)
                    <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
                        <div>
                            <div class="font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $req->type)) }}</div>
                            <div class="text-sm text-slate-500 mt-1">Diajukan: {{ $req->created_at->format('d M Y, H:i') }}</div>
                            @if($req->admin_notes)
                                <div class="text-sm text-slate-600 mt-2 bg-slate-50 p-2 rounded border border-slate-200">
                                    <span class="font-semibold text-slate-700">Catatan Admin:</span> {{ $req->admin_notes }}
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                @if($req->status === 'approved') bg-emerald-100 text-emerald-700
                                @elseif($req->status === 'rejected') bg-red-100 text-red-700
                                @elseif($req->status === 'cancelled') bg-slate-100 text-slate-700
                                @else bg-amber-100 text-amber-700
                                @endif
                            ">
                                {{ ucfirst($req->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif

        @if($booking->status === \App\Enums\BookingStatus::PendingPayment && $booking->is_hold_active)
            <div class="mt-4 pt-4 border-t {{ $colorClasses['border'] }}">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <p class="text-sm font-medium {{ $colorClasses['text'] }}">Batas waktu pembayaran</p>
                        <p class="text-xs {{ $colorClasses['text'] }} opacity-70">{{ $booking->payment_expires_at->translatedFormat('d F Y, H:i') }} WITA</p>
                    </div>
                    <a href="{{ route('booking.pay', $booking->booking_code) }}"
                       class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition shadow-sm">
                        Bayar Sekarang
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- CTAs for different statuses --}}
    @if($booking->status === BookingStatus::Confirmed)
        <div class="flex flex-col sm:flex-row gap-3">
            @if($booking->invoice_number)
                <a href="{{ route('booking.invoice', $booking->booking_code) }}"
                   class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Invoice
                </a>
            @endif
            <a href="{{ route('location') }}"
               class="flex-1 inline-flex items-center justify-center px-5 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Lihat Lokasi
            </a>
        </div>
    @elseif($booking->status === BookingStatus::Expired || $booking->status === BookingStatus::Cancelled)
        <a href="{{ route('home') }}#cari-kamar"
           class="w-full inline-flex items-center justify-center px-5 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cari Kamar Lagi
        </a>
    @elseif(($booking->status === BookingStatus::Completed || $booking->payment_status === PaymentStatus::Paid) && $booking->invoice_number)
        <a href="{{ route('booking.invoice', $booking->booking_code) }}"
           class="w-full inline-flex items-center justify-center px-5 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download Invoice
        </a>
    @endif

    {{-- Booking Info Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Detail Booking</h2>
        </div>
        <div class="px-5 sm:px-6 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Check-in</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->check_in->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Check-out</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->check_out->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Tipe Kamar</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->room_type_name_snapshot }}</p>
                    <p class="text-xs text-gray-500">{{ $booking->room_name_snapshot }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Durasi</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->nights }} malam</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Jumlah Tamu</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->guest_count }} orang</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Dibuat</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->created_at->translatedFormat('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pricing Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Rincian Biaya</h2>
        </div>
        <div class="px-5 sm:px-6 py-5 space-y-3">
            @if($booking->nightPrices->count() > 0)
                <div class="text-sm font-medium text-gray-700 mb-2">Rincian Harga:</div>
                <ul class="space-y-1 mb-3">
                    @foreach($booking->nightPrices as $np)
                        <li class="flex justify-between text-sm">
                            <span class="text-gray-500">
                                {{ $np->date->translatedFormat('d M Y') }}
                                @if($np->label)
                                    <span class="text-xs inline-block bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded ml-1">{{ $np->label }}</span>
                                @endif
                            </span>
                            <span class="text-gray-900">Rp{{ number_format($np->price, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="flex justify-between text-sm pt-2 border-t border-gray-50">
                    <span class="text-gray-600">Subtotal ({{ $booking->nights }} malam)</span>
                    <span class="text-gray-900">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Harga per malam</span>
                    <span class="text-gray-900">Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ $booking->nights }} malam × Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }}</span>
                    <span class="text-gray-900">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($booking->addons->count() > 0)
            <div class="text-sm font-medium text-gray-700 mt-3 mb-2">Layanan Tambahan:</div>
            <ul class="space-y-1 mb-3 border-b border-gray-50 pb-3">
                @foreach($booking->addons as $ba)
                    <li class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $ba->addon->name ?? 'Layanan' }} x{{ $ba->quantity }}</span>
                        <span class="text-gray-900">{{ $ba->formatted_subtotal }}</span>
                    </li>
                @endforeach
            </ul>
            @endif
            @if($booking->promotion_discount > 0)
            <div class="flex justify-between text-sm text-green-700">
                <span>Diskon promo{{ $booking->promotion_code_snapshot ? ' ('.$booking->promotion_code_snapshot.')' : '' }}</span>
                <span>−Rp{{ number_format($booking->promotion_discount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($booking->points_discount > 0)
            <div class="flex justify-between text-sm text-green-700">
                <span>Potongan poin ({{ number_format($booking->points_redeemed) }} poin)</span>
                <span>−Rp{{ number_format($booking->points_discount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between pt-3 border-t border-gray-100">
                <span class="font-bold text-gray-900">Total</span>
                <span class="font-bold text-lg text-primary-600">{{ $booking->formatted_total }}</span>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <span class="text-sm text-gray-600">Status Pembayaran</span>
                <x-status-badge :status="$booking->payment_status" />
            </div>
        </div>
    </div>

    {{-- Invoice --}}
    @if(($booking->status === BookingStatus::Completed || $booking->payment_status === PaymentStatus::Paid) && $booking->invoice_number)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">Invoice</p>
                <p class="text-xs text-gray-500">{{ $booking->invoice_number }}</p>
            </div>
        </div>
        <a href="{{ route('booking.invoice', $booking->booking_code) }}"
           class="text-sm text-primary-600 font-medium hover:text-primary-800 transition">
            Unduh PDF →
        </a>
    </div>
    @endif

    {{-- Status History --}}
    @if($booking->statusHistories->count())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Riwayat Status</h2>
        </div>
        <div class="px-5 sm:px-6 py-5">
            <div class="space-y-4">
                @foreach($booking->statusHistories as $history)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-primary-400 mt-2"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $history->to_status }}</p>
                        @if($history->reason)
                            <p class="text-xs text-gray-600 mt-0.5">{{ $history->reason }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-0.5">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
