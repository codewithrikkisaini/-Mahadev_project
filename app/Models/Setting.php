<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    /**
     * Get a setting by key with optional fallback.
     */
    public static function get(string $key, $default = null)
    {
        try {
            $setting = Cache::rememberForever("setting_{$key}", function () use ($key) {
                return static::where('key', $key)->first();
            });

            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'number', 'integer' => (int) $setting->value,
                'float', 'decimal' => (float) $setting->value,
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'json', 'array' => json_decode($setting->value, true) ?? $default,
                default => $setting->value ?? $default,
            };
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set or update a setting.
     */
    public static function set(string $key, $value, string $group = 'general', string $type = 'string', ?string $label = null, ?string $description = null): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'group' => $group,
                'type' => $type,
                'label' => $label,
                'description' => $description,
            ]
        );

        Cache::forget("setting_{$key}");
        Cache::forget("settings_group_{$group}");

        return $setting;
    }

    /**
     * Get all settings in a group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::rememberForever("settings_group_{$group}", function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Helper to get configured monthly amount.
     */
    public static function getMonthlyAmount(): float
    {
        return (float) static::get('monthly_amount', 200);
    }
}
