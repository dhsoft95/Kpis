<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Livewire\WithPagination;

class UserDemographic extends Widget
{
    protected static string $view = 'livewire.user-demographic';

    protected function getViewData(): array
    {
        return [
            'chartData' => [
                ['Country', 'Popularity'],
                ['Germany', 200],
                ['United States', 300],
                ['Brazil', 400],
                ['Canada', 500],
                ['France', 600],
                ['RU', 700]
            ],
            'chartOptions' => [
                'colorAxis' => ['colors' => ['#e7711c', '#4374e0']],
                'backgroundColor' => '#81d4fa',
                'datalessRegionColor' => '#f8bbd0',
                'defaultColor' => '#f5f5f5',
            ],
        ];
    }
}
