<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class BusinessSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'business_name', 'value' => 'Dawara Barbershop', 'type' => 'string'],
            ['key' => 'address', 'value' => 'Wortelhaven 83, 8911 GL Leeuwarden', 'type' => 'string'],
            ['key' => 'phone', 'value' => '058 206 0003', 'type' => 'string'],
            ['key' => 'email', 'value' => 'info@dawarabarbershop.nl', 'type' => 'string'],
            ['key' => 'website', 'value' => 'https://dawarabarbershop.nl', 'type' => 'string'],
            ['key' => 'instagram', 'value' => 'https://www.instagram.com/barbershop_dawara/', 'type' => 'string'],
            ['key' => 'tiktok', 'value' => 'https://www.tiktok.com/@barbershop.dawara', 'type' => 'string'],
            ['key' => 'google_maps', 'value' => 'https://maps.app.goo.gl/xNY2eFDwtrSRbG8T8', 'type' => 'string'],
            ['key' => 'booking_enabled', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'advance_booking_days', 'value' => '30', 'type' => 'integer'],
            ['key' => 'slot_interval_minutes', 'value' => '15', 'type' => 'integer'],
            ['key' => 'currency', 'value' => 'EUR', 'type' => 'string'],
            ['key' => 'opening_hours', 'value' => json_encode([
                ['day' => 1, 'open' => '10:00', 'close' => '18:00'],
                ['day' => 2, 'open' => '10:00', 'close' => '18:00'],
                ['day' => 3, 'open' => '10:00', 'close' => '18:00'],
                ['day' => 4, 'open' => '10:00', 'close' => '20:00'],
                ['day' => 5, 'open' => '10:00', 'close' => '18:00'],
                ['day' => 6, 'open' => '10:00', 'close' => '18:00'],
                ['day' => 0, 'open' => null, 'close' => null],
            ]), 'type' => 'array'],
        ];

        foreach ($defaults as $row) {
            BusinessSetting::updateOrCreate(['key' => $row['key']], $row);
        }

        $this->command->info('✓  Business settings aangemaakt');
    }
}
