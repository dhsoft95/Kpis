<x-filament-widgets::widget>
    <x-filament::section>
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4">User Demographic Map</h1>

            <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Age Group -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="text-lg font-semibold mb-2">Age Distribution</h2>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>18-24</span>
                                <div class="w-2/3 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <span>25-34</span>
                                <div class="w-2/3 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 65%"></div>
                                </div>
                            </div>
                            <!-- Add more age groups as needed -->
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="text-lg font-semibold mb-2">Gender</h2>
                        <div class="flex justify-around">
                            <div class="text-center">
                                <div class="inline-block rounded-full bg-pink-500 w-20 h-20 flex items-center justify-center text-white font-bold text-xl">
                                    52%
                                </div>
                                <p class="mt-2">Female</p>
                            </div>
                            <div class="text-center">
                                <div class="inline-block rounded-full bg-blue-500 w-20 h-20 flex items-center justify-center text-white font-bold text-xl">
                                    48%
                                </div>
                                <p class="mt-2">Male</p>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="text-lg font-semibold mb-2">Top Locations</h2>
                        <ul class="space-y-2">
                            <li class="flex justify-between">
                                <span>New York</span>
                                <span class="font-semibold">25%</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Los Angeles</span>
                                <span class="font-semibold">18%</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Chicago</span>
                                <span class="font-semibold">12%</span>
                            </li>
                            <!-- Add more locations as needed -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
