<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::withCount('appointments')
            ->where('role', 'customer')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'created_at' => $c->created_at?->format('d M Y'),
                'appointments_count' => $c->appointments_count,
            ]);

        return Inertia::render('Admin/Customers', compact('customers'));
    }

    public function show(User $user): Response
    {
        $user->load(['appointments' => fn ($q) => $q->with(['barber.user', 'service', 'payment'])->orderByDesc('starts_at')->take(20)]);

        $appointments = $user->appointments->map(fn ($a) => [
            'id' => $a->id,
            'starts_at' => $a->starts_at->format('d M Y H:i'),
            'service_name' => $a->service->name,
            'barber_name' => $a->barber->user->name,
            'status' => $a->status,
            'price_formatted' => $a->price_formatted,
            'is_paid' => $a->is_paid,
        ]);

        $customer = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'created_at' => $user->created_at?->format('d M Y'),
        ];

        return Inertia::render('Admin/CustomerDetail', compact('customer', 'appointments'));
    }
}
