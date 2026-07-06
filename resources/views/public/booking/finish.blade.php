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

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 text-left">
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
        <a href="{{ route('booking.verify.form') }}"
           class="inline-block bg-primary-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-700 transition">
            Cek Status Booking
        </a>
        <a href="{{ route('home') }}"
           class="inline-block bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
