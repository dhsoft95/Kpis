<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-medium">Downloads</h2>
        <div class="mt-4">
            <div class="mb-2">
                <span class="text-sm font-medium">iOS Downloads: {{ number_format($iosDownloads) }}</span>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($iosDownloads / ($iosDownloads + $androidDownloads)) * 100 }}%"></div>
                </div>
            </div>
            <div>
                <span class="text-sm font-medium">Android Downloads: {{ number_format($androidDownloads) }}</span>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ ($androidDownloads / ($iosDownloads + $androidDownloads)) * 100 }}%"></div>
                </div>
            </div>
        </div>
    </x-filament::card>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-medium">Gamer Gender Distribution</h2>
        <div class="mt-4">
            <canvas id="genderChart"></canvas>
        </div>
    </x-filament::card>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('genderChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Male', 'Female'],
                        datasets: [{
                            data: [{{ $maleGamersPercentage }}, {{ $femaleGamersPercentage }}],
                            backgroundColor: ['#3b82f6', '#ec4899'],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-filament::widget>
