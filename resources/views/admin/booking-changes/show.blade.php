@extends('layouts.admin')

@section('title', 'Detail Permintaan Perubahan Booking')
@section('page_title', 'Detail Permintaan Perubahan Booking')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Komparasi Data</h5>
                @if($bookingChangeRequest->status === 'pending')
                    <span class="badge bg-warning text-dark">Menunggu Keputusan</span>
                @elseif($bookingChangeRequest->status === 'approved')
                    <span class="badge bg-success">Disetujui</span>
                @elseif($bookingChangeRequest->status === 'rejected')
                    <span class="badge bg-danger">Ditolak</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($bookingChangeRequest->status) }}</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Data Lama -->
                    <div class="col-md-6 border-end">
                        <h6 class="text-muted mb-3">Data Lama (Saat Ini)</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="120" class="text-muted">Check-in</th>
                                <td>{{ \Carbon\Carbon::parse($bookingChangeRequest->original_data['check_in'])->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Check-out</th>
                                <td>{{ \Carbon\Carbon::parse($bookingChangeRequest->original_data['check_out'])->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tipe Kamar</th>
                                <td>{{ \App\Models\RoomType::find($bookingChangeRequest->original_data['room_type_id'])->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Jumlah Tamu</th>
                                <td>{{ $bookingChangeRequest->original_data['guest_count'] }} Orang</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Total Harga</th>
                                <td>Rp{{ number_format($bookingChangeRequest->original_data['total_amount'], 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                    <!-- Data Baru -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Data Baru (Pengajuan)</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="120" class="text-muted">Check-in</th>
                                <td>
                                    <span class="{{ $bookingChangeRequest->original_data['check_in'] !== $bookingChangeRequest->requested_data['check_in'] ? 'text-primary fw-bold' : '' }}">
                                        {{ \Carbon\Carbon::parse($bookingChangeRequest->requested_data['check_in'])->format('d M Y') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Check-out</th>
                                <td>
                                    <span class="{{ $bookingChangeRequest->original_data['check_out'] !== $bookingChangeRequest->requested_data['check_out'] ? 'text-primary fw-bold' : '' }}">
                                        {{ \Carbon\Carbon::parse($bookingChangeRequest->requested_data['check_out'])->format('d M Y') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tipe Kamar</th>
                                <td>
                                    @php $newRoomType = \App\Models\RoomType::find($bookingChangeRequest->requested_data['room_type_id']) @endphp
                                    <span class="{{ $bookingChangeRequest->original_data['room_type_id'] !== $bookingChangeRequest->requested_data['room_type_id'] ? 'text-primary fw-bold' : '' }}">
                                        {{ $newRoomType ? $newRoomType->name : '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Jumlah Tamu</th>
                                <td>
                                    <span class="{{ $bookingChangeRequest->original_data['guest_count'] != $bookingChangeRequest->requested_data['guest_count'] ? 'text-primary fw-bold' : '' }}">
                                        {{ $bookingChangeRequest->requested_data['guest_count'] }} Orang
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h6 class="mb-1">Selisih Harga</h6>
                        @if($bookingChangeRequest->price_difference > 0)
                            <div class="text-danger fw-bold fs-5">+ Rp{{ number_format($bookingChangeRequest->price_difference, 0, ',', '.') }}</div>
                            <small class="text-muted">Tamu wajib membayar kekurangan ini setelah disetujui (Tagihan Midtrans akan terbuat otomatis).</small>
                        @elseif($bookingChangeRequest->price_difference < 0)
                            <div class="text-primary fw-bold fs-5">- Rp{{ number_format(abs($bookingChangeRequest->price_difference), 0, ',', '.') }}</div>
                            <small class="text-muted">Sistem akan menandai ini sebagai kelebihan bayar. Mohon proses refund ke tamu secara manual.</small>
                        @else
                            <div class="fw-bold fs-5">Rp0</div>
                            <small class="text-muted">Tidak ada perubahan harga.</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informasi Pengajuan</h5>
            </div>
            <div class="card-body">
                <p><strong>Diajukan Oleh:</strong><br>
                {{ $bookingChangeRequest->user->name }} ({{ $bookingChangeRequest->user->email }})</p>

                <p><strong>Waktu Pengajuan:</strong><br>
                {{ $bookingChangeRequest->created_at->format('d M Y, H:i') }}</p>

                <p><strong>Tipe Perubahan:</strong><br>
                {{ ucfirst(str_replace('_', ' ', $bookingChangeRequest->type)) }}</p>
                
                @if($bookingChangeRequest->status !== 'pending')
                    <hr>
                    <p><strong>Diproses Pada:</strong><br>
                    {{ $bookingChangeRequest->processed_at ? $bookingChangeRequest->processed_at->format('d M Y, H:i') : '-' }}</p>
                    
                    <p><strong>Diproses Oleh:</strong><br>
                    {{ $bookingChangeRequest->processedByAdmin->name ?? '-' }}</p>
                    
                    @if($bookingChangeRequest->admin_notes)
                        <p><strong>Catatan Keputusan:</strong><br>
                        {{ $bookingChangeRequest->admin_notes }}</p>
                    @endif
                @endif
            </div>
        </div>

        @if($bookingChangeRequest->status === 'pending')
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Aksi Keputusan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.booking-changes.approve', $bookingChangeRequest) }}" method="POST" id="approveForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Catatan untuk Tamu (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Pesan ini akan dikirim via email..."></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin menyetujui perubahan ini? Sistem akan memperbarui data booking.')">
                            <i class="bi bi-check-circle me-1"></i> Setujui Pengajuan
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <form action="{{ route('admin.booking-changes.reject', $bookingChangeRequest) }}" method="POST" id="rejectForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-danger">Alasan Penolakan (Wajib jika menolak)</label>
                        <textarea name="notes" class="form-control border-danger" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin MENOLAK perubahan ini?')">
                            <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
