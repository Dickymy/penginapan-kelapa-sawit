@extends('layouts.admin')

@section('title', 'Tipe Kamar - Admin')
@section('page-title', 'Tipe Kamar')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Tipe Kamar</h1>
        <a href="{{ route('admin.room-types.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah Tipe</span>
            <span class="sm:hidden">Tambah</span>
        </a>
    </div>

    @if($roomTypes->isEmpty())
        <x-empty-state message="Belum ada tipe kamar." :action="route('admin.room-types.create')" action-text="Tambah Tipe Kamar" />
    @else
    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga/Malam</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kapasitas</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kamar</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($roomTypes as $type)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @php $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first(); @endphp
                                @if($cover)
                                <img src="{{ $cover->thumb_url }}" alt="" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                @else
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $type->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $type->bed_count }} {{ $type->bed_type ?? 'tempat tidur' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800">Rp{{ number_format($type->base_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $type->capacity }} org</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $type->rooms_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($type->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.room-types.edit', $type) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                                <form action="{{ route('admin.room-types.toggle', $type) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
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
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @foreach($roomTypes as $type)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                @php $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first(); @endphp
                @if($cover)
                <img src="{{ $cover->thumb_url }}" alt="" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                @else
                <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-800">{{ $type->name }}</p>
                        @if($type->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $type->capacity }} tamu · {{ $type->rooms_count }} kamar · {{ $type->bed_count }} {{ $type->bed_type ?? 'bed' }}</p>
                    <p class="text-sm font-bold text-primary-600 mt-1">Rp{{ number_format($type->base_price, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">/ malam</span></p>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.room-types.edit', $type) }}" class="text-xs font-medium text-primary-600 hover:text-primary-800">Edit</a>
                <form action="{{ route('admin.room-types.toggle', $type) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs font-medium {{ $type->is_active ? 'text-yellow-600' : 'text-green-600' }}">
                        {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
