
<x-filament::widget>
    <x-filament::card>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

        <div class="space-y-4 p-1" wire:poll.2s>
            <!-- Device Downloads Section -->
            <div class="">
                <h3 class="text-xs font-medium text-gray-900 dark:text-white mb-2"  style="color: #DCA915;">Device Downloads</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 dark:bg-gray-800 rounded-lg p-2">
                        <div class="flex items-center space-x-2">

                            <span class="material-symbols-outlined text-green-500 mr-2">Ios</span>
                            <div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($iosDownloads) }}</span>
                                <p class="text-xs text-blue-600 dark:text-blue-400">iOS</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 dark:bg-gray-800 rounded-lg p-3">
                        <div class="flex items-center space-x-2">
                            <span class="material-symbols-outlined text-green-500 mr-2">android</span>
                            <div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($androidDownloads) }}</span>
                                <p class="text-xs text-green-600 dark:text-green-400">Android</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Gender Stats Section -->
            <div>
                <h3 class="text-xs font-medium text-gray-900 dark:text-white mb-2" style="color: #DCA915;">User Gender Stats</h3>
                <div class="bg-purple-50 dark:bg-gray-800 rounded-lg p-2">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center space-x-2">
                            <div class="bg-blue-500 rounded-full p-1.5">
                                <x-heroicon-s-user class="w-4 h-4 text-white" />
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($maleUsers) }}</span>
                                <p class="text-xs text-blue-600 dark:text-blue-400">Male</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($femaleUsers) }}</span>
                                <p class="text-xs text-pink-600 dark:text-pink-400">Female</p>
                            </div>
                            <div class="bg-pink-500 rounded-full p-1.5">
                                <x-heroicon-s-user class="w-4 h-4 text-white" />
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                        @php
                            $total = $maleUsers + $femaleUsers;
                            $femalePercentage = $total > 0 ? ($femaleUsers / $total) * 100 : 0;
                        @endphp
                        <div class="bg-gradient-to-r from-blue-500 to-pink-500 h-1.5 rounded-full" style="width: {{ $femalePercentage }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1 text-xs text-gray-600 dark:text-gray-400">
                        <span>Male: {{ number_format($maleUsers) }}</span>
                        <span>Female: {{ number_format($femaleUsers) }}</span>
                    </div>
                </div>
            </div>

            <!-- Age Group Distribution Section -->
            <div>
                <h3 class="text-xs font-medium text-gray-900 dark:text-white mb-2" style="color: #DCA915;">Age Group Distribution</h3>
                <div class="grid grid-cols-4 gap-2">
                    @foreach(['18-24', '25-34', '35-44', '45+'] as $ageGroup)
                        <div class="bg-orange-50 dark:bg-gray-800 rounded-lg p-2">
                            <div class="flex items-center space-x-2">
                                <div class="bg-orange-500 rounded-full p-1.5">
                                    <x-heroicon-s-user-group class="w-4 h-4 text-white"/>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($ageGroupCounts[$ageGroup] ?? 0) }}</span>
                                    <p class="text-xs text-orange-600 " style="color: #DCA915;">{{ $ageGroup }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top User Locations Section -->
            <div>
                <h3 class="text-xs font-medium text-gray-900 dark:text-white mb-2" style="color: #DCA915;">Top User Locations</h3>
                <div class="bg-indigo-50 dark:bg-gray-800 rounded-lg p-2">
                    <ul class="space-y-2">
                        @foreach($topCountries as $country => $users)
                            <li class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-yellow-400 rounded-full p-1.5"  style="color: #DCA915 !important;">
                                        <x-heroicon-s-map-pin class="w-4 h-4" style="color: #27272a"/>
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $country }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($users) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>


{{--            <div wire:poll.300000ms="poll"></div>--}}
        </div>
    </x-filament::card>
</x-filament::widget>
