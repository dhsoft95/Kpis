<?php

namespace App\Livewire;

use App\Models\AppUser;
use Filament\Widgets\Widget;
use Google\Client;
use Google\Service\AnalyticsData;

// Import the User model

class devicDown extends Widget
{
    public function getDownloads(): array
    {
        // Initialize Google Client
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(AnalyticsData::ANALYTICS_READONLY);

        // Create Analytics Data service
        $analyticsData = new AnalyticsData($client);

        // Define the report request
        $request = new AnalyticsData\RunReportRequest([
            'property' => 'properties/[b291b2ae7a7be450565e49024a0d19020c77fd14]',
            'dimensions' => [
                new AnalyticsData\Dimension(['name' => 'appPlatform']),
            ],
            'metrics' => [
                new AnalyticsData\Metric(['name' => 'firstOpens']), // You might want to use a different metric based on your definition of "download"
            ],
        ]);

        // Execute the report request
        $response = $analyticsData->properties->runReport($request);

        // Process the response
        $downloads = [
            'ios' => 0,
            'android' => 0,
        ];
        foreach ($response->getRows() as $row) {
            $platform = $row->getDimensionValues()[0]->getValue();
            $count = $row->getMetricValues()[0]->getValue();
            $downloads[$platform] = $count;
        }

        return $downloads;
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
