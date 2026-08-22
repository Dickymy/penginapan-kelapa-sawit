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

        $baseQuery = Booking::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()]);

        $totalRevenue = (int) $baseQuery->sum('total_amount');
        
        $bySource = (clone $baseQuery)
            ->selectRaw('source, count(*) as count, sum(total_amount) as total')
            ->groupBy('source')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->source->value => [
                    'count' => $item->count,
                    'total' => $item->total,
                ]
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
            ->selectRaw('source, count(*) as count, sum(total_amount) as revenue')
            ->groupBy('source')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->source->value => [
                    'count' => $item->count,
                    'revenue' => $item->revenue,
                    'average' => $item->count > 0 ? (int) ($item->revenue / $item->count) : 0,
                ]
            ]);

        return view('admin.reports.sources', compact('data', 'startDate', 'endDate'));
    }

    public function exportRevenue(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $bookings = Booking::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->orderBy('created_at')
            ->get();

        $filename = "Laporan_Pendapatan_{$startDate}_sampai_{$endDate}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($bookings) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for UTF-8
            fputcsv($file, ['Tanggal Booking', 'Kode Booking', 'Sumber', 'Status', 'Total Pendapatan']);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->created_at->format('d/m/Y H:i'),
                    $booking->booking_code,
                    $booking->source->label(),
                    $booking->status->label(),
                    $booking->total_amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportOccupancy(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()));
        $endDate = Carbon::parse($request->input('end_date', now()->toDateString()));

        $bookings = Booking::whereIn('status', ['confirmed', 'checked_in', 'checked_out', 'completed'])
            ->where('check_in', '<', $endDate->copy()->addDay())
            ->where('check_out', '>', $startDate)
            ->orderBy('check_in')
            ->get();

        $filename = "Laporan_Okupansi_{$startDate->toDateString()}_sampai_{$endDate->toDateString()}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($bookings, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Kode Booking', 'Tamu', 'Kamar', 'Check-In', 'Check-Out', 'Malam Aktif']);

            foreach ($bookings as $booking) {
                $bStart = max($booking->check_in, $startDate);
                $bEnd = min($booking->check_out, $endDate->copy()->addDay());
                $activeNights = $bStart->diffInDays($bEnd);

                fputcsv($file, [
                    $booking->booking_code,
                    $booking->guest_name,
                    $booking->room_type_name_snapshot . ' - ' . $booking->room_name_snapshot,
                    $booking->check_in->format('d/m/Y'),
                    $booking->check_out->format('d/m/Y'),
                    $activeNights,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
