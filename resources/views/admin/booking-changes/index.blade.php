@extends('layouts.admin')

@section('title', 'Permintaan Perubahan Booking')
@section('page-title', 'Permintaan Perubahan Booking')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Permintaan Perubahan Booking</h1>
    <p class="text-sm text-gray-500 mt-1">Kelola permintaan perubahan jadwal atau kamar dari tamu.</p>
</div>

@if($requests->isEmpty())
    <x-empty-state message="Belum ada permintaan perubahan." />
@else
    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Booking</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tamu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe Perubahan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selisih Harga</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diajukan Pada</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($requests as $req)
                    <tr class="hover:bg-gray-50 border-l-4 {{ $req->status === 'pending' ? 'border-yellow-400 bg-yellow-50/30' : 'border-transparent' }} transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.bookings.show', $req->booking) }}" class="font-mono text-xs font-bold text-primary-600 hover:text-primary-800">
                                {{ $req->booking->booking_code }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $req->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $req->user->email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $typeLabel = match($req->type) {
                                    'reschedule' => 'Reschedule',
                                    'room_change' => 'Ubah Kamar',
                                    'guest_update' => 'Ubah Tamu',
                                    'cancellation' => 'Pembatalan',
                                    default => ucfirst(str_replace('_', ' ', $req->type))
                                };
                            @endphp
                            <span class="font-medium text-gray-700 bg-gray-100 px-2.5 py-1 rounded-md text-xs border border-gray-200">{{ $typeLabel }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($req->price_difference > 0)
                                <span class="text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded text-xs">+ Rp{{ number_format($req->price_difference, 0, ',', '.') }}</span>
                            @elseif($req->price_difference < 0)
                                <span class="text-amber-600 font-semibold bg-amber-50 px-2 py-0.5 rounded text-xs">- Rp{{ number_format(abs($req->price_difference), 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400 text-xs font-medium">Rp0</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($req->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                            @elseif($req->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($req->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $req->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.booking-changes.show', $req) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition">
                                @if($req->status === 'pending') Proses @else Detail @endif
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile View --}}
    <div class="md:hidden space-y-4">
        @foreach($requests as $req)
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <a href="{{ route('admin.bookings.show', $req->booking) }}" class="font-mono text-sm font-bold text-primary-600 hover:text-primary-800">
                        {{ $req->booking->booking_code }}
                    </a>
                    <div class="text-xs text-gray-500 mt-1">{{ $req->created_at->format('d M Y H:i') }}</div>
                </div>
                <div>
                    @if($req->status === 'pending')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($req->status === 'approved')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                    @elseif($req->status === 'rejected')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($req->status) }}</span>
                    @endif
                </div>
            </div>
            
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tamu:</span>
                    <span class="font-medium text-gray-800">{{ $req->user->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Perubahan:</span>
                    @php
                        $typeLabel = match($req->type) {
                            'reschedule' => 'Reschedule',
                            'room_change' => 'Ubah Kamar',
                            'guest_update' => 'Ubah Tamu',
                            'cancellation' => 'Pembatalan',
                            default => ucfirst(str_replace('_', ' ', $req->type))
                        };
                    @endphp
                    <span class="font-medium text-gray-800">{{ $typeLabel }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Selisih:</span>
                    @if($req->price_difference > 0)
                        <span class="text-red-600 font-medium">+ Rp{{ number_format($req->price_difference, 0, ',', '.') }}</span>
                    @elseif($req->price_difference < 0)
                        <span class="text-primary-600 font-medium">- Rp{{ number_format(abs($req->price_difference), 0, ',', '.') }}</span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </div>
            </div>
            
            <a href="{{ route('admin.booking-changes.show', $req) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                @if($req->status === 'pending') Proses Permintaan @else Lihat Detail @endif
            </a>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $requests->links() }}
    </div>
@endif

@endsection
