<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'capacity',
        'bed_count',
        'bed_type',
        'base_price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'bed_count' => 'integer',
            'base_price' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // Relations

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'room_type_facility')->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class)->orderBy('sort_order');
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors

    public function getCoverImageAttribute(): ?RoomImage
    {
        return $this->images->firstWhere('is_cover', true)
            ?? $this->images->first();
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->base_price, 0, ',', '.');
    }

    public function getAverageRatingAttribute(): ?float
    {
        return Review::published()
            ->whereHas('booking.room', fn($q) => $q->where('room_type_id', $this->id))
            ->avg('rating');
    }

    public function getReviewCountAttribute(): int
    {
        return Review::published()
            ->whereHas('booking.room', fn($q) => $q->where('room_type_id', $this->id))
            ->count();
    }
}
