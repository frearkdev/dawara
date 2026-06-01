<?php

namespace Database\Seeders;

use App\Models\Barber;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────────────────
        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Dawara Admin',
            'email' => 'admin@dawarabarbershop.nl',
            'phone' => '+31612345678',
            'password' => Hash::make('dawara2024!'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // ── Oman ──────────────────────────────────────────────────────────────
        $oman = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Oman',
            'email' => 'oman@dawarabarbershop.nl',
            'phone' => '+31611111111',
            'password' => Hash::make('barber2024!'),
            'role' => 'barber',
            'email_verified_at' => now(),
        ]);

        Barber::create([
            'id' => (string) Str::uuid(),
            'user_id' => $oman->id,
            'bio' => 'Vakkundige barber...',
            'specialties' => ['fades', 'knippen', 'baard trimmen', 'haarstyling'],
            'active' => true,
            'sort_order' => 1,
        ]);

        // ── Ahmad ─────────────────────────────────────────────────────────────
        $ahmad = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Ahmad',
            'email' => 'ahmad@dawarabarbershop.nl',
            'phone' => '+31622222222',
            'password' => Hash::make('barber2024!'),
            'role' => 'barber',
            'email_verified_at' => now(),
        ]);

        Barber::create([
            'id' => (string) Str::uuid(),
            'user_id' => $ahmad->id,
            'bio' => 'Gespecialiseerd in klassieke knippen, tapers en huidverzorging.',
            'specialties' => ['knippen', 'taper', 'huidverzorging', 'kinderknippen'],
            'active' => true,
            'sort_order' => 2,
        ]);

        $this->command->info('✓  Admin, Oman en Ahmad aangemaakt');
    }
}
