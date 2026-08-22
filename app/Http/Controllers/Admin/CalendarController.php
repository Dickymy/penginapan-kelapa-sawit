<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('admin.calendar.index');
    }

    public function data(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()));
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()));

        // Get all active rooms
        $rooms = Room::with('roomType')->where('is_active', true)->orderBy('sort_order')->get()->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'room_type' => $room->roomType->name,
            ];
        });

        // Get overlapping bookings
        $bookings = Booking::with('room')
            ->whereIn('status', ['pending_payment', 'confirmed', 'checked_in', 'checked_out'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in', [$startDate, $endDate])
                      ->orWhereBetween('check_out', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('check_in', '<=', $startDate)
                            ->where('check_out', '>=', $endDate);
                      });
            })
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'room_id' => $booking->room_id,
                    'guest_name' => $booking->guest_name,
                    'check_in' => $booking->check_in->toDateString(),
                    'check_out' => $booking->check_out->toDateString(),
                    'status' => $booking->status->value,
                    'status_label' => $booking->status->label(),
                ];
            });

        // Get overlapping room blocks
        $roomBlocks = RoomBlock::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
        })->get()->map(function ($block) {
            return [
                'id' => $block->id,
                'room_id' => $block->room_id,
                'start_date' => $block->start_date->toDateString(),
                'end_date' => $block->end_date->toDateString(),
                'reason' => $block->reason,
            ];
        });

        return response()->json([
            'rooms' => $rooms,
            'bookings' => $bookings,
            'room_blocks' => $roomBlocks,
        ]);
    }
}
