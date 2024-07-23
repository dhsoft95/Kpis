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

    public function render(): \Illuminate\Contracts\View\View
    {
        $downloads = $this->getDownloads();
        $userStats = $this->getUserStats();

        return view(static::$view, [
            'iosDownloads' => $downloads['ios'],
            'androidDownloads' => $downloads['android'],
            'maleUsers' => $userStats['male'],
            'femaleUsers' => $userStats['female'],
        ]);
    }

    protected static string $view = 'livewire.devic-down';
}
