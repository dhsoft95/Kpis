<x-filament::widget class="filament-widget-downloads-and-gender">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-filament::card class="h-full">
            <h2 class="text-base font-semibold mb-4">Downloads</h2>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>iOS</span>
                        <span class="font-medium">{{ number_format($iosDownloads) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ ($iosDownloads / ($iosDownloads + $androidDownloads)) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Android</span>
                        <span class="font-medium">{{ number_format($androidDownloads) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ ($androidDownloads / ($iosDownloads + $androidDownloads)) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card class="h-full">
            <h2 class="text-base font-semibold mb-4">Gamer Gender Distribution</h2>
            <div class="relative" style="height: 150px;">
                <canvas id="genderChart"></canvas>
            </div>
        </x-filament::card>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('genderChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Male', 'Female'],
                        datasets: [{
                            data: [{{ $maleGamersPercentage }}, {{ $femaleGamersPercentage }}],
                            backgroundColor: ['#3b82f6', '#ec4899'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15
                                }
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
