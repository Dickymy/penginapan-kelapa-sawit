@extends('layouts.admin')

@section('title', 'Tipe Kamar - Admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tipe Kamar</h1>
    <a href="{{ route('admin.room-types.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
        + Tambah Tipe Kamar
    </a>
</div>

@if($roomTypes->isEmpty())
    <x-empty-state message="Belum ada tipe kamar." />
@else
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Slug</th>
                <th class="px-4 py-3">Harga</th>
                <th class="px-4 py-3">Kapasitas</th>
                <th class="px-4 py-3">Kamar</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($roomTypes as $type)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $type->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $type->slug }}</td>
                <td class="px-4 py-3">Rp {{ number_format($type->base_price, 0, ',', '.') }}</td>
                <td class="px-4 py-3">{{ $type->capacity }} orang</td>
                <td class="px-4 py-3">{{ $type->rooms_count }}</td>
                <td class="px-4 py-3">
                    @if($type->is_active)
                        <x-badge type="success">Aktif</x-badge>
                    @else
                        <x-badge type="secondary">Nonaktif</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.room-types.edit', $type) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                        <form action="{{ route('admin.room-types.toggle', $type) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-medium {{ $type->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}">
                                {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
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
