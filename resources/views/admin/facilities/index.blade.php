@extends('layouts.admin')

@section('title', 'Fasilitas - Admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Fasilitas</h1>
    <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
        + Tambah Fasilitas
    </a>
</div>

@if($facilities->isEmpty())
    <x-empty-state message="Belum ada fasilitas." />
@else
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Slug</th>
                <th class="px-4 py-3">Icon</th>
                <th class="px-4 py-3">Tipe Kamar</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($facilities as $facility)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $facility->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $facility->slug }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $facility->icon ?? '-' }}</td>
                <td class="px-4 py-3">{{ $facility->room_types_count }}</td>
                <td class="px-4 py-3">
                    @if($facility->is_active)
                        <x-badge type="success">Aktif</x-badge>
                    @else
                        <x-badge type="secondary">Nonaktif</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.facilities.edit', $facility) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                        @if($facility->room_types_count === 0)
                        <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST" class="inline"
                              x-data="{ confirming: false }">
                            @csrf
                            @method('DELETE')
                            <button type="button" x-show="!confirming" @click="confirming = true" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                            <span x-show="confirming" x-cloak class="inline-flex items-center gap-1">
                                <button type="submit" class="text-red-700 font-medium text-xs hover:underline">Ya, hapus</button>
                                <button type="button" @click="confirming = false" class="text-gray-500 text-xs hover:underline">Batal</button>
                            </span>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
