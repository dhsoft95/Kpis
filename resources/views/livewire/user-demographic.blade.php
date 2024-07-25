<x-filament-widgets::widget>
    <x-filament::section>
        <div class="bg-gray-900 p-8 text-white font-sans">
            <h1 class="text-2xl font-bold mb-1">Sessions by country</h1>
            <p class="text-sm text-gray-400 mb-6">View website visitors by hovering over the map</p>

            <div class="mb-8 relative">
                <!-- Placeholder for the map -->
                <div class="bg-gray-800 h-[300px] w-full rounded-lg overflow-hidden">
                    <!-- You'd need to implement the actual map here -->
                </div>
                <div class="absolute bottom-2 left-2 flex space-x-2">
                    <button class="bg-gray-700 text-white w-8 h-8 flex items-center justify-center rounded-md">+</button>
                    <button class="bg-gray-700 text-white w-8 h-8 flex items-center justify-center rounded-md">-</button>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center">
                    <img src="path_to_us_flag.png" alt="US Flag" class="w-6 h-4 mr-3">
                    <span class="w-28 text-sm">United States</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[35%]"></div>
                    </div>
                    <span class="ml-3 text-sm">35%</span>
                </div>

                <div class="flex items-center">
                    <img src="path_to_canada_flag.png" alt="Canada Flag" class="w-6 h-4 mr-3">
                    <span class="w-28 text-sm">Canada</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[26%]"></div>
                    </div>
                    <span class="ml-3 text-sm">26%</span>
                </div>

                <div class="flex items-center">
                    <img src="path_to_france_flag.png" alt="France Flag" class="w-6 h-4 mr-3">
                    <span class="w-28 text-sm">France</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[18%]"></div>
                    </div>
                    <span class="ml-3 text-sm">18%</span>
                </div>

                <div class="flex items-center">
                    <img src="path_to_italy_flag.png" alt="Italy Flag" class="w-6 h-4 mr-3">
                    <span class="w-28 text-sm">Italy</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[14%]"></div>
                    </div>
                    <span class="ml-3 text-sm">14%</span>
                </div>

                <div class="flex items-centser">
                    <img src="path_to_australia_flag.png" alt="Australia Flag" class="w-6 h-4 mr-3">
                    <span class="w-28 text-sm">Australia</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[10%]"></div>
                    </div>
                    <span class="ml-3 text-sm">10%</span>
                </div>

                <div class="flex items-center">
                    <img src="path_to_india_flag.png" alt="India Flag" class="w-6 h-4 mr-3">
                    <span class="w-28 text-sm">India</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[7%]"></div>
                    </div>
                    <span class="ml-3 text-sm">7%</span>
                </div>
            </div>
        </div>
    </x-filament::section>

</x-filament-widgets::widget>

