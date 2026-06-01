<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Geeft alle beschikbare tijdslots terug voor een barber + dienst op een datum.
     * Resultaat: Collection van Carbon-objecten (slot starttijden).
     */
    public function getSlots(Barber $barber, Service $service, string $date): Collection
    {
        $day = Carbon::parse($date);

        // 1. Werk­uren ophalen (null = vrij die dag)
        $workHours = $this->getWorkHours($barber, $day);
        if (! $workHours) {
            return collect();
        }

        [$workStart, $workEnd] = $workHours;

        // 2. Bestaande afspraken die dag ophalen
        $existing = Appointment::where('barber_id', $barber->id)
            ->whereDate('starts_at', $day)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->get(['starts_at', 'ends_at']);

        // 3. Candidate slots genereren (elke 15 minuten)
        $duration = $service->duration_minutes;
        $slots = collect();
        $cursor = $workStart->copy();

        while ($cursor->copy()->addMinutes($duration)->lte($workEnd)) {
            $slotEnd = $cursor->copy()->addMinutes($duration);

            $overlaps = $existing->first(function ($appt) use ($cursor, $slotEnd) {
                return $appt->starts_at->lt($slotEnd) && $appt->ends_at->gt($cursor);
            });

            // Slots in het verleden + 30 min buffer overslaan
            $tooSoon = $cursor->lte(now()->addMinutes(30));

            if (! $overlaps && ! $tooSoon) {
                $slots->push($cursor->copy());
            }

            $cursor->addMinutes(15);
        }

        return $slots;
    }

    /**
     * Beschikbare slots voor ALLE actieve barbers voor een dienst op een datum.
     * Resultaat: ['barber_id' => [Carbon, ...], ...]
     */
    public function getSlotsForAllBarbers(Service $service, string $date): Collection
    {
        return Barber::with(['user', 'availability', 'availabilityExceptions'])
            ->where('active', true)
            ->whereHas('services', fn ($q) => $q->where('services.id', $service->id))
            ->get()
            ->mapWithKeys(fn (Barber $b) => [
                $b->id => $this->getSlots($b, $service, $date),
            ]);
    }

    /**
     * Controleert of een specifiek slot nog beschikbaar is (race-condition guard).
     */
    public function isSlotAvailable(Barber $barber, Service $service, Carbon $startsAt): bool
    {
        $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

        $conflict = Appointment::where('barber_id', $barber->id)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();

        if ($conflict) {
            return false;
        }

        $workHours = $this->getWorkHours($barber, $startsAt);
        if (! $workHours) {
            return false;
        }

        [$workStart, $workEnd] = $workHours;

        return $startsAt->gte($workStart) && $endsAt->lte($workEnd);
    }

    /**
     * Geeft [start, end] Carbon voor een barber op een datum. Null = vrij.
     */
    private function getWorkHours(Barber $barber, Carbon $date): ?array
    {
        // Eerst datum-specifieke uitzondering checken
        $exception = $barber->availabilityExceptions()
            ->whereDate('date', $date)
            ->first();

        if ($exception) {
            if ($exception->is_day_off) {
                return null;
            }
            if ($exception->start_time && $exception->end_time) {
                return [
                    Carbon::parse($date->toDateString().' '.$exception->start_time),
                    Carbon::parse($date->toDateString().' '.$exception->end_time),
                ];
            }
        }

        // Wekelijks rooster
        $schedule = $barber->availability()
            ->where('day_of_week', (int) $date->dayOfWeek)
            ->where('active', true)
            ->first();

        if (! $schedule) {
            return null;
        }

        return [
            Carbon::parse($date->toDateString().' '.$schedule->start_time),
            Carbon::parse($date->toDateString().' '.$schedule->end_time),
        ];
    }

    /**
     * Geeft de eerstvolgende N datums waarop een barber beschikbaar is.
     */
    public function getNextAvailableDates(Barber $barber, Service $service, int $days = 30): Collection
    {
        return collect(range(0, $days - 1))
            ->map(fn ($offset) => now()->addDays($offset)->toDateString())
            ->filter(fn ($date) => $this->getSlots($barber, $service, $date)->isNotEmpty())
            ->values();
    }
}
