<x-filament-widgets::widget>
    <x-filament::section>
        <div class="bg-gray-900 p-8 text-white font-sans">
            <h1 class="text-2xl font-bold mb-1">Sessions by region in Tanzania</h1>
            <p class="text-sm text-gray-400 mb-6">View website visitors by hovering over the map</p>

            <div class="mb-8 relative">
                <div class="bg-gray-800 h-[500px] w-full rounded-lg overflow-hidden">
                    <svg viewBox="0 0 800 900" class="w-full h-full">
                        <!-- Tanzania outline -->
                        <path d="M395,42 L428,42 L475,76 L544,80 L592,115 L626,115 L626,165 L592,204 L592,238 L619,238 L633,272 L633,306 L660,336 L660,370 L633,394 L633,423 L660,453 L660,506 L633,535 L633,564 L606,593 L579,617 L579,646 L552,675 L552,704 L525,724 L502,749 L475,768 L428,783 L395,783 L362,764 L335,739 L308,714 L281,685 L254,656 L227,627 L214,593 L187,564 L187,535 L160,506 L160,472 L133,443 L133,409 L160,379 L187,350 L187,306 L214,282 L241,257 L268,233 L295,208 L322,184 L349,159 L376,135 L395,110 L395,42Z"
                              fill="#374151" stroke="#4b5563" stroke-width="2"/>
                        <!-- Add circles for major cities/regions -->
                        <circle cx="450" cy="750" r="20" fill="#3b82f6" /> <!-- Dar es Salaam -->
                        <circle cx="250" cy="200" r="15" fill="#60a5fa" /> <!-- Mwanza -->
                        <circle cx="400" cy="150" r="15" fill="#93c5fd" /> <!-- Arusha -->
                        <circle cx="300" cy="650" r="12" fill="#bfdbfe" /> <!-- Mbeya -->
                        <circle cx="400" cy="450" r="10" fill="#dbeafe" /> <!-- Dodoma -->
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

