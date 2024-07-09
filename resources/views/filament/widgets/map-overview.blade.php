<x-filament-widgets::widget>
    <x-filament::section>
        <div class="card custom-card">
            <div class="card-header custom-card-header">
                <h4>User Locations</h4>
            </div>
            <div class="card-body custom-card-body">
                <div class="row custom-row">
                    <div class="col-md-4 custom-col">
                        <ul class="list-group custom-list-group">
{{--                            @foreach($userLocations as $location)--}}
{{--                                @if ($loop->first)--}}
{{--                                    @continue--}}
{{--                                @endif--}}
{{--                                <li class="list-group-item custom-list-group-item">--}}
{{--                                    <strong>{{ $location[0] }}</strong>--}}
{{--                                    <p>Users: {{ $location[4] }}</p>--}}
{{--                                </li>--}}
{{--                            @endforeach--}}
                        </ul>
                    </div>
                    <div class="col-md-8 custom-col">
                        <div wire:ignore>
                            <div id="userLocationsChart" class="custom-chart" style="width: 100%; height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            @assets
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            @endassets

            @script
            <script type="text/javascript">
                google.charts.load('current', {
                    packages: ['geochart'],
                    mapsApiKey: 'YOUR_GOOGLE_MAPS_API_KEY' // Replace with your actual API key
                });

                google.charts.setOnLoadCallback(drawRegionsMap);

                function drawRegionsMap() {
                    const userLocations = @json($userLocations); // Pass PHP data to JavaScript

                    const data = google.visualization.arrayToDataTable([
                        ['Country', 'Users'], // Header row
                            @foreach($userLocations as $location)
                            @if ($loop->first)
                            @continue
                            @endif
                        ['{{ $location[0] }}', {{ $location[4] }}], // Country name, user count
                        @endforeach
                    ]);
                    const options = {
                        colorAxis: { colors: ['#e7711c', '#4374e0'] }, // Gradient color (optional for markers)
                        backgroundColor: '#030712',
                        datalessRegionColor: '#f8bbd0',
                        defaultColor: '#f5f5f5',
                    };

                    const chart = new google.visualization.GeoChart(document.getElementById('userLocationsChart'));
                    chart.draw(data, options);
                }

                // Listen for 'userLocationsUpdated' event and update chart
                window.addEventListener('userLocationsUpdated', (event) => {
                    drawRegionsMap(); // Redraw the chart with updated data
                });
            </script>
            @endscript
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<!-- Add custom styles in your CSS file or within a <style> tag -->
<style>
    .custom-card {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }

    .custom-card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 0.75rem 1.25rem;
        font-size: 1.25rem;
        color: #495057;
    }

    .custom-card-body {
        padding: 1.25rem;
    }

    .custom-row {
        margin: 0 -0.75rem;
    }

    .custom-col {
        padding: 0 0.75rem;
    }

    .custom-list-group {
        margin-bottom: 0;
    }

    .custom-list-group-item {
        border: 1px solid #dee2e6;
        margin-bottom: 0.5rem;
        padding: 0.75rem 1.25rem;
    }

    .custom-chart {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
    }
</style>
