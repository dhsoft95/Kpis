<x-filament::widget>
    <x-filament::card>
        <div>
            <h3 class="text-lg font-medium">User Locations (Last 30 Days)</h3>
            <div class="mt-4">
                <div wire:ignore>
                    <div id="userLocationsChart" style="width: 100%; height:445px !important;"></div>
                </div>
            </div>
            @assets
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            @endassets
            @script
            <script type="text/javascript">
                google.charts.load('current', {
                    packages: ['geochart'],
                    mapsApiKey: 'YOUR_GOOGLE_MAPS_API_KEY'
                });
                google.charts.setOnLoadCallback(drawRegionsMap);

                function drawRegionsMap() {
                    const userLocations = @json($userLocations); // Pass your PHP data to JavaScript
                    const data = google.visualization.arrayToDataTable(userLocations);

                    const isDarkMode = document.querySelector('html').classList.contains('dark');

                    const options = {
                        colorAxis: { colors: ['#DCA915', '#584408'] },
                        backgroundColor: isDarkMode ? '#18181a' : '#FFFFFF',
                        datalessRegionColor: isDarkMode ? '#ffffff' : '#E5E7EB',
                        defaultColor: isDarkMode ? '#18181a' : '#F3F4F6',
                    };

                    const chart = new google.visualization.GeoChart(document.getElementById('userLocationsChart'));
                    chart.draw(data, options);
                }

                // Observe theme changes
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            drawRegionsMap();
                        }
                    });
                });

                observer.observe(document.querySelector('html'), {
                    attributes: true,
                    attributeFilter: ['class'],
                });
            </script>
            @endscript
        </div>
    </x-filament::card>
</x-filament::widget>
