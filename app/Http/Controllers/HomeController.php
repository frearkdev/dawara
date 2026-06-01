<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Review;
use App\Models\Service;
use App\Services\GoogleReviewScraper;
use App\Services\GoogleReviewService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        // Auto-sync Google reviews in the background
        // 1. Try official API if configured (reliable)
        $apiImported = GoogleReviewService::sync();

        // 2. Fallback: free scraper if API not configured or returned nothing
        if ($apiImported === null || $apiImported === 0) {
            GoogleReviewScraper::scrape('Dawara Barbershop', 'Leeuwarden');
        }
        $services = Service::where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description,
                'price_formatted' => $s->price_formatted,
                'duration_label' => $s->duration_label,
                'color' => $s->color,
            ]);

        $barbers = Barber::with('user')
            ->where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->user->name,
                'bio' => $b->bio,
                'specialties' => $b->specialties ?? [],
                'average_rating' => $b->average_rating,
                'review_count' => $b->reviews()->count(),
            ]);

        $reviews = Review::with(['customer', 'barber.user'])
            ->where('visible', true)
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'customer_name' => $r->google_author ?? $r->customer?->name ?? 'Klant',
                'barber_name' => $r->barber?->user?->name ?? '',
                'source' => $r->source,
                'reviewed_at' => $r->reviewed_at?->format('d M Y'),
            ]);

        return Inertia::render('Home', compact('services', 'barbers', 'reviews'));
    }
}
