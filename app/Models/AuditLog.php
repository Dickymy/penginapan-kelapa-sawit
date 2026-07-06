<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'actor_type',
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'before',
        'after',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public static function record(
        string $action,
        ?string $actorType = null,
        ?int $actorId = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?array $before = null,
        ?array $after = null,
        ?array $metadata = null,
    ): static {
        return static::create([
            'action' => $action,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'before' => $before,
            'after' => $after,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent() ? substr(request()->userAgent(), 0, 500) : null,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
