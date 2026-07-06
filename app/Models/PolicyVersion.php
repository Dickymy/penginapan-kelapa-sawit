<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyVersion extends Model
{
    protected $fillable = [
        'policy_key',
        'version',
        'title',
        'content',
        'is_current',
        'published_at',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function scopeCurrent(Builder $query, string $policyKey = 'guest_policy'): Builder
    {
        return $query->where('policy_key', $policyKey)->where('is_current', true);
    }
}
