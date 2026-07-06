<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private MidtransPaymentService $paymentService) {}

    /**
     * Show payment page with Snap.js.
     */
    public function pay(string $bookingCode): View
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        try {
            $result = $this->paymentService->createOrResumePayment($booking);
        } catch (\RuntimeException $e) {
            abort(422, $e->getMessage());
        }

        return view('public.booking.pay', [
            'booking' => $booking,
            'snapToken' => $result['snap_token'],
            'clientKey' => $result['client_key'],
        ]);
    }

    /**
     * Post-payment redirect (informational only).
     */
    public function finish(Request $request, string $bookingCode): View
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        return view('public.booking.finish', compact('booking'));
    }
}
