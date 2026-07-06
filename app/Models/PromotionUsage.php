<?php

namespace App\Models;

use App\Enums\PromotionUsageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'booking_id',
        'user_id',
        'status',
        'discount_amount',
        'reserved_at',
        'consumed_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PromotionUsageStatus::class,
            'discount_amount' => 'integer',
            'reserved_at' => 'datetime',
            'consumed_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
