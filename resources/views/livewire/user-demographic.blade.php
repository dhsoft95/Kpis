<x-filament-widgets::widget>
    <x-filament::section>
        <div class="bg-gray-900 p-8 text-white font-sans">
            <h1 class="text-2xl font-bold mb-1">Sessions by country</h1>
            <p class="text-sm text-gray-400 mb-6">View website visitors by hovering over the map</p>

            <div class="mb-8 relative">
                <div class="bg-gray-800 h-[300px] w-full rounded-lg overflow-hidden">
                    <svg viewBox="0 0 1000 500" class="w-full h-full">
                        <!-- Simplified world map paths -->
                        <path d="M150,100 Q200,50 250,100 T350,100 T450,100 T550,100 T650,100 T750,100 Q800,50 850,100" fill="none" stroke="#4a5568" stroke-width="30"/>
                        <path d="M100,200 Q150,150 200,200 T300,200 T400,200 T500,200 T600,200 T700,200 Q750,150 800,200" fill="none" stroke="#4a5568" stroke-width="30"/>
                        <path d="M200,300 Q250,250 300,300 T400,300 T500,300 T600,300" fill="none" stroke="#4a5568" stroke-width="30"/>
                        <!-- United States -->
                        <path d="M200,150 Q220,130 240,150 T280,150" fill="#3b82f6"/>
                        <!-- Canada -->
                        <path d="M200,100 Q220,80 240,100 T280,100" fill="#60a5fa"/>
                        <!-- France -->
                        <path d="M460,160 Q470,150 480,160" fill="#60a5fa"/>
                        <!-- Italy -->
                        <path d="M490,170 Q500,160 510,170" fill="#60a5fa"/>
                        <!-- India -->
                        <path d="M650,220 Q670,200 690,220" fill="#60a5fa"/>
                        <!-- Australia -->
                        <path d="M750,350 Q770,330 790,350" fill="#60a5fa"/>
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
                    <span class="w-28 text-sm">United States</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[35%]"></div>
                    </div>
                    <span class="ml-3 text-sm">35%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-400"></span>
                    <span class="w-28 text-sm">Canada</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[26%]"></div>
                    </div>
                    <span class="ml-3 text-sm">26%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-400"></span>
                    <span class="w-28 text-sm">France</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[18%]"></div>
                    </div>
                    <span class="ml-3 text-sm">18%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-400"></span>
                    <span class="w-28 text-sm">Italy</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[14%]"></div>
                    </div>
                    <span class="ml-3 text-sm">14%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-400"></span>
                    <span class="w-28 text-sm">Australia</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[10%]"></div>
                    </div>
                    <span class="ml-3 text-sm">10%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-400"></span>
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

