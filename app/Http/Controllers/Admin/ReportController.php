<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Expense;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function revenue(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $bookings = Booking::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->get();

        $totalRevenue = $bookings->sum('total_amount');
        $bySource = $bookings->groupBy('source')->map(fn($group) => [
            'count' => $group->count(),
            'total' => $group->sum('total_amount'),
        ]);

        return view('admin.reports.revenue', compact('totalRevenue', 'bySource', 'startDate', 'endDate'));
    }

    public function occupancy(Request $request): View
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()));
        $endDate = Carbon::parse($request->input('end_date', now()->toDateString()));
        $days = $startDate->diffInDays($endDate) + 1;

        $totalRooms = Room::where('is_active', true)->count();
        $availableRoomNights = $totalRooms * $days;

        $occupiedRoomNights = Booking::whereIn('status', ['confirmed', 'checked_in', 'checked_out', 'completed'])
            ->where('check_in', '<', $endDate->copy()->addDay())
            ->where('check_out', '>', $startDate)
            ->get()
            ->sum(function ($booking) use ($startDate, $endDate) {
                $bStart = max($booking->check_in, $startDate);
                $bEnd = min($booking->check_out, $endDate->copy()->addDay());
                return $bStart->diffInDays($bEnd);
            });

        $occupancyRate = $availableRoomNights > 0 ? round($occupiedRoomNights / $availableRoomNights * 100, 1) : 0;

        return view('admin.reports.occupancy', compact('occupancyRate', 'occupiedRoomNights', 'availableRoomNights', 'startDate', 'endDate'));
    }

    public function profit(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $revenue = Booking::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->sum('total_amount');

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        $profit = $revenue - $expenses;

        return view('admin.reports.profit', compact('revenue', 'expenses', 'profit', 'startDate', 'endDate'));
    }

    public function sources(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $data = Booking::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->get()
            ->groupBy('source')
            ->map(fn($group) => [
                'count' => $group->count(),
                'revenue' => $group->sum('total_amount'),
                'average' => $group->count() > 0 ? (int) ($group->sum('total_amount') / $group->count()) : 0,
            ]);

        return view('admin.reports.sources', compact('data', 'startDate', 'endDate'));
    }
}
