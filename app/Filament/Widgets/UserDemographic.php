<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UserDemographic extends Widget
{
    protected static string $view = 'filament.widgets.user-demographic';



    public array $chartData = [];

    public function mount()
    {
        $this->chartData = [
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