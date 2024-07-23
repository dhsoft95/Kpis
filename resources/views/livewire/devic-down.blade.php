<x-filament::widget class="filament-widget-downloads-and-gender">
    <x-filament::card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-base font-semibold mb-4 flex items-center">
                    <x-heroicon-s-arrow-down-circle class="w-5 h-5 mr-2 text-primary-500"/>
                    Downloads
                </h2>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="flex items-center">
                                <x-heroicon-s-device-phone-mobile class="w-4 h-4 mr-1 text-blue-500"/>
                                iOS
                            </span>
                            <span class="font-medium">{{ number_format($iosDownloads) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ ($iosDownloads / ($iosDownloads + $androidDownloads)) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="flex items-center">
                                <x-heroicon-s-device-tablet class="w-4 h-4 mr-1 text-green-500"/>
                                Android
                            </span>
                            <span class="font-medium">{{ number_format($androidDownloads) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ ($androidDownloads / ($iosDownloads + $androidDownloads)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-base font-semibold mb-4 flex items-center">
                    <x-heroicon-s-users class="w-5 h-5 mr-2 text-primary-500"/>
                    Gamer Gender Distribution
                </h2>
                <div class="relative" style="height: 200px;">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
    </x-filament::card>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('genderChart');
                if (ctx) {
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
                            maintainAspectRatio: false,
                        }
                    });
                } else {
                    console.error('Cannot find canvas element with id "genderChart"');
                    console.log('All canvas elements:', document.getElementsByTagName('canvas'));
                }
            });
        </script>
    @endpush
</x-filament::widget>
