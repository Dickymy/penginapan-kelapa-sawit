<?php

namespace App\Http\Controllers\Member;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $pointBalance = $user->loyalty_balance_cache ?? 0;
        $pointValue = $pointBalance * config('loyalty.point_value', 50);

        // Priority 1: Pending payment bookings (most urgent)
        $pendingPaymentBookings = $user->bookings()
            ->where('status', BookingStatus::PendingPayment)
            ->orderBy('payment_expires_at', 'asc')
            ->get();

        $hasExpired = false;
        foreach ($pendingPaymentBookings as $booking) {
            if ($booking->payment_expires_at && $booking->payment_expires_at->isPast()) {
                app(\App\Services\BookingService::class)->expirePendingBooking($booking);
                app(\App\Services\PromotionService::class)->releaseForBooking($booking);
                app(\App\Services\LoyaltyPointService::class)->reverseRedemptionForBooking($booking);
                $hasExpired = true;
            }
        }

        if ($hasExpired) {
            return redirect()->route('member.dashboard');
        }

        $pendingPaymentBookings = $pendingPaymentBookings->where('status', BookingStatus::PendingPayment)->take(3);

        // Priority 2: Upcoming confirmed bookings
        $upcomingBookings = $user->bookings()
            ->where('status', BookingStatus::Confirmed)
            ->where('check_in', '>=', today())
            ->orderBy('check_in', 'asc')
            ->limit(3)
            ->get();

        // Priority 3: Currently checked in
        $checkedInBookings = $user->bookings()
            ->where('status', BookingStatus::CheckedIn)
            ->limit(2)
            ->get();

        // Priority 4: Past bookings without reviews
        $reviewableBookings = $user->bookings()
            ->whereIn('status', [BookingStatus::CheckedOut, BookingStatus::Completed])
            ->doesntHave('review')
            ->orderBy('check_out', 'desc')
            ->limit(3)
            ->get();

        $activeBookings = $user->bookings()
            ->whereIn('status', [
                BookingStatus::PendingPayment,
                BookingStatus::Confirmed,
                BookingStatus::CheckedIn,
            ])
            ->count();

        $totalBookings = $user->bookings()->count();

        // Determine if this is a new user needing onboarding
        $showOnboarding = session()->pull('show_onboarding', false);
        $needsWhatsapp = empty($user->whatsapp);

        return view('member.dashboard', compact(
            'pointBalance',
            'pointValue',
            'activeBookings',
            'totalBookings',
            'pendingPaymentBookings',
            'upcomingBookings',
            'checkedInBookings',
            'reviewableBookings',
            'showOnboarding',
            'needsWhatsapp',
        ));
    }
}
