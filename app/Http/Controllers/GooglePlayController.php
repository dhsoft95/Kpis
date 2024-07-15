<?php

namespace App\Http\Controllers;

use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GooglePlayController extends Controller
{
    private function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Simba-Money');
        $client->setAuthConfig(storage_path('app/simba-money-9cc01-fa003209d4b2.json'));
        $client->addScope('https://www.googleapis.com/auth/playdeveloperreporting');
        $client->setAccessType('offline');

        return $client;
    }

    private function getAccessToken()
    {
        $client = $this->getClient();
        return $client->fetchAccessTokenWithAssertion()["access_token"];
    }

    private function getRequest($url)
    {
        $accessToken = $this->getAccessToken();
        $response = Http::withToken($accessToken)->get($url);

        return $response->json();
    }

    private function postRequest($url, $body)
    {
        $accessToken = $this->getAccessToken();
        $response = Http::withToken($accessToken)->post($url, $body);

        return $response->json();
    }

    public function getCrashRateMetricSet()
    {
        $url = 'https://playdeveloperreporting.googleapis.com/v1beta1/apps/com.example.app/crashRateMetricSet';
        $metadata = $this->getRequest($url);

        // Changed to return JSON instead of a view
        return response()->json(['metadata' => $metadata, 'queryResults' => []]);
    }

    public function queryCrashRateMetricSet()
    {
        $url = 'https://playdeveloperreporting.googleapis.com/v1beta1/apps/com.example.app/crashRateMetricSet:query';
        $body = [
            'timeline_spec' => [
                'aggregation_period' => 'DAILY',
                'start_time' => [
                    'year' => '2023',
                    'month' => '7',
                    'day' => '1',
                    'time_zone' => 'UTC'
                ],
                'end_time' => [
                    'year' => '2023', // Fixed year to 2023
                    'month' => '7',
                    'day' => '3',
                    'time_zone' => 'UTC'
                ]
            ],
            'dimensions' => ['apiLevel'],
            'metrics' => ['errorReportCount', 'distinctUsers'],
            'page_size' => 10
        ];


        $queryResults = $this->postRequest($url, $body);

        // Changed to return JSON instead of a view
        return response()->json(['metadata' => [], 'queryResults' => $queryResults]);
    }
}
