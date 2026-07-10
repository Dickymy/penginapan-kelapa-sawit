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

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->large_path ?? $this->path);
    }

    public function getThumbUrlAttribute(): string
    {
        $path = $this->thumb_path ?? $this->path;

        return Storage::disk('public')->url($path);
    }

    public function getMediumUrlAttribute(): string
    {
        $path = $this->medium_path ?? $this->path;

        return Storage::disk('public')->url($path);
    }

    public function getLargeUrlAttribute(): string
    {
        $path = $this->large_path ?? $this->path;

        return Storage::disk('public')->url($path);
    }
}
