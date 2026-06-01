<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Availability extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'availability'; // Laravel zou anders 'availabilities' gebruiken

    protected $fillable = [
        'id',
        'barber_id',
        'day_of_week',
        'start_time',
        'end_time',
        'active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'active' => 'boolean',
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

    public function getDayNameAttribute(): string
    {
        return ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'][$this->day_of_week] ?? '';
    }
}
