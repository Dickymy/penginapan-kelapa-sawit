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

        // Check access via BookingAccessService (covers admin, member owner, session grant)
        if (!$this->accessService->hasAccess($request, $booking)) {
            // Fallback: check URL token for legacy/direct links
            $token = $request->query('token');
            if (!$token || !$this->accessService->verifyByToken($booking, $token)) {
                abort(403, 'Akses tidak diizinkan.');
            }
        }

        if (!$this->invoiceService->isEligible($booking)) {
            abort(422, 'Invoice tidak tersedia untuk booking ini.');
        }

        $pdf = $this->invoiceService->generatePdf($booking);
        return $pdf->download("invoice-{$booking->booking_code}.pdf");
    }
}
