<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include CDNs -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <div class="flex justify-between">
            <!-- Left side: Gender distribution using ApexCharts -->
            <div class="w-1/2 pr-4">
                <h2 class="text-lg font-semibold mb-4">Registered Users by Gender</h2>
                <div x-data="{
                    chart: null,
                    init() {
                        this.chart = new ApexCharts($refs.chart, {
                            chart: {
                                type: 'pie',
                                height: 200
                            },
                            series: [60, 40],
                            labels: ['Male', 'Female'],
                            colors: ['#36A2EB', '#FF6384'],
                            legend: {
                                position: 'bottom'
                            }
                        });
                        this.chart.render();
                    }
                }" x-init="init()">
                    <div x-ref="chart"></div>
                </div>
            </div>

            <!-- Right side: Device downloads -->
            <div class="w-1/2 pl-4">
                <h2 class="text-lg font-semibold mb-4">Downloads by Platform</h2>
                <div class="relative w-48 h-48 mx-auto">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-full h-full bg-primary-500 rounded-full"></div>
                        <div class="absolute top-0 left-0 w-28 h-48 bg-gray-500 rounded-l-full"></div>
                    </div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-bold">55% iOS</span>
                        <span class="font-bold">45% Android</span>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
