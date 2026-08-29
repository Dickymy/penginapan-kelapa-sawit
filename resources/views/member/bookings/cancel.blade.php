@extends('layouts.member')

@section('title', 'Batalkan Booking - Penginapan Kelapa Sawit')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="mb-8">
        <a href="{{ route('member.bookings.show', $booking) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Detail Booking
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-4">Pengajuan Pembatalan Booking</h1>
        <p class="text-gray-600 mt-2">ID Booking: <span class="font-mono font-medium">{{ $booking->booking_code }}</span></p>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-8">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="font-semibold text-gray-900">Rincian Pengembalian Dana (Refund)</h2>
        </div>
        <div class="px-5 sm:px-6 py-5 space-y-4">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total Pembayaran Asli</span>
                <span class="text-gray-900">Rp{{ number_format($booking->total_amount, 0, ',', '.') }}</span>
            </div>
            @if($preview['points_to_return'])
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Poin Loyalitas yang Digunakan</span>
                    <span class="text-amber-600 font-medium">Akan dikembalikan</span>
                </div>
            @endif
            @if($preview['penalty_applied'])
                <div class="pt-2 flex justify-between text-sm text-red-600 font-medium">
                    <span>Penalti Pembatalan H-1 / H-2 (50%)</span>
                    <span>- Rp{{ number_format($booking->total_amount * 0.5, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="pt-4 mt-2 border-t border-gray-100 flex justify-between items-center">
                <span class="font-bold text-gray-900">Dana yang Akan Dikembalikan</span>
                <span class="font-bold text-xl text-primary-600">Rp{{ number_format($preview['refundable_amount'], 0, ',', '.') }}</span>
            </div>
            
            @if($preview['penalty_applied'])
                <div class="bg-red-50 text-red-800 text-sm p-4 rounded-xl mt-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p>Karena Anda membatalkan pesanan terlalu dekat dengan tanggal check-in (H-1 atau H-2), maka <strong>pengembalian dana Anda dipotong 50%</strong>. Namun, poin loyalitas Anda akan tetap dikembalikan 100%.</p>
                </div>
            @else
                <div class="bg-blue-50 text-blue-800 text-sm p-4 rounded-xl mt-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p>Pengajuan pembatalan Anda akan direview oleh admin. Jika disetujui, dana akan dikembalikan penuh (100%) ke rekening bank Anda.</p>
                </div>
            @endif
        </div>
    </div>

    <form action="{{ route('member.booking-cancellations.store', $booking) }}" method="POST" id="cancelForm">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="font-semibold text-gray-900">Formulir Rekening Bank & Alasan Pembatalan</h2>
            </div>
            <div class="px-5 sm:px-6 py-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_name" required placeholder="Contoh: BCA, BNI, Mandiri, BRI" class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 @error('bank_name') border-red-500 @enderror" value="{{ old('bank_name') }}">
                    @error('bank_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="account_number" required placeholder="Masukkan nomor rekening tujuan" class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 @error('account_number') border-red-500 @enderror" value="{{ old('account_number') }}">
                    @error('account_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama (Pemilik Rekening) <span class="text-red-500">*</span></label>
                    <input type="text" name="account_name" required placeholder="Nama sesuai pada buku tabungan" class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 @error('account_name') border-red-500 @enderror" value="{{ old('account_name') }}">
                    @error('account_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pembatalan <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="4" required placeholder="Beritahu kami mengapa Anda ingin membatalkan booking ini..." class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                    @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div x-data class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="button" @click="$dispatch('open-confirm', { id: 'confirm-cancel' })" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    Kirim Pengajuan Pembatalan
                </button>
                
                <x-confirm-modal 
                    id="confirm-cancel"
                    title="Batalkan Booking" 
                    message="Apakah Anda yakin ingin membatalkan pesanan ini? Pengajuan pembatalan yang sudah dikirim tidak dapat ditarik kembali."
                    confirm-text="Ya, Batalkan"
                    cancel-text="Batal"
                    variant="danger"
                    form-id="cancelForm"
                />
            </div>
        </div>
    </form>
</div>
@endsection
