<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

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
        // Fetch user statistics from the database
        $userStats = DB::connection('mysql_second')->table('users')
            ->select(DB::raw('gender, COUNT(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        // Ensure that both 'male' and 'female' are set in case of missing data
        return [
            'male' => $userStats['male'] ?? 0,
            'female' => $userStats['female'] ?? 0,
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
