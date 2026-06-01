<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'key';

    protected $fillable = ['key', 'value', 'type'];

    public $timestamps = false;

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);
        if (! $setting) return $default;

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'array' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $type === 'array' ? json_encode($value) : (string) $value,
                'type' => $type,
            ]
        );
    }
}
