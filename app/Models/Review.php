<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'appointment_id',
        'customer_id',
        'barber_id',
        'rating',
        'comment',
        'visible',
        'source',
        'google_review_id',
        'google_author',
        'reviewed_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'visible' => 'boolean',
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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating).str_repeat('☆', 5 - $this->rating);
    }
}
