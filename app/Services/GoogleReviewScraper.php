<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Free Google review scraper — no API key needed.
 * Scrapes Google Search knowledge panel for review data.
 */
class GoogleReviewScraper
{
    /**
     * Attempt to scrape reviews from Google Search knowledge panel.
     * Returns count imported, or null if nothing found.
     */
    public static function scrape(string $businessName, string $city = 'Leeuwarden'): ?int
    {
        $query = urlencode("{$businessName} {$city} reviews");

        try {
            $request = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Accept-Language' => 'nl-NL,nl;q=0.9,en;q=0.8',
                ]);

            // Windows dev environments often lack proper CA bundles; try strict first
            try {
                $html = $request->get("https://www.google.com/search?q={$query}&hl=nl")->body();
            } catch (\Exception $sslEx) {
                if (str_contains($sslEx->getMessage(), 'SSL') || str_contains($sslEx->getMessage(), 'certificate')) {
                    Log::warning('SSL verification failed for Google scrape, retrying without verify (dev only)');
                    $html = $request->withoutVerifying()->get("https://www.google.com/search?q={$query}&hl=nl")->body();
                } else {
                    throw $sslEx;
                }
            }

            // Google embeds knowledge panel data in AF_initDataCallback script blocks
            $reviews = self::extractFromAfInitData($html);

            if (empty($reviews)) {
                // Fallback: try to extract from any JSON-LD on the page
                $reviews = self::extractFromJsonLd($html);
            }

            if (empty($reviews)) {
                return null;
            }

            $imported = 0;
            foreach ($reviews as $r) {
                $hash = md5(($r['author'] ?? '') . ($r['text'] ?? ''));

                Review::updateOrCreate(
                    ['google_review_id' => 'scraped_' . $hash],
                    [
                        'source' => 'google',
                        'rating' => $r['rating'] ?? 5,
                        'comment' => $r['text'] ?? null,
                        'google_author' => $r['author'] ?? 'Google Gebruiker',
                        'visible' => true,
                        'reviewed_at' => $r['date'] ?? now(),
                    ]
                );
                $imported++;
            }

