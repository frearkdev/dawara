<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AvailabilityException extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'availability_exceptions';

    protected $fillable = [
        'id',
        'barber_id',
        'date',
        'is_day_off',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'is_day_off' => 'boolean',
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

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
