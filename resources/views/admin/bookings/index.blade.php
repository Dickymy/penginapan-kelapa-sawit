@extends('layouts.admin')

@section('title', 'Kelola Booking')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Booking</h1>
        <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-sm font-medium">
            + Booking Manual
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 rounded-md border-gray-300 text-sm">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\BookingStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Sumber</label>
            <select name="source" class="mt-1 rounded-md border-gray-300 text-sm">
                <option value="">Semua Sumber</option>
                @foreach(\App\Enums\BookingSource::cases() as $source)
                    <option value="{{ $source->value }}" @selected(request('source') === $source->value)>{{ $source->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm hover:bg-gray-200">Filter</button>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tamu</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr>
                    <td class="px-4 py-3 text-sm font-mono">{{ $booking->booking_code }}</td>
                    <td class="px-4 py-3 text-sm">{{ $booking->guest_name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $booking->room?->name ?? $booking->room_name_snapshot }}</td>
                    <td class="px-4 py-3 text-sm">{{ $booking->check_in->format('d/m/Y') }} - {{ $booking->check_out->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <x-status-badge :status="$booking->status" />
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $booking->source->label() }}</td>
                    <td class="px-4 py-3 text-sm">{{ $booking->formatted_total }}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 hover:text-primary-800">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada booking.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $bookings->withQueryString()->links() }}
</div>
@endsection
