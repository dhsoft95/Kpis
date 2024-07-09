<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class MapOverview extends Widget
{
    protected int | string | array $columnSpan = 'full';

    public $userLocations = [
        ['Country', 'Code', 'Latitude', 'Longitude', 'Users'], // Modified header row
        // Africa
        ['Kenya', 'KE', -1.286389, 36.817223, 100], // Include country code
        ['Tanzania', 'TZ', -6.792354, 39.208328, 150], // Include country code
        ['Uganda', 'UG', 0.347596, 32.582520, 75], // Include country code
        ['Rwanda', 'RW', -1.953592, 30.060572, 50], // Include country code
        ['Nigeria', 'NG', 9.082000, 8.675277, 200],
        ['South Africa', 'ZA', -30.559482, 22.937506, 300],

        // Europe
        ['United Kingdom', 'GB', 51.509865, -0.118092, 250],
        ['Germany', 'DE', 51.165691, 10.451526, 180],
        ['France', 'FR', 46.603354, 1.888334, 220],
        ['Spain', 'ES', 40.463667, -3.749220, 190],

        // North America
        ['United States', 'US', 37.090240, -95.712891, 500],
        ['Canada', 'CA', 56.130366, -106.346771, 300],
        ['Mexico', 'MX', 23.634501, -102.552784, 150],

        // South America
        ['Brazil', 'BR', -14.235004, -51.925280, 400],
        ['Argentina', 'AR', -38.416097, -63.616672, 120],
        ['Chile', 'CL', -35.675147, -71.542969, 80],

        // Asia
        ['China', 'CN', 35.861660, 104.195397, 600],
        ['India', 'IN', 20.593684, 78.962880, 550],
        ['Japan', 'JP', 36.204824, 138.252924, 320],
        ['South Korea', 'KR', 35.907757, 127.766922, 210],

        // Oceania
        ['Australia', 'AU', -25.274398, 133.775136, 180],
        ['New Zealand', 'NZ', -40.900557, 174.885971, 60],
    ];


    protected static string $view = 'filament.widgets.map-overview';
}
