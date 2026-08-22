@extends('layouts.admin')

@section('title', 'Permintaan Perubahan Booking')
@section('page_title', 'Permintaan Perubahan Booking')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Tamu</th>
                        <th>Tipe Perubahan</th>
                        <th>Selisih Harga</th>
                        <th>Status</th>
                        <th>Diajukan Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>
                            <a href="{{ route('admin.bookings.show', $req->booking) }}" class="fw-bold text-decoration-none">
                                {{ $req->booking->booking_code }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $req->user->name }}</div>
                            <div class="text-muted small">{{ $req->user->email }}</div>
                        </td>
                        <td>
                            {{ ucfirst(str_replace('_', ' ', $req->type)) }}
                        </td>
                        <td>
                            @if($req->price_difference > 0)
                                <span class="text-danger">+ Rp{{ number_format($req->price_difference, 0, ',', '.') }}</span>
                            @elseif($req->price_difference < 0)
                                <span class="text-primary">- Rp{{ number_format(abs($req->price_difference), 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($req->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($req->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($req->status) }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $req->created_at->format('d M Y H:i') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.booking-changes.show', $req) }}" class="btn btn-sm btn-primary">
                                @if($req->status === 'pending') Proses @else Detail @endif
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada permintaan perubahan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
