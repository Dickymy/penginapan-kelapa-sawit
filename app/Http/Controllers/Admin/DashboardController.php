<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        // Today's check-ins (confirmed, ready to check in)
        $checkInsToday = Booking::where('check_in', $today->toDateString())
            ->where('status', BookingStatus::Confirmed->value)
            ->with('room')
            ->get();

        // Today's check-outs (currently checked in, should leave today)
        $checkOutsToday = Booking::where('check_out', $today->toDateString())
            ->where('status', BookingStatus::CheckedIn->value)
            ->with('room')
            ->get();

        // Pending payment (active, not expired)
        $pendingPayment = Booking::where('status', BookingStatus::PendingPayment->value)
            ->where(function ($q) {
                $q->whereNull('payment_expires_at')
                  ->orWhere('payment_expires_at', '>', now());
            })
            ->count();

        // Needs attention
        $needsAttention = Booking::where('needs_attention', true)->count();

        // Room occupancy
        $totalRooms = Room::where('is_active', true)->count();
        $occupiedRooms = Booking::where('status', BookingStatus::CheckedIn->value)->count();

        // Monthly revenue
        $startOfMonth = $today->copy()->startOfMonth();
        $monthlyRevenue = Booking::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfMonth, now()])
            ->sum('total_amount');

        // Recent bookings (last 5)
        $recentBookings = Booking::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'checkInsToday',
            'checkOutsToday',
            'pendingPayment',
            'needsAttention',
            'totalRooms',
            'occupiedRooms',
            'monthlyRevenue',
            'recentBookings'
        ));
    }
}
