<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'business_name' => BusinessSetting::get('business_name', 'Dawara Barbershop'),
            'address' => BusinessSetting::get('address', 'Wortelhaven 83, 8911 GL Leeuwarden'),
            'phone' => BusinessSetting::get('phone', '058 206 0003'),
            'email' => BusinessSetting::get('email', 'info@dawarabarbershop.nl'),
            'website' => BusinessSetting::get('website', ''),
            'instagram' => BusinessSetting::get('instagram', ''),
            'tiktok' => BusinessSetting::get('tiktok', ''),
            'google_maps' => BusinessSetting::get('google_maps', ''),
            'booking_enabled' => (bool) BusinessSetting::get('booking_enabled', true),
            'advance_booking_days' => (int) BusinessSetting::get('advance_booking_days', 30),
            'slot_interval_minutes' => (int) BusinessSetting::get('slot_interval_minutes', 15),
            'currency' => BusinessSetting::get('currency', 'EUR'),
        ];

        $openingHours = BusinessSetting::get('opening_hours', [
            ['day' => 0, 'open' => null, 'close' => null],
            ['day' => 1, 'open' => '10:00', 'close' => '18:00'],
            ['day' => 2, 'open' => '10:00', 'close' => '18:00'],
            ['day' => 3, 'open' => '10:00', 'close' => '18:00'],
            ['day' => 4, 'open' => '10:00', 'close' => '20:00'],
            ['day' => 5, 'open' => '10:00', 'close' => '18:00'],
            ['day' => 6, 'open' => '10:00', 'close' => '18:00'],
        ]);

        return Inertia::render('Admin/Settings', compact('settings', 'openingHours'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email'],
            'website' => ['nullable', 'url', 'max:200'],
            'instagram' => ['nullable', 'url', 'max:200'],
            'tiktok' => ['nullable', 'url', 'max:200'],
            'google_maps' => ['nullable', 'url', 'max:200'],
            'booking_enabled' => ['boolean'],
            'advance_booking_days' => ['required', 'integer', 'min:1', 'max:90'],
            'slot_interval_minutes' => ['required', 'integer', 'min:5', 'max:60'],
            'currency' => ['required', 'string', 'size:3'],
            'opening_hours' => ['required', 'array', 'size:7'],
            'opening_hours.*.day' => ['required', 'integer', 'between:0,6'],
            'opening_hours.*.open' => ['nullable', 'date_format:H:i'],
            'opening_hours.*.close' => ['nullable', 'date_format:H:i', 'after:opening_hours.*.open'],
        ]);

        // Save all string settings
        foreach (['business_name', 'address', 'phone', 'email', 'website', 'instagram', 'tiktok', 'google_maps', 'currency'] as $key) {
            BusinessSetting::set($key, $data[$key] ?? '', 'string');
        }

        BusinessSetting::set('booking_enabled', $data['booking_enabled'] ? '1' : '0', 'boolean');
        BusinessSetting::set('advance_booking_days', (string) $data['advance_booking_days'], 'integer');
        BusinessSetting::set('slot_interval_minutes', (string) $data['slot_interval_minutes'], 'integer');
        BusinessSetting::set('opening_hours', $data['opening_hours'], 'array');

        return back()->with('success', 'Instellingen opgeslagen.');
    }
}
