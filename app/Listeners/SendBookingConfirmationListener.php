<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking;

        if (empty($booking->guest_email)) {
            return;
        }

        // Idempotency check
        if ($booking->confirmation_email_sent_at !== null) {
            return;
        }

        try {
            Mail::to($booking->guest_email)->send(new BookingConfirmationMail($booking));
            
            $booking->update([
                'confirmation_email_sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email konfirmasi booking: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
            ]);
            throw $e; // Re-throw to retry in queue
        }
    }
}
