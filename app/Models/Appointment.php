<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'barber_id',
        'service_id',
        'starts_at',
        'ends_at',
        'status',
        'price_cents',
        'notes',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'reminder_sent_at',
        'source',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'price_cents' => 'integer',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('starts_at', today());
    }

    public function scopeForBarber($query, string $barberId)
    {
        return $query->where('barber_id', $barberId);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getPriceFormattedAttribute(): string
    {
        return '€'.number_format($this->price_cents / 100, 2, ',', '.');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment?->status === 'paid';
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->starts_at->gt(now()->addHours(2));
    }

    // ── Methods ──────────────────────────────────────────────────────────────

    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markNoShow(): void
    {
        $this->update(['status' => 'no_show']);
    }

    public function cancel(?string $reason = null, ?string $cancelledBy = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy,
        ]);
    }
}
