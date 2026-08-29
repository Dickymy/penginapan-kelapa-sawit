@extends('layouts.public')

@section('title', 'Lokasi Sekitar')

@section('content')
<div class="bg-primary-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold text-primary-900 sm:text-5xl">Lokasi Sekitar</h1>
        <p class="mt-4 text-xl text-primary-700 max-w-2xl mx-auto">
            Temukan berbagai destinasi wisata, kuliner lokal, dan fasilitas penting di sekitar Penginapan Kelapa Sawit.
        </p>
    </div>
</div>

<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @forelse($placesByCategory as $category => $places)
            <div class="mb-16 last:mb-0">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 border-b border-gray-100 pb-4 inline-block">
                    {{ $category }}
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($places as $place)
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden flex flex-col h-full">
                            @if($place->image)
                                <img src="{{ Storage::url($place->image) }}" alt="{{ $place->name }}" class="w-full h-48 object-cover" width="400" height="192" loading="lazy" decoding="async">
                            @else
                                <div class="w-full h-48 bg-primary-50 flex items-center justify-center">
                                    <svg class="h-16 w-16 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                            @endif
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 leading-tight">
                                        {{ $place->name }}
                                    </h3>
                                </div>
                                
                                @if($place->distance)
                                    <div class="flex items-center text-sm text-gray-500 mb-3">
                                        <svg class="w-4 h-4 mr-1.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                        Berjarak sekitar {{ $place->distance }}
                                    </div>
                                @endif
                                
                                @if($place->description)
                                    <p class="text-gray-600 text-sm mb-6 flex-grow">
                                        {{ $place->description }}
                                    </p>
                                @endif
                                
                                @if($place->map_link)
                                    <div class="mt-auto pt-4 border-t border-gray-100">
                                        <a href="{{ $place->map_link }}" target="_blank" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 transition">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Lihat di Google Maps
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data</h3>
                <p class="text-gray-500">Informasi lokasi sekitar belum ditambahkan untuk saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
