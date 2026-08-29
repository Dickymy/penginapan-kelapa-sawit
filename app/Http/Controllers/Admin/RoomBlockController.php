<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomBlockRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomBlock;
use App\Enums\BookingStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoomBlockController extends Controller
{
    public function index(): View
    {
        $blocks = RoomBlock::with(['room.roomType', 'createdBy'])
            ->latest()
            ->paginate(20);

        return view('admin.room-blocks.index', compact('blocks'));
    }

    public function create(): View
    {
        $rooms = Room::with('roomType')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.room-blocks.create', compact('rooms'));
    }

    public function store(StoreRoomBlockRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Check booking conflicts for the room + dates
        $hasConflict = Booking::where('room_id', $validated['room_id'])
            ->where('check_out', '>', $validated['start_date'])
            ->when(!empty($validated['end_date']), function ($query) use ($validated) {
                $query->where('check_in', '<', $validated['end_date']);
            })
            ->where(function ($query) {
                $query->whereIn('status', [
                    BookingStatus::Confirmed->value,
                    BookingStatus::CheckedIn->value,
                ])
                ->orWhere(function ($q) {
                    $q->where('status', BookingStatus::PendingPayment->value)
                      ->where('payment_expires_at', '>', now());
                });
            })
            ->exists();

        if ($hasConflict) {
            return back()->withInput()->withErrors([
                'start_date' => 'Terdapat booking aktif pada rentang tanggal tersebut.',
            ]);
        }

        RoomBlock::create([
            'room_id' => $validated['room_id'],
            'start_date' => $validated['start_date'],
            'end_date' => !empty($validated['is_indefinite']) ? null : $validated['end_date'],
            'reason_type' => $validated['reason_type'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'created_by_admin_id' => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.room-blocks.index')
            ->with('success', 'Block kamar berhasil ditambahkan.');
    }

    public function destroy(RoomBlock $roomBlock): RedirectResponse
    {
        $roomBlock->delete();

        return redirect()->route('admin.room-blocks.index')
            ->with('success', 'Block kamar berhasil dihapus.');
    }
}
