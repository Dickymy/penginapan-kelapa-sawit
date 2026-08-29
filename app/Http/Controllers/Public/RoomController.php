<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(
        private AvailabilityService $availability,
    ) {}

    public function index(): View
    {
        $roomTypes = RoomType::active()
            ->ordered()
            ->with(['images', 'facilities' => fn ($q) => $q->active()->ordered(), 'rooms' => fn($q) => $q->active()])
            ->get();

        $facilities = \App\Models\Facility::active()->ordered()->get();

        return view('public.rooms.index', compact('roomTypes', 'facilities'));
    }

    public function show(string $slug): View
    {
        $roomType = RoomType::where('slug', $slug)
            ->active()
            ->with(['images', 'facilities' => fn ($q) => $q->active()->ordered()])
            ->firstOrFail();

        $reviews = \App\Models\Review::published()
            ->with('user')
            ->whereHas('booking.room', function ($query) use ($roomType) {
                $query->where('room_type_id', $roomType->id);
            })
            ->latest()
            ->limit(5)
            ->get();

        $isAvailableForSearch = null;
        $request = request();
        if ($request->has(['check_in', 'check_out'])) {
            try {
                $checkIn = Carbon::parse($request->check_in);
                $checkOut = Carbon::parse($request->check_out);
                if ($checkIn->isValid() && $checkOut->isValid() && $checkOut->isAfter($checkIn)) {
                    $availableRooms = $this->availability->findAvailableRooms($roomType->id, $checkIn, $checkOut);
                    $isAvailableForSearch = $availableRooms->isNotEmpty();
                }
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        return view('public.rooms.show', compact('roomType', 'reviews', 'isAvailableForSearch'));
    }
}
