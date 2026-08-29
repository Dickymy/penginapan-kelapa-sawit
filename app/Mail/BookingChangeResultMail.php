<?php

namespace App\Mail;

use App\Models\BookingChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingChangeResultMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public BookingChangeRequest $changeRequest
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->changeRequest->status === 'approved' ? 'Disetujui' : 'Ditolak';
        return new Envelope(
            subject: "Pengajuan Perubahan Booking {$status} - " . $this->changeRequest->booking->booking_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking_change_result',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
