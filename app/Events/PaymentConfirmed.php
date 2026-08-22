<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Booking $booking;
    public Payment $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, Payment $payment)
    {
        $this->booking = $booking;
        $this->payment = $payment;
    }
}
