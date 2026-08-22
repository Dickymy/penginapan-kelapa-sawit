<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\View\View;

class RoomController extends Controller
{
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

        return view('public.rooms.show', compact('roomType', 'reviews'));
    }
}
