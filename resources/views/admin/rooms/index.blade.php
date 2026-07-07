@extends('layouts.admin')

@section('title', 'Kamar - Admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Kamar</h1>
    <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
        + Tambah Kamar
    </a>
</div>

@if($rooms->isEmpty())
    <x-empty-state message="Belum ada kamar." />
@else
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Kode</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Tipe</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Aktif</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($rooms as $room)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-gray-700">{{ $room->code }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $room->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $room->roomType->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    <x-badge :type="$room->status === \App\Enums\RoomStatus::Active ? 'success' : ($room->status === \App\Enums\RoomStatus::Maintenance ? 'warning' : 'secondary')">
                        {{ $room->status->label() }}
                    </x-badge>
                </td>
                <td class="px-4 py-3">
                    @if($room->is_active)
                        <x-badge type="success">Ya</x-badge>
                    @else
                        <x-badge type="secondary">Tidak</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.rooms.edit', $room) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                        <form action="{{ route('admin.rooms.toggle', $room) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-medium {{ $room->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}">
                                {{ $room->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
