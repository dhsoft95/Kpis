<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class devicDown extends Widget
{



    private function getIOSDownloads(): int
    {
        // Implement logic to fetch iOS downloads
        return 100000; // Placeholder value
    }

    private function getAndroidDownloads(): int
    {
        // Implement logic to fetch Android downloads
        return 150000; // Placeholder value
    }

    private function getMaleGamersPercentage(): float
    {
        // Implement logic to calculate male gamers percentage
        return 60.5; // Placeholder value
    }

    private function getFemaleGamersPercentage(): float
    {
        // Implement logic to calculate female gamers percentage
        return 39.5; // Placeholder value
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view(static::$view, [
            'iosDownloads' => $this->getIOSDownloads(),
            'androidDownloads' => $this->getAndroidDownloads(),
            'maleGamersPercentage' => $this->getMaleGamersPercentage(),
            'femaleGamersPercentage' => $this->getFemaleGamersPercentage(),
        ]);
    }


    protected static string $view = 'livewire.devic-down';
}
