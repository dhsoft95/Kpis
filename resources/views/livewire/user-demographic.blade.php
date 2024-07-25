<x-filament-widgets::widget>
    <x-filament::section>
        <div class="bg-gray-900 p-8 text-white font-sans">
            <h1 class="text-2xl font-bold mb-1">Sessions by region in Tanzania</h1>
            <p class="text-sm text-gray-400 mb-6">View website visitors by hovering over the map</p>

            <div class="mb-8 relative">
                <div class="bg-gray-800 h-[300px] w-full rounded-lg overflow-hidden">
                    <svg viewBox="0 0 500 500" class="w-full h-full">
                        <!-- Simplified Tanzania map -->
                        <path d="M100,100 L400,50 L450,200 L350,450 L100,400 Z" fill="#374151" stroke="#4b5563" stroke-width="2"/>
                        <!-- Regions (simplified) -->
                        <circle cx="250" cy="150" r="30" fill="#3b82f6" /> <!-- Dar es Salaam -->
                        <circle cx="150" cy="250" r="25" fill="#60a5fa" /> <!-- Mwanza -->
                        <circle cx="350" cy="200" r="20" fill="#93c5fd" /> <!-- Arusha -->
                        <circle cx="200" cy="350" r="15" fill="#bfdbfe" /> <!-- Mbeya -->
                        <circle cx="300" cy="300" r="10" fill="#dbeafe" /> <!-- Dodoma -->
                    </svg>
                </div>
                <div class="absolute bottom-2 left-2 flex space-x-2">
                    <button class="bg-gray-700 text-white w-8 h-8 flex items-center justify-center rounded-md">+</button>
                    <button class="bg-gray-700 text-white w-8 h-8 flex items-center justify-center rounded-md">-</button>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-600"></span>
                    <span class="w-28 text-sm">Dar es Salaam</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[35%]"></div>
                    </div>
                    <span class="ml-3 text-sm">35%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-400"></span>
                    <span class="w-28 text-sm">Mwanza</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[26%]"></div>
                    </div>
                    <span class="ml-3 text-sm">26%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-300"></span>
                    <span class="w-28 text-sm">Arusha</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[18%]"></div>
                    </div>
                    <span class="ml-3 text-sm">18%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-200"></span>
                    <span class="w-28 text-sm">Mbeya</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[14%]"></div>
                    </div>
                    <span class="ml-3 text-sm">14%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-100"></span>
                    <span class="w-28 text-sm">Dodoma</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[7%]"></div>
                    </div>
                    <span class="ml-3 text-sm">7%</span>
                </div>
            </div>
        </div>
    </x-filament::section>

</x-filament-widgets::widget>

