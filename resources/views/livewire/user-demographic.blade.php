<x-filament-widgets::widget>
    <x-filament::section>
        <div class="bg-gray-900 p-8 text-white font-sans">
            <h1 class="text-2xl font-bold mb-1">Sessions by region in Tanzania</h1>
            <p class="text-sm text-gray-400 mb-6">View website visitors on the map</p>

            <div class="mb-8 relative">
                <div id="map" class="h-[500px] w-full rounded-lg overflow-hidden"></div>
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

    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
    <script>
        function initMap() {
            const tanzania = { lat: -6.369028, lng: 34.888822 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: tanzania,
                styles: [
                    { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                    { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                    { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                    {
                        featureType: "administrative.locality",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#d59563" }],
                    },
                    {
                        featureType: "poi",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#d59563" }],
                    },
                    {
                        featureType: "poi.park",
                        elementType: "geometry",
                        stylers: [{ color: "#263c3f" }],
                    },
                    {
                        featureType: "poi.park",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#6b9a76" }],
                    },
                    {
                        featureType: "road",
                        elementType: "geometry",
                        stylers: [{ color: "#38414e" }],
                    },
                    {
                        featureType: "road",
                        elementType: "geometry.stroke",
                        stylers: [{ color: "#212a37" }],
                    },
                    {
                        featureType: "road",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#9ca5b3" }],
                    },
                    {
                        featureType: "road.highway",
                        elementType: "geometry",
                        stylers: [{ color: "#746855" }],
                    },
                    {
                        featureType: "road.highway",
                        elementType: "geometry.stroke",
                        stylers: [{ color: "#1f2835" }],
                    },
                    {
                        featureType: "road.highway",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#f3d19c" }],
                    },
                    {
                        featureType: "transit",
                        elementType: "geometry",
                        stylers: [{ color: "#2f3948" }],
                    },
                    {
                        featureType: "transit.station",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#d59563" }],
                    },
                    {
                        featureType: "water",
                        elementType: "geometry",
                        stylers: [{ color: "#17263c" }],
                    },
                    {
                        featureType: "water",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#515c6d" }],
                    },
                    {
                        featureType: "water",
                        elementType: "labels.text.stroke",
                        stylers: [{ color: "#17263c" }],
                    },
                ],
            });

            const cities = [
                { name: "Dar es Salaam", lat: -6.792354, lng: 39.208328, percentage: 35 },
                { name: "Mwanza", lat: -2.516667, lng: 32.900000, percentage: 26 },
                { name: "Arusha", lat: -3.366667, lng: 36.683333, percentage: 18 },
                { name: "Mbeya", lat: -8.900000, lng: 33.450000, percentage: 14 },
                { name: "Dodoma", lat: -6.173056, lng: 35.741944, percentage: 7 },
            ];

            cities.forEach(city => {
                const marker = new google.maps.Marker({
                    position: { lat: city.lat, lng: city.lng },
                    map: map,
                    title: city.name,
                });

                const infowindow = new google.maps.InfoWindow({
                    content: `<div style="color: black;">${city.name}: ${city.percentage}%</div>`,
                });

                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });
            });
        }

        window.addEventListener("load", initMap);
    </script>
</x-filament-widgets::widget>
