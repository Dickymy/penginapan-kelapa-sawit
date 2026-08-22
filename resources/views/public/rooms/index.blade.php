@extends('layouts.public')

@section('title', 'Kamar - Penginapan Kelapa Sawit')

@section('content')
{{-- Page Hero --}}
<section class="bg-primary-700 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-bold">Tipe Kamar Kami</h1>
        <p class="mt-2 text-primary-100">Temukan kamar yang sesuai dengan kebutuhan Anda</p>
    </div>
</section>

@php
    $roomsJson = $roomTypes->map(function ($type) {
        $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first();
        return [
            'id' => $type->id,
            'name' => $type->name,
            'slug' => $type->slug,
            'base_price' => $type->base_price,
            'formatted_price' => number_format($type->base_price, 0, ',', '.'),
            'capacity' => $type->capacity,
            'bed_count' => $type->bed_count,
            'bed_type' => $type->bed_type ?? '',
            'short_description' => $type->short_description,
            'facilities' => $type->facilities->pluck('id')->toArray(),
            'facility_names' => $type->facilities->take(6)->map(fn($f) => $f->name)->toArray(),
            'more_facilities_count' => max(0, $type->facilities->count() - 6),
            'review_count' => $type->review_count ?? 0,
            'average_rating' => $type->average_rating ? number_format($type->average_rating, 1) : 0,
            'average_rating_raw' => $type->average_rating ?? 0,
            'active_room_count' => $type->rooms->count(),
            'cover_thumb' => $cover ? $cover->thumb_url : null,
            'cover_medium' => $cover ? $cover->medium_url : null,
            'cover_large' => $cover ? $cover->large_url : null,
            'url' => route('rooms.show', $type->slug),
        ];
    })->values()->toJson();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    {{-- Skeleton Loader (Shown before Alpine init) --}}
    <div id="rooms-skeleton" class="flex flex-col md:flex-row gap-8 items-start animate-pulse">
        <div class="w-full md:w-64 flex-shrink-0 bg-gray-100 rounded-xl h-96 hidden md:block"></div>
        <div class="flex-1 w-full space-y-6">
            <div class="h-8 bg-gray-100 rounded-lg w-1/3 mb-6"></div>
            @for($i=0; $i<3; $i++)
            <div class="bg-white rounded-2xl border border-gray-100 flex flex-col lg:flex-row overflow-hidden">
                <div class="lg:w-72 xl:w-80 h-64 lg:h-full bg-gray-200"></div>
                <div class="flex-1 p-5 md:p-6 space-y-4">
                    <div class="h-6 bg-gray-200 rounded w-1/2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mt-4"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    {{-- Alpine App --}}
    <div x-data="roomListApp()" x-cloak x-init="document.getElementById('rooms-skeleton').style.display = 'none'">
        <div class="flex flex-col md:flex-row gap-8 items-start">
        
        {{-- Mobile Filter Toggle --}}
        <div class="w-full md:hidden flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="font-bold text-gray-800">Filter & Sortir</span>
            <button @click="showFilters = !showFilters" class="flex items-center gap-2 text-primary-600 bg-primary-50 px-3 py-1.5 rounded-lg font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                Filter
            </button>
        </div>

        {{-- Sidebar Filters --}}
        <div class="w-full md:w-64 flex-shrink-0 bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm transition-all duration-300 md:block"
             :class="showFilters ? 'block' : 'hidden'">
            
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter Pencarian
                </h3>
                <button @click="resetFilters" class="text-xs text-primary-600 hover:text-primary-800 font-medium" x-show="hasActiveFilters">Reset</button>
            </div>

            <div class="p-5 space-y-6">
                {{-- Harga --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">Rentang Harga (Rp)</label>
                    <div class="space-y-3">
                        <div>
                            <input type="number" x-model.number="filter.minPrice" min="0" step="50000" placeholder="Minimum" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <input type="number" x-model.number="filter.maxPrice" min="0" step="50000" placeholder="Maksimum" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                    </div>
                </div>

                {{-- Kapasitas --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">Kapasitas Minimum</label>
                    <div class="flex items-center gap-3">
                        <input type="range" x-model.number="filter.minCapacity" min="1" max="10" step="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary-600">
                        <span class="text-sm font-medium text-gray-700 w-12 text-right" x-text="`${filter.minCapacity} org`"></span>
                    </div>
                </div>

                {{-- Fasilitas --}}
                @if($facilities->isNotEmpty())
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">Fasilitas Tersedia</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($facilities as $facility)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" value="{{ $facility->id }}" x-model="filter.facilities" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-gray-600 group-hover:text-gray-900">{{ $facility->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 w-full">
            
            {{-- Top Bar --}}
            <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
                <p class="text-gray-600">
                    Menampilkan <span class="font-bold text-gray-800" x-text="filteredAndSortedRooms.length"></span> dari <span x-text="rooms.length"></span> tipe kamar
                </p>
                
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-500 font-medium">Urutkan:</label>
                    <select x-model="sortBy" class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500 py-1.5 shadow-sm">
                        <option value="default">Rekomendasi</option>
                        <option value="price_asc">Harga Terendah</option>
                        <option value="price_desc">Harga Tertinggi</option>
                        <option value="capacity_desc">Kapasitas Terbesar</option>
                    </select>
                </div>
            </div>

            {{-- Room List --}}
            <div class="space-y-6 relative min-h-[400px]">
                
                <template x-for="type in filteredAndSortedRooms" :key="type.id">
                    <div x-transition.opacity.duration.300ms class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] hover:-translate-y-1 transition-all duration-300 flex flex-col lg:flex-row group">
                        
                        {{-- Image --}}
                        <div class="lg:w-72 xl:w-80 flex-shrink-0 relative overflow-hidden">
                            <template x-if="type.cover_medium">
                                <img :src="type.cover_medium"
                                     :alt="type.name"
                                     loading="lazy"
                                     decoding="async"
                                     class="w-full h-64 lg:h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </template>
                            <template x-if="!type.cover_medium">
                                <div class="w-full h-64 lg:h-full min-h-[240px] flex flex-col items-center justify-center bg-gradient-to-br from-primary-50 to-gray-100">
                                    <div class="flex flex-col items-center text-primary-300 gap-3">
                                        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2" stroke-width="1.5"/>
                                            <path d="M16 3H8L2 7h20l-6-4z" stroke-width="1.5"/>
                                            <path d="M12 11v6M9 14h6" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                        <span class="text-xs font-medium text-primary-400 tracking-wide">Foto belum tersedia</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        {{-- Info --}}
                        <div class="flex-1 p-5 md:p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h2 class="text-xl font-bold text-gray-800" x-text="type.name"></h2>
                                    
                                    <template x-if="type.review_count > 0">
                                        <div class="flex items-center bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                            <span class="text-sm font-bold text-gray-900 mr-1" x-text="type.average_rating"></span>
                                            <!-- SVG Star inline to avoid partial component logic inside Alpine template -->
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span class="text-xs text-gray-500 ml-1" x-text="`(${type.review_count})`"></span>
                                        </div>
                                    </template>
                                    <template x-if="type.review_count === 0">
                                        <span class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Belum ada ulasan</span>
                                    </template>
                                </div>
                                
                                <template x-if="type.active_room_count <= 3 && type.active_room_count > 0">
                                    <div class="mb-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                            <svg class="mr-1.5 h-3.5 w-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Hanya sisa <span x-text="type.active_room_count" class="mx-1"></span> kamar!
                                        </span>
                                    </div>
                                </template>
                                
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span x-text="`${type.capacity} orang`"></span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        <span x-text="`${type.bed_count} tempat tidur ${type.bed_type}`"></span>
                                    </span>
                                </div>
                                
                                <template x-if="type.short_description">
                                    <p class="text-gray-600 text-sm mb-3" x-text="type.short_description"></p>
                                </template>
                                
                                <template x-if="type.facility_names.length > 0">
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <template x-for="fac in type.facility_names">
                                            <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs rounded-md" x-text="fac"></span>
                                        </template>
                                        <template x-if="type.more_facilities_count > 0">
                                            <span class="inline-flex items-center px-2.5 py-1 text-gray-400 text-xs" x-text="`+${type.more_facilities_count} lainnya`"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6 pt-5 border-t border-gray-50">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-medium text-white bg-red-500 px-1.5 py-0.5 rounded">Hemat 20%</span>
                                        <span class="text-sm text-gray-400 line-through decoration-gray-300" x-text="`Rp${new Intl.NumberFormat('id-ID').format(type.base_price * 1.25)}`"></span>
                                    </div>
                                    <span class="text-primary-600 font-extrabold text-2xl" x-text="`Rp${type.formatted_price}`"></span>
                                    <span class="text-sm font-medium text-gray-400">/ malam</span>
                                </div>
                                <a :href="type.url" class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-50 text-primary-700 rounded-xl text-sm font-bold hover:bg-primary-600 hover:text-white transition-colors duration-300">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty State --}}
                <div x-show="filteredAndSortedRooms.length === 0" x-transition.opacity.duration.300ms class="absolute inset-0 z-10 w-full h-full bg-white flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Tidak ada kamar yang sesuai</h3>
                    <p class="text-gray-500 mt-2 max-w-md">Kriteria filter yang Anda terapkan tidak cocok dengan tipe kamar manapun. Silakan sesuaikan kembali filter Anda.</p>
                    <button @click="resetFilters" class="mt-4 px-4 py-2 bg-primary-50 text-primary-700 rounded-lg font-medium hover:bg-primary-100 transition">Reset Filter</button>
                </div>
            </div>
            
        </div>
    </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9; 
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1; 
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8; 
}
</style>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roomListApp', () => ({
        rooms: {!! $roomsJson !!},
        showFilters: false,
        sortBy: 'default',
        filter: {
            minPrice: null,
            maxPrice: null,
            minCapacity: 1,
            facilities: []
        },

        get hasActiveFilters() {
            return this.filter.minPrice > 0 || 
                   this.filter.maxPrice > 0 || 
                   this.filter.minCapacity > 1 || 
                   this.filter.facilities.length > 0;
        },

        resetFilters() {
            this.filter.minPrice = null;
            this.filter.maxPrice = null;
            this.filter.minCapacity = 1;
            this.filter.facilities = [];
            this.sortBy = 'default';
        },

        get filteredAndSortedRooms() {
            let result = this.rooms.filter(room => {
                // Harga
                if (this.filter.minPrice && room.base_price < this.filter.minPrice) return false;
                if (this.filter.maxPrice && room.base_price > this.filter.maxPrice) return false;
                
                // Kapasitas
                if (room.capacity < this.filter.minCapacity) return false;
                
                // Fasilitas (Harus punya SEMUA fasilitas yang dicentang)
                if (this.filter.facilities.length > 0) {
                    const hasAll = this.filter.facilities.every(fid => room.facilities.includes(parseInt(fid)));
                    if (!hasAll) return false;
                }
                
                return true;
            });

            // Sorting
            if (this.sortBy === 'price_asc') {
                result.sort((a, b) => a.base_price - b.base_price);
            } else if (this.sortBy === 'price_desc') {
                result.sort((a, b) => b.base_price - a.base_price);
            } else if (this.sortBy === 'capacity_desc') {
                result.sort((a, b) => b.capacity - a.capacity);
            } else {
                // Default (bawaan urutan dari controller)
                // Karena kita butuh original order, kita tidak melakukan re-sort. 
                // Atau bisa sort berdasarkan id jika teracak
                result.sort((a, b) => a.id - b.id);
            }

            return result;
        }
    }));
});
</script>
@endpush
@endsection
