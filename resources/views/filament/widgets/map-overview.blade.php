<x-filament-widgets::widget class="bg-transparent p-4">
    <x-filament::section>
        <div class="card custom-card">
            <div class="card-header custom-card-header">
                <h4>User Statistics</h4>
            </div>
            <div class="card-body custom-card-body">
                <div class="row custom-row">
                    <!-- Gender Distribution Chart -->
                    <div class="col-md-6 custom-col">
                        <div x-data="{
                            chart: null,
                            init() {
                                this.chart = new ApexCharts($refs.genderChart, {
                                    chart: {
                                        type: 'donut',
                                        height: 250,
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
                            <div x-ref="genderChart" class="custom-chart"></div>
                        </div>
                    </div>

                    <!-- Device Downloads -->
                    <div class="col-md-6 custom-col">
                        <div class="relative w-full h-64 mx-auto">
                            <div x-data="{
                                chart: null,
                                init() {
                                    this.chart = new ApexCharts($refs.downloadsChart, {
                                        chart: {
                                            type: 'donut',
                                            height: 250,
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
                                        series: [55, 45], // Update these values with actual data
                                        labels: ['iOS', 'Android'],
                                        colors: ['#4ade80', '#3b82f6'],
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
                                <div x-ref="downloadsChart" class="custom-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        background-color: transparent; /* Transparent background */
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

    .custom-chart {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
    }
</style>
