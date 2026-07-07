<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function __construct(
        private AvailabilityService $availability,
        private PricingService $pricing,
    ) {}

    public function search(Request $request): View
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guest_count' => ['required', 'integer', 'min:1'],
        ], [
            'check_in.required' => 'Tanggal check-in wajib diisi.',
            'check_in.after_or_equal' => 'Tanggal check-in tidak boleh tanggal yang sudah lewat.',
            'check_out.required' => 'Tanggal check-out wajib diisi.',
            'check_out.after' => 'Tanggal check-out harus setelah tanggal check-in.',
            'guest_count.required' => 'Jumlah tamu wajib diisi.',
            'guest_count.min' => 'Jumlah tamu minimal 1 orang.',
        ]);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $guestCount = (int) $validated['guest_count'];
        $nights = $this->pricing->calculateNights($checkIn, $checkOut);

        $results = $this->availability->searchAvailableRoomTypes($checkIn, $checkOut, $guestCount);

        // Add pricing to each result
        $results = $results->map(function ($item) use ($checkIn, $checkOut) {
            $item['quote'] = $this->pricing->calculateQuote($item['room_type'], $checkIn, $checkOut);

            return $item;
        });

        return view('public.availability.results', compact(
            'results', 'checkIn', 'checkOut', 'guestCount', 'nights'
        ));
    }
}
