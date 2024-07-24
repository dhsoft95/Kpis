<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament-widgets::widget>
            <x-filament::section>
                <div class="space-y-6">
                    <div class="p-4 bg-white rounded-xl shadow">
                        <h2 class="text-lg font-medium mb-4">Sessions by Country</h2>
                        <div class="space-y-2">
                            @foreach($this->getCountryData() as $country)
                                <div class="flex items-center">
                                    <span class="w-6 h-4 bg-{{ $country['color'] }}-500 mr-2"></span>
                                    <span class="w-24">{{ $country['name'] }}</span>
                                    <div class="flex-grow bg-gray-200 rounded-full h-4">
                                        <div class="bg-{{ $country['color'] }}-600 h-4 rounded-full" style="width: {{ $country['percentage'] }}%"></div>
                                    </div>
                                    <span class="ml-2">{{ $country['percentage'] }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-4 bg-white rounded-xl shadow">
                            <h2 class="text-lg font-medium mb-4">Age Distribution</h2>
                            <div class="space-y-2">
                                @foreach($this->getAgeDistribution() as $age)
                                    <div class="flex justify-between">
                                        <span>{{ $age['range'] }}</span>
                                        <div class="w-2/3 bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $age['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 bg-white rounded-xl shadow">
                            <h2 class="text-lg font-medium mb-4">Gender</h2>
                            <div class="flex justify-around">
                                @foreach($this->getGenderDistribution() as $gender)
                                    <div class="text-center">
                                        <div class="inline-block rounded-full bg-{{ $gender['color'] }}-500 w-20 h-20 flex items-center justify-center text-white font-bold text-xl">
                                            {{ $gender['percentage'] }}%
                                        </div>
                                        <p class="mt-2">{{ $gender['gender'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 bg-white rounded-xl shadow">
                            <h2 class="text-lg font-medium mb-4">Top Locations</h2>
                            <ul class="space-y-2">
                                @foreach($this->getTopLocations() as $location)
                                    <li class="flex justify-between">
                                        <span>{{ $location['name'] }}</span>
                                        <span class="font-semibold">{{ $location['percentage'] }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </x-filament-widgets::widget>
    </x-filament::section>
</x-filament-widgets::widget>
