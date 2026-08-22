@extends('mail.layout')

@section('content')
<h2>Halo {{ $booking->guest_name }},</h2>
<p>Terima kasih telah memilih Penginapan Kelapa Sawit sebagai akomodasi Anda.</p>
<p>Kami berharap Anda menikmati masa menginap yang nyaman dan menyenangkan bersama kami.</p>

<p>Kami akan sangat menghargai jika Anda bersedia meluangkan waktu sejenak untuk membagikan pengalaman Anda.</p>

<!-- Tautan ke form ulasan akan ditambahkan pada Fase 2 -->
<p>Sampai jumpa di kunjungan Anda berikutnya!</p>
@endsection
