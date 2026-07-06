<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_id',
        'requested_by_admin_id',
        'processed_by_admin_id',
        'amount',
        'reason',
        'notes',
        'status',
        'requested_at',
        'processed_at',
        'provider_refund_id',
        'provider_response',
    ];

    protected function casts(): array
    {
        return [
            'status' => RefundStatus::class,
            'amount' => 'integer',
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
            'provider_response' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function requestedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'requested_by_admin_id');
    }

    public function processedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by_admin_id');
    }
}
