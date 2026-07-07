@extends('layouts.public')

@section('title', $propertyName . ' - Kota Bangun, Kalimantan Timur')

@section('content')
{{-- Hero --}}
<section class="relative bg-gradient-to-br from-primary-700 to-primary-900 text-white py-16 md:py-20">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-3">{{ $propertyName }}</h1>
        @if($shortDescription)
            <p class="text-lg text-primary-100 mb-3 max-w-2xl mx-auto">{{ $shortDescription }}</p>
        @endif
        <p class="text-sm text-primary-200 mb-6">
            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Kota Bangun, Kalimantan Timur
        </p>
        @if($cheapestPrice)
            <p class="text-primary-200">Mulai dari <span class="text-2xl font-bold text-white">Rp{{ number_format($cheapestPrice, 0, ',', '.') }}</span> / malam</p>
        @endif
    </div>
</section>

{{-- Availability Search --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
    <div class="bg-white rounded-xl shadow-lg p-5 md:p-6 border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 text-center md:text-left">Cari Kamar Tersedia</h2>
        <form action="{{ route('availability.search') }}" method="GET"
              class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end"
              x-data="{ submitting: false, checkIn: '', checkOut: '', error: '' }"
              @submit.prevent="
                  error = '';
                  if (!checkIn) { error = 'Pilih tanggal check-in'; return; }
                  if (!checkOut) { error = 'Pilih tanggal check-out'; return; }
                  if (checkOut <= checkIn) { error = 'Tanggal check-out harus setelah check-in'; return; }
                  submitting = true;
                  $el.submit();
              ">
            {{-- Inline Error --}}
            <template x-if="error">
                <p class="md:col-span-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2" x-text="error"></p>
            </template>
            <div>
                <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Tanggal check-in <span class="text-red-500">*</span></label>
                <input type="date" name="check_in" id="check_in" min="{{ date('Y-m-d') }}" required
                       x-model="checkIn"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">Tanggal check-out <span class="text-red-500">*</span></label>
                <input type="date" name="check_out" id="check_out" :min="checkIn || '{{ date('Y-m-d', strtotime('+1 day')) }}'" required
                       x-model="checkOut"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="guest_count" class="block text-sm font-medium text-gray-700 mb-1">Jumlah tamu</label>
                <input type="number" name="guest_count" id="guest_count" min="1" value="2"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <button type="submit"
                        :disabled="submitting"
                        class="w-full px-4 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center">
                    <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!submitting">Cari Kamar</span>
                    <span x-show="submitting" x-cloak>Mencari...</span>
                </button>
            </div>
        </form>
    </div>
</section>

{{-- Room Types --}}
@if($roomTypes->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-10">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Tipe Kamar</h2>
        <p class="text-gray-500 mt-2">Pilih kamar yang sesuai dengan kebutuhan Anda</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roomTypes as $type)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition group">
            @php $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first(); @endphp
            @if($cover)
                <div class="aspect-[4/3] overflow-hidden">
                    <img src="{{ asset('storage/' . $cover->path) }}" alt="{{ $type->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
            @else
                <div class="aspect-[4/3] bg-gray-100 flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-gray-400 text-sm mt-2">Foto belum tersedia</span>
                </div>
            @endif
            <div class="p-5">
                <h3 class="text-lg font-semibold text-gray-800">{{ $type->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $type->capacity }} orang &middot; {{ $type->bed_count }} tempat tidur {{ $type->bed_type ?? '' }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <span class="text-primary-600 font-bold text-lg">Rp{{ number_format($type->base_price, 0, ',', '.') }}</span>
                        <span class="text-sm text-gray-400">/ malam</span>
                    </div>
                    <a href="{{ route('rooms.show', $type->slug) }}" class="px-4 py-2 text-sm font-medium text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-50 transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- WhatsApp CTA --}}
@if($whatsapp)
<section class="bg-green-50 border-t border-green-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Butuh Bantuan?</h2>
        <p class="text-gray-600 mb-5">Hubungi kami langsung via WhatsApp untuk informasi dan pemesanan.</p>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.614-1.46A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-2.115 0-4.107-.57-5.82-1.563l-.418-.248-4.327 1.37 1.394-4.212-.273-.433A9.708 9.708 0 012.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75z"/></svg>
            Hubungi via WhatsApp
        </a>
    </div>
</section>
@endif
@endsection
