<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-2xl font-semibold mb-4">Sessions by country</h2>
        <p class="text-gray-400 mb-8">View website visitors by hovering over the map</p>
        <div class="flex flex-col md:flex-row">
            <div class="w-full md:w-2/3 h-96 bg-gray-800 rounded-lg shadow-lg p-4">
                <div id="regions_div" class="w-full h-full"></div>
            </div>
            <div class="w-full md:w-1/3 mt-4 md:mt-0 md:ml-4">
                <ul>
                    @foreach($topCountries as $country)
                        <li class="flex items-center justify-between py-2">
                            <span class="flex items-center">
                                <img src="https://cdn.jsdelivr.net/npm/flag-icons@1.0.0/flags/4x3/{{ strtolower($country['code']) }}.svg" class="w-6 h-4 mr-2" alt="{{ $country['name'] }} Flag">
                                {{ $country['name'] }}
                            </span>
                            <span class="text-gray-400">{{ $country['percentage'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        @push('scripts')
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript">
                google.charts.load('current', {'packages':['geochart']});
                google.charts.setOnLoadCallback(drawRegionsMap);

                function drawRegionsMap() {
                    var data = google.visualization.arrayToDataTable(@json($countryData));
                    var options = {}; // Customize options if needed
                    var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
                    chart.draw(data, options);
                }
            </script>
        @endpush
    </x-filament::section>
</x-filament-widgets::widget>
