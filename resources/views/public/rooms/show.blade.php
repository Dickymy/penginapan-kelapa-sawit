@extends('layouts.public')

@section('title', $roomType->name . ' - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('rooms.index') }}" class="hover:text-primary-600">Kamar</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800">{{ $roomType->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Images & Description --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Image Gallery --}}
            @if($roomType->images->isNotEmpty())
            @php $imagePaths = $roomType->images->pluck('path')->toArray(); @endphp
            <div x-data="{ activeImage: 0, images: {{ json_encode($imagePaths) }} }" class="space-y-3">
                <div class="rounded-lg overflow-hidden">
                    <img :src="'{{ asset('storage') }}/' + images[activeImage]" alt="{{ $roomType->name }}" class="w-full h-72 md:h-96 object-cover">
                </div>
                @if($roomType->images->count() > 1)
                <div class="flex gap-2 overflow-x-auto">
                    @foreach($roomType->images as $index => $image)
                    <button @click="activeImage = {{ $index }}" class="flex-shrink-0 rounded-lg overflow-hidden border-2 transition" :class="activeImage === {{ $index }} ? 'border-primary-500' : 'border-transparent hover:border-gray-300'">
                        <img src="{{ asset('storage/' . $image->path) }}" alt="" class="w-20 h-14 object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="w-full h-72 bg-gray-200 rounded-lg flex items-center justify-center">
                <span class="text-gray-400">Belum ada gambar</span>
            </div>
            @endif

            {{-- Description --}}
            @if($roomType->description)
            <div class="prose prose-sm max-w-none text-gray-700">
                <h2 class="text-xl font-bold text-gray-800">Deskripsi</h2>
                {!! nl2br(e($roomType->description)) !!}
            </div>
            @endif

            {{-- Facilities --}}
            @if($roomType->facilities->isNotEmpty())
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-3">Fasilitas</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($roomType->facilities as $facility)
                    <div class="flex items-center space-x-2 text-sm text-gray-700">
                        <span class="text-primary-500">✓</span>
                        <span>{{ $facility->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Info & CTA --}}
        <div>
            <div class="bg-white border rounded-lg shadow-sm p-6 sticky top-6 space-y-4">
                <h1 class="text-2xl font-bold text-gray-800">{{ $roomType->name }}</h1>

                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Kapasitas</span>
                        <span class="font-medium text-gray-800">{{ $roomType->capacity }} orang</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tempat Tidur</span>
                        <span class="font-medium text-gray-800">{{ $roomType->bed_count }} tempat tidur {{ $roomType->bed_type ?? '' }}</span>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="text-sm text-gray-500">Harga per malam</p>
                    <p class="text-2xl font-bold text-primary-600">Rp {{ number_format($roomType->base_price, 0, ',', '.') }}</p>
                </div>

                {{-- Booking Form --}}
                <form action="{{ route('availability.search') }}" method="GET" class="space-y-3 border-t pt-4">
                    <input type="hidden" name="room_type" value="{{ $roomType->slug }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Check-in</label>
                        <input type="date" name="check_in" min="{{ date('Y-m-d') }}" required
                               class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Check-out</label>
                        <input type="date" name="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                               class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tamu</label>
                        <input type="number" name="guest_count" min="1" max="{{ $roomType->capacity }}" value="2"
                               class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <button type="submit" class="w-full px-4 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
                        Cek Ketersediaan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
