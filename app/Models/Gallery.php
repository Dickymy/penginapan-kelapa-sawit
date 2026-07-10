<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'path',
        'thumb_path',
        'medium_path',
        'large_path',
        'alt_text',
        'is_active',
        'sort_order',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
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
