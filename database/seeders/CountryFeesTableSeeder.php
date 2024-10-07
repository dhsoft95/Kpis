<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountryFeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'United States Dollar'],
            ['code' => 'EUR', 'name' => 'Euro'],
            ['code' => 'GBP', 'name' => 'British Pound Sterling'],
            ['code' => 'JPY', 'name' => 'Japanese Yen'],
            ['code' => 'CHF', 'name' => 'Swiss Franc'],
            ['code' => 'CAD', 'name' => 'Canadian Dollar'],
            ['code' => 'AUD', 'name' => 'Australian Dollar'],
            ['code' => 'ZAR', 'name' => 'South African Rand'],
            ['code' => 'TZS', 'name' => 'Tanzanian Shilling'],
            ['code' => 'KES', 'name' => 'Kenyan Shilling'],
            ['code' => 'UGX', 'name' => 'Ugandan Shilling'],
            ['code' => 'RWF', 'name' => 'Rwandan Franc'],
            ['code' => 'BIF', 'name' => 'Burundian Franc'],
            ['code' => 'NGN', 'name' => 'Nigerian Naira'],
            ['code' => 'GHS', 'name' => 'Ghanaian Cedi'],
            ['code' => 'EGP', 'name' => 'Egyptian Pound'],
            ['code' => 'MAD', 'name' => 'Moroccan Dirham'],
            ['code' => 'XOF', 'name' => 'West African CFA franc'],
            ['code' => 'XAF', 'name' => 'Central African CFA franc'],
            ['code' => 'MZN', 'name' => 'Mozambican Metical'],
            ['code' => 'ZMW', 'name' => 'Zambian Kwacha'],
            ['code' => 'BWP', 'name' => 'Botswana Pula'],
            ['code' => 'MUR', 'name' => 'Mauritian Rupee'],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                [
                    'name' => $currency['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
