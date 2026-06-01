<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Appointment::with(['customer', 'barber.user', 'service'])
            ->orderByDesc('starts_at');

        if ($request->filled('barber_id')) {
            $query->where('barber_id', $request->barber_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('starts_at', $request->date);
        }

        $appointments = $query->paginate(20)->through(fn ($a) => [
            'id' => $a->id,
            'starts_at' => $a->starts_at->format('d M Y H:i'),
            'ends_at' => $a->ends_at->format('H:i'),
            'customer_name' => $a->customer->name,
            'customer_phone' => $a->customer->phone,
            'barber_name' => $a->barber->user->name,
            'service_name' => $a->service->name,
            'status' => $a->status,
            'price_formatted' => $a->price_formatted,
            'notes' => $a->notes,
            'can_cancel' => $a->can_be_cancelled,
        ]);

        $barbers = Barber::with('user')->where('active', true)->get()
            ->map(fn ($b) => ['id' => $b->id, 'name' => $b->user->name]);

        return Inertia::render('Admin/Appointments', [
            'appointments' => $appointments,
            'barbers' => $barbers,
            'filters' => $request->only('barber_id', 'status', 'date'),
        ]);
    }

    public function show(Appointment $appointment): Response
    {
        $appointment->load(['customer', 'barber.user', 'service', 'payment', 'review']);

        return Inertia::render('Admin/AppointmentDetail', [
            'appointment' => [
                'id' => $appointment->id,
                'starts_at' => $appointment->starts_at->format('d M Y H:i'),
                'ends_at' => $appointment->ends_at->format('H:i'),
                'status' => $appointment->status,
                'customer_name' => $appointment->customer->name,
                'customer_email' => $appointment->customer->email,
                'customer_phone' => $appointment->customer->phone,
                'barber_name' => $appointment->barber->user->name,
                'service_name' => $appointment->service->name,
                'price_formatted' => $appointment->price_formatted,
                'notes' => $appointment->notes,
                'can_cancel' => $appointment->can_be_cancelled,
                'is_paid' => $appointment->is_paid,
            ],
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => ['required', 'in:confirmed,completed,cancelled,no_show'],
            'reason' => ['nullable', 'string'],
        ]);

        match ($request->status) {
            'confirmed' => $appointment->confirm(),
            'completed' => $appointment->complete(),
            'no_show' => $appointment->markNoShow(),
            'cancelled' => $appointment->cancel($request->reason, auth()->id()),
        };

        return back()->with('success', 'Status bijgewerkt.');
    }

    public function store(Request $request)
    {
        // Admin handmatig een afspraak inplannen
        $data = $request->validate([
            'customer_name' => ['required', 'string'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['nullable', 'string'],
            'service_id' => ['required', 'exists:services,id'],
            'barber_id' => ['required', 'exists:barbers,id'],
            'starts_at' => ['required', 'date'],
        ]);

        $service = Service::findOrFail($data['service_id']);
        $customer = User::firstOrCreate(
            ['email' => $data['customer_email']],
            [
                'name' => $data['customer_name'],
                'phone' => $data['customer_phone'] ?? null,
                'password' => Hash::make(str()->random(16)),
                'role' => 'customer',
            ]
        );

        Appointment::create([
            'customer_id' => $customer->id,
            'barber_id' => $data['barber_id'],
            'service_id' => $data['service_id'],
            'starts_at' => $data['starts_at'],
            'ends_at' => Carbon::parse($data['starts_at'])->addMinutes($service->duration_minutes),
            'status' => 'confirmed',
            'price_cents' => $service->price_cents,
            'source' => 'admin',
        ]);

        return back()->with('success', 'Afspraak aangemaakt.');
    }
}
