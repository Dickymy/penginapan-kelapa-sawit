<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'debit_transaction_id',
        'credit_transaction_id',
        'points',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
        ];
    }

    public function debitTransaction(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTransaction::class, 'debit_transaction_id');
    }

    public function creditTransaction(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTransaction::class, 'credit_transaction_id');
    }
}
