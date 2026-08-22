@extends('layouts.admin')

@section('title', 'Manajemen Ulasan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Ulasan Tamu</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola dan moderasi ulasan dari tamu yang telah menginap.</p>
    </div>
</div>

{{-- Filters --}}
<div class="mb-6">
    <div class="sm:hidden">
        <select onchange="window.location.href=this.value" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm">
            <option value="{{ route('admin.reviews.index') }}" {{ !request('status') ? 'selected' : '' }}>Semua Ulasan</option>
            <option value="{{ route('admin.reviews.index', ['status' => 'pending']) }}" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Moderasi</option>
            <option value="{{ route('admin.reviews.index', ['status' => 'published']) }}" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
        </select>
    </div>
    <div class="hidden sm:block">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('admin.reviews.index') }}" 
                   class="{{ !request('status') ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                    Semua Ulasan
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" 
                   class="{{ request('status') === 'pending' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                    Menunggu Moderasi
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'published']) }}" 
                   class="{{ request('status') === 'published' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                    Dipublikasikan
                </a>
            </nav>
        </div>
    </div>
</div>

<div class="bg-white shadow ring-1 ring-black ring-opacity-5 rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Detail Ulasan</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 hidden md:table-cell">Kamar & Booking</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Aksi</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($reviews as $review)
                <tr x-data="{ showReplyForm: false }">
                    <td class="whitespace-normal py-4 pl-4 pr-3 text-sm sm:pl-6">
                        <div class="flex items-center mb-1">
                            <x-star-rating :rating="$review->rating" size="4" />
                            <span class="ml-2 font-medium text-gray-900">{{ $review->user->name }}</span>
                        </div>
                        @if($review->title)
                            <div class="font-bold text-gray-900 mt-2">{{ $review->title }}</div>
                        @endif
                        <div class="text-gray-700 mt-1 italic">"{{ $review->comment }}"</div>
                        <div class="text-gray-400 text-xs mt-2">{{ $review->created_at->format('d M Y, H:i') }}</div>

                        {{-- Admin Reply Area --}}
                        <div class="mt-4 p-3 bg-gray-50 rounded-md border border-gray-200 text-sm" x-show="!showReplyForm && {{ $review->admin_reply ? 'true' : 'false' }}">
                            <div class="font-semibold text-primary-700 mb-1">Balasan Anda:</div>
                            <div class="text-gray-700">{{ $review->admin_reply }}</div>
                            <div class="text-gray-400 text-xs mt-1">{{ $review->replied_at?->format('d M Y, H:i') }}</div>
                        </div>

                        {{-- Reply Form --}}
                        <div x-show="showReplyForm" class="mt-4" style="display: none;">
                            <form action="{{ route('admin.reviews.reply', $review) }}" method="POST">
                                @csrf
                                <textarea name="admin_reply" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Tulis balasan untuk ulasan ini...">{{ $review->admin_reply }}</textarea>
                                <div class="mt-2 flex justify-end space-x-2">
                                    <button type="button" @click="showReplyForm = false" class="inline-flex items-center rounded border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none">Batal</button>
                                    <button type="submit" class="inline-flex items-center rounded border border-transparent bg-primary-600 px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none">Simpan Balasan</button>
                                </div>
                            </form>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 hidden md:table-cell">
                        <div class="font-medium text-gray-900">{{ $review->booking->room_type_name_snapshot }}</div>
                        <div>{{ $review->booking->room_name_snapshot }}</div>
                        <a href="{{ route('admin.bookings.show', $review->booking) }}" class="text-primary-600 hover:text-primary-900 mt-1 inline-block">{{ $review->booking->booking_code }}</a>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @if($review->is_published)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                Dipublikasikan
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                Menunggu Moderasi
                            </span>
                        @endif
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex flex-col space-y-2 items-end">
                            {{-- Publish/Unpublish form --}}
                            <form action="{{ route('admin.reviews.publish', $review) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="{{ $review->is_published ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }}">
                                    {{ $review->is_published ? 'Sembunyikan' : 'Publikasikan' }}
                                </button>
                            </form>
                            
                            {{-- Reply button toggle --}}
                            <button type="button" @click="showReplyForm = !showReplyForm" class="text-primary-600 hover:text-primary-900">
                                {{ $review->admin_reply ? 'Edit Balasan' : 'Balas' }}
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500">
                        Belum ada ulasan yang sesuai dengan filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($reviews->hasPages())
        <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
