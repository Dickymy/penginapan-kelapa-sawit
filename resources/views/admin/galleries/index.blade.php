@extends('layouts.admin')

@section('title', 'Galeri - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Galeri</h1>
</div>

{{-- Upload Form --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-sm font-semibold text-gray-700 mb-3">Unggah Gambar Baru</h2>
    <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-4">
        @csrf
        <div class="flex-1 min-w-[200px]">
            <label for="images" class="block text-xs text-gray-500 mb-1">Gambar (bisa pilih lebih dari satu)</label>
            <input type="file" name="images[]" id="images" multiple accept="image/*" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
        </div>
        <div>
            <label for="title" class="block text-xs text-gray-500 mb-1">Judul (opsional)</label>
            <input type="text" name="title" id="title" class="border-gray-300 rounded-lg shadow-sm text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <x-button type="submit">Unggah</x-button>
    </form>
    <x-form-error field="images" />
    <x-form-error field="images.*" />
</div>

{{-- Gallery Grid --}}
@if($galleries->isEmpty())
    <x-empty-state message="Belum ada gambar di galeri." />
@else
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach($galleries as $gallery)
    <div class="relative group border rounded-lg overflow-hidden bg-white shadow-sm">
        <img src="{{ asset('storage/' . $gallery->path) }}" alt="{{ $gallery->title ?? 'Gallery' }}" class="w-full h-40 object-cover">
        @if($gallery->title)
            <p class="text-xs text-gray-600 p-2 truncate">{{ $gallery->title }}</p>
        @endif
        <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition">
            <form action="{{ route('admin.galleries.toggle', $gallery) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-white/90 text-xs px-2 py-1 rounded shadow {{ $gallery->is_active ? 'text-yellow-600' : 'text-green-600' }}" title="{{ $gallery->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                    {{ $gallery->is_active ? '●' : '○' }}
                </button>
            </form>
            <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" onsubmit="return confirm('Hapus gambar ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-white/90 text-red-600 text-xs px-2 py-1 rounded shadow">&times;</button>
            </form>
        </div>
        @if(!$gallery->is_active)
            <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                <span class="text-white text-xs font-medium">Nonaktif</span>
            </div>
        @endif
    </div>
    @endforeach
</div>
@endif
@endsection
