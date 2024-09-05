<?php

namespace App\Http\Controllers\GoogleServices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GooglePlayReportingController extends Controller
{

    private $baseUrl = 'https://playdeveloperreporting.googleapis.com/v1beta1';

    public function getCrashRateMetrics(Request $request)
    {
        $accessToken = config('services.google_play.api_token');
        $appId = $request->input('app_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $metrics = $request->input('metrics', ['errorReportCount', 'distinctUsers']);
        $dimensions = $request->input('dimensions', ['apiLevel']);
        $pageSize = $request->input('page_size', 1000);

        $url = "{$this->baseUrl}/apps/{$appId}/crashRateMetricSet:query";

        try {
            $response = Http::withToken($accessToken)
                ->post($url, [
                    'timeline_spec' => [
                        'aggregation_period' => 'DAILY',
                        'start_time' => $this->formatDate($startDate),
                        'end_time' => $this->formatDate($endDate),
                    ],
                    'dimensions' => $dimensions,
                    'metrics' => $metrics,
                    'page_size' => $pageSize
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Failed to fetch crash rate metrics'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function formatDate($date)
    {
        $dateTime = new \DateTime($date, new \DateTimeZone('America/Los_Angeles'));
        return [
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('n'),
            'day' => $dateTime->format('j'),
            'time_zone' => 'America/Los_Angeles'
        ];
    }

}
