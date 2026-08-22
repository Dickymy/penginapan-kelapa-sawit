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
            <div x-data="{ activeImage: 0 }" class="space-y-3">
                <div class="rounded-lg overflow-hidden bg-gray-100">
                    @foreach($roomType->images as $index => $image)
                    <img x-show="activeImage === {{ $index }}"
                         src="{{ $image->large_url }}"
                         alt="{{ $image->alt_text ?? $roomType->name }}"
                         {{ $index === 0 ? '' : 'loading=lazy' }}
                         decoding="{{ $index === 0 ? 'sync' : 'async' }}"
                         width="1920" height="1440"
                         class="w-full h-72 md:h-96 object-cover">
                    @endforeach
                </div>
                @if($roomType->images->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($roomType->images as $index => $image)
                    <button @click="activeImage = {{ $index }}" class="flex-shrink-0 rounded-lg overflow-hidden border-2 transition" :class="activeImage === {{ $index }} ? 'border-primary-500' : 'border-transparent hover:border-gray-300'">
                        <img src="{{ $image->thumb_url }}" alt="" loading="lazy" decoding="async" width="80" height="56" class="w-20 h-14 object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="w-full h-72 md:h-96 relative flex flex-col items-center justify-center overflow-hidden rounded-2xl bg-gray-50 border border-gray-100">
                {{-- Elegant Fallback Background --}}
                <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80" 
                     alt="Placeholder" 
                     class="absolute inset-0 w-full h-full object-cover opacity-20 grayscale">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-200/90 to-gray-50/50"></div>
                <div class="relative z-10 flex flex-col items-center text-gray-400">
                    <svg class="w-12 h-12 mb-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="text-sm font-semibold uppercase tracking-wider">Foto Segera Hadir</span>
                </div>
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

            {{-- Reviews --}}
            @if($reviews->isNotEmpty())
            <div id="ulasan" class="pt-8 border-t border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Ulasan Tamu</h2>
                
                <div class="space-y-6">
                    @foreach($reviews as $review)
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $review->created_at->format('d M Y') }}</div>
                            </div>
                            <div class="bg-white px-2 py-1 rounded border border-gray-200">
                                <x-star-rating :rating="$review->rating" size="4" />
                            </div>
                        </div>
                        @if($review->title)
                            <div class="font-bold text-gray-800 mb-1">{{ $review->title }}</div>
                        @endif
                        <p class="text-sm text-gray-600 italic">"{{ $review->comment }}"</p>
                        
                        @if($review->admin_reply)
                        <div class="mt-4 pl-4 border-l-2 border-primary-500">
                            <div class="text-xs font-semibold text-primary-700 mb-1">Balasan dari Penginapan:</div>
                            <p class="text-sm text-gray-700">{{ $review->admin_reply }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Info & CTA --}}
        <div>
            <div class="bg-white border-gray-100 border rounded-2xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] p-6 sticky top-6 space-y-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $roomType->name }}</h1>
                    @if($roomType->review_count > 0)
                        <div class="flex items-center mt-2 text-sm">
                            <span class="font-bold text-gray-900 mr-2">{{ number_format($roomType->average_rating, 1) }}</span>
                            <x-star-rating :rating="$roomType->average_rating" size="5" />
                            <a href="#ulasan" class="ml-2 text-primary-600 hover:text-primary-800 transition-colors">
                                ({{ $roomType->review_count }} Ulasan)
                            </a>
                        </div>
                    @else
                        <div class="mt-2 text-sm text-gray-500">Belum ada ulasan</div>
                    @endif
                </div>

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

                <div class="border-t border-gray-100 pt-5 mt-2">
                    <p class="text-sm text-gray-500 mb-1">Harga per malam</p>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-white bg-red-500 px-1.5 py-0.5 rounded">Hemat 20%</span>
                        <span class="text-sm text-gray-400 line-through decoration-gray-300">Rp {{ number_format($roomType->base_price * 1.25, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-3xl font-extrabold text-primary-600">Rp {{ number_format($roomType->base_price, 0, ',', '.') }}</p>
                </div>

                {{-- Booking Form --}}
                <form action="{{ route('availability.search') }}" method="GET" class="space-y-3 border-t pt-4"
                      x-data="{
                          submitting: false,
                          checkIn: '{{ request('check_in', date('Y-m-d')) }}',
                          checkOut: '{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}',
                          error: '',
                          adjustCheckOut() {
                              if (this.checkOut <= this.checkIn) {
                                  const next = new Date(this.checkIn);
                                  next.setDate(next.getDate() + 1);
                                  this.checkOut = next.toISOString().split('T')[0];
                              }
                          }
                      }"
                      @submit.prevent="
                          error = '';
                          if (!checkIn) { error = 'Pilih tanggal check-in'; return; }
                          if (!checkOut) { error = 'Pilih tanggal check-out'; return; }
                          if (checkOut <= checkIn) { error = 'Tanggal check-out harus setelah check-in'; return; }
                          submitting = true;
                          $el.submit();
                      ">
                    <input type="hidden" name="room_type" value="{{ $roomType->slug }}">

                    {{-- Error Message --}}
                    <template x-if="error">
                        <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2" x-text="error"></p>
                    </template>

                    @if($errors->any())
                        <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">{{ $errors->first() }}</p>
                    @endif

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal check-in <span class="text-red-500">*</span></label>
                        <input type="date" name="check_in" min="{{ date('Y-m-d') }}" required
                               x-model="checkIn"
                               @change="adjustCheckOut()"
                               class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal check-out <span class="text-red-500">*</span></label>
                        <input type="date" name="check_out" :min="checkIn || '{{ date('Y-m-d', strtotime('+1 day')) }}'" required
                               x-model="checkOut"
                               class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah tamu</label>
                        <input type="number" name="guest_count" min="1" max="{{ $roomType->capacity }}" value="{{ request('guest_count', 2) }}"
                               class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <button type="submit" :disabled="submitting"
                            class="w-full px-4 py-3.5 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-500 hover:-translate-y-1 hover:shadow-lg hover:shadow-primary-600/30 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center">
                        <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!submitting">Cek Ketersediaan</span>
                        <span x-show="submitting" x-cloak>Mencari...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
