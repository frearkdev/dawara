<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['customer', 'barber.user', 'appointment'])
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(fn ($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'visible' => $r->visible,
                'source' => $r->source,
                'customer_name' => $r->google_author ?? $r->customer?->name ?? 'Onbekend',
                'barber_name' => $r->barber?->user?->name ?? '',
                'created_at' => $r->reviewed_at?->format('d M Y') ?? $r->created_at->format('d M Y'),
            ]);

        $googlePlaceId = BusinessSetting::get('google_places_place_id', '');

        return Inertia::render('Admin/Reviews', compact('reviews', 'googlePlaceId'));
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate([
            'visible' => ['required', 'boolean'],
        ]);

        $review->update($data);

        return back()->with('success', 'Review bijgewerkt.');
    }

    /**
     * Try API first, then fallback to free scraper.
     */
    public function importGoogle(Request $request)
    {
        $request->validate([
            'place_id' => ['nullable', 'string', 'max:100'],
        ]);

        $placeId = $request->input('place_id');

        // 1. Try official API if place_id is given
        if ($placeId) {
            BusinessSetting::set('google_places_place_id', $placeId, 'string');
            $imported = \App\Services\GoogleReviewService::sync($placeId);

            if ($imported !== null && $imported > 0) {
                return back()->with('success', "{$imported} Google reviews via API gesynchroniseerd.");
            }
        }

        // 2. Fallback: free scraper (no API key needed)
        $scraped = \App\Services\GoogleReviewScraper::scrape('Dawara Barbershop', 'Leeuwarden');

        if ($scraped === null) {
            return back()->with('error', 'Kon geen reviews vinden. Probeer reviews handmatig te plakken.');
        }

        return back()->with('success', "{$scraped} Google reviews gevonden en opgeslagen.");
    }

    /**
     * Parse reviews pasted from Google Maps.
     */
    public function importPasted(Request $request)
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:50000'],
        ]);

        $imported = \App\Services\GoogleReviewScraper::parsePastedText($data['text']);

        return back()->with('success', "{$imported} reviews geïmporteerd van plaktekst.");
    }

    /**
     * Manually add a single review.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'author' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
            'source' => ['required', 'in:google,system'],
        ]);

        Review::create([
            'source' => $data['source'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'google_author' => $data['source'] === 'google' ? $data['author'] : null,
            'visible' => true,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Review toegevoegd.');
    }
}
