<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\DocumentSequence;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function isEligible(Booking $booking): bool
    {
        return in_array($booking->payment_status->value, ['paid', 'refunded', 'partial_refund'])
            && in_array($booking->status->value, ['confirmed', 'checked_in', 'checked_out', 'completed']);
    }

    public function generateInvoiceNumber(Booking $booking): string
    {
        if ($booking->invoice_number) {
            return $booking->invoice_number;
        }

        $number = DB::transaction(function () use ($booking) {
            $period = $booking->created_at->format('Ym');
            $sequence = DocumentSequence::lockForUpdate()->firstOrCreate(
                ['document_type' => 'invoice', 'period' => $period],
                ['last_number' => 0]
            );
            $sequence->increment('last_number');
            $sequence->refresh();
            return 'INV-' . $period . '-' . str_pad($sequence->last_number, 4, '0', STR_PAD_LEFT);
        });

        $booking->update(['invoice_number' => $number]);
        return $number;
    }

    public function generatePdf(Booking $booking)
    {
        $invoiceNumber = $this->generateInvoiceNumber($booking);
        $booking->load('payments');
        $paidPayment = $booking->payments->firstWhere('status', 'paid');

        $data = [
            'booking' => $booking,
            'invoiceNumber' => $invoiceNumber,
            'payment' => $paidPayment,
        ];

        return Pdf::loadView('invoices.booking', $data)->setPaper('a4');
    }
}
