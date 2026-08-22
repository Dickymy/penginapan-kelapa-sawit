@extends('mail.layout')

@section('content')
<h2>Halo {{ $booking->guest_name }},</h2>
<p>Terima kasih telah melakukan pemesanan di Penginapan Kelapa Sawit. Berikut adalah rincian pesanan Anda:</p>

<table class="data-table">
    <tr>
        <th>Kode Booking</th>
        <td>{{ $booking->booking_code }}</td>
    </tr>
    <tr>
        <th>Tipe Kamar</th>
        <td>{{ $booking->room_type_name_snapshot }}</td>
    </tr>
    <tr>
        <th>Check-in</th>
        <td>{{ $booking->check_in->format('d M Y') }}</td>
    </tr>
    <tr>
        <th>Check-out</th>
        <td>{{ $booking->check_out->format('d M Y') }}</td>
    </tr>
    <tr>
        <th>Total Pembayaran</th>
        <td>{{ $booking->formatted_total }}</td>
    </tr>
</table>

<p>Harap segera selesaikan pembayaran Anda sebelum batas waktu yang telah ditentukan agar pesanan Anda dapat dikonfirmasi.</p>
@endsection
