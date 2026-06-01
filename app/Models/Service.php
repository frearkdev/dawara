<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'description',
        'duration_minutes',
        'price_cents',
        'color',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'duration_minutes' => 'integer',
        'price_cents' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /** €20,00 */
    public function getPriceFormattedAttribute(): string
    {
        return '€'.number_format($this->price_cents / 100, 2, ',', '.');
    }

    /** 45 min  of  1u 15min */
    public function getDurationLabelAttribute(): string
    {
        if ($this->duration_minutes >= 60) {
            $h = intdiv($this->duration_minutes, 60);
            $m = $this->duration_minutes % 60;

            return $m > 0 ? "{$h}u {$m}min" : "{$h}u";
        }

        return "{$this->duration_minutes} min";
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function barbers(): BelongsToMany
    {
        return $this->belongsToMany(Barber::class, 'barber_service');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
