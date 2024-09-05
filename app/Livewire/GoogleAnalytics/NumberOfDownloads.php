<?php

namespace App\Livewire\GoogleAnalytics;

use AllowDynamicProperties;
use App\Http\Controllers\GoogleServices\GoogleAnalyticsController;
use App\Models\AppUser;
use Filament\Widgets\Widget;
use Google\ApiCore\ApiException;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;

#[AllowDynamicProperties] #[Lazy]
class NumberOfDownloads extends Widget
{
    public $iosDownloads = 0;
    public $androidDownloads = 0;
    public $topCountries = [];
    public $userStats = [];
    public $ageGroupCounts = [];
    public $loading = false;

    private $googleAnalyticsController;

    public function mount()
    {
        $this->googleAnalyticsController = new GoogleAnalyticsController();
        $this->loadAllData();
    }

    public function loadAllData()
    {
        $this->loading = true;
        $this->loadDeviceData();
        $this->loadTopCountries();
        $this->loadUserStats();
        $this->loadAgeGroupDistribution();
        $this->loading = false;
    }

    public function loadDeviceData()
    {
        try {
            $downloads = $this->googleAnalyticsController->getDownloads();
            $this->iosDownloads = $downloads['ios'];
            $this->androidDownloads = $downloads['android'];
        } catch (ApiException $e) {
            \Log::error('Failed to fetch Google Analytics data: ' . $e->getMessage());
            $this->iosDownloads = 0;
            $this->androidDownloads = 0;
        }
    }

    public function loadTopCountries()
    {
        try {
            $this->topCountries = $this->googleAnalyticsController->getTopCountries();
        } catch (ApiException $e) {
            \Log::error('Failed to fetch top countries from Google Analytics: ' . $e->getMessage());
            $this->topCountries = [];
        }
    }

    public function loadUserStats()
    {
        $this->userStats = [
            'male' => AppUser::where('gender', 'male')->count(),
            'female' => AppUser::where('gender', 'female')->count(),
        ];
    }

    public function loadAgeGroupDistribution()
    {
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

        $this->ageGroupCounts = $results->mapWithKeys(function ($item) {
            return [$item->age_group => $item->count];
        })->toArray();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view(static::$view, [
            'iosDownloads' => $this->iosDownloads,
            'androidDownloads' => $this->androidDownloads,
            'maleUsers' => $this->userStats['male'] ?? 0,
            'femaleUsers' => $this->userStats['female'] ?? 0,
            'ageGroupCounts' => $this->ageGroupCounts,
            'topCountries' => $this->topCountries,
        ]);
    }

    protected static string $view = 'livewire.devic-down';

//    protected int | string | array $columnSpan = 'full';
    public function poll()
    {
        $this->loadAllData();
    }


    public function getListeners()
    {
        return [
            'refreshData' => 'loadAllData',
            '$refresh' => '$refresh',
            'poll' => 'poll'
        ];
    }
}
