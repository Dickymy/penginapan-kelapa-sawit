@extends('layouts.admin')

@section('title', 'Detail Lokasi Sekitar')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.nearby-places.index') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $nearbyPlace->name }}</h1>
    </div>
    <div class="ml-auto flex items-center gap-3">
        <a href="{{ route('admin.nearby-places.edit', $nearbyPlace) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-transparent rounded-lg font-medium text-indigo-700 hover:bg-indigo-100 transition">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            Edit
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 ">
    <div class="md:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($nearbyPlace->image)
                <img src="{{ Storage::url($nearbyPlace->image) }}" alt="{{ $nearbyPlace->name }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                    <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            @endif
            <div class="p-4 border-t border-gray-100">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</h3>
                @if($nearbyPlace->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Nonaktif
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="md:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-medium text-gray-900">Informasi Detail</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ $nearbyPlace->category }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jarak</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ $nearbyPlace->distance ?: 'Tidak ada informasi' }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi Singkat</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ $nearbyPlace->description ?: 'Tidak ada informasi' }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Link Google Maps</h4>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($nearbyPlace->map_link)
                            <a href="{{ $nearbyPlace->map_link }}" target="_blank" class="text-primary-600 hover:text-primary-800 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                {{ $nearbyPlace->map_link }}
                            </a>
                        @else
                            Tidak ada informasi
                        @endif
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan Tampil</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ $nearbyPlace->sort_order }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
