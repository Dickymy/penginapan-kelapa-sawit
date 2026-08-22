<?php

namespace App\Console\Commands;

use App\Mail\CheckinReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCheckinReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:send-checkin-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim email pengingat check-in untuk tamu yang akan tiba besok';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = today()->addDay()->toDateString();

        $bookings = Booking::where('status', 'confirmed')
            ->where('check_in', $tomorrow)
            ->whereNotNull('guest_email')
            ->whereNull('reminder_email_sent_at')
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->guest_email)->send(new CheckinReminderMail($booking));
                $booking->update(['reminder_email_sent_at' => now()]);
                $count++;
            } catch (\Exception $e) {
                Log::error("Gagal mengirim pengingat check-in (Booking ID: {$booking->id}): " . $e->getMessage());
            }
        }

        $this->info("Berhasil mengirim {$count} email pengingat check-in.");
    }
}
