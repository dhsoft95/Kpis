<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class devicDown extends Widget
{


    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.widgets.user-statistics-widget', [
            'iosDownloads' => 1234,
            'androidDownloads' => 5678,
            'maleUsers' => 3000,
            'femaleUsers' => 2000,
        ]);
    }

    protected static string $view = 'livewire.devic-down';
}
