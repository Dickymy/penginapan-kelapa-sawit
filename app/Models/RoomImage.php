<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RoomImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'path',
        'thumb_path',
        'medium_path',
        'large_path',
        'alt_text',
        'is_cover',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RoomImage $image) {
            // If is_cover is not explicitly set, check if room type already has a cover
            if (!$image->is_cover) {
                $hasCover = static::where('room_type_id', $image->room_type_id)
                    ->where('is_cover', true)
                    ->exists();

                if (!$hasCover) {
                    $image->is_cover = true;
                }
            }
        });

        static::saved(function (RoomImage $image) {
            if ($image->is_cover) {
                static::where('room_type_id', $image->room_type_id)
                    ->where('id', '!=', $image->id)
                    ->where('is_cover', true)
                    ->update(['is_cover' => false]);
            } else {
                $hasCover = static::where('room_type_id', $image->room_type_id)
                    ->where('is_cover', true)
                    ->exists();

                if (!$hasCover) {
                    $fallback = static::where('room_type_id', $image->room_type_id)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->first();

                    if ($fallback && $fallback->id !== $image->id) {
                        $fallback->is_cover = true;
                        $fallback->saveQuietly();
                    }
                }
            }
        });

        static::deleted(function (RoomImage $image) {
            $hasCover = static::where('room_type_id', $image->room_type_id)
                ->where('is_cover', true)
                ->exists();

            if (!$hasCover) {
                $next = static::where('room_type_id', $image->room_type_id)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();

                if ($next) {
                    $next->is_cover = true;
                    $next->saveQuietly();
                }
            }
        });
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function getUrlAttribute(): string
    {
        $path = $this->large_path ?? $this->path;
        return Storage::disk('public')->url($path) . '?v=' . $this->updated_at?->timestamp;
    }

    public function getThumbUrlAttribute(): string
    {
        $path = $this->thumb_path ?? $this->path;
        return Storage::disk('public')->url($path) . '?v=' . $this->updated_at?->timestamp;
    }

    public function getMediumUrlAttribute(): string
    {
        $path = $this->medium_path ?? $this->path;
        return Storage::disk('public')->url($path) . '?v=' . $this->updated_at?->timestamp;
    }

    public function getLargeUrlAttribute(): string
    {
        $path = $this->large_path ?? $this->path;
        return Storage::disk('public')->url($path) . '?v=' . $this->updated_at?->timestamp;
    }
}
