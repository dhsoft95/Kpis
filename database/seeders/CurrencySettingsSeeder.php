<?php

namespace Database\Seeders;

use App\Models\CurrencySetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $settings = [
            [
                'key' => 'usd_determinant',
                'value' => 1.05,
                'type' => 'percentage',
                'description' => 'USD determinant applied to currency conversions'
            ],

        ];

        foreach ($settings as $setting) {
            CurrencySetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
