<x-filament-widgets::widget>
    <x-filament::section>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>User and Download Statistics</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </head>
        <body class="bg-gray-900 p-4">
        <div class="flex justify-between p-6 bg-gray-800 rounded-lg shadow-xl">
            <!-- Left side: Gender distribution using Chart.js -->
            <div class="w-1/2 pr-4">
                <h2 class="text-lg font-semibold mb-4 text-white">Registered Users by Gender</h2>
                <canvas id="genderChart" width="200" height="200"></canvas>
            </div>

            <!-- Right side: Device downloads -->
            <div class="w-1/2 pl-4">
                <h2 class="text-lg font-semibold mb-4 text-white">Downloads by Platform</h2>
                <div class="relative w-48 h-48 mx-auto">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-full h-full bg-green-500 rounded-full"></div>
                        <div class="absolute top-0 left-0 w-28 h-48 bg-gray-600 rounded-l-full"></div>
                    </div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-white font-bold">55% iOS</span>
                        <span class="text-white font-bold">45% Android</span>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Chart.js code for the gender distribution chart
            const ctx = document.getElementById('genderChart').getContext('2d');
            new Chart(ctx, {
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
                            labels: {
                                color: 'white'
                            }
                        },
                        title: {
                            display: false,
                        }
                    }
                }
            });
        </script>
        </body>
        </html>
    </x-filament::section>
</x-filament-widgets::widget>
