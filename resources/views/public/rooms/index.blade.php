@extends('layouts.public')

@section('title', 'Kamar - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Tipe Kamar Kami</h1>

    @if($roomTypes->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500">Belum ada tipe kamar yang tersedia.</p>
        </div>
    @else
    <div class="space-y-8">
        @foreach($roomTypes as $type)
        <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col md:flex-row">
            {{-- Image --}}
            <div class="md:w-1/3">
                @php $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first(); @endphp
                @if($cover)
                    <img src="{{ asset('storage/' . $cover->path) }}" alt="{{ $type->name }}" class="w-full h-56 md:h-full object-cover">
                @else
                    <div class="w-full h-56 md:h-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400 text-sm">Belum ada gambar</span>
                    </div>
                @endif
            </div>
            {{-- Info --}}
            <div class="md:w-2/3 p-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $type->name }}</h2>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                        <span>👤 {{ $type->capacity }} orang</span>
                        <span>🛏️ {{ $type->bed_count }} {{ $type->bed_type ?? 'bed' }}</span>
                    </div>
                    @if($type->short_description)
                        <p class="text-gray-600 text-sm mb-3">{{ $type->short_description }}</p>
                    @endif
                    @if($type->facilities->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($type->facilities as $facility)
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                                {{ $facility->name }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="flex items-center justify-between mt-4">
                    <p class="text-primary-600 font-bold text-lg">Rp {{ number_format($type->base_price, 0, ',', '.') }} <span class="text-sm font-normal text-gray-400">/ malam</span></p>
                    <a href="{{ route('rooms.show', $type->slug) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">Lihat Detail</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
