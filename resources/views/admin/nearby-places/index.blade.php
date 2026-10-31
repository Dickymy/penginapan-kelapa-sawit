@extends('layouts.admin')

@section('title', 'Toko Sembako - Panel Admin')

@section('content')
<div class="pb-16 sm:pb-8"
     x-data="{ 
    searchQuery: '', 
    selectedCategory: '', 
    selectedStatus: '', 
    viewMode: 'grid',
    zoomImage: null,
    zoomTitle: '',
    matches(name, category, isActive) {
        const q = this.searchQuery.toLowerCase().trim();
        const matchesQuery = !q || (name && name.toLowerCase().includes(q)) || (category && category.toLowerCase().includes(q));
        const matchesCat = !this.selectedCategory || category === this.selectedCategory;
        const matchesStat = !this.selectedStatus || (this.selectedStatus === 'active' ? isActive : !isActive);
        return matchesQuery && matchesCat && matchesStat;
    }
}">

    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Katalog & Etalase Toko
                </span>
                <span class="text-xs text-gray-400">•</span>
                <span class="text-xs text-gray-500">Toko Sembako Arkhan</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Katalog Toko Sembako</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola foto produk, harga, ketersediaan, dan etalase toko sembako yang tampil di website publik.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('nearby-places') }}" target="_blank" rel="noopener" 
               class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 shadow-sm transition-all duration-200 group">
                <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-primary-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Lihat di Web Publik
            </a>
            <a href="{{ route('admin.nearby-places.create') }}" 
               class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm shadow-sm hover:shadow-md transition-all duration-200 transform active:scale-95">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk / Foto
            </a>
        </div>
    </div>

    {{-- Summary KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Produk --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Item</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $totalCount ?? $places->total() }}</p>
            </div>
        </div>

        {{-- Produk Aktif --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Aktif di Web</p>
                <p class="text-2xl font-bold text-green-600 mt-0.5">{{ $activeCount ?? $places->where('is_active', true)->count() }}</p>
            </div>
        </div>

        {{-- Disembunyikan --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nonaktif</p>
                <p class="text-2xl font-bold text-gray-700 mt-0.5">{{ $inactiveCount ?? $places->where('is_active', false)->count() }}</p>
            </div>
        </div>

        {{-- Kategori Terdaftar --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</p>
                <p class="text-2xl font-bold text-blue-600 mt-0.5">{{ isset($categories) ? $categories->count() : '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Filter, Search & View Controls --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Search Input --}}
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" x-model="searchQuery" placeholder="Cari nama produk, harga, atau kategori..." 
                   class="w-full pl-10 pr-9 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
            <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Filter Dropdowns & View Toggle --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Category Filter --}}
            @if(isset($categories) && $categories->isNotEmpty())
                <select x-model="selectedCategory" 
                        class="text-xs sm:text-sm bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-emerald-200 text-gray-700 font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Status Filter --}}
            <select x-model="selectedStatus" 
                    class="text-xs sm:text-sm bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-emerald-200 text-gray-700 font-medium">
                <option value="">Semua Status</option>
                <option value="active">Aktif di Web</option>
                <option value="inactive">Nonaktif</option>
            </select>

            {{-- View Mode Toggle --}}
            <div class="inline-flex rounded-xl p-1 bg-gray-100 border border-gray-200/80">
                <button type="button" @click="viewMode = 'grid'" 
                        :class="viewMode === 'grid' ? 'bg-white text-emerald-700 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Grid Foto
                </button>
                <button type="button" @click="viewMode = 'table'" 
                        :class="viewMode === 'table' ? 'bg-white text-emerald-700 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    Tabel
                </button>
            </div>
        </div>
    </div>

    {{-- Content Display --}}
    @if($places->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 text-center max-w-lg mx-auto">
            <div class="w-20 h-20 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Produk / Foto Toko Sembako</h3>
            <p class="text-sm text-gray-500 mb-6">Mulai tambahkan produk sembako, minuman, cemilan, atau foto etalase toko Anda ke sistem.</p>
            <a href="{{ route('admin.nearby-places.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm shadow-md transition">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Produk Pertama
            </a>
        </div>
    @else
        {{-- 1. GRID VIEW --}}
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($places as $place)
                <div x-show="matches('{{ addslashes($place->name) }}', '{{ addslashes($place->category) }}', {{ $place->is_active ? 'true' : 'false' }})"
                     class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all duration-200 overflow-hidden flex flex-col group">
                    
                    {{-- Image Container with Zoom Trigger --}}
                    <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                        @if($place->image)
                            <img src="{{ Storage::url($place->image) }}" alt="{{ $place->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            {{-- Click to zoom overlay --}}
                            <button type="button" @click="zoomImage = '{{ Storage::url($place->image) }}'; zoomTitle = '{{ addslashes($place->name) }}'" 
                                    class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white"
                                    title="Klik untuk perbesar foto">
                                <div class="bg-black/60 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                    Perbesar
                                </div>
                            </button>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[11px] font-medium text-gray-400">Tidak ada foto</span>
                            </div>
                        @endif

                        {{-- Category Badge --}}
                        <div class="absolute top-2.5 left-2.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/90 backdrop-blur-md text-emerald-800 shadow-sm border border-emerald-100">
                                {{ $place->category }}
                            </span>
                        </div>

                        {{-- Status Badge --}}
                        <div class="absolute top-2.5 right-2.5">
                            @if($place->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-500/90 backdrop-blur-md text-white shadow-sm">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-600/90 backdrop-blur-md text-white shadow-sm">
                                    Nonaktif
                                </span>
                            @endif
                        </div>

                        {{-- Sort Order Badge --}}
                        <div class="absolute bottom-2.5 left-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-black/60 backdrop-blur-md text-white">
                                Urutan: #{{ $place->sort_order }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2" title="{{ $place->name }}">
                                {{ $place->name }}
                            </h3>
                            
                            {{-- Price / Extra Info Tag --}}
                            @if($place->distance)
                                <div class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100/60">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <span>{{ $place->distance }}</span>
                                </div>
                            @endif

                            {{-- Description preview --}}
                            @if($place->description)
                                <p class="mt-2 text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                    {{ $place->description }}
                                </p>
                            @endif
                        </div>

                        {{-- Card Actions --}}
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between gap-1">
                            <a href="{{ route('admin.nearby-places.show', $place) }}" 
                               class="text-xs font-semibold text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 p-2 rounded-lg transition" title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.nearby-places.edit', $place) }}" 
                               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <button type="button" @click="$dispatch('open-confirm', { id: 'delete-grid-{{ $place->id }}' })" 
                                    class="text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus Produk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            <x-confirm-modal 
                                id="delete-grid-{{ $place->id }}"
                                title="Hapus Produk Toko Sembako" 
                                message="Apakah Anda yakin ingin menghapus produk '{{ $place->name }}' ini?"
                                confirm-text="Ya, Hapus"
                                cancel-text="Batal"
                                variant="danger"
                                form-action="{{ route('admin.nearby-places.destroy', $place) }}"
                                method="DELETE"
                            />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 2. TABLE VIEW --}}
        <div x-show="viewMode === 'table'" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Urutan</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Foto & Nama Produk</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga / Info</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($places as $place)
                            <tr x-show="matches('{{ addslashes($place->name) }}', '{{ addslashes($place->category) }}', {{ $place->is_active ? 'true' : 'false' }})"
                                class="hover:bg-gray-50/60 transition">
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-gray-400">
                                    #{{ $place->sort_order }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 relative group/img cursor-pointer"
                                             @click="zoomImage = '{{ $place->image ? Storage::url($place->image) : '' }}'; zoomTitle = '{{ addslashes($place->name) }}'">
                                            @if($place->image)
                                                <img class="h-12 w-12 object-cover" src="{{ Storage::url($place->image) }}" alt="">
                                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover/img:opacity-100 transition flex items-center justify-center text-white">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                </div>
                                            @else
                                                <div class="h-12 w-12 flex items-center justify-center text-gray-300">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3.5">
                                            <div class="text-sm font-bold text-gray-900">{{ $place->name }}</div>
                                            @if($place->description)
                                                <div class="text-xs text-gray-500 line-clamp-1 max-w-xs">{{ $place->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-100">
                                        {{ $place->category }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-gray-600">
                                    {{ $place->distance ?: '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($place->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-1.5">
                                        <a href="{{ route('admin.nearby-places.show', $place) }}" class="text-gray-500 hover:text-emerald-700 hover:bg-emerald-50 p-2 rounded-lg transition" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <a href="{{ route('admin.nearby-places.edit', $place) }}" class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 p-2 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <button type="button" @click="$dispatch('open-confirm', { id: 'delete-table-{{ $place->id }}' })" class="text-red-600 hover:text-red-900 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                        <x-confirm-modal 
                                            id="delete-table-{{ $place->id }}"
                                            title="Hapus Produk Toko Sembako" 
                                            message="Apakah Anda yakin ingin menghapus produk '{{ $place->name }}' ini?"
                                            confirm-text="Ya, Hapus"
                                            cancel-text="Batal"
                                            variant="danger"
                                            form-action="{{ route('admin.nearby-places.destroy', $place) }}"
                                            method="DELETE"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Pagination --}}
    @if($places->hasPages())
        <div class="mt-6">
            {{ $places->links() }}
        </div>
    @endif

    {{-- Interactive Lightbox / Zoom Modal --}}
    <div x-show="zoomImage" x-cloak 
         @keydown.escape.window="zoomImage = null"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="relative max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl"
             @click.outside="zoomImage = null">
            
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-900 text-sm sm:text-base truncate" x-text="zoomTitle"></h3>
                <button type="button" @click="zoomImage = null" class="p-2 text-gray-400 hover:text-gray-700 rounded-xl hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-2 sm:p-4 bg-gray-950 flex items-center justify-center max-h-[75vh] overflow-hidden">
                <img :src="zoomImage" class="max-h-[70vh] w-auto max-w-full object-contain rounded-xl shadow-lg">
            </div>

            <div class="p-3 bg-gray-50 flex justify-end">
                <button type="button" @click="zoomImage = null" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
