<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'description' => $s->description,
            'duration_minutes' => $s->duration_minutes,
            'duration_label' => $s->duration_label,
            'price_cents' => $s->price_cents,
            'price_formatted' => $s->price_formatted,
            'color' => $s->color,
            'active' => $s->active,
            'sort_order' => $s->sort_order,
        ]);

        return Inertia::render('Admin/Services', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'color' => ['required', 'string', 'size:7'],
            'active' => ['boolean'],
        ]);

        Service::create($data);

        return back()->with('success', 'Dienst aangemaakt.');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['sometimes', 'integer', 'min:5'],
            'price_cents' => ['sometimes', 'integer', 'min:0'],
            'color' => ['sometimes', 'string', 'size:7'],
            'active' => ['boolean'],
        ]);

        $service->update($data);

        return back()->with('success', 'Dienst bijgewerkt.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return back()->with('success', 'Dienst verwijderd.');
    }
}
