<x-filament-widgets::widget>
    <x-filament::section>
        <div class="bg-gray-900 p-8 text-white font-sans">
            <h1 class="text-2xl font-bold mb-1">Sessions by country</h1>
            <p class="text-sm text-gray-400 mb-6">View website visitors by hovering over the map</p>

            <div class="mb-8 relative">
                <div class="bg-gray-800 h-[300px] w-full rounded-lg overflow-hidden">
                    <svg viewBox="0 0 1000 500" class="w-full h-full">
                        <!-- Simplified world map background -->
                        <path d="M0,0 v500 h1000 v-500 z" fill="#374151"/>

                        <!-- Highlighted countries -->
                        <!-- United States -->
                        <path d="M200,180 q30,-30 60,0 t60,0 t60,0" fill="#3b82f6"/>
                        <!-- Canada -->
                        <path d="M200,120 q30,-30 60,0 t60,0" fill="#60a5fa"/>
                        <!-- France -->
                        <path d="M470,170 q5,-5 10,0 t10,0" fill="#93c5fd"/>
                        <!-- Italy -->
                        <path d="M490,180 q5,-5 10,0 t10,0" fill="#bfdbfe"/>
                        <!-- Australia -->
                        <path d="M800,350 q20,-20 40,0" fill="#dbeafe"/>
                        <!-- India -->
                        <path d="M700,230 q20,-20 40,0" fill="#eff6ff"/>

                        <!-- Other major landmasses (simplified) -->
                        <path d="M300,200 q50,-50 100,0 t100,0 t100,0" fill="#4b5563" />
                        <path d="M150,300 q100,-100 200,0 t200,0" fill="#4b5563" />
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
                    <span class="w-6 h-4 mr-3 bg-blue-300"></span>
                    <span class="w-28 text-sm">France</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[18%]"></div>
                    </div>
                    <span class="ml-3 text-sm">18%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-200"></span>
                    <span class="w-28 text-sm">Italy</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[14%]"></div>
                    </div>
                    <span class="ml-3 text-sm">14%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-100"></span>
                    <span class="w-28 text-sm">Australia</span>
                    <div class="flex-grow bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2 w-[10%]"></div>
                    </div>
                    <span class="ml-3 text-sm">10%</span>
                </div>

                <div class="flex items-center">
                    <span class="w-6 h-4 mr-3 bg-blue-50"></span>
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

