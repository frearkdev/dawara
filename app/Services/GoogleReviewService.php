<?php

namespace App\Services;

use App\Models\BusinessSetting;
use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleReviewService
{
    /**
     * Fetch and upsert Google reviews for the configured place.
     * Returns count imported or null if skipped (no config / recently synced).
     */
    public static function sync(?string $forcePlaceId = null): ?int
    {
        $placeId = $forcePlaceId ?? BusinessSetting::get('google_places_place_id', '');
        $apiKey = config('services.google_places.api_key');

        if (! $placeId || ! $apiKey) {
            return null;
        }

        // Throttle: only sync once per hour unless forced
        if (! $forcePlaceId) {
            $lastSync = BusinessSetting::get('google_reviews_last_sync');
            if ($lastSync && \Carbon\Carbon::parse($lastSync)->gt(now()->subHour())) {
                return null;
            }
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'id,reviews',
            ])->get('https://places.googleapis.com/v1/places/' . urlencode($placeId));

            if (! $response->successful()) {
                Log::warning('Google Places API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $googleReviews = $data['reviews'] ?? [];
            $imported = 0;

            foreach ($googleReviews as $gr) {
                $googleId = $gr['name'] ?? null;
                if (! $googleId) {
                    continue;
                }

                $rating = $gr['rating'] ?? 5;
                $text = $gr['text'] ?? null;
                $author = $gr['authorAttribution']['displayName'] ?? 'Google Gebruiker';
                $time = $gr['publishTime'] ?? null;

                Review::updateOrCreate(
                    ['google_review_id' => $googleId],
                    [
                        'source' => 'google',
                        'rating' => $rating,
                        'comment' => $text,
                        'google_author' => $author,
                        'visible' => true,
                        'reviewed_at' => $time ? \Carbon\Carbon::parse($time) : now(),
                    ]
                );

                $imported++;
            }

            BusinessSetting::set('google_reviews_last_sync', now()->toDateTimeString(), 'string');

            return $imported;
        } catch (\Exception $e) {
            Log::error('Google review sync failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
