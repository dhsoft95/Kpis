<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-between p-4 bg-gray-100 rounded-lg shadow-md">
            <!-- Left side: Gender distribution -->
            <div class="w-1/2 pr-2">
                <h2 class="text-lg font-semibold mb-2">Registered Users by Gender</h2>
                <div class="relative w-48 h-48">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-full h-full bg-blue-500 rounded-full"></div>
                        <div class="absolute top-0 right-0 w-24 h-48 bg-pink-500 rounded-r-full"></div>
                    </div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-white font-bold">60% Male</span>
                        <span class="text-white font-bold">40% Female</span>
                    </div>
                </div>
            </div>

            <!-- Right side: Device downloads -->
            <div class="w-1/2 pl-2">
                <h2 class="text-lg font-semibold mb-2">Downloads by Platform</h2>
                <div class="relative w-48 h-48">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-full h-full bg-green-500 rounded-full"></div>
                        <div class="absolute top-0 left-0 w-28 h-48 bg-gray-700 rounded-l-full"></div>
                    </div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-white font-bold">55% iOS</span>
                        <span class="text-white font-bold">45% Android</span>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
