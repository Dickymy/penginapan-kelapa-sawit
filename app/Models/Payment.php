<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'provider', 'provider_order_id', 'transaction_id',
        'attempt_no', 'snap_token', 'payment_type', 'gross_amount',
        'status', 'provider_transaction_status', 'fraud_status',
        'provider_transaction_time', 'paid_at', 'expired_at', 'refunded_at',
        'raw_response', 'last_status_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'gross_amount' => 'integer',
            'attempt_no' => 'integer',
            'provider_transaction_time' => 'datetime',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'refunded_at' => 'datetime',
            'last_status_checked_at' => 'datetime',
            'raw_response' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function isExpired(): bool
    {
        return $this->status === PaymentStatus::Expired;
    }
}
