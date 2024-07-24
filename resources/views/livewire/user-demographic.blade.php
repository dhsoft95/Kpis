<x-filament-widgets::widget>
    <x-filament::section>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sessions by Country</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                .country { fill: #374151; }
                .country:hover { fill: #60A5FA; }
            </style>
        </head>
        <body class="bg-gray-900 text-white p-8">
        <h1 class="text-2xl font-bold mb-2">Sessions by country</h1>
        <p class="text-gray-400 mb-4">View website visitors by hovering over the map</p>

        <div class="w-full h-96 bg-gray-800 rounded-lg mb-4 overflow-hidden">
            <!-- Simplified world map SVG -->
            <svg viewBox="0 0 1000 500" class="w-full h-full">
                <!-- You would need to add path elements for each country here -->
                <path class="country" d="M200,100 L220,100 L220,110 L200,110 Z" />
                <!-- More paths... -->
            </svg>
        </div>

        <div class="flex space-x-2 mb-4">
            <button class="bg-gray-700 text-white p-2 rounded">+</button>
            <button class="bg-gray-700 text-white p-2 rounded">-</button>
        </div>

        <div class="space-y-2">
            <div class="flex items-center">
                <span class="w-6 h-4 bg-blue-500 mr-2"></span>
                <span class="w-24">United States</span>
                <div class="flex-grow bg-gray-700 rounded-full h-4">
                    <div class="bg-blue-600 h-4 rounded-full" style="width: 35%"></div>
                </div>
                <span class="ml-2">35%</span>
            </div>
            <div class="flex items-center">
                <span class="w-6 h-4 bg-red-500 mr-2"></span>
                <span class="w-24">Canada</span>
                <div class="flex-grow bg-gray-700 rounded-full h-4">
                    <div class="bg-blue-600 h-4 rounded-full" style="width: 26%"></div>
                </div>
                <span class="ml-2">26%</span>
            </div>
            <!-- Add more countries here -->
        </div>

        <script>
            // You would add JavaScript here for interactivity
            // For example, to handle zoom buttons and country hover effects
            document.querySelectorAll('.country').forEach(country => {
                country.addEventListener('mouseover', () => {
                    // Update info based on country
                });
            });
        </script>
        </body>
        </html>
    </x-filament::section>
</x-filament-widgets::widget>
