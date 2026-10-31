<?php

namespace App\Models;

use App\Enums\PromotionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'minimum_booking_amount',
        'maximum_discount',
        'usage_quota',
        'max_usage_per_user',
        'is_active',
        'show_in_listing',
    ];

    protected function casts(): array
    {
        return [
            'type' => PromotionType::class,
            'value' => 'integer',
            'minimum_booking_amount' => 'integer',
            'maximum_discount' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'show_in_listing' => 'boolean',
            'usage_quota' => 'integer',
            'max_usage_per_user' => 'integer',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class);
    }

    /**
     * Scope: active promotions within valid date range.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    /**
     * Scope: promotions that should appear in checkout listing.
     */
    public function scopeShowInListing(Builder $query): Builder
    {
        return $query->where('show_in_listing', true);
    }

    /**
     * Mutator: uppercase code on set.
     */
    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }
}
