<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'active');

        $query = Booking::where('user_id', $user->id)->latest();

        $bookings = match($tab) {
            'completed' => (clone $query)->whereIn('status', ['completed', 'checked_out'])->paginate(10),
            'cancelled' => (clone $query)->whereIn('status', ['cancelled', 'expired', 'no_show'])->paginate(10),
            default => (clone $query)->whereIn('status', ['pending_payment', 'confirmed', 'checked_in'])->paginate(10),
        };

        // Proactively expire pending bookings that have passed their deadline
        // This ensures the UI is correct even if the background cron job hasn't run yet
        if ($tab === 'active' || $tab === 'active' /* fallback for default */) {
            $hasExpired = false;
            foreach ($bookings as $booking) {
                if ($booking->status === \App\Enums\BookingStatus::PendingPayment && 
                    $booking->payment_expires_at && 
                    $booking->payment_expires_at->isPast()) {
                    
                    app(\App\Services\BookingService::class)->expirePendingBooking($booking);
                    app(\App\Services\PromotionService::class)->releaseForBooking($booking);
                    app(\App\Services\LoyaltyPointService::class)->reverseRedemptionForBooking($booking);
                    
                    $hasExpired = true;
                }
            }

            // If we expired any, just reload the page to get the fresh data
            if ($hasExpired) {
                return redirect()->route('member.bookings.index', ['tab' => $tab]);
            }
        }

        return view('member.bookings.index', compact('bookings', 'tab'));
    }

    public function show(Booking $booking): View
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }
        $booking->load(['room.roomType', 'statusHistories', 'addons.addon']);
        return view('member.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== \App\Enums\BookingStatus::PendingPayment) {
            return back()->with('error', 'Hanya booking yang menunggu pembayaran yang dapat dibatalkan.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($booking) {
            $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();
            
            $fromStatus = $booking->status->value;

            $booking->update([
                'status' => \App\Enums\BookingStatus::Cancelled->value,
                'cancelled_at' => now(),
                'cancellation_reason' => 'Dibatalkan oleh pelanggan',
            ]);

            \App\Models\BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => $fromStatus,
                'to_status' => \App\Enums\BookingStatus::Cancelled->value,
                'reason' => 'Dibatalkan oleh pelanggan',
                'actor_type' => 'user',
                'actor_id' => auth()->id(),
                'created_at' => now(),
            ]);

            app(\App\Services\PromotionService::class)->releaseForBooking($booking);
            app(\App\Services\LoyaltyPointService::class)->reverseRedemptionForBooking($booking);
        });

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
