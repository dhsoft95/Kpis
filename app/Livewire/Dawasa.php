<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class Dawasa extends Widget
{
    protected static string $view = 'livewire.dawasa';
    protected int | string | array $columnSpan = 'full';

    public function calculateStats()
    {
        return [
            'totalMeters' => 1000,
            'openMeters' => 750,
            'closedMeters' => 200,
            'offlineMeters' => 50,
            'onlineMeters' => 950,
            'averageForwardFlow' => number_format(rand(100, 1000) / 100, 2),
            ];
    }
}
