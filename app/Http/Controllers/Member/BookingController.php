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

        return view('member.bookings.index', compact('bookings', 'tab'));
    }

    public function show(Booking $booking): View
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }
        $booking->load(['room.roomType', 'statusHistories']);
        return view('member.bookings.show', compact('booking'));
    }
}
