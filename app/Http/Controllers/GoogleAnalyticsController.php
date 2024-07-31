<?php

namespace App\Http\Controllers;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;

class GoogleAnalyticsController extends Controller
{
    private $propertyId;
    private $client;

    /**
     * @throws ValidationException
     */
    public function __construct()
    {
        $this->propertyId = config('services.google_analytics.property_id');
        $this->client = new BetaAnalyticsDataClient([
            'credentials' => storage_path('app/analytics/monitoring-portal-430511-6490ca22e998.json'),
        ]);
    }

    /**
     * @throws ApiException
     */
    public function getDownloads(): array
    {
        $response = $this->client->runReport([
            'property' => 'properties/' . $this->propertyId,
            'dateRanges' => [
                new DateRange([
                    'start_date' => '7daysAgo',
                    'end_date' => 'today',
                ]),
            ],
            'dimensions' => [new Dimension(['name' => 'operatingSystem'])],
            'metrics' => [new Metric(['name' => 'screenPageViews'])],
            'orderBys' => [
                new OrderBy([
                    'dimension' => new OrderBy\DimensionOrderBy(['dimension_name' => 'operatingSystem']),
                    'desc' => false,
                ]),
            ],
        ]);

        $downloads = [
            'ios' => 0,
            'android' => 0,
        ];

        foreach ($response->getRows() as $row) {
            $os = strtolower($row->getDimensionValues()[0]->getValue());
            $views = (int)$row->getMetricValues()[0]->getValue();

            if (strpos($os, 'ios') !== false) {
                $downloads['ios'] += $views;
            } elseif (strpos($os, 'android') !== false) {
                $downloads['android'] += $views;
            }
        }

        return $downloads;
    }


    /**
     * @throws ApiException
     */
    public function getTopCountries($limit = 3): array
    {
        $response = $this->client->runReport([
            'property' => 'properties/' . $this->propertyId,
            'dateRanges' => [
                new DateRange([
                    'start_date' => '30daysAgo',
                    'end_date' => 'today',
                ]),
            ],
            'dimensions' => [new Dimension(['name' => 'country'])],
            'metrics' => [new Metric(['name' => 'totalUsers'])],
            'orderBys' => [
                new OrderBy([
                    'metric' => new OrderBy\MetricOrderBy(['metric_name' => 'totalUsers']),
                    'desc' => true,
                ]),
            ],
            'limit' => $limit,
        ]);

        $topCountries = [];
        foreach ($response->getRows() as $row) {
            $country = $row->getDimensionValues()[0]->getValue();
            $users = (int)$row->getMetricValues()[0]->getValue();
            $topCountries[$country] = $users;
        }

        return $topCountries;
    }

}
