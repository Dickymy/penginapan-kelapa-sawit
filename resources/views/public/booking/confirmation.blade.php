@extends('layouts.public')

@section('title', 'Booking Berhasil - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Title --}}
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Booking Berhasil Dibuat!</h1>

    {{-- Success Alert --}}
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
        <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-green-800">
            Booking Anda telah berhasil dibuat. Silakan lakukan pembayaran sebelum batas waktu yang ditentukan.
        </p>
    </div>

    {{-- Booking Details --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Booking</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Kode Booking</span>
                <p class="font-bold text-gray-900 text-lg">{{ $booking->booking_code }}</p>
            </div>
            <div>
                <span class="text-gray-500">Tipe Kamar</span>
                <p class="font-medium text-gray-900">{{ $booking->room_type_name_snapshot }}</p>
            </div>
            <div>
                <span class="text-gray-500">Kamar</span>
                <p class="font-medium text-gray-900">{{ $booking->room_name_snapshot }}</p>
            </div>
            <div>
                <span class="text-gray-500">Tamu</span>
                <p class="font-medium text-gray-900">{{ $booking->guest_count }} orang</p>
            </div>
            <div>
                <span class="text-gray-500">Check-in</span>
                <p class="font-medium text-gray-900">{{ $booking->check_in->format('d M Y') }}</p>
            </div>
            <div>
                <span class="text-gray-500">Check-out</span>
                <p class="font-medium text-gray-900">{{ $booking->check_out->format('d M Y') }}</p>
            </div>
            <div>
                <span class="text-gray-500">Durasi</span>
                <p class="font-medium text-gray-900">{{ $booking->nights }} malam</p>
            </div>
            <div>
                <span class="text-gray-500">Total Pembayaran</span>
                <p class="font-bold text-primary-600 text-lg">{{ $booking->formatted_total }}</p>
            </div>
        </div>
    </div>

    {{-- Important Info Box --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
        <h3 class="text-base font-semibold text-yellow-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            Informasi Penting
        </h3>

        <ul class="space-y-3 text-sm text-yellow-900">
            <li>
                <strong>Simpan kode booking Anda:</strong>
                <span class="font-mono bg-yellow-100 px-2 py-0.5 rounded">{{ $booking->booking_code }}</span>
            </li>

            @if($rawToken)
                <li class="bg-yellow-100 rounded-lg p-3">
                    <strong>Token akses (simpan ini):</strong>
                    <span class="font-mono block mt-1 break-all">{{ $rawToken }}</span>
                    <p class="text-xs text-yellow-700 mt-2">
                        ⚠️ Token ini hanya ditampilkan sekali. Gunakan untuk mengakses status booking Anda tanpa login.
                    </p>
                </li>
            @endif

            <li>
                <strong>Batas waktu pembayaran:</strong>
                {{ $booking->payment_expires_at->format('d M Y H:i') }} WITA
            </li>
        </ul>
    </div>

    {{-- CTA Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3">
        {{-- Bayar Sekarang --}}
        <a href="{{ route('booking.pay', $booking->booking_code) }}"
           class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold text-center hover:bg-primary-700 transition">
            Bayar Sekarang
        </a>

        {{-- Cek Status --}}
        <a href="{{ route('booking.verify.form') }}"
           class="flex-1 bg-white border border-primary-600 text-primary-600 py-3 px-6 rounded-lg font-semibold text-center hover:bg-primary-50 transition">
            Cek Status Booking
        </a>
    </div>
</div>
@endsection
