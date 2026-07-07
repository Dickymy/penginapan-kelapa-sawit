@extends('layouts.member')

@section('title', 'Booking Saya')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">Booking Saya</h1>

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-4">
            <a href="{{ route('member.bookings.index', ['tab' => 'active']) }}"
               class="pb-2 border-b-2 text-sm font-medium {{ $tab === 'active' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Aktif
            </a>
            <a href="{{ route('member.bookings.index', ['tab' => 'completed']) }}"
               class="pb-2 border-b-2 text-sm font-medium {{ $tab === 'completed' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Selesai
            </a>
            <a href="{{ route('member.bookings.index', ['tab' => 'cancelled']) }}"
               class="pb-2 border-b-2 text-sm font-medium {{ $tab === 'cancelled' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Batal
            </a>
        </nav>
    </div>

    {{-- Booking Cards --}}
    <div class="space-y-4">
        @forelse($bookings as $booking)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-mono text-gray-500">{{ $booking->booking_code }}</span>
                <x-status-badge :status="$booking->status" />
            </div>
            <p class="font-semibold text-gray-800">{{ $booking->room_type_name_snapshot }} — {{ $booking->room_name_snapshot }}</p>
            <p class="text-sm text-gray-600">{{ $booking->check_in->format('d M Y') }} &rarr; {{ $booking->check_out->format('d M Y') }} ({{ $booking->nights }} malam)</p>
            <div class="flex items-center justify-between mt-3">
                <span class="text-lg font-bold text-gray-800">{{ $booking->formatted_total }}</span>
                <a href="{{ route('member.bookings.show', $booking) }}" class="text-sm text-primary-600 hover:text-primary-800">Lihat Detail &rarr;</a>
            </div>
        </div>
        @empty
        <x-empty-state message="Belum ada booking pada kategori ini." action="{{ route('rooms.index') }}" action-text="Pesan Kamar" />
        @endforelse
    </div>

    {{ $bookings->withQueryString()->links() }}
</div>
@endsection
