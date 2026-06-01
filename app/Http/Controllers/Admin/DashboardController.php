<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Barber;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = today();

        // Statistieken
        $stats = [
            'today_count' => Appointment::today()->whereNotIn('status', ['cancelled', 'no_show'])->count(),
            'week_count' => Appointment::whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()])->whereNotIn('status', ['cancelled', 'no_show'])->count(),
            'month_revenue' => Appointment::whereMonth('starts_at', now()->month)->where('status', 'completed')->sum('price_cents') / 100,
            'customer_count' => User::where('role', 'customer')->count(),
            'pending_payments' => Appointment::whereIn('status', ['pending', 'confirmed'])->whereDoesntHave('payment', fn ($q) => $q->where('status', 'paid'))->count(),
        ];

        // Vandaag's afspraken
        $todayAppointments = Appointment::with(['customer', 'barber.user', 'service', 'payment'])
            ->today()
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->orderBy('starts_at')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'starts_at' => $a->starts_at->format('H:i'),
                'ends_at' => $a->ends_at->format('H:i'),
                'customer_name' => $a->customer->name,
                'customer_phone' => $a->customer->phone,
                'barber_name' => $a->barber->user->name,
                'service_name' => $a->service->name,
                'status' => $a->status,
                'price_formatted' => $a->price_formatted,
                'is_paid' => $a->is_paid,
            ]);

        // Komende afspraken (morgen en verder, max 10)
        $upcomingAppointments = Appointment::with(['customer', 'barber.user', 'service'])
            ->where('starts_at', '>', now()->endOfDay())
            ->upcoming()
            ->take(10)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'starts_at' => $a->starts_at->format('D d M H:i'),
                'customer_name' => $a->customer->name,
                'barber_name' => $a->barber->user->name,
                'service_name' => $a->service->name,
                'status' => $a->status,
            ]);

        $barbers = Barber::with('user')->where('active', true)->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->user->name,
            ]);

        $quickStats = [
            ['label' => 'Vandaag', 'value' => $stats['today_count'], 'icon' => 'calendar'],
            ['label' => 'Deze week', 'value' => $stats['week_count'], 'icon' => 'chart'],
            ['label' => 'Omzet', 'value' => '€'.number_format($stats['month_revenue'], 2, ',', '.'), 'icon' => 'euro'],
            ['label' => 'Klanten', 'value' => $stats['customer_count'], 'icon' => 'users'],
            ['label' => 'Open betaal.', 'value' => $stats['pending_payments'], 'icon' => 'warning'],
        ];

        return Inertia::render('Admin/Dashboard', compact(
            'stats', 'todayAppointments', 'upcomingAppointments', 'barbers', 'quickStats'
        ));
    }
}
