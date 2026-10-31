<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_public',
        'updated_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get a setting value.
     */
    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        $cacheKey = "settings.{$group}.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($group, $key, $default) {
            $setting = static::where('group', $group)->where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $group, string $key, mixed $value, ?int $adminId = null, ?string $type = null, bool $isPublic = false): void
    {
        if ($type === null) {
            $type = match (true) {
                is_bool($value) => 'boolean',
                is_int($value) => 'integer',
                is_array($value) => 'json',
                default => 'string',
            };
        }

        $storedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : (string) $value,
            default => (string) $value,
        };

        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value' => $storedValue,
                'type' => $type,
                'is_public' => $isPublic,
                'updated_by_admin_id' => $adminId,
            ]
        );

        Cache::forget("settings.{$group}.{$key}");
    }

    private static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