            return $imported;
        } catch (\Exception $e) {
            Log::warning('Google review scrape failed', ['error' => $e->getMessage()]);
            return null;
        } finally {
            // Always attempt a parse of any HTML we got, even on error
        }
    }

    /**
     * Parse pasted text from Google Maps review section.
     * User copies review text from Google Maps and pastes it here.
     */
    public static function parsePastedText(string $text): int
    {
        $lines = explode("\n", $text);
        $reviews = [];
        $current = ['author' => '', 'rating' => 5, 'text' => '', 'date' => null];
        $buffer = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                if (! empty($buffer)) {
                    $reviews[] = self::extractReviewFromBlock($buffer);
                    $buffer = [];
                }
                continue;
            }
            $buffer[] = $line;
        }
        if (! empty($buffer)) {
            $reviews[] = self::extractReviewFromBlock($buffer);
        }

        $imported = 0;
        foreach ($reviews as $r) {
            if (empty($r['text']) && empty($r['author'])) {
                continue;
            }

            $hash = md5(($r['author'] ?? '') . ($r['text'] ?? ''));

            Review::updateOrCreate(
                ['google_review_id' => 'pasted_' . $hash],
                [
                    'source' => 'google',
                    'rating' => $r['rating'] ?? 5,
                    'comment' => $r['text'] ?? null,
                    'google_author' => $r['author'] ?? 'Google Gebruiker',
                    'visible' => true,
                    'reviewed_at' => $r['date'] ?? now(),
                ]
            );
            $imported++;
        }

        return $imported;
    }

    // ── Extractors ───────────────────────────────────────────────────────────

    private static function extractFromAfInitData(string $html): array
    {
        $reviews = [];

        // Find AF_initDataCallback blocks that might contain review data
        if (! preg_match_all('/AF_initDataCallback\({[^}]*data\s*:\s*(\[[\s\S]*?\])\s*,/i', $html, $matches)) {
            // Try alternate pattern
            preg_match_all('/AF_initDataCallback\({[^}]*data\s*:\s*(\[.*?\])\s*,/i', $html, $matches);
        }

        foreach ($matches[1] ?? [] as $json) {
            $data = @json_decode($json, true);
            if (! is_array($data)) continue;

            $found = self::digForReviews($data);
            foreach ($found as $r) {
                $reviews[] = $r;
            }
        }

        return $reviews;
    }

    private static function digForReviews(array $node): array
    {
        $reviews = [];
        $stack = [$node];

        while (! empty($stack)) {
            $current = array_pop($stack);

            if (! is_array($current)) continue;

            // Heuristic: look for arrays that look like review objects
            // Google knowledge panel review blocks often have: [author, avatar, rating, text, date, ...]
            if (isset($current[0]) && is_array($current[0]) && count($current[0]) >= 4) {
                foreach ($current as $item) {
                    if (! is_array($item) || count($item) < 4) continue;

                    $rating = self::guessRating($item);
                    $text = self::guessText($item);
                    $author = self::guessAuthor($item);
                    $date = self::guessDate($item);

                    if ($text || $author) {
                        $reviews[] = compact('rating', 'text', 'author', 'date');
                    }
                }
            }

            foreach ($current as $child) {
                if (is_array($child)) {
                    $stack[] = $child;
                }
            }
        }

        return $reviews;
    }

    private static function extractFromJsonLd(string $html): array
    {
        $reviews = [];

        if (! preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches)) {
            return $reviews;
        }

        foreach ($matches[1] as $json) {
            $data = @json_decode($json, true);
            if (! is_array($data)) continue;

            // Could be a @graph array
            $nodes = $data['@graph'] ?? [$data];

            foreach ($nodes as $node) {
                if (($node['@type'] ?? '') !== 'LocalBusiness') continue;

                foreach ($node['review'] ?? [] as $r) {
                    $reviews[] = [
                        'author' => $r['author']['name'] ?? 'Google Gebruiker',
                        'rating' => $r['reviewRating']['ratingValue'] ?? 5,
                        'text' => $r['reviewBody'] ?? null,
                        'date' => $r['datePublished'] ?? null,
                    ];
                }
            }
        }

        return $reviews;
    }

    private static function extractReviewFromBlock(array $lines): array
    {
        $text = [];
        $author = '';
        $rating = 5;
        $date = null;

        foreach ($lines as $line) {
            // Star indicators
            if (preg_match('/([1-5])\s*ster/', $line, $m) || preg_match('/rating\s*[:=]\s*([1-5])/', $line, $m)) {
                $rating = (int) $m[1];
                continue;
            }
            // Date patterns
            if (preg_match('/(\d{1,2})\s+(januari|februari|maart|april|mei|juni|juli|augustus|september|oktober|november|december)\s+(\d{4})/i', $line, $m)
                || preg_match('/(\d{4}-\d{2}-\d{2})/', $line, $m)
                || preg_match('/(\d{1,2})\s+(jan|feb|mrt|apr|mei|jun|jul|aug|sep|okt|nov|dec)[a-z]*\s+(\d{4})/i', $line, $m)
            ) {
                $date = $line;
                continue;
            }
            // Author is usually a short line at the start, before the main text
            if (strlen($line) < 50 && empty($author) && ! preg_match('/^\d+\s+(reviews?|beoordeling)/i', $line)) {
                $author = $line;
                continue;
            }

            $text[] = $line;
        }

        return [
            'author' => $author,
            'rating' => $rating,
            'text' => implode("\n", $text),
            'date' => $date,
        ];
    }

    private static function guessRating(array $item): int
    {
        foreach ($item as $val) {
            if (is_int($val) && $val >= 1 && $val <= 5) return $val;
            if (is_string($val) && preg_match('/^([1-5])\.0$/', $val, $m)) return (int) $m[1];
        }
        return 5;
    }

    private static function guessText(array $item): ?string
    {
        foreach ($item as $val) {
            if (is_string($val) && strlen($val) > 20) return $val;
        }
        return null;
    }

    private static function guessAuthor(array $item): string
    {
        foreach ($item as $val) {
            if (is_string($val) && strlen($val) > 2 && strlen($val) < 60 && ! preg_match('/\d{4}/', $val)) {
                return $val;
            }
        }
        return 'Google Gebruiker';
    }

    private static function guessDate(array $item): ?string
    {
        foreach ($item as $val) {
            if (is_string($val) && preg_match('/\d{1,2}\s+(januari|februari|maart|april|mei|juni|juli|augustus|september|oktober|november|december)/i', $val)) {
                return $val;
            }
        }
        return null;
    }
}
