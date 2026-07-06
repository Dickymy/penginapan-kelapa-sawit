<?php

namespace App\Http\Controllers\Admin;

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
        $startOfMonth = $today->copy()->startOfMonth();

        $checkInsToday = Booking::where('check_in', $today->toDateString())
            ->whereIn('status', ['confirmed', 'checked_in'])->count();

        $checkedInToday = Booking::where('check_in', $today->toDateString())
            ->where('status', 'checked_in')->count();

        $occupiedRooms = Booking::where('status', 'checked_in')->count();

        $totalRooms = Room::where('is_active', true)->count();
        $availableRooms = $totalRooms - $occupiedRooms;

        $monthlyRevenue = Booking::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfMonth, now()])
            ->sum('total_amount');

        $pendingAttention = Booking::where('needs_attention', true)->count();

        $recentBookings = Booking::with('room.roomType')
            ->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'checkInsToday', 'checkedInToday', 'occupiedRooms', 'availableRooms',
            'totalRooms', 'monthlyRevenue', 'pendingAttention', 'recentBookings'
        ));
    }
}
