<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingClaimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClaimController extends Controller
{
    public function __construct(private BookingClaimService $claimService) {}

    public function index(): View
    {
        $claimable = $this->claimService->getClaimableBookings(auth()->user());
        return view('member.claim.index', compact('claimable'));
    }

    public function claim(Booking $booking): RedirectResponse
    {
        try {
            $this->claimService->claimByEmail(auth()->user(), $booking);
            return back()->with('success', 'Booking berhasil diklaim.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
