<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingAccessService;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private MidtransPaymentService $paymentService,
        private BookingAccessService $accessService,
    ) {}

    /**
     * Show payment page with Snap.js.
     */
    public function pay(Request $request, string $bookingCode): View
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        // Verify access
        if (!$this->accessService->hasAccess($request, $booking)) {
            abort(403, 'Anda tidak memiliki akses ke halaman pembayaran ini.');
        }

        try {
            $result = $this->paymentService->createOrResumePayment($booking);
        } catch (\RuntimeException $e) {
            return view('public.booking.pay-error', [
                'booking' => $booking,
                'message' => $e->getMessage(),
            ]);
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

        // Verify access
        if (!$this->accessService->hasAccess($request, $booking)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Manual sync fallback (especially useful for local testing where webhooks don't arrive)
        if ($booking->status === \App\Enums\BookingStatus::PendingPayment) {
            $payment = $booking->payments()->latest()->first();
            if ($payment && $payment->status === \App\Enums\PaymentStatus::Unpaid) {
                try {
                    $this->paymentService->reconcilePayment($payment);
                    $booking->refresh();
                } catch (\Exception $e) {
                    // Ignore gracefully
                }
            }
        }

        return view('public.booking.finish', compact('booking'));
    }
}
