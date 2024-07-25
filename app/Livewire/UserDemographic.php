<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Livewire\WithPagination;

class UserDemographic extends Widget
{
    protected static string $view = 'livewire.user-demographic';

    public function getViewData(): array
    {
        // In a real application, you'd fetch this data from your database or analytics service
        return [
            'countries' => [
                ['name' => 'United States', 'code' => 'US', 'percentage' => 35],
                ['name' => 'Canada', 'code' => 'CA', 'percentage' => 26],
                ['name' => 'France', 'code' => 'FR', 'percentage' => 18],
                ['name' => 'Italy', 'code' => 'IT', 'percentage' => 14],
                ['name' => 'Australia', 'code' => 'AU', 'percentage' => 10],
                ['name' => 'India', 'code' => 'IN', 'percentage' => 7],
            ],
        ];
    }
}
