@extends('mail.layout')

@section('content')
<h2>Halo {{ $booking->guest_name }},</h2>
<p>Kami mengingatkan bahwa jadwal check-in Anda di Penginapan Kelapa Sawit adalah besok, <strong>{{ $booking->check_in->format('d M Y') }}</strong>.</p>

<p>Kamar <strong>{{ $booking->room_type_name_snapshot }}</strong> Anda telah kami siapkan.</p>
<p>Waktu check-in dimulai pada pukul 14:00 WITA. Jika Anda memiliki permintaan khusus atau akan tiba terlambat, jangan ragu untuk membalas email ini atau menghubungi kami via WhatsApp.</p>

<p>Semoga perjalanan Anda menyenangkan!</p>
@endsection
