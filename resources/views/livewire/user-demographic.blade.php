<x-filament-widgets::widget>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <x-filament::section>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-semibold mb-4">Sessions by country</h1>
            <p class="text-gray-400 mb-8">View website visitors by hovering over the map</p>
            <div class="flex flex-col md:flex-row">
                <div class="w-full md:w-2/3 h-96 bg-gray-800 rounded-lg shadow-lg p-4">
                    <svg class="map w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2000 1001">
                        <!-- Example paths for countries -->
                        <path d="M500,500L600,500L600,600L500,600Z" class="fill-current text-gray-600" data-country="United States"></path>
                        <path d="M700,500L800,500L800,600L700,600Z" class="fill-current text-gray-600" data-country="Canada"></path>
                        <!-- Add more paths for other countries -->
                    </svg>
                </div>
                <div class="w-full md:w-1/3 mt-4 md:mt-0 md:ml-4">
                    <ul id="country-list">
                        <li class="flex items-center justify-between py-2">
                            <span class="flex items-center"><img src="https://cdn.jsdelivr.net/npm/flag-icons@1.0.0/flags/4x3/us.svg" class="w-6 h-4 mr-2"> United States</span>
                            <span class="text-gray-400">35%</span>
                        </li>
                        <li class="flex items-center justify-between py-2">
                            <span class="flex items-center"><img src="https://cdn.jsdelivr.net/npm/flag-icons@1.0.0/flags/4x3/ca.svg" class="w-6 h-4 mr-2"> Canada</span>
                            <span class="text-gray-400">26%</span>
                        </li>
                        <!-- Add more list items for other countries -->
                    </ul>
                </div>
            </div>
        </div>
        <script>
            document.querySelectorAll('.map path').forEach(function(path) {
                path.addEventListener('mouseover', function() {
                    const countryName = path.getAttribute('data-country');
                    const listItems = document.querySelectorAll('#country-list li span:first-child');
                    listItems.forEach(function(item) {
                        if (item.innerText.includes(countryName)) {
                            item.parentNode.classList.add('bg-gray-700');
                        }
                    });
                });
                path.addEventListener('mouseout', function() {
                    const countryName = path.getAttribute('data-country');
                    const listItems = document.querySelectorAll('#country-list li span:first-child');
                    listItems.forEach(function(item) {
                        if (item.innerText.includes(countryName)) {
                            item.parentNode.classList.remove('bg-gray-700');
                        }
                    });
                });
            });
        </script>
    </x-filament::section>
</x-filament-widgets::widget>z
