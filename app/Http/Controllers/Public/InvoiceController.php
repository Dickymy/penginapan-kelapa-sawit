<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function download(Request $request, string $bookingCode)
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        // Auth check: admin, member owner, or guest with token
        $user = auth()->user();
        $admin = auth('admin')->user();

        if (!$admin && (!$user || $user->id !== $booking->user_id)) {
            // Guest access via token
            $token = $request->query('token');
            if (!$token || hash('sha256', $token) !== $booking->guest_access_token_hash) {
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
