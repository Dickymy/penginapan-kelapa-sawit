@extends('layouts.admin')

@section('title', 'Detail Permintaan Perubahan Booking')
@section('page-title', 'Detail Permintaan Perubahan Booking')

@section('content')
<div class="space-y-4">
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800">Perubahan Booking: {{ $bookingChangeRequest->booking->booking_code }}</h1>
        </div>
        <a href="{{ route('admin.booking-changes.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span class="hidden sm:inline">Kembali</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Komparasi Data -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-base font-semibold text-gray-800">Komparasi Data</h2>
                    @if($bookingChangeRequest->status === 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Menunggu Keputusan</span>
                    @elseif($bookingChangeRequest->status === 'approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Disetujui</span>
                    @elseif($bookingChangeRequest->status === 'rejected')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">{{ ucfirst($bookingChangeRequest->status) }}</span>
                    @endif
                </div>
                <div class="p-5">
                    @if($bookingChangeRequest->type === 'cancellation')
                        <div class="space-y-6">
                            <div class="bg-red-50 p-4 rounded-xl border border-red-100 flex items-start gap-3">
                                <svg class="w-6 h-6 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h3 class="text-sm font-bold text-red-800">Permintaan Pembatalan Booking</h3>
                                    <p class="text-sm text-red-700 mt-1">Tamu ini mengajukan pembatalan booking. Jika disetujui, booking akan dibatalkan, kamar akan dilepas, dan admin wajib mengembalikan dana ke rekening tamu di bawah ini.</p>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800 mb-3 uppercase tracking-wider">Alasan Pembatalan</h3>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                    {{ $bookingChangeRequest->requested_data['reason'] ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-800 mb-3 uppercase tracking-wider">Rekening Tujuan Refund</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Nama Bank</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $bookingChangeRequest->requested_data['bank_name'] ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Nomor Rekening</p>
                                        <p class="text-sm font-medium text-gray-900 font-mono">{{ $bookingChangeRequest->requested_data['account_number'] ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Atas Nama</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $bookingChangeRequest->requested_data['account_name'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                            <!-- Data Lama -->
                            <div class="relative md:after:content-[''] md:after:absolute md:after:top-0 md:after:bottom-0 md:after:-right-4 md:after:w-px md:after:bg-gray-200">
                                <h3 class="text-sm font-semibold text-gray-500 mb-4 uppercase tracking-wider">Data Lama (Saat Ini)</h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Check-in</p>
                                        <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($bookingChangeRequest->original_data['check_in'])->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Check-out</p>
                                        <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($bookingChangeRequest->original_data['check_out'])->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tipe Kamar</p>
                                        <p class="text-sm font-medium text-gray-900">{{ \App\Models\RoomType::find($bookingChangeRequest->original_data['room_type_id'])->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Jumlah Tamu</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $bookingChangeRequest->original_data['guest_count'] }} Orang</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Total Harga</p>
                                        <p class="text-sm font-medium text-gray-900">Rp{{ number_format($bookingChangeRequest->original_data['total_amount'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Baru -->
                            <div class="mt-6 md:mt-0 pt-6 md:pt-0 border-t md:border-t-0 border-gray-200">
                                <h3 class="text-sm font-semibold text-primary-600 mb-4 uppercase tracking-wider">Data Baru (Pengajuan)</h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Check-in</p>
                                        <p class="text-sm {{ $bookingChangeRequest->original_data['check_in'] !== $bookingChangeRequest->requested_data['check_in'] ? 'font-bold text-primary-700 bg-primary-50 px-2 py-0.5 rounded-md inline-block -ml-2' : 'font-medium text-gray-900' }}">
                                            {{ \Carbon\Carbon::parse($bookingChangeRequest->requested_data['check_in'])->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Check-out</p>
                                        <p class="text-sm {{ $bookingChangeRequest->original_data['check_out'] !== $bookingChangeRequest->requested_data['check_out'] ? 'font-bold text-primary-700 bg-primary-50 px-2 py-0.5 rounded-md inline-block -ml-2' : 'font-medium text-gray-900' }}">
                                            {{ \Carbon\Carbon::parse($bookingChangeRequest->requested_data['check_out'])->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tipe Kamar</p>
                                        @php $newRoomType = \App\Models\RoomType::find($bookingChangeRequest->requested_data['room_type_id'] ?? null) @endphp
                                        <p class="text-sm {{ ($bookingChangeRequest->original_data['room_type_id'] ?? null) !== ($bookingChangeRequest->requested_data['room_type_id'] ?? null) ? 'font-bold text-primary-700 bg-primary-50 px-2 py-0.5 rounded-md inline-block -ml-2' : 'font-medium text-gray-900' }}">
                                            {{ $newRoomType ? $newRoomType->name : '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Jumlah Tamu</p>
                                        <p class="text-sm {{ ($bookingChangeRequest->original_data['guest_count'] ?? 0) != ($bookingChangeRequest->requested_data['guest_count'] ?? 0) ? 'font-bold text-primary-700 bg-primary-50 px-2 py-0.5 rounded-md inline-block -ml-2' : 'font-medium text-gray-900' }}">
                                            {{ $bookingChangeRequest->requested_data['guest_count'] ?? '-' }} Orang
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Total Harga Baru</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            Rp{{ number_format($bookingChangeRequest->original_data['total_amount'] + $bookingChangeRequest->price_difference, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!empty($bookingChangeRequest->requested_data['addon_details']))
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-500 mb-3 uppercase tracking-wider">Layanan Tambahan (Terbawa Otomatis)</h3>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                    <ul class="space-y-2">
                                        @foreach($bookingChangeRequest->requested_data['addon_details'] as $addon)
                                            <li class="text-sm flex justify-between items-center">
                                                <span class="text-gray-700">{{ $addon['name'] }} <span class="text-gray-500 text-xs ml-1">(x{{ $addon['quantity'] }})</span></span>
                                                <span class="font-medium text-gray-900">Rp{{ number_format($addon['subtotal'], 0, ',', '.') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="border-t border-gray-200 bg-gray-50/50 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">Selisih Harga</h3>
                    @if($bookingChangeRequest->type === 'cancellation')
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-bold text-red-600">Rp{{ number_format(abs($bookingChangeRequest->price_difference), 0, ',', '.') }}</span>
                            <span class="text-xs text-red-700 bg-red-100 px-2 py-1 rounded-md">Total Refund</span>
                            @if(isset($bookingChangeRequest->requested_data['penalty_applied']) && $bookingChangeRequest->requested_data['penalty_applied'])
                                <span class="text-xs text-amber-700 bg-amber-100 px-2 py-1 rounded-md">Dipotong Penalti 50%</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Ini adalah jumlah yang harus Anda transfer ke rekening tamu. Selisih ini dihitung berdasarkan uang riil yang masuk. Poin loyalitas akan otomatis dikembalikan 100%.</p>
                    @elseif($bookingChangeRequest->price_difference > 0)
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-bold text-amber-600">+ Rp{{ number_format($bookingChangeRequest->price_difference, 0, ',', '.') }}</span>
                            <span class="text-xs text-amber-700 bg-amber-100 px-2 py-1 rounded-md">Tamu Kurang Bayar</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Tamu wajib membayar kekurangan ini setelah disetujui (Tagihan Midtrans akan terbuat otomatis).</p>
                    @elseif($bookingChangeRequest->price_difference < 0)
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-bold text-emerald-600">- Rp{{ number_format(abs($bookingChangeRequest->price_difference), 0, ',', '.') }}</span>
                            <span class="text-xs text-emerald-700 bg-emerald-100 px-2 py-1 rounded-md">Kelebihan Bayar</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Sistem akan menandai ini sebagai kelebihan bayar. Mohon proses refund ke tamu secara manual.</p>
                    @else
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-bold text-gray-800">Rp0</span>
                            <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-md">Tidak Ada Perubahan</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Tidak ada selisih harga untuk perubahan ini.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Info & Aksi -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4 bg-gray-50/50">
                    <h2 class="text-base font-semibold text-gray-800">Informasi Pengajuan</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Diajukan Oleh</p>
                        <p class="text-sm font-medium text-gray-900">{{ $bookingChangeRequest->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $bookingChangeRequest->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Waktu Pengajuan</p>
                        <p class="text-sm font-medium text-gray-900">{{ $bookingChangeRequest->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tipe Perubahan</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            {{ ucfirst(str_replace('_', ' ', $bookingChangeRequest->type)) }}
                        </span>
                    </div>

                    @if($bookingChangeRequest->status !== 'pending')
                        <div class="pt-4 mt-4 border-t border-gray-200 space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Diproses Pada</p>
                                <p class="text-sm font-medium text-gray-900">{{ $bookingChangeRequest->processed_at ? $bookingChangeRequest->processed_at->format('d M Y, H:i') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Diproses Oleh</p>
                                <p class="text-sm font-medium text-gray-900">{{ $bookingChangeRequest->processedByAdmin->name ?? '-' }}</p>
                            </div>
                            @if($bookingChangeRequest->admin_notes)
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Catatan Keputusan</p>
                                    <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700 border border-gray-100">
                                        {{ $bookingChangeRequest->admin_notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($bookingChangeRequest->status === 'pending')
                <div class="bg-white rounded-xl border border-primary-200 overflow-hidden shadow-sm shadow-primary-100/50">
                    <div class="border-b border-primary-100 px-5 py-4 bg-primary-50">
                        <h2 class="text-base font-semibold text-primary-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Aksi Keputusan
                        </h2>
                    </div>
                    <div class="p-5 space-y-6">
                        <form action="{{ route('admin.booking-changes.approve', $bookingChangeRequest) }}" method="POST" id="approveForm">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan untuk Tamu (Opsional)</label>
                                <textarea name="notes" rows="2" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Pesan ini akan dikirim via email..."></textarea>
                            </div>
                            <div class="mt-3">
                                <button type="button" @click="$dispatch('open-confirm', { id: 'confirm-approve' })" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:border-emerald-800 focus:ring ring-emerald-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Setujui Pengajuan
                                </button>
                                
                                <!-- Tailwind Modal for Approve -->
                                <x-confirm-modal 
                                    id="confirm-approve"
                                    title="Setujui Perubahan" 
                                    message="Apakah Anda yakin menyetujui perubahan ini? Sistem akan memperbarui data reservasi dan mengirim notifikasi email ke tamu."
                                    confirm-text="Ya, Setujui"
                                    cancel-text="Batal"
                                    variant="primary"
                                    form-id="approveForm"
                                />
                            </div>
                        </form>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="px-2 bg-white text-xs text-gray-500">atau</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.booking-changes.reject', $bookingChangeRequest) }}" method="POST" id="rejectForm">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-red-700 mb-1">Alasan Penolakan (Opsional)</label>
                                <textarea name="notes" rows="2" class="block w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Jelaskan alasan penolakan..."></textarea>
                            </div>
                            <div class="mt-3">
                                <button type="button" @click="$dispatch('open-confirm', { id: 'confirm-reject' })" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-white border border-red-300 rounded-lg font-semibold text-xs text-red-700 uppercase tracking-widest hover:bg-red-50 active:bg-red-100 focus:outline-none focus:border-red-400 focus:ring ring-red-200 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak Pengajuan
                                </button>

                                <!-- Tailwind Modal for Reject -->
                                <x-confirm-modal 
                                    id="confirm-reject"
                                    title="Tolak Perubahan" 
                                    message="Apakah Anda yakin MENOLAK perubahan ini? Permintaan tamu akan dibatalkan."
                                    confirm-text="Ya, Tolak"
                                    cancel-text="Batal"
                                    variant="danger"
                                    form-id="rejectForm"
                                />
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
