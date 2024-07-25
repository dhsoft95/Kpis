<x-filament::widget>
    <x-filament::card>
        <div id="geo-chart" style="width: 100%; height: 400px;"></div>
    </x-filament::card>

    @pushOnce('scripts')
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    @endPushOnce

    <script>
        console.log('Widget script started');
        document.addEventListener('livewire:load', function () {
            console.log('Livewire loaded');
            google.charts.load('current', {
                'packages': ['geochart']
            });
            google.charts.setOnLoadCallback(drawRegionsMap);

            function drawRegionsMap() {
                console.log('Drawing map');
                var data = google.visualization.arrayToDataTable(@json($chartData));
                var options = @json($chartOptions);
                console.log('Chart data:', data);
                console.log('Chart options:', options);
                var chart = new google.visualization.GeoChart(document.getElementById('geo-chart'));
                chart.draw(data, options);
                console.log('Map drawn');
            }

            window.addEventListener('resize', drawRegionsMap);
        });
    </script>
</x-filament::widget>
