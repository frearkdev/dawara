@component('mail::message')
# Afspraak bevestigd

Beste {{ $appointment->customer->name }},

Je afspraak bij **Dawara Barbershop** is succesvol geboekt.

@component('mail::table')
|         |                       |
| ------- | --------------------- |
| Dienst  | {{ $appointment->service->name }} |
| Barber  | {{ $appointment->barber->user->name }} |
| Datum   | {{ $appointment->starts_at->format('l d F Y') }} |
| Tijd    | {{ $appointment->starts_at->format('H:i') }} – {{ $appointment->ends_at->format('H:i') }} |
| Prijs   | {{ $appointment->price_formatted }} |
@endcomponent

@component('mail::button', ['url' => route('booking.confirmation', $appointment->id)])
Bekijk afspraak
@endcomponent

**Annuleren?**
Je kunt gratis annuleren tot 2 uur voor aanvang via de website.

Bedankt voor je vertrouwen,<br>
Dawara Barbershop
@endcomponent