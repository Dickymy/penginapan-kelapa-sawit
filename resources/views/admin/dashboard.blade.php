@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Admin</h1>

<div class="grid md:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('admin.bookings.index') }}" class="bg-white rounded-lg shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Check-in Hari Ini</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $checkedInToday }} / {{ $checkInsToday }}</p>
        <p class="text-xs text-gray-400 mt-1">sudah / dijadwalkan</p>
    </a>
    <a href="{{ route('admin.bookings.index') }}" class="bg-white rounded-lg shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Kamar Terisi</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $occupiedRooms }} / {{ $totalRooms }}</p>
        <p class="text-xs text-gray-400 mt-1">tersedia: {{ $availableRooms }}</p>
    </a>
    <a href="{{ route('admin.reports.revenue') }}" class="bg-white rounded-lg shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Pendapatan Bulan Ini</h3>
        <p class="text-2xl font-bold text-gray-800">Rp{{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
    </a>
    <a href="{{ route('admin.bookings.index') }}" class="bg-white rounded-lg shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Perlu Perhatian</h3>
        <p class="text-2xl font-bold {{ $pendingAttention > 0 ? 'text-red-600' : 'text-gray-800' }}">
            {{ $pendingAttention }}
            @if($pendingAttention > 0)
                <span class="inline-block ml-1 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">!</span>
            @endif
        </p>
    </a>
</div>

{{-- Recent Bookings --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Booking Terbaru</h2>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-primary-600 hover:underline">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-left">Kode</th>
                    <th class="px-5 py-3 text-left">Tamu</th>
                    <th class="px-5 py-3 text-left">Kamar</th>
                    <th class="px-5 py-3 text-left">Check-in</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentBookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-xs">{{ $booking->booking_code }}</td>
                    <td class="px-5 py-3">{{ $booking->guest_name }}</td>
                    <td class="px-5 py-3">{{ $booking->room_name_snapshot }}</td>
                    <td class="px-5 py-3">{{ $booking->check_in->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-block px-2 py-0.5 text-xs rounded-full
                            @if($booking->status->value === 'confirmed') bg-green-100 text-green-700
                            @elseif($booking->status->value === 'checked_in') bg-blue-100 text-blue-700
                            @elseif($booking->status->value === 'pending_payment') bg-yellow-100 text-yellow-700
                            @elseif(in_array($booking->status->value, ['cancelled', 'expired', 'no_show'])) bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ $booking->status->value }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">Rp{{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">Belum ada booking.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
