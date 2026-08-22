@extends('mail.layout')

@section('content')
<h2>Halo {{ $booking->guest_name }},</h2>
<p>Pembayaran Anda untuk pesanan <strong>{{ $booking->booking_code }}</strong> telah berhasil kami terima. Pesanan Anda kini telah dikonfirmasi.</p>

<table class="data-table">
    <tr>
        <th>Tipe Kamar</th>
        <td>{{ $booking->room_type_name_snapshot }}</td>
    </tr>
    <tr>
        <th>Check-in</th>
        <td>{{ $booking->check_in->format('d M Y') }} (mulai 14:00 WITA)</td>
    </tr>
    <tr>
        <th>Check-out</th>
        <td>{{ $booking->check_out->format('d M Y') }} (sebelum 12:00 WITA)</td>
    </tr>
</table>

<p>Anda dapat mengunduh Invoice Anda melalui tautan di bawah ini:</p>
<p style="text-align: center;">
    <a href="{{ route('booking.invoice', $booking->booking_code) }}" class="button">Unduh Invoice</a>
</p>

<p>Kami menantikan kedatangan Anda di Penginapan Kelapa Sawit.</p>
@endsection
