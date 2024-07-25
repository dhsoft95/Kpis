<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Livewire\WithPagination;

class UserDemographic extends Widget
{
    protected static string $view = 'livewire.user-demographic';

    use WithPagination;

    public $topCountries = [];
    public $countryData = [];

    public function mount()
    {
        $this->topCountries = [
            ['name' => 'United States', 'code' => 'us', 'percentage' => '35%'],
            ['name' => 'Canada', 'code' => 'ca', 'percentage' => '26%'],
            ['name' => 'France', 'code' => 'fr', 'percentage' => '18%'],
            ['name' => 'Italy', 'code' => 'it', 'percentage' => '14%'],
            ['name' => 'Australia', 'code' => 'au', 'percentage' => '10%'],
            ['name' => 'India', 'code' => 'in', 'percentage' => '7%'],
        ];

        $this->countryData = [
            ['Country', 'Popularity'],
            ['Germany', 200],
            ['United States', 300],
            ['Brazil', 400],
            ['Canada', 500],
            ['France', 600],
            ['RU', 700]
        ];
    }
}
