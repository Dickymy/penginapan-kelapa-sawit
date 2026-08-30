@extends('layouts.admin')

@section('title', 'Edit Foto / Produk Toko')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.nearby-places.index') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Foto / Produk Toko</h1>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden ">
    <form action="{{ route('admin.nearby-places.update', $nearbyPlace) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Foto / Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $nearbyPlace->name) }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="category" id="category" value="{{ old('category', $nearbyPlace->category) }}" placeholder="Contoh: Sembako, Minuman, Snack" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="distance" class="block text-sm font-medium text-gray-700">Harga / Info Tambahan (Opsional)</label>
                <input type="text" name="distance" id="distance" value="{{ old('distance', $nearbyPlace->distance) }}" placeholder="Contoh: Rp 15.000 / Kemasan 1 Liter"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                @error('distance') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Singkat (Opsional)</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $nearbyPlace->description) }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 hidden">
                <label for="map_link" class="block text-sm font-medium text-gray-700">Link Google Maps (Opsional)</label>
                <input type="url" name="map_link" id="map_link" value="{{ old('map_link', $nearbyPlace->map_link) }}" placeholder="https://maps.google.com/..."
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                @error('map_link') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan <span class="text-red-500">*</span></label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $nearbyPlace->sort_order) }}" min="0" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center mt-6">
                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $nearbyPlace->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Aktif (Tampilkan di halaman publik)
                </label>
            </div>

            <div class="md:col-span-2">
                @if($nearbyPlace->image)
                    <div class="mt-2 mb-4">
                        <p class="text-xs text-gray-500 mb-2">Foto Saat Ini:</p>
                        <img src="{{ Storage::url($nearbyPlace->image) }}" alt="{{ $nearbyPlace->name }}" class="h-32 rounded-lg object-cover border border-gray-200">
                    </div>
                @endif
                <x-image-uploader 
                    name="image" 
                    directory="nearby-places" 
                    :multiple="false" 
                    :variants="false" 
                    :max-size-mb="2" 
                    label="Ganti Foto (Opsional)"
                    hint="Maksimal 2MB. Format: JPG, PNG, WEBP. Biarkan kosong jika tidak ingin mengubah foto." />
                @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('admin.nearby-places.index') }}" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition">
                Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection
