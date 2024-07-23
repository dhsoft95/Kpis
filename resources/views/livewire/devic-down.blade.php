<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            <!-- Device Downloads Section -->
            <div>
                <h3 class="text-sm font-medium">Device Downloads</h3>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <x-filament::stats.card
                        :label="__('iOS')"
                        :value="number_format($iosDownloads)"
                        icon="heroicon-o-device-phone-mobile"
                        color="primary"
                    />
                    <x-filament::stats.card
                        :label="__('Android')"
                        :value="number_format($androidDownloads)"
                        icon="heroicon-o-device-tablet"
                        color="success"
                    />
                </div>
            </div>

            <!-- User Gender Stats Section -->
            <div>
                <h3 class="text-sm font-medium">User Gender Stats</h3>
                <div class="mt-2">
                    <x-filament::card>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <x-heroicon-o-user class="w-5 h-5 text-primary-500" />
                                <div>
                                    <span class="text-xl font-bold">{{ number_format($maleUsers) }}</span>
                                    <p class="text-xs text-gray-600">Male</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div>
                                    <span class="text-xl font-bold">{{ number_format($femaleUsers) }}</span>
                                    <p class="text-xs text-gray-600">Female</p>
                                </div>
                                <x-heroicon-o-user class="w-5 h-5 text-danger-500" />
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-200 rounded-full h-1">
                            @php
                                $total = $maleUsers + $femaleUsers;
                                $femalePercentage = $total > 0 ? ($femaleUsers / $total) * 100 : 0;
                            @endphp
                            <div class="bg-primary-500 h-1 rounded-full" style="width: {{ $femalePercentage }}%"></div>
                        </div>
                    </x-filament::card>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
