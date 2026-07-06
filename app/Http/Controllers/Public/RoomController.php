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
            ->with(['images', 'facilities' => fn ($q) => $q->active()->ordered()])
            ->get();

        return view('public.rooms.index', compact('roomTypes'));
    }

    public function show(string $slug): View
    {
        $roomType = RoomType::where('slug', $slug)
            ->active()
            ->with(['images', 'facilities' => fn ($q) => $q->active()->ordered()])
            ->firstOrFail();

        return view('public.rooms.show', compact('roomType'));
    }
}
