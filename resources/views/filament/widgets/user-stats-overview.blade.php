<x-filament-widgets::widget>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <x-filament::section>
        <div class="flex space-x-4">
            <!-- User Actives Card -->
            <div class="bg-white rounded-lg shadow-md p-4 w-56">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">USER ACTIVES</h2>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                </div>
                <div class="text-3xl font-bold text-gray-800 mb-2">310</div>
                <div class="flex items-center text-sm">
                    <span class="text-green-500 mr-1">▲1.16%</span>
                    <span class="text-gray-500">Average Users 525</span>
                </div>
            </div>

            <!-- New Users Card -->
            <div class="bg-white rounded-lg shadow-md p-4 w-56">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">NEW USERS</h2>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                </div>
                <div class="text-3xl font-bold text-gray-800 mb-2">162</div>
                <div class="flex items-center text-sm">
                    <span class="text-red-500 mr-1">▼6.07%</span>
                    <span class="text-gray-500">Average New Users 199</span>
                </div>
            </div>

            <!-- AVG. TIME ON PAGE Card -->
            <div class="bg-white rounded-lg shadow-md p-4 w-56">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">AVG. TIME ON PAGE</h2>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                </div>
                <div class="text-3xl font-bold text-gray-800 mb-2">00:03:12</div>
                <div class="flex items-center text-sm">
                    <span class="text-green-500 mr-1">▲8.32%</span>
                    <span class="text-gray-500">Average time 00:02:43</span>
                </div>
            </div>

            <!-- BOUNCE RATE Card -->
            <div class="bg-white rounded-lg shadow-md p-4 w-56">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">BOUNCE RATE</h2>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                </div>
                <div class="text-3xl font-bold text-gray-800 mb-2">86.18%</div>
                <div class="flex items-center text-sm">
                    <span class="text-red-500 mr-1">▼0.81%</span>
                    <span class="text-gray-500">Average rate 87.54%</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
