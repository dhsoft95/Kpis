<x-filament::widget>
    <x-filament::card>
        <div id="geo-chart" style="width: 100%; height: 500px;"></div>
    </x-filament::card>

    @pushOnce('scripts')
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    @endPushOnce

    <script>
        document.addEventListener('livewire:load', function () {
            google.charts.load('current', {
                'packages': ['geochart']
            });
            google.charts.setOnLoadCallback(drawRegionsMap);

            function drawRegionsMap() {
                var data = google.visualization.arrayToDataTable(@json($this->chartData));
                var options = @json($this->chartOptions);
                var chart = new google.visualization.GeoChart(document.getElementById('geo-chart'));
                chart.draw(data, options);
            }

            window.addEventListener('resize', drawRegionsMap);
        });
    </script>
</x-filament::widget>
