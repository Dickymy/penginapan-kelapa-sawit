<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Mail\BookingCancelledMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBookingCancelledListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(BookingCancelled $event): void
    {
        $booking = $event->booking;

        if (empty($booking->guest_email)) {
            return;
        }

        // Idempotency check
        if ($booking->cancellation_email_sent_at !== null) {
            return;
        }

        try {
            Mail::to($booking->guest_email)->send(new BookingCancelledMail($booking));
            
            $booking->update([
                'cancellation_email_sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email pembatalan booking: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
            ]);
            throw $e;
        }
    }
}
