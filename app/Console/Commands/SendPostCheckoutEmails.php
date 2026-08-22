<?php

namespace App\Console\Commands;

use App\Mail\PostCheckoutMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPostCheckoutEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:send-post-checkout-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim email ucapan terima kasih dan ulasan untuk tamu yang check-out kemarin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = today()->subDay()->toDateString();

        $bookings = Booking::where('status', 'checked_out')
            // Using check_out date as proxy for now, but really should track checked_out_at time.
            ->where('check_out', $yesterday)
            ->whereNotNull('guest_email')
            ->whereNull('checkout_email_sent_at')
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->guest_email)->send(new PostCheckoutMail($booking));
                $booking->update(['checkout_email_sent_at' => now()]);
                $count++;
            } catch (\Exception $e) {
                Log::error("Gagal mengirim email post-checkout (Booking ID: {$booking->id}): " . $e->getMessage());
            }
        }

        $this->info("Berhasil mengirim {$count} email post-checkout.");
    }
}
