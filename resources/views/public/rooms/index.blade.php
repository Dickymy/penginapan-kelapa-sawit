@extends('layouts.public')

@section('title', 'Kamar - Penginapan Kelapa Sawit')

@section('content')
{{-- Page Hero --}}
<section class="bg-primary-700 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-bold">Tipe Kamar Kami</h1>
        <p class="mt-2 text-primary-100">Temukan kamar yang sesuai dengan kebutuhan Anda</p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($roomTypes->isEmpty())
        <x-empty-state message="Belum ada tipe kamar yang tersedia." action="{{ route('home') }}" action-text="Kembali ke Beranda" />
    @else
    <div class="space-y-6">
        @foreach($roomTypes as $type)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition flex flex-col md:flex-row">
            {{-- Image --}}
            <div class="md:w-80 lg:w-96 flex-shrink-0">
                @php $cover = $type->images->where('is_cover', true)->first() ?? $type->images->first(); @endphp
                @if($cover)
                    <img src="{{ Storage::disk('public')->url($cover->path) }}" alt="{{ $type->name }}" class="w-full h-56 md:h-full object-cover">
                @else
                    <div class="w-full h-56 md:h-full min-h-[200px] bg-gray-100 flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-gray-400 text-sm mt-2">Foto belum tersedia</span>
                    </div>
                @endif
            </div>
            {{-- Info --}}
            <div class="flex-1 p-5 md:p-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $type->name }}</h2>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $type->capacity }} orang
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            {{ $type->bed_count }} tempat tidur {{ $type->bed_type ?? '' }}
                        </span>
                    </div>
                    @if($type->short_description)
                        <p class="text-gray-600 text-sm mb-3">{{ $type->short_description }}</p>
                    @endif
                    @if($type->facilities->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($type->facilities->take(6) as $facility)
                            <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs rounded-md">
                                {{ $facility->name }}
                            </span>
                        @endforeach
                        @if($type->facilities->count() > 6)
                            <span class="inline-flex items-center px-2.5 py-1 text-gray-400 text-xs">+{{ $type->facilities->count() - 6 }} lainnya</span>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-4 pt-4 border-t border-gray-100">
                    <div>
                        <span class="text-primary-600 font-bold text-xl">Rp{{ number_format($type->base_price, 0, ',', '.') }}</span>
                        <span class="text-sm text-gray-400">/ malam</span>
                    </div>
                    <a href="{{ route('rooms.show', $type->slug) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
