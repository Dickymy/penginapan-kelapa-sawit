@extends('layouts.member')

@section('title', 'Dashboard Member')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Selamat datang, {{ auth()->user()->name }}!</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Saldo Poin</h3>
        <p class="text-2xl font-bold text-primary-700">{{ number_format($pointBalance) }}</p>
        <p class="text-xs text-gray-400 mt-1">≈ Rp{{ number_format($pointValue, 0, ',', '.') }}</p>
        <a href="{{ route('member.points.index') }}" class="text-xs text-primary-600 hover:text-primary-800 mt-2 inline-block">Lihat riwayat →</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Booking Aktif</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $activeBookings }}</p>
        @if($activeBookings > 0)
            <a href="{{ route('member.bookings.index', ['tab' => 'active']) }}" class="text-xs text-primary-600 hover:text-primary-800 mt-2 inline-block">Lihat detail →</a>
        @endif
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Booking</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $totalBookings }}</p>
        @if($totalBookings > 0)
            <a href="{{ route('member.bookings.index') }}" class="text-xs text-primary-600 hover:text-primary-800 mt-2 inline-block">Lihat semua →</a>
        @endif
    </div>
</div>

{{-- Recent Bookings --}}
<div class="mt-8">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Booking Terbaru</h2>
    @if($recentBookings->isNotEmpty())
        <div class="space-y-3">
            @foreach($recentBookings as $booking)
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-mono text-gray-500">{{ $booking->booking_code }}</span>
                        <x-status-badge :status="$booking->status" />
                    </div>
                    <p class="text-sm text-gray-800 mt-1 font-medium">{{ $booking->room_type_name_snapshot }}</p>
                    <p class="text-xs text-gray-500">{{ $booking->check_in->format('d M Y') }} → {{ $booking->check_out->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-gray-800">{{ $booking->formatted_total }}</span>
                    <a href="{{ route('member.bookings.show', $booking) }}" class="text-sm text-primary-600 hover:text-primary-800">Detail →</a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <x-empty-state message="Belum ada booking. Pesan kamar sekarang!" />
    @endif
</div>

<div class="mt-6">
    <a href="{{ route('rooms.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Pesan Kamar
    </a>
</div>
@endsection
