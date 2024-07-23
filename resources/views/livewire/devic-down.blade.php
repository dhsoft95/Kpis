<x-filament-widgets::widget class="bg-transparent p-4">
    <x-filament::section>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Gender distribution chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h2 class="text-base font-semibold mb-2 text-gray-900 dark:text-white">Registered Users by Gender</h2>
                <div x-data="{
                    chart: null,
                    init() {
                        this.chart = new ApexCharts($refs.chart, {
                            chart: {
                                type: 'donut',
                                height: 200,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800,
                                    animateGradually: {
                                        enabled: true,
                                        delay: 150
                                    },
                                    dynamicAnimation: {
                                        enabled: true,
                                        speed: 350
                                    }
                                }
                            },
                            series: [60, 40], // Update these values with actual data
                            labels: ['Male', 'Female'],
                            colors: ['#3b82f6', '#ec4899'],
                            legend: {
                                position: 'bottom'
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '70%'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function (val) {
                                    return val.toFixed(1) + '%'
                                }
                            },
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 150
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }]
                        });
                        this.chart.render();
                    }
                }" x-init="init()" class="mt-4">
                    <div x-ref="chart"></div>
                </div>
            </div>

            <!-- Device downloads -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h2 class="text-base font-semibold mb-2 text-gray-900 dark:text-white">Downloads by Platform</h2>
                <div class="relative w-48 h-48 mx-auto">
                    <svg class="w-full h-full" viewBox="0 0 36 36">
                        <path d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                              fill="none"
                              stroke="#4ade80"
                              stroke-width="2"
                              stroke-dasharray="55, 100" <!-- Update this value -->
                        />
                        <path d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                              fill="none"
                              stroke="#3b82f6"
                              stroke-width="2"
                              stroke-dasharray="45, 100" <!-- Update this value -->
                        />
                        <text x="18" y="20.35" class="text-4xl font-bold text-gray-900 dark:text-white" text-anchor="middle">55%</text> <!-- Update this percentage -->
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center mt-8">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">iOS</span>
                        <span class="text-xl font-bold text-green-500">70%</span> <!-- Update this percentage -->
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-2">Android</span>
                        <span class="text-xl font-bold text-blue-500">45%</span> <!-- Update this percentage -->
                    </div>
                </div>
            </div>
        </div>

        @assets
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        @endassets
    </x-filament::section>
</x-filament-widgets::widget>
