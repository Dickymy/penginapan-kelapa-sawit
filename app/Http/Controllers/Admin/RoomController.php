<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::with('roomType')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        $roomTypes = RoomType::orderBy('name')->get();
        return view('admin.rooms.create', compact('roomTypes'));
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());
        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Room $room): View
    {
        $roomTypes = RoomType::orderBy('name')->get();
        return view('admin.rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());
        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil diperbarui.');
    }

    public function toggleActive(Room $room): RedirectResponse
    {
        $room->update(['is_active' => !$room->is_active]);
        $status = $room->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kamar berhasil {$status}.");
    }
}
