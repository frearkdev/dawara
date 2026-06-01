<?php

namespace Database\Seeders;

use App\Models\Barber;
use App\Models\Service;
use Illuminate\Database\Seeder;

class BarberServiceSeeder extends Seeder
{
    public function run(): void
    {
        $oman = Barber::whereHas('user', fn ($q) => $q->where('name', 'Oman'))->firstOrFail();
        $ahmad = Barber::whereHas('user', fn ($q) => $q->where('name', 'Ahmad'))->firstOrFail();

        $s = Service::all()->keyBy('name');

        // Oman — alle diensten behalve kinderknippen
        $oman->services()->attach([
            $s['Knippen']->id,
            $s['Baard trimmen']->id,
            $s['Knippen + baard']->id,
            $s['Gezicht verzorging']->id,
            $s['Haar wassen']->id,
            $s['Tondeuse']->id,
            $s['Wax neus']->id,
        ]);

        // Ahmad — alle diensten inclusief kinderknippen
        $ahmad->services()->attach([
            $s['Knippen']->id,
            $s['Baard trimmen']->id,
            $s['Knippen + baard']->id,
            $s['Gezicht verzorging']->id,
            $s['Haar wassen']->id,
            $s['Kinderknippen']->id,
            $s['Tondeuse']->id,
            $s['Wax neus']->id,
        ]);

        $this->command->info('✓  Diensten gekoppeld aan Oman en Ahmad');
    }
}
