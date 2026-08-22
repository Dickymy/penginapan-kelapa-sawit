@extends('layouts.admin')

@section('title', 'Reservasi')
@section('page-title', 'Reservasi')

@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Reservasi</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.bookings.export', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span class="hidden sm:inline">Unduh CSV</span>
            </a>
            <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Booking Manual</span>
                <span class="sm:hidden">Tambah</span>
            </a>
        </div>
    </div>

    {{-- Quick Filters (horizontal chips) --}}
    <div class="flex gap-2 overflow-x-auto pb-1 -mx-4 px-4 md:mx-0 md:px-0">
        <a href="{{ route('admin.bookings.index') }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-medium border transition {{ !request()->hasAny(['status','source','search']) ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400' }}">
            Semua
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'confirmed', 'check_in' => today()->toDateString()]) }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-medium border transition {{ request('check_in') == today()->toDateString() && request('status') == 'confirmed' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400' }}">
            Check-in Hari Ini
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'pending_payment']) }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-medium border transition {{ request('status') == 'pending_payment' && !request('check_in') ? 'bg-yellow-600 text-white border-yellow-600' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400' }}">
            Menunggu Bayar
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'checked_in']) }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-medium border transition {{ request('status') == 'checked_in' && !request('check_in') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400' }}">
            Sedang Menginap
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'completed']) }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-medium border transition {{ request('status') == 'completed' ? 'bg-gray-600 text-white border-gray-600' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400' }}">
            Selesai
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-medium border transition {{ request('status') == 'cancelled' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400' }}">
            Dibatalkan
        </a>
    </div>

    {{-- Search & Filters --}}
    <form method="GET" class="flex flex-col sm:flex-row gap-2">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, atau WhatsApp..."
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Status</option>
            @foreach(\App\Enums\BookingStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <select name="source" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Sumber</option>
            @foreach(\App\Enums\BookingSource::cases() as $source)
                <option value="{{ $source->value }}" @selected(request('source') === $source->value)>{{ $source->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Filter</button>
        @if(request()->hasAny(['status', 'source', 'search', 'check_in']))
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 text-center">Hapus</a>
        @endif
    </form>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-5 py-3.5 text-left text-[11px] font-semibold tracking-wider text-gray-500 uppercase">Kode</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-semibold tracking-wider text-gray-500 uppercase">Tamu</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-semibold tracking-wider text-gray-500 uppercase">Kamar</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-semibold tracking-wider text-gray-500 uppercase">Tanggal</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-semibold tracking-wider text-gray-500 uppercase">Reservasi</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-semibold tracking-wider text-gray-500 uppercase">Pembayaran</th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-semibold tracking-wider text-gray-500 uppercase">Sumber</th>
                    <th class="px-5 py-3.5 text-right text-[11px] font-semibold tracking-wider text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $booking)
                <tr class="hover:bg-primary-50/50 transition-colors cursor-pointer group" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
                    <td class="px-5 py-3.5 font-mono text-xs text-gray-600">{{ $booking->booking_code }}</td>
                    <td class="px-5 py-3.5 text-gray-800 font-medium">{{ $booking->guest_name }}</td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $booking->room_name_snapshot }}</td>
                    <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap">{{ $booking->check_in->format('d/m/Y') }} → {{ $booking->check_out->format('d/m/Y') }}</td>
                    <td class="px-5 py-3.5"><x-status-badge :status="$booking->status" /></td>
                    <td class="px-5 py-3.5"><x-status-badge :status="$booking->payment_status" /></td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $booking->source->label() }}</td>
                    <td class="px-5 py-3.5 text-right font-medium text-gray-800 group-hover:text-primary-700">{{ $booking->formatted_total }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center">
                        <x-empty-state message="Belum ada reservasi." :action="route('admin.bookings.create')" action-text="Buat Booking Manual" />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($bookings as $booking)
        <a href="{{ route('admin.bookings.show', $booking) }}" class="block bg-white rounded-2xl border border-gray-100 p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $booking->guest_name }}</p>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $booking->booking_code }}</p>
                </div>
                <x-status-badge :status="$booking->status" />
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                <span>{{ $booking->room_name_snapshot }}</span>
                <span>{{ $booking->check_in->format('d/m') }} → {{ $booking->check_out->format('d/m') }}</span>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <x-status-badge :status="$booking->payment_status" />
                <span class="text-sm font-semibold text-gray-800">{{ $booking->formatted_total }}</span>
            </div>
        </a>
        @empty
        <x-empty-state message="Belum ada reservasi." :action="route('admin.bookings.create')" action-text="Buat Booking Manual" />
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $bookings->withQueryString()->links() }}
    </div>
</div>
@endsection
