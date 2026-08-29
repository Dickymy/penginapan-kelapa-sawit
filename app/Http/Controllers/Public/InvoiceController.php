<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingAccessService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService,
        private BookingAccessService $accessService,
    ) {}

    public function download(Request $request, string $bookingCode)
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        $hasAccess = $this->accessService->hasAccess($request, $booking);

        // Fallback: check URL token for legacy/direct links from email
        if (!$hasAccess) {
            $token = $request->query('token');
            if ($token && $this->accessService->verifyByToken($booking, $token)) {
                $hasAccess = true;
            }
        }

        // Fallback: if logged-in user's email matches the booking guest email
        if (!$hasAccess && auth()->check()) {
            $user = auth()->user();
            if ($user->email && $booking->guest_email && strtolower($user->email) === strtolower($booking->guest_email)) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            // If not logged in at all, redirect to login so they can try again after authenticating
            if (!auth()->check()) {
                return redirect()->guest(route('login'))->with('warning', 'Silakan login terlebih dahulu untuk mengunduh invoice.');
            }
            abort(403, 'Akses tidak diizinkan.');
        }

        if (!$this->invoiceService->isEligible($booking)) {
            abort(422, 'Invoice tidak tersedia untuk booking ini.');
        }

        $pdf = $this->invoiceService->generatePdf($booking);
        return $pdf->download("invoice-{$booking->booking_code}.pdf");
    }
}
