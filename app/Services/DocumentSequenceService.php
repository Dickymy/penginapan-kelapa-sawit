<?php

namespace App\Services;

use App\Models\DocumentSequence;
use Carbon\Carbon;

class DocumentSequenceService
{
    /**
     * Generate booking code: BKG-YYYYMM-0001
     * Must be called within a database transaction.
     */
    public function generateBookingCode(): string
    {
        return $this->generate('booking', 'BKG');
    }

    /**
     * Generate invoice number: INV-YYYYMM-0001
     * Must be called within a database transaction.
     */
    public function generateInvoiceNumber(): string
    {
        return $this->generate('invoice', 'INV');
    }

    private function generate(string $type, string $prefix): string
    {
        $period = Carbon::now()->format('Ym'); // YYYYMM

        $sequence = DocumentSequence::lockForUpdate()->firstOrCreate(
            ['document_type' => $type, 'period' => $period],
            ['last_number' => 0]
        );

        $sequence->increment('last_number');
        $sequence->refresh();

        $number = str_pad($sequence->last_number, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$period}-{$number}";
    }
}
