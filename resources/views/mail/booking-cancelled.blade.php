@extends('mail.layout')

@section('content')
<h2>Halo {{ $booking->guest_name }},</h2>
<p>Dengan menyesal kami informasikan bahwa pesanan Anda dengan kode <strong>{{ $booking->booking_code }}</strong> telah dibatalkan.</p>
<p>Jika ini adalah pembatalan otomatis karena batas waktu pembayaran, Anda dapat membuat pesanan baru kapan saja melalui website kami.</p>

<p>Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi kami.</p>
@endsection
