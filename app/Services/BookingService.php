<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private readonly AvailabilityService $availability,
    ) {}

    /**
     * Boek een afspraak. Gebruikt DB-transactie + row-lock om dubbele boekingen te voorkomen.
     *
     * @throws ValidationException als het slot niet meer beschikbaar is
     */
    public function book(
        User $customer,
        Barber $barber,
        Service $service,
        Carbon $startsAt,
        ?string $notes = null,
        string $source = 'online',
    ): Appointment {
        return DB::transaction(function () use ($customer, $barber, $service, $startsAt, $notes, $source) {

            // Lock de barber-rij terwijl we checken en inserten
            Barber::lockForUpdate()->find($barber->id);

            if (! $this->availability->isSlotAvailable($barber, $service, $startsAt)) {
                throw ValidationException::withMessages([
                    'starts_at' => 'Dit tijdslot is helaas niet meer beschikbaar. Kies een ander tijdstip.',
                ]);
            }

            $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

            $appointment = Appointment::create([
                'customer_id' => $customer->id,
                'barber_id' => $barber->id,
                'service_id' => $service->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'confirmed',
                'price_cents' => $service->price_cents,
                'notes' => $notes,
                'source' => $source,
            ]);

            return $appointment;
        });
    }

    /**
     * Annuleer een afspraak.
     *
     * @throws ValidationException als annuleren niet meer mogelijk is
     */
    public function cancel(Appointment $appointment, User $by, ?string $reason = null): void
    {
        if (! $appointment->can_be_cancelled) {
            throw ValidationException::withMessages([
                'appointment' => 'Je kunt een afspraak niet meer annuleren binnen 2 uur voor aanvang.',
            ]);
        }

        $appointment->cancel($reason, $by->id);
    }

    /**
     * Verzet een afspraak: annuleer de oude en maak een nieuwe aan.
     */
    public function reschedule(
        Appointment $old,
        User $by,
        Carbon $newStartsAt,
    ): Appointment {
        return DB::transaction(function () use ($old, $by, $newStartsAt) {
            $this->cancel($old, $by, 'Afspraak verzet');

            return $this->book(
                customer: $old->customer,
                barber: $old->barber,
                service: $old->service,
                startsAt: $newStartsAt,
                source: $old->source,
            );
        });
    }
}
