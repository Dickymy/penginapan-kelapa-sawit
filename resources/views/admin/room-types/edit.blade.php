@extends('layouts.admin')

@section('title', 'Edit Tipe Kamar - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Tipe Kamar: {{ $roomType->name }}</h1>
    <a href="{{ route('admin.room-types.index') }}" class="text-sm text-primary-600 hover:text-primary-800">&larr; Kembali</a>
</div>

<form action="{{ route('admin.room-types.update', $roomType) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 max-w-3xl space-y-5">
    @csrf
    @method('PUT')

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $roomType->name) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        <x-form-error field="name" />
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $roomType->slug) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        <x-form-error field="slug" />
    </div>

    <div>
        <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
        <input type="text" name="short_description" id="short_description" value="{{ old('short_description', $roomType->short_description) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
        <x-form-error field="short_description" />
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
        <textarea name="description" id="description" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">{{ old('description', $roomType->description) }}</textarea>
        <x-form-error field="description" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (orang) <span class="text-red-500">*</span></label>
            <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $roomType->capacity) }}" min="1" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="capacity" />
        </div>
        <div>
            <label for="bed_count" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tempat Tidur</label>
            <input type="number" name="bed_count" id="bed_count" value="{{ old('bed_count', $roomType->bed_count) }}" min="1" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="bed_count" />
        </div>
        <div>
            <label for="bed_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Tempat Tidur</label>
            <input type="text" name="bed_type" id="bed_type" value="{{ old('bed_type', $roomType->bed_type) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="bed_type" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">Harga per Malam (Rp) <span class="text-red-500">*</span></label>
            <input type="number" name="base_price" id="base_price" value="{{ old('base_price', $roomType->base_price) }}" min="0" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="base_price" />
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $roomType->sort_order) }}" min="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <x-form-error field="sort_order" />
        </div>
    </div>

    <div>
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $roomType->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <span class="ml-2 text-sm text-gray-700">Aktif</span>
        </label>
    </div>

    @if($facilities->isNotEmpty())
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Fasilitas</label>
        @php $selectedFacilities = old('facilities', $roomType->facilities->pluck('id')->toArray()); @endphp
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            @foreach($facilities as $facility)
            <label class="inline-flex items-center">
                <input type="checkbox" name="facilities[]" value="{{ $facility->id }}" {{ in_array($facility->id, $selectedFacilities) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="ml-2 text-sm text-gray-700">{{ $facility->name }}</span>
            </label>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Existing Images --}}
    @if($roomType->images->isNotEmpty())
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($roomType->images as $image)
            <div class="relative border rounded-lg overflow-hidden">
                <img src="{{ $image->thumb_url }}" alt="Room image" class="w-full h-24 object-cover">
                <div class="absolute top-1 right-1 flex space-x-1">
                    @if($image->is_cover)
                        <span class="bg-green-500 text-white text-xs px-1 rounded">Cover</span>
                    @else
                        <form action="{{ route('admin.room-images.cover', $image) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-blue-500 text-white text-xs px-1 rounded hover:bg-blue-600" title="Jadikan Cover">★</button>
                        </form>
                    @endif
                    <form action="{{ route('admin.room-images.destroy', $image) }}" method="POST"
                          x-data
                          @submit.prevent="$dispatch('open-confirm', { id: 'delete-image-{{ $image->id }}' })">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white text-xs px-1 rounded hover:bg-red-600">&times;</button>
                    </form>
                    <x-confirm-modal
                        id="delete-image-{{ $image->id }}"
                        title="Hapus gambar?"
                        message="Gambar ini akan dihapus secara permanen."
                        confirm-text="Ya, Hapus"
                        cancel-text="Batal"
                        variant="danger"
                        :form-action="route('admin.room-images.destroy', $image)"
                        method="DELETE"
                    />
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div>
        <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Tambah Gambar</label>
        <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
        <x-form-error field="images" />
        <x-form-error field="images.*" />
    </div>

    <div class="pt-4">
        <x-button type="submit">Perbarui</x-button>
    </div>
</form>
@endsection
