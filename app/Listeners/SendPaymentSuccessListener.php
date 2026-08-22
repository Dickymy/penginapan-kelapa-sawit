<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Mail\PaymentSuccessMail;
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

        try {
            Mail::to($booking->guest_email)->send(new PaymentSuccessMail($booking));
            
            $booking->update([
                'payment_email_sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email sukses pembayaran: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
            ]);
            throw $e;
        }
    }
}
