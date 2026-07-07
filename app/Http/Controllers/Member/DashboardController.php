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

        $activeBookings = $user->bookings()
            ->whereIn('status', [
                BookingStatus::PendingPayment,
                BookingStatus::Confirmed,
                BookingStatus::CheckedIn,
            ])
            ->count();

        $totalBookings = $user->bookings()->count();

        $recentBookings = $user->bookings()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('member.dashboard', compact(
            'pointBalance',
            'pointValue',
            'activeBookings',
            'totalBookings',
            'recentBookings',
        ));
    }
}
