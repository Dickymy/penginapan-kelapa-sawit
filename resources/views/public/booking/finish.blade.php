@extends('layouts.public')

@section('title', 'Status Pembayaran - ' . $booking->booking_code)

@section('meta')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 text-center">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Terima Kasih</h1>

    <p class="text-gray-600 mb-6">
        Pembayaran Anda sedang diproses. Status booking akan diperbarui secara otomatis setelah pembayaran diverifikasi.
    </p>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-6 text-left shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Kode Booking</span>
                <p class="font-bold text-gray-900">{{ $booking->booking_code }}</p>
            </div>
            <div>
                <span class="text-gray-500">Status Booking</span>
                <p class="font-medium">{{ $booking->status->label() }}</p>
            </div>
            <div>
                <span class="text-gray-500">Status Pembayaran</span>
                <p class="font-medium">{{ $booking->payment_status->label() }}</p>
            </div>
            <div>
                <span class="text-gray-500">Total</span>
                <p class="font-bold text-gray-900">{{ $booking->formatted_total }}</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('booking.guest.detail', $booking->booking_code) }}"
           class="inline-block bg-primary-600 text-white px-6 py-3.5 rounded-xl font-bold hover:bg-primary-500 hover:-translate-y-1 hover:shadow-lg hover:shadow-primary-600/30 transition-all duration-300">
            Lihat Detail Booking
        </a>
        <a href="{{ route('home') }}"
           class="inline-block bg-white border border-gray-200 text-gray-700 px-6 py-3.5 rounded-xl font-bold hover:bg-gray-50 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
