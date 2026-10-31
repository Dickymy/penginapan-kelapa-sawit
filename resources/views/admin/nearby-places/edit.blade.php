@extends('layouts.admin')

@section('title', 'Edit Produk - ' . $nearbyPlace->name)

@section('content')
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
                <span class="text-xs text-gray-500">Edit Data</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit: {{ $nearbyPlace->name }}</h1>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.nearby-places.show', $nearbyPlace) }}" class="px-4 py-2 border border-gray-200 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
            Lihat Detail
        </a>
        <a href="{{ route('admin.nearby-places.index') }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
            Batal
        </a>
    </div>
</div>

<form action="{{ route('admin.nearby-places.update', $nearbyPlace) }}" method="POST" enctype="multipart/form-data"
      class="pb-16 sm:pb-8"
      x-data="{ 
          categoryVal: '{{ old('category', $nearbyPlace->category) }}',
          setCategory(cat) {
              this.categoryVal = cat;
              $refs.categoryInput.value = cat;
          }
      }">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Left Column: Form Details (7 cols) --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-7 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Perbarui Informasi Produk
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Ubah nama, kategori, harga, atau keterangan produk toko sembako ini.</p>
                </div>

                {{-- Nama Produk / Foto --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-800">
                        Nama Produk / Foto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $nearbyPlace->name) }}" required
                           class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('name') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori with Preset Chips --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="category" class="block text-sm font-semibold text-gray-800">
                            Kategori Produk <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs text-gray-400">Pilih cepat atau ketik sendiri</span>
                    </div>

                    {{-- Quick Category Preset Pills --}}
                    <div class="flex flex-wrap gap-1.5 mb-2.5">
                        <button type="button" @click="setCategory('Beras & Pokok')" 
                                :class="categoryVal === 'Beras & Pokok' ? 'bg-emerald-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                class="px-2.5 py-1 rounded-lg text-xs transition">
                            🌾 Beras & Pokok
                        </button>
                        <button type="button" @click="setCategory('Minyak & Bumbu')" 
                                :class="categoryVal === 'Minyak & Bumbu' ? 'bg-emerald-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                class="px-2.5 py-1 rounded-lg text-xs transition">
                            🍳 Minyak & Bumbu
                        </button>
                        <button type="button" @click="setCategory('Minuman')" 
                                :class="categoryVal === 'Minuman' ? 'bg-emerald-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                class="px-2.5 py-1 rounded-lg text-xs transition">
                            🥤 Minuman & Kopi
                        </button>
                        <button type="button" @click="setCategory('Snack & Makanan Ringan')" 
                                :class="categoryVal === 'Snack & Makanan Ringan' ? 'bg-emerald-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                class="px-2.5 py-1 rounded-lg text-xs transition">
                            🍪 Snack & Biskuit
                        </button>
                        <button type="button" @click="setCategory('Perlengkapan Mandi')" 
                                :class="categoryVal === 'Perlengkapan Mandi' ? 'bg-emerald-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                class="px-2.5 py-1 rounded-lg text-xs transition">
                            🧼 Sabun & Mandi
                        </button>
                        <button type="button" @click="setCategory('Gas & Galon')" 
                                :class="categoryVal === 'Gas & Galon' ? 'bg-emerald-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                class="px-2.5 py-1 rounded-lg text-xs transition">
                            ⚡ Gas & Galon
                        </button>
                        <button type="button" @click="setCategory('Foto Toko & Etalase')" 
                                :class="categoryVal === 'Foto Toko & Etalase' ? 'bg-emerald-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                class="px-2.5 py-1 rounded-lg text-xs transition">
                            🏪 Foto Toko
                        </button>
                    </div>

                    <input type="text" name="category" id="category" x-ref="categoryInput" x-model="categoryVal" required
                           placeholder="Contoh: Beras & Pokok, Minuman, Snack, Perlengkapan Mandi..."
                           class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('category') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Harga / Info Tambahan --}}
                <div>
                    <label for="distance" class="block text-sm font-semibold text-gray-800">
                        Harga / Info Kemasan (Opsional)
                    </label>
                    <div class="mt-1.5 relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <input type="text" name="distance" id="distance" value="{{ old('distance', $nearbyPlace->distance) }}" 
                               placeholder="Contoh: Rp 14.500 / kg atau Kemasan 1000ml"
                               class="block w-full pl-10 rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Informasi ini akan ditampilkan sebagai label harga atau info ukuran pada katalog.</p>
                    @error('distance') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi Singkat --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-800">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <textarea name="description" id="description" rows="3"
                              placeholder="Deskripsi singkat mengenai produk, stok, atau catatan tambahan..."
                              class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('description', $nearbyPlace->description) }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Urutan & Status Tampil --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <label for="sort_order" class="block text-sm font-semibold text-gray-800">
                            Urutan Tampil <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $nearbyPlace->sort_order) }}" min="0" required
                               class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <p class="mt-1 text-[11px] text-gray-400">Angka lebih kecil tampil lebih dulu di depan.</p>
                        @error('sort_order') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                            Status Publikasi
                        </label>
                        <label for="is_active" class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $nearbyPlace->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Tampilkan di Website</span>
                                <span class="text-[11px] text-gray-500 block">Aktifkan agar produk dapat dilihat tamu</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        {{-- Right Column: Photo Upload with Realtime Progress & Compression (5 cols) --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5 sticky top-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Foto Produk / Toko
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Sistem secara otomatis mengompres foto baru ke WebP hemat ukuran.</p>
                </div>

                {{-- Current Active Photo (If available) --}}
                @if($nearbyPlace->image)
                    <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Foto Aktif Saat Ini
                            </span>
                            <a href="{{ Storage::url($nearbyPlace->image) }}" target="_blank" class="text-[11px] font-semibold text-emerald-600 hover:underline">
                                Buka Ukuran Asli
                            </a>
                        </div>
                        <div class="relative aspect-video rounded-xl overflow-hidden bg-gray-200 border border-gray-300 shadow-inner">
                            <img src="{{ Storage::url($nearbyPlace->image) }}" alt="{{ $nearbyPlace->name }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif

                {{-- Advanced Image Uploader Component for Replacing --}}
                <div>
                    <x-image-uploader 
                        name="image" 
                        directory="nearby-places" 
                        :multiple="false" 
                        :variants="false" 
                        :max-size-mb="15" 
                        label="Ganti dengan Foto Baru (Opsional)"
                        hint="Maksimal 15MB. Format: JPG, PNG, WEBP. Biarkan kosong jika tidak ingin mengganti foto saat ini." />
                    
                    @error('image') <p class="text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Informational Box --}}
                <div class="p-3.5 bg-emerald-50/70 border border-emerald-100 rounded-xl text-xs text-emerald-900 space-y-1.5">
                    <div class="flex items-center gap-1.5 font-bold text-emerald-800">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Fitur Optimasi Otomatis
                    </div>
                    <p class="text-[11px] text-emerald-700 leading-relaxed">
                        Foto baru akan otomatis dikompres di browser dan server. Jika Anda tidak memilih foto baru, foto yang lama akan tetap dipertahankan.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.nearby-places.index') }}" 
                       class="px-4 py-2.5 border border-gray-300 text-sm font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition transform active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
