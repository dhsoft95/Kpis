<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Geographic Distribution</h3>
            <div id="regions_div" class="w-full h-96 md:h-[500px]"></div>
        </div>

        @pushOnce('scripts')
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        @endPushOnce

        <script>
            document.addEventListener('livewire:load', function () {
                google.charts.load('current', {
                    'packages': ['geochart'],
                });

                google.charts.setOnLoadCallback(drawRegionsMap);

                function drawRegionsMap() {
                    var data = google.visualization.arrayToDataTable(@json($this->chartData));
                    var options = {
                        colorAxis: {colors: ['#e5f5e0', '#31a354']}, // Light green to dark green
                        backgroundColor: '#f8fafc', // Light gray background
                        datalessRegionColor: '#edf2f7', // Light blue-gray for regions without data
                        defaultColor: '#e2e8f0', // Default color for regions
                    };
                    var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
                    chart.draw(data, options);
                }

                window.addEventListener('resize', drawRegionsMap);
            });
        </script>
    </x-filament::section>
</x-filament-widgets::widget>
