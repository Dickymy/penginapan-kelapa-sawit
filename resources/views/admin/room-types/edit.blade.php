@extends('layouts.admin')

@section('title', 'Edit Tipe Kamar - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Tipe Kamar: {{ $roomType->name }}</h1>
    <a href="{{ route('admin.room-types.index') }}" class="text-sm text-primary-600 hover:text-primary-800">&larr; Kembali</a>
</div>

<form action="{{ route('admin.room-types.update', $roomType) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-5">
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
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-semibold text-gray-800">
                Gambar Saat Ini <span class="text-xs text-gray-500 font-normal">({{ $roomType->images->count() }} foto)</span>
            </label>
            <span class="text-xs text-gray-500">Foto bertanda <span class="text-emerald-700 font-bold">★ Cover Utama</span> tampil di kartu kamar</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($roomType->images as $image)
            <div class="relative rounded-xl overflow-hidden border {{ $image->is_cover ? 'border-emerald-500 ring-2 ring-emerald-200 shadow-md' : 'border-gray-200 hover:border-gray-300' }} bg-gray-50 transition-all group">
                <div class="aspect-[4/3] w-full overflow-hidden">
                    <img src="{{ $image->thumb_url }}" alt="Room image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>

                {{-- Badges & Controls Overlay --}}
                <div class="absolute inset-0 p-2 flex flex-col justify-between pointer-events-none">
                    <div class="flex items-start justify-between gap-1 pointer-events-auto">
                        @if($image->is_cover)
                            <span class="inline-flex items-center gap-1 bg-emerald-600 text-white text-[11px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Cover Utama
                            </span>
                        @else
                            <div></div>
                        @endif
                        <button type="button" @click="$dispatch('open-confirm', { id: 'delete-image-{{ $image->id }}' })" 
                                class="p-1.5 rounded-lg bg-black/60 hover:bg-red-600 text-white text-xs shadow-md transition" title="Hapus foto ini">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>

                    @if(!$image->is_cover)
                    <div class="pointer-events-auto">
                        <button type="submit" form="cover-form-{{ $image->id }}" 
                                class="w-full text-center py-1 px-2 rounded-lg bg-white/95 hover:bg-white text-gray-800 hover:text-emerald-700 text-xs font-semibold shadow-md border border-gray-200/80 transition flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Jadikan Cover
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="border-t border-gray-100 pt-5">
        <x-image-uploader 
            name="images" 
            directory="room-images" 
            :multiple="true" 
            :variants="true" 
            :max-files="10" 
            :max-size-mb="15" 
            label="Tambah Gambar Baru"
            hint="Maksimal 10 gambar per tipe kamar. JPG/PNG/WEBP hingga 15MB." />
        <x-form-error field="images" />
        <x-form-error field="images.*" />

        <div class="mt-3 space-y-2">
            <label class="inline-flex items-center gap-2.5 p-3 bg-emerald-50/70 border border-emerald-200/80 rounded-xl text-sm text-emerald-950 cursor-pointer hover:bg-emerald-50 transition w-full select-none">
                <input type="checkbox" name="set_new_as_cover" value="1" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                <span>Jadikan foto baru yang diunggah sebagai <strong>Cover / Foto Utama</strong> di halaman depan.</span>
            </label>
            <p class="text-xs text-gray-500 flex items-center gap-1.5 px-1">
                <svg class="w-4 h-4 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Setelah memilih foto, tekan tombol <strong>"Perbarui"</strong> di bawah untuk menyimpan perubahan.</span>
            </p>
        </div>
    </div>

    <div class="pt-4">
        <x-button type="submit">Perbarui</x-button>
    </div>
</form>

{{-- Hidden Forms for Image Actions --}}
@if($roomType->images->isNotEmpty())
    @foreach($roomType->images as $image)
        @if(!$image->is_cover)
            <form id="cover-form-{{ $image->id }}" action="{{ route('admin.room-images.cover', $image) }}" method="POST" class="hidden">
                @csrf
                @method('PATCH')
            </form>
        @endif
        
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
    @endforeach
@endif

@endsection
