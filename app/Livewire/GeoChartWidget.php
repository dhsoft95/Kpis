<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;

class GeoChartWidget extends Widget
{
    protected static string $view = 'livewire.geo-chart-widget';

    /**
     * @throws ApiException
     * @throws ValidationException
     */
    public function getData()
    {
        $client = new BetaAnalyticsDataClient([
            'credentials' => storage_path('app/analytics/monitoring-portal-430511-6490ca22e998.json'),
        ]);

        $propertyId = '446716346';

        $response = $client->runReport([
            'property' => 'properties/' . $propertyId,
            'dateRanges' => [
                new DateRange([
                    'start_date' => '30daysAgo',
                    'end_date' => 'today',
                ]),
            ],
            'dimensions' => [new Dimension(['name' => 'country'])],
            'metrics' => [new Metric(['name' => 'activeUsers'])],
        ]);

        $userLocations = [['Country', 'Users']];
        foreach ($response->getRows() as $row) {
            $userLocations[] = [
                $row->getDimensionValues()[0]->getValue(),
                (int) $row->getMetricValues()[0]->getValue()
            ];
        }

        return $userLocations;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view(static::$view, [
            'userLocations' => $this->getData(),
        ]);
    }


}
