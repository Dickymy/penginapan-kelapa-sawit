@extends('layouts.admin')

@section('title', 'Detail Produk - ' . $nearbyPlace->name)

@section('content')
<div x-data="{ zoomOpen: false }">
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.nearby-places.index') }}" 
               class="p-2 -ml-2 text-gray-400 hover:text-gray-700 rounded-xl hover:bg-gray-100 transition" 
               title="Kembali ke Daftar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">Toko Sembako Arkhan</span>
                    <span class="text-xs text-gray-400">•</span>
                    <span class="text-xs text-gray-500">Detail Produk</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $nearbyPlace->name }}</h1>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('nearby-places') }}" target="_blank" rel="noopener" 
               class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-200 rounded-xl font-medium text-xs text-gray-700 hover:bg-gray-50 hover:text-primary-600 shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                Lihat di Web
            </a>
            <a href="{{ route('admin.nearby-places.edit', $nearbyPlace) }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-transparent rounded-xl font-semibold text-xs text-indigo-700 hover:bg-indigo-100 transition">
                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Edit Produk
            </a>
            <button type="button" @click="$dispatch('open-confirm', { id: 'delete-show-{{ $nearbyPlace->id }}' })" 
                    class="inline-flex items-center px-3.5 py-2 bg-red-50 text-red-700 hover:bg-red-100 rounded-xl text-xs font-semibold transition">
                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Hapus
            </button>
            <x-confirm-modal 
                id="delete-show-{{ $nearbyPlace->id }}"
                title="Hapus Produk Toko Sembako" 
                message="Apakah Anda yakin ingin menghapus produk '{{ $nearbyPlace->name }}' ini?"
                confirm-text="Ya, Hapus"
                cancel-text="Batal"
                variant="danger"
                form-action="{{ route('admin.nearby-places.destroy', $nearbyPlace) }}"
                method="DELETE"
            />
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Photo Card (5 cols) --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden group cursor-pointer"
                     @click="zoomOpen = true">
                    @if($nearbyPlace->image)
                        <img src="{{ Storage::url($nearbyPlace->image) }}" alt="{{ $nearbyPlace->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                            <span class="bg-black/60 backdrop-blur-sm px-3.5 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Klik untuk Perbesar
                            </span>
                        </div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                            <svg class="h-14 w-14 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-xs text-gray-400">Belum ada foto</span>
                        </div>
                    @endif
                </div>

                <div class="p-4 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] text-gray-400 font-medium">Status Publikasi</p>
                        @if($nearbyPlace->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                Aktif di Website
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                Nonaktif (Disembunyikan)
                            </span>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-gray-400 font-medium">Urutan Katalog</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">#{{ $nearbyPlace->sort_order }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details Specifications Card (7 cols) --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">Spesifikasi & Informasi Produk</h3>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Produk</h4>
                            <p class="mt-1 text-base font-bold text-gray-900">{{ $nearbyPlace->name }}</p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</h4>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-100">
                                    {{ $nearbyPlace->category }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Harga / Info Kemasan</h4>
                            <p class="mt-1 text-sm font-semibold text-emerald-700">
                                {{ $nearbyPlace->distance ?: 'Tidak ada label harga/info' }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Urutan Tampil</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">Urutan ke-{{ $nearbyPlace->sort_order }}</p>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Deskripsi Produk</h4>
                        @if($nearbyPlace->description)
                            <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-700 leading-relaxed">
                                {{ $nearbyPlace->description }}
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Tidak ada deskripsi tambahan.</p>
                        @endif
                    </div>

                    <div class="pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-500">
                        <div>
                            <span class="text-gray-400 block font-medium">Ditambahkan pada:</span>
                            <span class="font-semibold text-gray-700">{{ $nearbyPlace->created_at ? $nearbyPlace->created_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium">Terakhir diperbarui:</span>
                            <span class="font-semibold text-gray-700">{{ $nearbyPlace->updated_at ? $nearbyPlace->updated_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fullscreen Zoom Lightbox --}}
    @if($nearbyPlace->image)
        <div x-show="zoomOpen" x-cloak 
             @keydown.escape.window="zoomOpen = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="relative max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl"
                 @click.outside="zoomOpen = false">
                <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base">{{ $nearbyPlace->name }}</h3>
                    <button type="button" @click="zoomOpen = false" class="p-2 text-gray-400 hover:text-gray-700 rounded-xl hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-2 sm:p-4 bg-gray-950 flex items-center justify-center max-h-[75vh]">
                    <img src="{{ Storage::url($nearbyPlace->image) }}" class="max-h-[70vh] w-auto max-w-full object-contain rounded-xl shadow-lg">
                </div>
                <div class="p-3 bg-gray-50 flex justify-end">
                    <button type="button" @click="zoomOpen = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
