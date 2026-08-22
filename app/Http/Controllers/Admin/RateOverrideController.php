<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRateOverrideRequest;
use App\Models\RateOverride;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RateOverrideController extends Controller
{
    public function index(Request $request)
    {
        $roomTypes = RoomType::where('is_active', true)->get();
        $selectedRoomTypeId = $request->query('room_type_id', $roomTypes->first()->id ?? null);

        $query = RateOverride::query();
        if ($selectedRoomTypeId) {
            $query->where('room_type_id', $selectedRoomTypeId);
        }

        $overrides = $query->orderBy('date', 'desc')->paginate(30)->withQueryString();

        return view('admin.rate-overrides.index', compact('roomTypes', 'selectedRoomTypeId', 'overrides'));
    }

    public function store(StoreRateOverrideRequest $request)
    {
        $validated = $request->validated();

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $days = $start->diffInDays($end);

        for ($i = 0; $i <= $days; $i++) {
            $currentDate = $start->copy()->addDays($i)->format('Y-m-d');
            RateOverride::updateOrCreate(
                [
                    'room_type_id' => $validated['room_type_id'],
                    'date' => $currentDate,
                ],
                [
                    'price' => $validated['price'],
                    'label' => $validated['label'],
                ]
            );
        }

        return redirect()->route('admin.rate-overrides.index', ['room_type_id' => $validated['room_type_id']])
            ->with('success', 'Harga khusus berhasil disimpan untuk rentang tanggal tersebut.');
    }

    public function destroy(RateOverride $rateOverride)
    {
        $roomTypeId = $rateOverride->room_type_id;
        $rateOverride->delete();

        return redirect()->route('admin.rate-overrides.index', ['room_type_id' => $roomTypeId])
            ->with('success', 'Harga khusus berhasil dihapus.');
    }
}
