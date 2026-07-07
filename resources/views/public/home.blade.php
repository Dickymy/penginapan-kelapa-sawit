@extends('layouts.public')

@section('title', $propertyName . ' - Kota Bangun, Kalimantan Timur')

@section('content')
{{-- Hero --}}
<section class="bg-primary-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-3">{{ $propertyName }}</h1>
        @if($shortDescription)
            <p class="text-lg text-primary-100 mb-4">{{ $shortDescription }}</p>
        @endif
        @if($cheapestPrice)
            <p class="text-primary-200">Mulai dari <span class="text-2xl font-bold text-white">Rp {{ number_format($cheapestPrice, 0, ',', '.') }}</span> / malam</p>
        @endif
    </div>
</section>

{{-- Availability Form Placeholder --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('availability.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                <input type="date" name="check_in" min="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                <input type="date" name="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tamu</label>
                <input type="number" name="guest_count" min="1" value="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">Cari Kamar</button>
            </div>
        </form>
    </div>
</section>

{{-- Room Types --}}
@if($roomTypes->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tipe Kamar</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roomTypes as $type)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            @php $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first(); @endphp
            @if($cover)
                <img src="{{ asset('storage/' . $cover->path) }}" alt="{{ $type->name }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-400 text-sm">Belum ada gambar</span>
                </div>
            @endif
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-800">{{ $type->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $type->capacity }} orang &middot; {{ $type->bed_count }} {{ $type->bed_type ?? 'bed' }}</p>
                <p class="text-primary-600 font-bold mt-2">Rp {{ number_format($type->base_price, 0, ',', '.') }} <span class="text-sm font-normal text-gray-400">/ malam</span></p>
                <a href="{{ route('rooms.show', $type->slug) }}" class="inline-block mt-3 text-sm text-primary-600 hover:text-primary-800 font-medium">Lihat Detail &rarr;</a>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- WhatsApp CTA --}}
@if($whatsapp)
<section class="bg-green-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Butuh Bantuan?</h2>
        <p class="text-gray-600 mb-4">Hubungi kami langsung via WhatsApp untuk informasi dan pemesanan.</p>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.614-1.46A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-2.115 0-4.107-.57-5.82-1.563l-.418-.248-4.327 1.37 1.394-4.212-.273-.433A9.708 9.708 0 012.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75z"/></svg>
            Hubungi via WhatsApp
        </a>
    </div>
</section>
@endif
@endsection
