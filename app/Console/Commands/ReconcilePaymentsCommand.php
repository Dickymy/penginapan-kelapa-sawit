<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\MidtransPaymentService;
use Illuminate\Console\Command;

class ReconcilePaymentsCommand extends Command
{
    protected $signature = 'payment:reconcile';
    protected $description = 'Reconcile pending payments by checking status with Midtrans';

    public function handle(MidtransPaymentService $service): int
    {
        $payments = Payment::where('status', PaymentStatus::Pending->value)
            ->orWhere(function ($q) {
                $q->where('status', PaymentStatus::Unpaid->value)
                  ->where('created_at', '<', now()->subMinutes(10));
            })
            ->where(function ($q) {
                $q->whereNull('last_status_checked_at')
                  ->orWhere('last_status_checked_at', '<', now()->subMinutes(5));
            })
            ->whereNotNull('snap_token')
            ->get();

        $count = 0;
        foreach ($payments as $payment) {
            $service->reconcilePayment($payment);
            $count++;
        }

        $this->info("Reconciled {$count} payment(s).");

        return self::SUCCESS;
    }
}
