<?php

namespace App\Console\Commands;

use App\Services\GoogleReviewService;
use Illuminate\Console\Command;

class SyncGoogleReviews extends Command
{
    protected $signature = 'reviews:sync-google {--place-id=} {--force}';
    protected $description = 'Sync Google reviews for the configured or provided place ID';

    public function handle(): int
    {
        $placeId = $this->option('place-id') ?: null;
        $force = (bool) $this->option('force');

        $this->info('Syncing Google reviews...');

        $imported = $placeId && $force
            ? GoogleReviewService::sync($placeId)
            : GoogleReviewService::sync();

        if ($imported === null) {
            $this->warn('Skipped — no Place ID / API key configured, or already synced recently.');
            return self::FAILURE;
        }

        $this->info("{$imported} Google reviews synced.");
        return self::SUCCESS;
    }
}
