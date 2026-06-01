<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Barber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AvailabilitySeeder extends Seeder
{
    // day_of_week: 0 = zondag, 1 = maandag … 6 = zaterdag
    private array $schedule = [
        ['day' => 1, 'start' => '10:00', 'end' => '18:00'], // maandag
        ['day' => 2, 'start' => '10:00', 'end' => '18:00'], // dinsdag
        ['day' => 3, 'start' => '10:00', 'end' => '18:00'], // woensdag
        ['day' => 4, 'start' => '10:00', 'end' => '20:00'], // donderdag
        ['day' => 5, 'start' => '10:00', 'end' => '18:00'], // vrijdag
        ['day' => 6, 'start' => '10:00', 'end' => '18:00'], // zaterdag
        // zondag (0) = gesloten, niet aanmaken
    ];

    public function run(): void
    {
        $barbers = Barber::with('user')->get();

        foreach ($barbers as $barber) {
            foreach ($this->schedule as $slot) {
                Availability::create([
                    'id' => (string) Str::uuid(),
                    'barber_id' => $barber->id,
                    'day_of_week' => $slot['day'],
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                    'active' => true,
                ]);
            }
            $this->command->info("✓  Rooster aangemaakt voor {$barber->user->name}");
        }

        $this->command->newLine();
        $this->command->line('  Ma–Wo   10:00 – 18:00');
        $this->command->line('  Do       10:00 – 20:00');
        $this->command->line('  Vr–Za    10:00 – 18:00');
        $this->command->line('  Zo       Gesloten');
    }
}
