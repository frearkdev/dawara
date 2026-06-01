<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Barber;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = Barber::with(['user', 'availability', 'services'])->get()->map(fn ($b) => [
            'id' => $b->id,
            'name' => $b->user->name,
            'email' => $b->user->email,
            'bio' => $b->bio,
            'avatar' => $b->avatar,
            'specialties' => $b->specialties ?? [],
            'active' => $b->active,
            'sort_order' => $b->sort_order,
            'service_ids' => $b->services->pluck('id'),
            'services' => $b->services->pluck('name'),
            'availability' => $b->availability->map(fn ($a) => [
                'day_of_week' => $a->day_of_week,
                'day_name' => $a->day_name,
                'start_time' => $a->start_time,
                'end_time' => $a->end_time,
                'active' => $a->active,
            ]),
        ]);

        $allServices = Service::orderBy('sort_order')->get(['id', 'name']);

        return Inertia::render('Admin/Barbers', compact('barbers', 'allServices'));
    }

    public function update(Request $request, Barber $barber)
    {
        $data = $request->validate([
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['string', 'max:50'],
        ]);

        $barber->update($data);

        return back()->with('success', 'Barber bijgewerkt.');
    }

    public function updateServices(Request $request, Barber $barber)
    {
        $data = $request->validate([
            'service_ids' => ['required', 'array'],
            'service_ids.*' => ['exists:services,id'],
        ]);

        $barber->services()->sync($data['service_ids']);

        return back()->with('success', 'Diensten bijgewerkt.');
    }

    public function updateAvailability(Request $request, Barber $barber)
    {
        $data = $request->validate([
            'availability' => ['required', 'array'],
            'availability.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'availability.*.start_time' => ['required', 'date_format:H:i'],
            'availability.*.end_time' => ['required', 'date_format:H:i', 'after:availability.*.start_time'],
            'availability.*.active' => ['boolean'],
        ]);

        foreach ($data['availability'] as $slot) {
            Availability::updateOrCreate(
                ['barber_id' => $barber->id, 'day_of_week' => $slot['day_of_week']],
                [
                    'id' => (string) Str::uuid(),
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'active' => $slot['active'] ?? true,
                ]
            );
        }

        return back()->with('success', 'Rooster bijgewerkt.');
    }
}
