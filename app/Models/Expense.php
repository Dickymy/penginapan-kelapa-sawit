<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'listrik',
        'air',
        'internet',
        'laundry',
        'perlengkapan_kamar',
        'perbaikan',
        'gaji',
        'other',
    ];

    protected $fillable = [
        'expense_date',
        'category',
        'amount',
        'description',
        'receipt_path',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'integer',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
