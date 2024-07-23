<x-filament::widget>
    <x-filament::card>
        <div class="space-y-6">
            <!-- Device Downloads Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Device Downloads</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 dark:bg-gray-800 rounded-xl p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-500 rounded-full p-2">
                                <x-heroicon-s-device-phone-mobile class="w-6 h-6 text-white"/>
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($iosDownloads) }}</span>
                                <p class="text-sm text-blue-600 dark:text-blue-400">iOS</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 dark:bg-gray-800 rounded-xl p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-500 rounded-full p-2">
                                <x-heroicon-s-device-tablet class="w-6 h-6 text-white"/>
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($androidDownloads) }}</span>
                                <p class="text-sm text-green-600 dark:text-green-400">Android</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Gender Stats Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">User Gender Stats</h3>
                <div class="bg-purple-50 dark:bg-gray-800 rounded-xl p-4">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-500 rounded-full p-2">
                                <x-heroicon-s-user class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($maleUsers) }}</span>
                                <p class="text-sm text-blue-600 dark:text-blue-400">Male</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($femaleUsers) }}</span>
                                <p class="text-sm text-pink-600 dark:text-pink-400">Female</p>
                            </div>
                            <div class="bg-pink-500 rounded-full p-2">
                                <x-heroicon-s-user class="w-6 h-6 text-white" />
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                        @php
                            $total = $maleUsers + $femaleUsers;
                            $femalePercentage = $total > 0 ? ($femaleUsers / $total) * 100 : 0;
                        @endphp
                        <div class="bg-gradient-to-r from-blue-500 to-pink-500 h-2 rounded-full" style="width: {{ $femalePercentage }}%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-600 dark:text-gray-400">
                        <span>Male: {{ number_format($maleUsers) }}</span>
                        <span>Female: {{ number_format($femaleUsers) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
