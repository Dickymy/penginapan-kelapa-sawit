@extends('layouts.public')

@section('title', $propertyName . ' — Penginapan di Kota Bangun II')

@section('meta')
<meta name="description" content="Penginapan Kelapa Sawit — penginapan nyaman di Kota Bangun II, Kutai Kartanegara, Kalimantan Timur. Pesan kamar langsung tanpa perlu akun.">
<meta property="og:title" content="{{ $propertyName }} — Penginapan di Kota Bangun II">
<meta property="og:description" content="Penginapan nyaman di Kota Bangun II, Kutai Kartanegara. Pesan langsung tanpa akun atau masuk untuk menyimpan histori dan mengumpulkan poin.">
<meta property="og:type" content="website">
<link rel="canonical" href="{{ route('home') }}">
@endsection

@section('content')
{{-- Hero --}}
<section class="relative text-white py-20 md:py-32 bg-primary-900 overflow-hidden">
    {{-- Hero Background Pattern --}}
    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#4ade80 1.5px, transparent 1.5px); background-size: 32px 32px;"></div>
    
    {{-- Gradient Overlay for Depth --}}
    <div class="absolute inset-0 bg-gradient-to-t from-primary-950 via-primary-900/60 to-transparent"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col items-center justify-center min-h-[30vh]">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-lg">{{ $propertyName }}</h1>
        <p class="text-base md:text-lg text-gray-200 mb-6 flex items-center justify-center gap-1.5 drop-shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Kota Bangun II, Kutai Kartanegara, Kalimantan Timur
        </p>
        @if($cheapestPrice)
            <p class="text-gray-100 mb-8 bg-black/30 backdrop-blur-sm px-6 py-2 rounded-full border border-white/10 drop-shadow-md">Mulai dari <span class="text-2xl font-bold text-white">Rp{{ number_format($cheapestPrice, 0, ',', '.') }}</span> / malam</p>
        @endif
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#cari-kamar" class="px-8 py-3.5 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-500 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary-600/30 transition-all duration-300">
                Cari Kamar
            </a>
            <a href="{{ route('location') }}" class="px-8 py-3.5 bg-white/10 backdrop-blur-md border border-white/30 text-white rounded-xl font-semibold hover:bg-white/20 hover:-translate-y-1 transition-all duration-300">
                Lihat Lokasi
            </a>
        </div>
    </div>
</section>

