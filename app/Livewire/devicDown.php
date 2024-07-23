<?php

namespace App\Livewire;

use App\Models\AppUser;
use Filament\Widgets\Widget;
use App\Models\User; // Import the User model

class devicDown extends Widget
{
    public function getDownloads()
    {
        // Keep dummy data for downloads
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
