@extends('layouts.admin')

@section('title', 'Kamar Fisik - Admin')
@section('page-title', 'Kamar Fisik')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Kamar Fisik</h1>
        <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah Kamar</span>
            <span class="sm:hidden">Tambah</span>
        </a>
    </div>

    @if($rooms->isEmpty())
        <x-empty-state message="Belum ada kamar." :action="route('admin.rooms.create')" action-text="Tambah Kamar" />
    @else
    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aktif</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($rooms as $room)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $room->code }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $room->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $room->roomType->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <x-badge :type="$room->status === \App\Enums\RoomStatus::Active ? 'success' : ($room->status === \App\Enums\RoomStatus::Maintenance ? 'warning' : 'secondary')">
                                {{ $room->status->label() }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($room->is_active)
                                <span class="inline-flex w-2 h-2 rounded-full bg-green-500" title="Aktif"></span>
                            @else
                                <span class="inline-flex w-2 h-2 rounded-full bg-gray-300" title="Nonaktif"></span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.rooms.edit', $room) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                                <form action="{{ route('admin.rooms.toggle', $room) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
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
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @foreach($rooms as $room)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $room->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $room->code }} · {{ $room->roomType->name ?? '-' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-badge :type="$room->status === \App\Enums\RoomStatus::Active ? 'success' : ($room->status === \App\Enums\RoomStatus::Maintenance ? 'warning' : 'secondary')">
                        {{ $room->status->label() }}
                    </x-badge>
                    @if(!$room->is_active)
                        <span class="text-[10px] text-gray-400">(Nonaktif)</span>
                    @endif
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.rooms.edit', $room) }}" class="text-xs font-medium text-primary-600 hover:text-primary-800">Edit</a>
                <form action="{{ route('admin.rooms.toggle', $room) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs font-medium {{ $room->is_active ? 'text-yellow-600' : 'text-green-600' }}">
                        {{ $room->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
