<?php

namespace App\Models;

use App\Enums\LoyaltyTransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'type',
        'points',
        'balance_after',
        'remaining_points',
        'description',
        'expires_at',
        'source_transaction_id',
        'idempotency_key',
        'created_by_admin_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'type' => LoyaltyTransactionType::class,
            'points' => 'integer',
            'balance_after' => 'integer',
            'remaining_points' => 'integer',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function sourceTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_transaction_id');
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function allocationsAsDebit(): HasMany
    {
        return $this->hasMany(LoyaltyPointAllocation::class, 'debit_transaction_id');
    }

    public function allocationsAsCredit(): HasMany
    {
        return $this->hasMany(LoyaltyPointAllocation::class, 'credit_transaction_id');
    }
}
