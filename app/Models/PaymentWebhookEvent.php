<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    protected $fillable = [
        'provider', 'deduplication_key', 'provider_order_id', 'transaction_id',
        'event_status', 'signature_valid', 'amount_valid', 'processing_status',
        'payload', 'error_message', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'signature_valid' => 'boolean',
            'amount_valid' => 'boolean',
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}
