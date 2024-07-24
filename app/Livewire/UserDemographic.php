<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class UserDemographic extends Widget
{
    public function getCountryData()
    {
        return [
            'US' => ['name' => 'United States', 'percentage' => 35, 'color' => 'blue'],
            'CA' => ['name' => 'Canada', 'percentage' => 26, 'color' => 'red'],
            'FR' => ['name' => 'France', 'percentage' => 18, 'color' => 'green'],
            'DE' => ['name' => 'Germany', 'percentage' => 14, 'color' => 'yellow'],
            'AU' => ['name' => 'Australia', 'percentage' => 7, 'color' => 'purple'],
        ];
    }

    public function getAgeDistribution()
    {
        return [
            ['range' => '18-24', 'percentage' => 45],
            ['range' => '25-34', 'percentage' => 65],
            ['range' => '35-44', 'percentage' => 30],
            ['range' => '45-54', 'percentage' => 20],
            ['range' => '55+', 'percentage' => 10],
        ];
    }

    public function getGenderDistribution()
    {
        return [
            ['gender' => 'Female', 'percentage' => 52, 'color' => 'pink'],
            ['gender' => 'Male', 'percentage' => 48, 'color' => 'blue'],
        ];
    }

    public function getTopLocations()
    {
        return [
            ['name' => 'New York', 'percentage' => 25],
            ['name' => 'Los Angeles', 'percentage' => 18],
            ['name' => 'Chicago', 'percentage' => 12],
            ['name' => 'Houston', 'percentage' => 8],
            ['name' => 'Phoenix', 'percentage' => 5],
        ];
    }

    protected static string $view = 'livewire.user-demographic';
}
