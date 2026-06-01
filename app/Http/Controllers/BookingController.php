<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use App\Mail\BookingConfirmation;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly BookingService $booking,
    ) {}

    public function index(): Response
    {
        $services = Service::where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description,
                'price_cents' => $s->price_cents,
                'price_formatted' => $s->price_formatted,
                'duration_minutes' => $s->duration_minutes,
                'duration_label' => $s->duration_label,
                'color' => $s->color,
            ]);

        $barbers = Barber::with(['user', 'services'])
            ->where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->user->name,
                'bio' => $b->bio,
                'specialties' => $b->specialties ?? [],
                'service_ids' => $b->services->pluck('id'),
            ]);

        return Inertia::render('Booking/Index', compact('services', 'barbers'));
    }

    /** JSON — called by Vue via fetch to get available time slots */
    public function availability(Request $request): JsonResponse
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'barber_id' => ['required', 'exists:barbers,id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);

        $barber = Barber::findOrFail($request->barber_id);
        $service = Service::findOrFail($request->service_id);
        $slots = $this->availability->getSlots($barber, $service, $request->date);

        return response()->json([
            'slots' => $slots->map(fn ($s) => $s->format('H:i'))->values(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'barber_id' => ['required', 'exists:barbers,id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Gast of ingelogde gebruiker
        $customer = auth()->user();

        if (! $customer) {
            // Maak een gastaccount aan of zoek bestaande klant op e-mail
            $customer = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make(str()->random(16)),
                    'role' => 'customer',
                ]
            );
        }

        $startsAt = Carbon::parse("{$data['date']} {$data['time']}");
        $barber = Barber::findOrFail($data['barber_id']);
        $service = Service::findOrFail($data['service_id']);

        try {
            $appointment = $this->booking->book(
                customer: $customer,
                barber: $barber,
                service: $service,
                startsAt: $startsAt,
                notes: $data['notes'] ?? null,
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Send confirmation email
        Mail::to($customer->email)->send(new BookingConfirmation($appointment));

        return redirect()->route('booking.confirmation', $appointment->id);
    }

    public function confirmation(Appointment $appointment): Response
    {
        $appointment->load(['barber.user', 'service', 'customer', 'payment']);

        return Inertia::render('Booking/Confirmation', [
            'appointment' => [
                'id' => $appointment->id,
                'starts_at' => $appointment->starts_at->format('D d M Y H:i'),
                'ends_at' => $appointment->ends_at->format('H:i'),
                'barber_name' => $appointment->barber->user->name,
                'service_name' => $appointment->service->name,
                'price_formatted' => $appointment->price_formatted,
                'customer_name' => $appointment->customer->name,
                'customer_email' => $appointment->customer->email,
                'status' => $appointment->status,
                'can_cancel' => $appointment->can_be_cancelled,
                'is_paid' => $appointment->is_paid,
                'payment_status' => $appointment->payment?->status ?? null,
            ],
        ]);
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        if (! $appointment->can_be_cancelled) {
            return back()->with('error', 'Annuleren is niet meer mogelijk binnen 2 uur van de afspraak.');
        }

        $appointment->cancel($request->input('reason'), $request->user()?->id);

        return redirect()->route('home')->with('success', 'Je afspraak is geannuleerd.');
    }

    public function rescheduleForm(Appointment $appointment): Response
    {
        if (! $appointment->can_be_cancelled) {
            return redirect()->route('booking.confirmation', $appointment->id)
                ->with('error', 'Herboeken is niet meer mogelijk binnen 2 uur van de afspraak.');
        }

        $services = Service::where('active', true)->orderBy('sort_order')->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'duration_minutes' => $s->duration_minutes,
        ]);

        $barbers = Barber::with('user')->where('active', true)->orderBy('sort_order')->get()->map(fn ($b) => [
            'id' => $b->id,
            'name' => $b->user->name,
        ]);

        return Inertia::render('Booking/Reschedule', [
            'appointment' => [
                'id' => $appointment->id,
                'service_id' => $appointment->service_id,
                'barber_id' => $appointment->barber_id,
                'starts_at' => $appointment->starts_at->format('Y-m-d H:i'),
            ],
            'services' => $services,
            'barbers' => $barbers,
        ]);
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        if (! $appointment->can_be_cancelled) {
            return back()->with('error', 'Herboeken is niet meer mogelijk binnen 2 uur van de afspraak.');
        }

        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'barber_id' => ['required', 'exists:barbers,id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        $startsAt = Carbon::parse("{$data['date']} {$data['time']}");
        $barber = Barber::findOrFail($data['barber_id']);
        $service = Service::findOrFail($data['service_id']);

        try {
            $newAppointment = $this->booking->book(
                customer: $appointment->customer,
                barber: $barber,
                service: $service,
                startsAt: $startsAt,
                notes: $appointment->notes,
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $appointment->cancel('Herboekt naar nieuwe afspraak', $request->user()?->id);

        Mail::to($appointment->customer->email)->send(new BookingConfirmation($newAppointment));

        return redirect()->route('booking.confirmation', $newAppointment->id)
            ->with('success', 'Je afspraak is succesvol verplaatst.');
    }
}
