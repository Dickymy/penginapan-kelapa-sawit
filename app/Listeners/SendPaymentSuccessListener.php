<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Mail\PaymentSuccessMail;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPaymentSuccessListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(PaymentConfirmed $event): void
    {
        $booking = $event->booking;

        if (empty($booking->guest_email)) {
            return;
        }

        // Idempotency check
        if ($booking->payment_email_sent_at !== null) {
            return;
        }

        $sendMail = function () use ($booking) {
            try {
                $freshBooking = Booking::find($booking->id);
                if (!$freshBooking || $freshBooking->payment_email_sent_at !== null) {
                    return;
                }

                Mail::to($freshBooking->guest_email)->send(new PaymentSuccessMail($freshBooking));
                
                $freshBooking->update([
                    'payment_email_sent_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email sukses pembayaran: ' . $e->getMessage(), [
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
