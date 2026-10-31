<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
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

        $sendMail = function () use ($booking) {
            try {
                $freshBooking = Booking::find($booking->id);
                if (!$freshBooking || $freshBooking->confirmation_email_sent_at !== null) {
                    return;
                }

                Mail::to($freshBooking->guest_email)->send(new BookingConfirmationMail($freshBooking));
                
                $freshBooking->update([
                    'confirmation_email_sent_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email konfirmasi booking: ' . $e->getMessage(), [
                    'booking_id' => $booking->id,
                ]);

                if (config('queue.default') !== 'sync') {
                    throw $e;
                }
            }
        };

        if (config('queue.default') === 'sync' && !app()->runningUnitTests()) {
            dispatch($sendMail)->afterResponse();
        } else {
            $sendMail();
        }
    }
}
