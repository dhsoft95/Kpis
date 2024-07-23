<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class devicDown extends Widget
{


    public function getDownloads()
    {
        // Replace with actual data fetching logic
        return [
            'ios' => 4352,
            'android' => 5352,
        ];
    }

    public function getUserStats()
    {
        // Replace with actual data fetching logic
        return [
            'male' => 3520,
            'female' => 6184,
        ];
    }

    protected static string $view = 'livewire.devic-down';
}
