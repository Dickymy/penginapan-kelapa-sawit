<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingBookingsCommand extends Command
{
    protected $signature = 'booking:expire-pending';
    protected $description = 'Expire pending bookings that have passed their payment deadline';

    public function handle(BookingService $bookingService): int
    {
        $bookings = Booking::where('status', BookingStatus::PendingPayment->value)
            ->where('payment_expires_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            $expired = DB::transaction(function () use ($booking, $bookingService) {
                // Re-fetch with lock
                $locked = Booking::where('id', $booking->id)->lockForUpdate()->first();

                if (! $locked || $locked->status !== BookingStatus::PendingPayment) {
                    return false;
                }

                if ($locked->payment_expires_at->isFuture()) {
                    return false;
                }

                $bookingService->expirePendingBooking($locked);

                // Release reserved promotion quota
                app(\App\Services\PromotionService::class)->releaseForBooking($locked);

                // Reverse any redeemed loyalty points
                app(\App\Services\LoyaltyPointService::class)->reverseRedemptionForBooking($locked);
                
                return true;
            });

            if ($expired) {
                $count++;
            }
        }

        $this->info("Expired {$count} pending booking(s).");

        return self::SUCCESS;
    }
}
