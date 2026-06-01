<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Barber extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'bio',
        'avatar',
        'specialties',   // ← json kolom, cast hieronder regelt serialisatie
        'active',
        'sort_order',
    ];

    protected $casts = [
        'specialties' => 'array',   // ← array in/uit, JSON in de database
        'active' => 'boolean',
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

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'barber_service');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function availabilityExceptions(): HasMany
    {
        return $this->hasMany(AvailabilityException::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '';
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }
}
