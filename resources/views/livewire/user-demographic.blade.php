<x-filament-widgets::widget>
    <x-filament::section>
        <div class="bg-gray-900 p-8 text-white font-sans">
            <h1 class="text-2xl font-bold mb-1">Sessions by region in Tanzania</h1>
            <p class="text-sm text-gray-400 mb-6">View website visitors on the map</p>

            <div id="regions_div" style="width: 100%; height: 500px;"></div>

            <div class="space-y-3 mt-6">
                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-600"></span>
                    <span class="w-28 text-sm">Dar es Salaam</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[35%]"></div>
                    </div>
                    <span class="ml-3 text-sm">35%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-400"></span>
                    <span class="w-28 text-sm">Mwanza</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[26%]"></div>
                    </div>
                    <span class="ml-3 text-sm">26%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-300"></span>
                    <span class="w-28 text-sm">Arusha</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[18%]"></div>
                    </div>
                    <span class="ml-3 text-sm">18%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-200"></span>
                    <span class="w-28 text-sm">Mbeya</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[14%]"></div>
                    </div>
                    <span class="ml-3 text-sm">14%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-100"></span>
                    <span class="w-28 text-sm">Dodoma</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[7%]"></div>
                    </div>
                    <span class="ml-3 text-sm">7%</span>
                </div>
            </div>
        </div>
    </x-filament::section>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['geochart'],
            'mapsApiKey': 'AIzaSyA7AzYDQTKAMaSjyKUxoyJevka5f1QyBNQ'
        });
        google.charts.setOnLoadCallback(drawRegionsMap);

        function drawRegionsMap() {
            var data = google.visualization.arrayToDataTable([
                ['Region', 'Sessions'],
                ['TZ-02', 35], // Dar es Salaam
                ['TZ-13', 26], // Mwanza
                ['TZ-01', 18], // Arusha
                ['TZ-14', 14], // Mbeya
                ['TZ-03', 7]   // Dodoma
            ]);

            var options = {
                region: 'TZ',
                resolution: 'provinces',
                colorAxis: {colors: ['#dbeafe', '#3b82f6']},
                backgroundColor: '#1f2937',
                datalessRegionColor: '#4b5563',
                defaultColor: '#4b5563',
                legend: 'none',
            };
            var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
            chart.draw(data, options);
        }
    </script>
</x-filament-widgets::widget>
