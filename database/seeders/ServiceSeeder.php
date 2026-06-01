<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Knippen',
                'description' => 'Klassieke knipbeurt met schaar of tondeuse, afgestemd op jouw stijl en gezichtsvorm.',
                'duration_minutes' => 30,
                'price_cents' => 2200,
                'color' => '#B8860B',
                'sort_order' => 1,
            ],
            [
                'name' => 'Baard trimmen',
                'description' => 'Bijwerken, contouren en verzorgen van je baard. Scherpe lijnen gegarandeerd.',
                'duration_minutes' => 20,
                'price_cents' => 1500,
                'color' => '#378ADD',
                'sort_order' => 2,
            ],
            [
                'name' => 'Knippen + baard',
                'description' => 'Complete behandeling van hoofd tot baard. De populairste keuze bij Dawara.',
                'duration_minutes' => 50,
                'price_cents' => 3000,
                'color' => '#D85A30',
                'sort_order' => 3,
            ],
            [
                'name' => 'Gezicht verzorging',
                'description' => 'Specialistische huidbehandeling voor het gezicht. Dieptereinigend en verzorgend.',
                'duration_minutes' => 30,
                'price_cents' => 3000,
                'color' => '#1D9E75',
                'sort_order' => 4,
            ],
            [
                'name' => 'Haar wassen',
                'description' => 'Verfrissende haarwas met kwaliteitsproducten voor een schone basis.',
                'duration_minutes' => 10,
                'price_cents' => 400,
                'color' => '#7F77DD',
                'sort_order' => 5,
            ],
            [
                'name' => 'Kinderknippen',
                'description' => 'Geduldig en rustig knippen voor kinderen tot 12 jaar.',
                'duration_minutes' => 25,
                'price_cents' => 1800,
                'color' => '#639922',
                'sort_order' => 6,
            ],
            [
                'name' => 'Tondeuse',
                'description' => 'Strakke tondeuse-knipbeurt voor een cleane, onderhouden uitstraling.',
                'duration_minutes' => 25,
                'price_cents' => 1800,
                'color' => '#8B5CF6',
                'sort_order' => 7,
            ],
            [
                'name' => 'Wax neus',
                'description' => 'Snelle en hygiënische neuswax voor een verzorgd gevoel.',
                'duration_minutes' => 5,
                'price_cents' => 400,
                'color' => '#EC4899',
                'sort_order' => 8,
            ],
        ];

        foreach ($services as $data) {
            Service::create(array_merge($data, ['id' => (string) Str::uuid()]));
        }

        $this->command->info('✓  '.count($services).' diensten aangemaakt');
    }
}
