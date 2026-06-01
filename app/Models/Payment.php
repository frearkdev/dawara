<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'appointment_id',
        'mollie_payment_id',
        'mollie_checkout_url',
        'amount_cents',
        'currency',
        'status',
        'method',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'amount_cents' => 'integer',
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

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getAmountFormattedAttribute(): string
    {
        return '€'.number_format($this->amount_cents / 100, 2, ',', '.');
    }
}
