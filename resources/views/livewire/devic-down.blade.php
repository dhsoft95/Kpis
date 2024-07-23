<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            <!-- Device Downloads Section -->
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Device Downloads</h3>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-device-phone-mobile class="w-6 h-6 text-primary-500"/>
                            <div>
                                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($iosDownloads) }}</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400">iOS</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-device-tablet class="w-6 h-6 text-success-500"/>
                            <div>
                                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($androidDownloads) }}</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Android</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Gender Stats Section -->
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">User Gender Stats</h3>
                <div class="mt-2 bg-white dark:bg-gray-800 rounded-lg p-3 shadow">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-user class="w-6 h-6 text-primary-500" />
                            <div>
                                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($maleUsers) }}</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Male</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div>
                                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($femaleUsers) }}</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Female</p>
                            </div>
                            <x-heroicon-o-user class="w-6 h-6 text-danger-500" />
                        </div>
                    </div>
                    <div class="mt-3 bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                        @php
                            $total = $maleUsers + $femaleUsers;
                            $femalePercentage = $total > 0 ? ($femaleUsers / $total) * 100 : 0;
                        @endphp
                        <div class="bg-primary-500 h-1 rounded-full" style="width: {{ $femalePercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
