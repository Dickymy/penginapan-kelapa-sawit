@extends('layouts.member')

@section('title', 'Klaim Booking')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">Klaim Booking</h1>
    <p class="text-gray-600">Booking yang dibuat menggunakan email Anda sebelum mendaftar dapat diklaim di sini.</p>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="space-y-4">
        @forelse($claimable as $booking)
        <div class="bg-white rounded-lg shadow p-4 flex items-center justify-between">
            <div>
                <p class="font-mono text-sm text-gray-500">{{ $booking->booking_code }}</p>
                <p class="font-semibold">{{ $booking->room_type_name_snapshot }} — {{ $booking->room_name_snapshot }}</p>
                <p class="text-sm text-gray-600">{{ $booking->check_in->format('d M Y') }} &rarr; {{ $booking->check_out->format('d M Y') }}</p>
                <p class="text-sm text-gray-600">{{ $booking->formatted_total }}</p>
            </div>
            <form method="POST" action="{{ route('member.claim.claim', $booking) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700">
                    Klaim
                </button>
            </form>
        </div>
        @empty
        <p class="text-center text-gray-500 py-8">Tidak ada booking yang dapat diklaim.</p>
        @endforelse
    </div>
</div>
@endsection
