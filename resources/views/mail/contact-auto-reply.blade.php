@extends('mail.layout')

@section('content')
<div style="padding: 20px 0;">
    <h2 style="color: #065f46; font-size: 24px; margin-top: 0; margin-bottom: 15px;">Terima Kasih Telah Menghubungi Kami</h2>
    
    <p style="font-size: 16px; line-height: 1.5; color: #4b5563; margin-bottom: 20px;">
        Halo <strong>{{ $contactMessage->name }}</strong>,
    </p>

    <p style="font-size: 16px; line-height: 1.5; color: #4b5563; margin-bottom: 20px;">
        Pesan Anda telah kami terima dengan subjek: <strong>"{{ $contactMessage->subject }}"</strong>. 
    </p>

    <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
        <p style="font-size: 14px; line-height: 1.6; color: #374151; margin: 0; font-style: italic;">
            "{!! nl2br(e($contactMessage->message)) !!}"
        </p>
    </div>

    <p style="font-size: 16px; line-height: 1.5; color: #4b5563; margin-bottom: 20px;">
        Tim kami akan segera membaca pesan Anda dan merespons dalam waktu 1x24 jam. Jika pertanyaan Anda mendesak, Anda juga dapat menghubungi kami langsung melalui WhatsApp di <strong>+62 812-3456-7890</strong>.
    </p>

    <p style="font-size: 16px; line-height: 1.5; color: #4b5563; margin-bottom: 0;">
        Salam hangat,<br>
        <strong>Tim Penginapan Kelapa Sawit</strong>
    </p>
</div>
@endsection
