@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Priority Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('admin.bookings.index', ['status' => 'confirmed', 'check_in' => today()->toDateString()]) }}"
           class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full {{ $checkInsToday->count() > 0 ? 'bg-blue-500' : 'bg-gray-300' }}"></span>
                <span class="text-xs font-medium text-gray-500">Check-in</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $checkInsToday->count() }}</p>
            <p class="text-xs text-gray-400">hari ini</p>
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'checked_in']) }}"
           class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full {{ $checkOutsToday->count() > 0 ? 'bg-indigo-500' : 'bg-gray-300' }}"></span>
                <span class="text-xs font-medium text-gray-500">Check-out</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $checkOutsToday->count() }}</p>
            <p class="text-xs text-gray-400">hari ini</p>
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'pending_payment']) }}"
           class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full {{ $pendingPayment > 0 ? 'bg-yellow-500' : 'bg-gray-300' }}"></span>
                <span class="text-xs font-medium text-gray-500">Menunggu Bayar</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $pendingPayment }}</p>
            <p class="text-xs text-gray-400">reservasi</p>
        </a>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-xs font-medium text-gray-500">Kamar Terisi</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $occupiedRooms }}<span class="text-sm font-normal text-gray-400">/{{ $totalRooms }}</span></p>
            <p class="text-xs text-gray-400">tersedia {{ $totalRooms - $occupiedRooms }}</p>
        </div>
    </div>

    {{-- Needs Attention --}}
    @if($needsAttention > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
        <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </span>
        <div class="flex-1">
            <p class="text-sm font-medium text-red-800">{{ $needsAttention }} reservasi membutuhkan perhatian</p>
            <p class="text-xs text-red-600">Pembayaran terlambat atau masalah lain yang perlu ditinjau.</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-red-700 hover:underline flex-shrink-0">Periksa →</a>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('admin.bookings.create') }}" class="flex items-center gap-3 bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition">
            <span class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </span>
            <span class="text-sm font-medium text-gray-700">Booking Manual</span>
        </a>
        <a href="{{ route('admin.room-blocks.create') }}" class="flex items-center gap-3 bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition">
            <span class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </span>
            <span class="text-sm font-medium text-gray-700">Blokir Kamar</span>
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition">
            <span class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </span>
            <span class="text-sm font-medium text-gray-700">Semua Reservasi</span>
        </a>
        <a href="{{ route('admin.reports.revenue') }}" class="flex items-center gap-3 bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition">
            <span class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </span>
            <span class="text-sm font-medium text-gray-700">Laporan</span>
        </a>
    </div>

    {{-- Today's Schedule --}}
    <div class="grid md:grid-cols-2 gap-4">
        {{-- Check-in Today --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Check-in Hari Ini</h3>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $checkInsToday->count() > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">{{ $checkInsToday->count() }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($checkInsToday as $booking)
                <a href="{{ route('admin.bookings.show', $booking) }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $booking->guest_name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->room_name_snapshot }} · {{ $booking->nights }} mlm</p>
                    </div>
                    <span class="text-xs font-medium text-blue-600 flex-shrink-0 ml-2">Check-in →</span>
                </a>
                @empty
                <div class="px-4 py-6 text-center">
                    <p class="text-xs text-gray-400">Tidak ada check-in hari ini</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Check-out Today --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Check-out Hari Ini</h3>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $checkOutsToday->count() > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">{{ $checkOutsToday->count() }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($checkOutsToday as $booking)
                <a href="{{ route('admin.bookings.show', $booking) }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $booking->guest_name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->room_name_snapshot }}</p>
                    </div>
                    <span class="text-xs font-medium text-indigo-600 flex-shrink-0 ml-2">Check-out →</span>
                </a>
                @empty
                <div class="px-4 py-6 text-center">
                    <p class="text-xs text-gray-400">Tidak ada check-out hari ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Revenue --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Pendapatan Bulan Ini</p>
                <p class="text-xl font-bold text-gray-800 mt-1">Rp{{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
            </div>
            <a href="{{ route('admin.reports.revenue') }}" class="text-xs text-primary-600 hover:underline">Detail →</a>
        </div>
    </div>

    {{-- Recent Bookings --}}
    @if($recentBookings->count())
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Booking Terbaru</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs text-primary-600 hover:underline">Semua</a>
        </div>
        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-medium">Kode</th>
                        <th class="px-4 py-2.5 text-left font-medium">Tamu</th>
                        <th class="px-4 py-2.5 text-left font-medium">Kamar</th>
                        <th class="px-4 py-2.5 text-left font-medium">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-medium">Status</th>
                        <th class="px-4 py-2.5 text-right font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentBookings as $booking)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
                        <td class="px-4 py-2.5 font-mono text-xs text-gray-600">{{ $booking->booking_code }}</td>
                        <td class="px-4 py-2.5 text-gray-800">{{ $booking->guest_name }}</td>
                        <td class="px-4 py-2.5 text-gray-600">{{ $booking->room_name_snapshot }}</td>
                        <td class="px-4 py-2.5 text-gray-600">{{ $booking->check_in->format('d/m') }} - {{ $booking->check_out->format('d/m') }}</td>
                        <td class="px-4 py-2.5"><x-status-badge :status="$booking->status" /></td>
                        <td class="px-4 py-2.5 text-right text-gray-800">{{ $booking->formatted_total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-gray-50">
            @foreach($recentBookings as $booking)
            <a href="{{ route('admin.bookings.show', $booking) }}" class="block px-4 py-3 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-800">{{ $booking->guest_name }}</span>
                    <x-status-badge :status="$booking->status" />
                </div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-xs text-gray-500">{{ $booking->room_name_snapshot }} · {{ $booking->check_in->format('d/m') }}</span>
                    <span class="text-xs font-medium text-gray-700">{{ $booking->formatted_total }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