{{-- Search Availability --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10" id="cari-kamar"
         x-data="{ shown: false }" x-intersect.once="shown = true" 
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="bg-white rounded-2xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] p-6 md:p-8 border border-white/50 ring-1 ring-black/5">
        <h2 class="text-xl font-bold text-gray-800 mb-5 text-center md:text-left">Cari Kamar Tersedia</h2>
        <form action="{{ route('availability.search') }}" method="GET"
              class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end"
              x-data="{
                  submitting: false,
                  checkIn: '{{ request('check_in', old('check_in', date('Y-m-d'))) }}',
                  checkOut: '{{ request('check_out', old('check_out', date('Y-m-d', strtotime('+1 day')))) }}',
                  guestCount: {{ request('guest_count', old('guest_count', 1)) }},
                  error: '',
                  adjustCheckOut() {
                      if (this.checkOut <= this.checkIn) {
                          const next = new Date(this.checkIn);
                          next.setDate(next.getDate() + 1);
                          this.checkOut = next.toISOString().split('T')[0];
                      }
                  },
                  increment() { if (this.guestCount < 10) this.guestCount++; },
                  decrement() { if (this.guestCount > 1) this.guestCount--; }
              }"
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
                <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Check-in <span class="text-red-500">*</span></label>
                <input type="date" name="check_in" id="check_in" min="{{ date('Y-m-d') }}" required
                       x-model="checkIn"
                       @change="adjustCheckOut()"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">Check-out <span class="text-red-500">*</span></label>
                <input type="date" name="check_out" id="check_out" :min="checkIn || '{{ date('Y-m-d', strtotime('+1 day')) }}'" required
                       x-model="checkOut"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah tamu</label>
                <div class="flex items-center border border-gray-300 rounded-lg shadow-sm overflow-hidden">
                    <button type="button" @click="decrement()"
                            class="px-3 py-2.5 text-gray-600 hover:bg-gray-100 active:bg-gray-200 transition text-lg font-bold"
                            :class="guestCount <= 1 && 'opacity-40 cursor-not-allowed'"
                            :disabled="guestCount <= 1" aria-label="Kurangi tamu">−</button>
                    <input type="number" name="guest_count" x-model="guestCount" min="1" max="10" readonly
                           class="flex-1 text-center border-0 focus:ring-0 text-sm font-medium py-2.5 bg-transparent [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    <button type="button" @click="increment()"
                            class="px-3 py-2.5 text-gray-600 hover:bg-gray-100 active:bg-gray-200 transition text-lg font-bold"
                            :class="guestCount >= 10 && 'opacity-40 cursor-not-allowed'"
                            :disabled="guestCount >= 10" aria-label="Tambah tamu">+</button>
                </div>
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

{{-- Booking Options --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out delay-100">
    <h2 class="text-center text-2xl font-bold text-gray-800 mb-8">Pilih cara yang paling nyaman</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-100 rounded-2xl p-8 text-center shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-primary-50 group-hover:bg-primary-100 flex items-center justify-center transition-colors">
                <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Booking Langsung</h3>
            <p class="text-sm text-gray-500 mb-6">Tidak perlu membuat akun, langsung pesan kamar yang Anda inginkan.</p>
            <a href="#cari-kamar" class="inline-block px-6 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30 transition-all duration-300">Cari Kamar</a>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-8 text-center shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-primary-50 group-hover:bg-primary-100 flex items-center justify-center transition-colors">
                <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Masuk sebagai Member</h3>
            <p class="text-sm text-gray-500 mb-6">Booking tersimpan otomatis di akun Anda dan kumpulkan poin loyalitas.</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2.5 border-2 border-primary-600 text-primary-600 rounded-xl text-sm font-semibold hover:bg-primary-50 hover:shadow-lg transition-all duration-300">Masuk / Daftar</a>
        </div>
    </div>
</section>

{{-- Room Types --}}
@if($roomTypes->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="text-center mb-8">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Tipe Kamar</h2>
        <p class="text-gray-500 mt-2">Pilih kamar yang sesuai dengan kebutuhan Anda</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roomTypes as $type)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition group">
            @php $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first(); @endphp
            @if($cover)
                <div class="aspect-[4/3] overflow-hidden">
                    <img src="{{ $cover->medium_url }}"
                         srcset="{{ $cover->thumb_url }} 480w, {{ $cover->medium_url }} 960w, {{ $cover->large_url }} 1920w"
                         sizes="(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw"
                         alt="Kamar {{ $type->name }}"
                         loading="lazy"
                         decoding="async"
                         width="960" height="720"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
            @else
                <div class="aspect-[4/3] bg-gray-100 flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-gray-400 text-sm mt-2">Foto belum tersedia</span>
                </div>
            @endif
            <div class="p-5">
                <h3 class="text-lg font-semibold text-gray-800">{{ $type->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $type->capacity }} tamu &middot; {{ $type->bed_count }} tempat tidur {{ $type->bed_type ?? '' }}</p>
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


{{-- Guest Reviews Widget --}}
@if(isset($reviews) && $reviews->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 bg-gray-50 rounded-2xl mb-12"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="text-center mb-8">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Apa Kata Tamu Kami</h2>
        <p class="text-gray-500 mt-2">Pengalaman mereka menginap di {{ $propertyName }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($reviews as $review)
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col h-full">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-lg mr-3">
                    {{ substr($review->user->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $review->user->name }}</h3>
                    <div class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <div class="mb-3">
                <x-star-rating :rating="$review->rating" size="4" />
            </div>
            @if($review->title)
                <h4 class="font-bold text-gray-800 mb-1">{{ $review->title }}</h4>
            @endif
            <p class="text-gray-600 text-sm italic flex-1">"{{ $review->comment }}"</p>
            <div class="mt-4 pt-4 border-t border-gray-50 text-xs text-gray-500">
                Menginap di <span class="font-medium">{{ $review->booking->room_type_name_snapshot }}</span>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- Nearby Places Preview --}}
@if(isset($nearbyPlaces) && $nearbyPlaces->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="text-center mb-6">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Tempat Menarik di Sekitar</h2>
        <p class="text-gray-500 mt-2">Jelajahi destinasi dan fasilitas di sekitar penginapan kami</p>
    </div>
    <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($nearbyPlaces as $place)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full group hover:shadow-md transition">
            <div class="aspect-[4/3] overflow-hidden bg-gray-100 relative">
                @if($place->image)
                    <img src="{{ Storage::url($place->image) }}" alt="{{ $place->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                @endif
                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-xs font-medium text-gray-700 shadow-sm">
                    {{ $place->category }}
                </div>
            </div>
            <div class="p-4 flex flex-col flex-grow">
                <h3 class="font-bold text-gray-900 mb-1 leading-tight">{{ $place->name }}</h3>
                @if($place->distance)
                    <p class="text-xs text-primary-600 font-medium mb-2">{{ $place->distance }}</p>
                @endif
                <p class="text-xs text-gray-500 line-clamp-2 flex-grow">{{ $place->description }}</p>
            </div>
        </div>
        @endforeach
    </div>
    <div class="text-center sm:mt-8">
        <a href="{{ route('nearby-places') }}" class="inline-flex items-center px-5 py-2.5 border border-primary-600 text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-50 transition">
            Lihat Semua Lokasi Sekitar
            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
@endif

{{-- Gallery Preview --}}
@if($galleryPhotos->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="text-center mb-6">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Galeri</h2>
        <p class="text-gray-500 mt-2">Suasana penginapan kami</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
        @foreach($galleryPhotos as $photo)
        <div class="aspect-[4/3] overflow-hidden rounded-xl bg-gray-100">
            <img src="{{ $photo->medium_url }}"
                 srcset="{{ $photo->thumb_url }} 480w, {{ $photo->medium_url }} 960w"
                 sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                 alt="{{ $photo->alt_text ?? $photo->title ?? 'Foto Penginapan Kelapa Sawit' }}"
                 loading="lazy"
                 decoding="async"
                 width="480" height="360"
                 class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>
        @endforeach
    </div>
    @if($galleryPhotos->count() >= 8)
    <div class="text-center mt-6">
        <a href="{{ route('gallery') }}" class="inline-flex items-center px-5 py-2.5 border border-primary-600 text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-50 transition">
            Lihat Semua Foto
            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    @endif
</section>
@endif
{{-- Property Info --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="bg-white border border-gray-100 rounded-2xl p-8 md:p-10 shadow-sm relative overflow-hidden hover:shadow-md transition-shadow duration-300">
        {{-- Decorative background --}}
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-primary-50 rounded-full opacity-50 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-primary-100 rounded-full opacity-30 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 md:flex md:items-start md:gap-10">
            <div class="md:w-1/2 mb-8 md:mb-0">
                <div class="inline-flex items-center justify-center p-3 bg-primary-100 rounded-xl text-primary-700 mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Penginapan</h2>
                <div class="space-y-4">
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Penginapan satu lantai di Kota Bangun II dengan <strong class="text-primary-700">{{ $activeRoomCount }} kamar aktif</strong> yang siap melayani tamu. Kamar terus dikembangkan untuk memenuhi kebutuhan pengunjung.
                    </p>
                    @if($shortDescription)
                    <p class="text-gray-600 leading-relaxed text-sm">{{ $shortDescription }}</p>
                    @endif
                </div>
            </div>
            
            <div class="md:w-1/2 grid grid-cols-2 gap-4">
                <div class="bg-gray-50/80 border border-gray-100 p-5 rounded-xl hover:shadow-sm hover:bg-white transition duration-300 group">
                    <svg class="w-8 h-8 text-primary-500 mb-3 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="font-semibold text-gray-800 text-sm mb-1">Resepsionis 24 Jam</h3>
                    <p class="text-xs text-gray-500">Staf siap sedia kapanpun Anda butuh</p>
                </div>
                <div class="bg-gray-50/80 border border-gray-100 p-5 rounded-xl hover:shadow-sm hover:bg-white transition duration-300 group">
                    <svg class="w-8 h-8 text-primary-500 mb-3 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <h3 class="font-semibold text-gray-800 text-sm mb-1">Nyaman & Bersih</h3>
                    <p class="text-xs text-gray-500">Istirahat tenang seperti di rumah sendiri</p>
                </div>
                <div class="bg-gray-50/80 border border-gray-100 p-5 rounded-xl hover:shadow-sm hover:bg-white transition duration-300 group">
                    <svg class="w-8 h-8 text-primary-500 mb-3 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h3 class="font-semibold text-gray-800 text-sm mb-1">Proses Cepat</h3>
                    <p class="text-xs text-gray-500">Check-in dan check-out tanpa antri</p>
                </div>
                <div class="bg-gray-50/80 border border-gray-100 p-5 rounded-xl hover:shadow-sm hover:bg-white transition duration-300 group">
                    <svg class="w-8 h-8 text-primary-500 mb-3 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <h3 class="font-semibold text-gray-800 text-sm mb-1">Lokasi Strategis</h3>
                    <p class="text-xs text-gray-500">Akses mudah di Kota Bangun II</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Location Preview --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-12"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Lokasi</h2>
        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
            <div class="flex-1">
                <p class="text-sm text-gray-700 font-medium">{{ $propertyName }}</p>
                @if($address)
                    <p class="text-sm text-gray-500 mt-1">{{ $address }}</p>
                @else
                    <p class="text-sm text-gray-500 mt-1">Kota Bangun II, Kecamatan Kota Bangun, Kabupaten Kutai Kartanegara, Kalimantan Timur</p>
                @endif
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                @php $googleLink = $mapLink ?: $mapUrl; @endphp
                @if($googleLink)
                <a href="{{ $googleLink }}" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Buka di Google Maps
                </a>
                @endif
                <a href="{{ route('location') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Lihat Detail Lokasi
                </a>
            </div>
        </div>
    </div>
</section>

{{-- WhatsApp CTA --}}
@php $waUrl = \App\Support\WhatsApp::url($whatsapp, 'Halo, saya ingin bertanya tentang ketersediaan kamar di Penginapan Kelapa Sawit.'); @endphp
@if($waUrl)
<section class="bg-green-50 border-y border-green-100 py-10"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-lg font-bold text-gray-800 mb-2">Butuh bantuan sebelum memesan?</h2>
        <p class="text-gray-600 text-sm mb-5">Hubungi kami langsung via WhatsApp.</p>
        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.614-1.46A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-2.115 0-4.107-.57-5.82-1.563l-.418-.248-4.327 1.37 1.394-4.212-.273-.433A9.708 9.708 0 012.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75z"/></svg>
            Chat Penginapan
        </a>
    </div>
</section>
@endif

{{-- Policy Summary --}}
@if($policy)
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Informasi Penting</h2>
        <p class="text-sm text-gray-500 mb-4">Ringkasan kebijakan penginapan sebelum Anda memesan.</p>
        <div class="text-sm text-gray-600 leading-relaxed line-clamp-4">
            {!! Str::limit(strip_tags($policy->content), 300) !!}
        </div>
        <a href="{{ route('policy') }}" class="inline-flex items-center mt-4 text-sm text-primary-600 hover:text-primary-800 font-medium">
            Lihat Kebijakan Lengkap
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
@endif

{{-- FAQ Summary --}}
@if(isset($faqs) && $faqs->isNotEmpty())
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
         x-data="{ shown: false }" x-intersect.margin.-10%.once="shown = true"
         :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'" 
         class="transition-all duration-700 ease-out">
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Pertanyaan yang Sering Diajukan</h2>
        <div class="space-y-4 mb-4">
            @foreach($faqs as $faq)
                <div class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                    <h3 class="text-sm font-medium text-gray-800">{{ $faq->question }}</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $faq->answer }}</p>
                </div>
            @endforeach
        </div>
        <a href="{{ route('faq') }}" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-800 font-medium">
            Lihat semua FAQ
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
@endif

{{-- Welcome Choice (First Visit) --}}
@guest
<div x-data="{ showWelcome: false }"
     x-init="
        if (!localStorage.getItem('kelapa_sawit_welcomed')) {
            setTimeout(() => showWelcome = true, 800);
        }
     "
     @keydown.escape.window="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())">

    {{-- Desktop Modal --}}
    <template x-teleport="body">
        <div x-show="showWelcome" x-cloak class="fixed inset-0 z-50 hidden md:flex items-center justify-center">
            <div x-show="showWelcome" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                 class="fixed inset-0 bg-black/40"></div>
            <div x-show="showWelcome" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-7 z-10" role="dialog" aria-modal="true" aria-labelledby="welcome-title">
                <h2 id="welcome-title" class="text-xl font-bold text-gray-900 mb-2">Selamat datang di Penginapan Kelapa Sawit</h2>
                <p class="text-sm text-gray-600 mb-6">Pesan kamar langsung tanpa akun, atau masuk agar data booking tersimpan dan Anda dapat mengumpulkan poin.</p>
                <div class="space-y-3">
                    <a href="#cari-kamar" @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                       class="block w-full px-4 py-2.5 text-center text-sm font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                        Pesan Kamar
                    </a>
                    <a href="{{ route('login') }}" @click="localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                       class="block w-full px-4 py-2.5 text-center text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Masuk / Daftar
                    </a>
                    <button type="button" @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                            class="block w-full px-4 py-2 text-center text-sm text-gray-500 hover:text-gray-700 transition">
                        Lihat Dulu
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Mobile Bottom Sheet --}}
    <template x-teleport="body">
        <div x-show="showWelcome" x-cloak class="fixed inset-0 z-50 md:hidden flex items-end">
            <div x-show="showWelcome" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                 class="fixed inset-0 bg-black/40"></div>
            <div x-show="showWelcome" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                 class="relative w-full bg-white rounded-t-2xl shadow-xl p-6 pb-8 z-10 safe-area-bottom" role="dialog" aria-modal="true" aria-labelledby="welcome-title-mobile">
                <div class="w-10 h-1 bg-gray-300 rounded-full mx-auto mb-5"></div>
                <h2 id="welcome-title-mobile" class="text-lg font-bold text-gray-900 mb-2">Selamat datang di Penginapan Kelapa Sawit</h2>
                <p class="text-sm text-gray-600 mb-5">Pesan kamar langsung tanpa akun, atau masuk agar data booking tersimpan dan Anda dapat mengumpulkan poin.</p>
                <div class="space-y-3">
                    <a href="#cari-kamar" @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                       class="block w-full px-4 py-3 text-center text-sm font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                        Pesan Kamar
                    </a>
                    <a href="{{ route('login') }}" @click="localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                       class="block w-full px-4 py-3 text-center text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Masuk / Daftar
                    </a>
                    <button type="button" @click="showWelcome = false; localStorage.setItem('kelapa_sawit_welcomed', Date.now())"
                            class="block w-full px-4 py-2 text-center text-sm text-gray-500 hover:text-gray-700 transition">
                        Lihat Dulu
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endguest
@endsection
