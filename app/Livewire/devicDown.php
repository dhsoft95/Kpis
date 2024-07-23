<?php

namespace App\Livewire;

use App\Models\AppUser;
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
        // Fetch user statistics using the User model
        $maleCount = AppUser::where('gender', 'male')->count();
        $femaleCount = AppUser::where('gender', 'female')->count();

        // Return the results
        return [
            'male' => $maleCount,
            'female' => $femaleCount,
        ];
    }


    protected static string $view = 'livewire.devic-down';
}
