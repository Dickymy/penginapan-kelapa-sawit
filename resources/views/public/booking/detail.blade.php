@extends('layouts.public')

@section('title', 'Detail Booking ' . $booking->booking_code . ' - Penginapan Kelapa Sawit')

@section('meta')
<meta name="robots" content="noindex, nofollow">
@endsection

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
        'yellow' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'icon' => 'text-amber-600', 'badge' => 'bg-amber-100 text-amber-800'],
        'green' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => 'text-green-600', 'badge' => 'bg-green-100 text-green-800'],
        'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'icon' => 'text-blue-600', 'badge' => 'bg-blue-100 text-blue-800'],
        'indigo' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-800', 'icon' => 'text-indigo-600', 'badge' => 'bg-indigo-100 text-indigo-800'],
        'red' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'icon' => 'text-red-600', 'badge' => 'bg-red-100 text-red-800'],
        default => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-800', 'icon' => 'text-gray-600', 'badge' => 'bg-gray-100 text-gray-800'],
    };
@endphp

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
    {{-- Back Link --}}
    <a href="{{ route('booking.my') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-5 transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Booking Saya
    </a>

    {{-- Status Header --}}
    <div class="rounded-2xl {{ $colorClasses['bg'] }} {{ $colorClasses['border'] }} border p-5 sm:p-6 mb-6">
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

        {{-- Countdown for pending --}}
        @if($booking->status === BookingStatus::PendingPayment && $booking->is_hold_active)
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

    {{-- Primary CTA for different statuses --}}
    @if($booking->status === BookingStatus::Confirmed)
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
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
        <div class="mb-6">
            <a href="{{ route('home') }}#cari-kamar"
               class="w-full inline-flex items-center justify-center px-5 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari Kamar Lagi
            </a>
        </div>
    @elseif($booking->status === BookingStatus::Completed && $booking->invoice_number)
        <div class="mb-6">
            <a href="{{ route('booking.invoice', $booking->booking_code) }}"
               class="w-full inline-flex items-center justify-center px-5 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download Invoice
            </a>
        </div>
    @endif

    {{-- Booking Info Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Detail Booking</h2>
        </div>
        <div class="px-5 sm:px-6 py-5 space-y-4">
            {{-- Guest Info --}}
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Tamu</p>
                <p class="font-semibold text-gray-900">{{ $booking->guest_name }}</p>
            </div>

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
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Durasi</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->nights }} malam</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Jumlah Tamu</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->guest_count }} orang</p>
                </div>
                @if($booking->arrival_estimate)
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Estimasi Tiba</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->arrival_estimate }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Pricing Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Rincian Biaya</h2>
        </div>
        <div class="px-5 sm:px-6 py-5 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Harga per malam</span>
                <span class="text-gray-900">Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">{{ $booking->nights }} malam × Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }}</span>
                <span class="text-gray-900">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span>
            </div>
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

            {{-- Payment Status --}}
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <span class="text-sm text-gray-600">Status Pembayaran</span>
                <x-status-badge :status="$booking->payment_status" />
            </div>
        </div>
    </div>

    {{-- Share / Access Link --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6" x-data="{ copied: false }">
        <div class="px-5 sm:px-6 py-4 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-900">Link Akses Booking</p>
                <p class="text-xs text-gray-500 mt-0.5">Simpan link ini untuk mengakses booking kapan saja.</p>
            </div>
            <button type="button"
                    @click="
                        navigator.clipboard.writeText('{{ url()->current() }}');
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                        $dispatch('toast', { type: 'success', message: 'Link berhasil disalin' });
                    "
                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition"
                    :class="copied ? 'text-green-700 bg-green-50' : 'text-primary-600 bg-primary-50 hover:bg-primary-100'">
                <svg x-show="!copied" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                <svg x-show="copied" x-cloak class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span x-text="copied ? 'Tersalin' : 'Salin Link'"></span>
            </button>
        </div>
    </div>

    {{-- Contact CTA --}}
    @php $waUrl = \App\Support\WhatsApp::url(\App\Models\Setting::get('contact', 'whatsapp', ''), 'Halo, saya ingin bertanya tentang booking ' . $booking->booking_code); @endphp
    @if($waUrl)
    <div class="text-center">
        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center text-sm text-gray-600 hover:text-green-700 transition">
            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.614-1.46A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-2.115 0-4.107-.57-5.82-1.563l-.418-.248-4.327 1.37 1.394-4.212-.273-.433A9.708 9.708 0 012.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75z"/></svg>
            Hubungi Penginapan via WhatsApp
        </a>
    </div>
    @endif
</div>
@endsection
