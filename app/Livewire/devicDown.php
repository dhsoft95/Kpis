<?php

namespace App\Livewire;

use App\Models\AppUser;
use App\Models\User;
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
        // Fetch user statistics using the User model
        $maleCount = AppUser::where('gender', 'male')->count();
        $femaleCount = AppUser::where('gender', 'female')->count();

        // Return the results
        return [
            'male' => $maleCount,
            'female' => $femaleCount,
        ];
    }

    public function getAgeGroupDistribution()
    {
        // Fetch age group distribution using mysql_second connection
        $results = AppUser::on('mysql_second')->select(DB::raw("
        CASE
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 24 THEN '18-24'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 45 THEN '45+'
            ELSE 'Unknown'
        END as age_group,
        COUNT(*) as count
    "))
            ->groupBy('age_group')
            ->orderBy('age_group')
            ->get();

        // Convert the results to an associative array
        return $results->mapWithKeys(function ($item) {
            return [$item->age_group => $item->count];
        })->toArray();
    }

    public function getTopLocations()
    {
        // Fetch top 5 user locations using mysql_second connection
        $results = AppUser::on('mysql_second')->select('city', DB::raw('COUNT(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get();

        // Convert the results to an associative array
        return $results->mapWithKeys(function ($item) {
            return [$item->city => $item->count];
        })->toArray();
    }


    public function render(): \Illuminate\Contracts\View\View
    {
        $downloads = $this->getDownloads();
        $userStats = $this->getUserStats();
        $ageGroupCounts = $this->getAgeGroupDistribution();
        $topLocations = $this->getTopLocations();

        return view(static::$view, [
            'iosDownloads' => $downloads['ios'],
            'androidDownloads' => $downloads['android'],
            'maleUsers' => $userStats['male'],
            'femaleUsers' => $userStats['female'],
            'ageGroupCounts' => $ageGroupCounts,
            'topLocations' => $topLocations,
        ]);
    }

    protected static string $view = 'livewire.devic-down';
}
