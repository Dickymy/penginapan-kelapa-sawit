@extends('layouts.member')

@section('title', 'Tulis Ulasan')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-3 text-sm text-gray-500 mb-2">
        <a href="{{ route('member.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
        <span>/</span>
        <a href="{{ route('member.bookings.show', $booking) }}" class="hover:text-primary-600 transition-colors">Booking {{ $booking->booking_code }}</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Tulis Ulasan</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Tulis Ulasan</h1>
    <p class="text-gray-600 mt-1">Bagikan pengalaman menginap Anda di {{ $booking->room_type_name_snapshot }}</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <form action="{{ route('member.reviews.store') }}" method="POST" class="p-6 md:p-8" x-data="{ rating: {{ old('rating', 5) }} }">
        @csrf
        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

        <div class="mb-8">
            <h3 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Pemesanan</h3>
            <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="bg-white p-3 rounded shadow-sm border border-gray-100 mr-4">
                    <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-gray-900">{{ $booking->room_type_name_snapshot }} ({{ $booking->room_name_snapshot }})</div>
                    <div class="text-sm text-gray-500 mt-1">
                        Check-in: {{ $booking->check_in->format('d M Y') }} &bull; Check-out: {{ $booking->check_out->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <label class="block text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Rating Anda <span class="text-red-500">*</span></label>
            <div class="flex items-center space-x-2">
                <template x-for="i in 5">
                    <button type="button" 
                        @click="rating = i"
                        @mouseenter="rating = i"
                        class="focus:outline-none transition-transform hover:scale-110">
                        <svg class="w-10 h-10 transition-colors" :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </button>
                </template>
                <input type="hidden" name="rating" x-model="rating">
            </div>
            @error('rating')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">Judul Ulasan (Opsional)</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" 
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                placeholder="Mis: Penginapan yang sangat nyaman!">
            @error('title')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-8">
            <label for="comment" class="block text-sm font-semibold text-gray-900 mb-2">Ceritakan Pengalaman Anda <span class="text-red-500">*</span></label>
            <textarea id="comment" name="comment" rows="5" required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                placeholder="Bagaimana pelayanan, kebersihan, dan kenyamanan kamar?">{{ old('comment') }}</textarea>
            <p class="mt-2 text-xs text-gray-500">Minimal 10 karakter.</p>
            @error('comment')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('member.dashboard') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors shadow-sm">
                Kirim Ulasan
            </button>
        </div>
    </form>
</div>
@endsection
