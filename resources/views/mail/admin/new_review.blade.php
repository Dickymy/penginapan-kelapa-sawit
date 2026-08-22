@extends('mail.layout')

@section('header', 'Ulasan Baru Diterima')

@section('content')
<p style="margin: 0 0 16px;">Halo Admin,</p>

<p style="margin: 0 0 16px;">Tamu <strong>{{ $review->booking->guest_name }}</strong> baru saja mengirimkan ulasan untuk pemesanan kamar mereka.</p>

<div style="background-color: #f3f4f6; padding: 16px; border-radius: 6px; margin-bottom: 24px;">
    <h3 style="margin: 0 0 12px; font-size: 16px; color: #1f2937;">Detail Ulasan</h3>
    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
        <tr>
            <td style="padding-bottom: 8px; color: #6b7280; width: 120px;">Kode Booking:</td>
            <td style="padding-bottom: 8px; font-weight: 600;">{{ $review->booking->booking_code }}</td>
        </tr>
        <tr>
            <td style="padding-bottom: 8px; color: #6b7280;">Kamar:</td>
            <td style="padding-bottom: 8px;">{{ $review->booking->room_type_name_snapshot }} - {{ $review->booking->room_name_snapshot }}</td>
        </tr>
        <tr>
            <td style="padding-bottom: 8px; color: #6b7280;">Rating:</td>
            <td style="padding-bottom: 8px;"><strong>{{ $review->rating }} / 5</strong></td>
        </tr>
        @if($review->title)
        <tr>
            <td style="padding-bottom: 8px; color: #6b7280;">Judul:</td>
            <td style="padding-bottom: 8px;">{{ $review->title }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding-top: 8px; color: #6b7280; vertical-align: top;">Komentar:</td>
            <td style="padding-top: 8px; font-style: italic;">"{{ $review->comment }}"</td>
        </tr>
    </table>
</div>

<p style="margin: 0 0 24px;">Ulasan saat ini belum dipublikasikan. Silakan masuk ke panel admin untuk meninjau, mempublikasikan, atau membalas ulasan ini.</p>

<div style="text-align: center; margin: 32px 0;">
    <a href="{{ route('admin.dashboard') }}" style="display: inline-block; padding: 12px 24px; background-color: #059669; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">Lihat Ulasan di Dashboard Admin</a>
</div>
@endsection
