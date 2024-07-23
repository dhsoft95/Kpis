<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-between">
            <!-- Left side: Gender distribution using Chart.js -->
            <div class="w-1/2 pr-4">
                <h2 class="text-lg font-semibold mb-4">Registered Users by Gender</h2>
                <div x-data="{}" x-init="
                    new Chart($refs.genderChart, {
                        type: 'pie',
                        data: {
                            labels: ['Male', 'Female'],
                            datasets: [{
                                data: [60, 40],
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.8)',
                                    'rgba(255, 99, 132, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 99, 132, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                title: {
                                    display: false,
                                }
                            }
                        }
                    })
                ">
                    <canvas x-ref="genderChart" width="200" height="200"></canvas>
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
