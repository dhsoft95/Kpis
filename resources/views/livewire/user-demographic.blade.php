<x-filament-widgets::widget>
    <x-filament::section>
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-4">Sessions by Country</h1>
            <p class="mb-8">Hover over the countries to see the data</p>

            <div class="relative bg-gray-800 h-96 flex items-center justify-center overflow-hidden">
                <!-- Zoom Controls -->
                <div class="absolute top-4 right-4 z-10">
                    <button id="zoom-in" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600 focus:outline-none">
                        Zoom In
                    </button>
                    <button id="zoom-out" class="bg-blue-500 text-white p-2 mt-2 rounded hover:bg-blue-600 focus:outline-none">
                        Zoom Out
                    </button>
                </div>

                <!-- SVG World Map -->
                <img src="{{ asset('asset/images/world.svg') }}" alt="World Map" class="w-full h-auto" id="world-map">

                <!-- Tooltip info -->
                <div id="map-info" class="absolute bg-white text-black p-2 hidden rounded shadow-md">
                    <!-- Tooltip info will be displayed here -->
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const mapInfo = document.getElementById('map-info');
                const worldMap = document.getElementById('world-map');
                const zoomInButton = document.getElementById('zoom-in');
                const zoomOutButton = document.getElementById('zoom-out');

                let scale = 1;

                worldMap.addEventListener('load', () => {
                    // Since we're using an <img> tag, we need to manually handle the SVG interactions
                    // Create an invisible SVG overlay to capture hover events
                    const svgOverlay = document.createElement('div');
                    svgOverlay.style.position = 'absolute';
                    svgOverlay.style.top = '0';
                    svgOverlay.style.left = '0';
                    svgOverlay.style.width = '100%';
                    svgOverlay.style.height = '100%';
                    svgOverlay.style.pointerEvents = 'none'; // Allow interactions with underlying map
                    worldMap.parentElement.appendChild(svgOverlay);

                    // Add dummy event listeners to the overlay to handle hover effects
                    // Here you should add actual event listeners based on your SVG structure

                    // Example code for adding hover events to overlay (you need to customize this part)
                    svgOverlay.addEventListener('mouseover', (e) => {
                        // Assuming country elements are in the SVG and have data-info attributes
                        const info = e.target.getAttribute('data-info');
                        if (info) {
                            mapInfo.textContent = info;
                            mapInfo.style.display = 'block';
                            mapInfo.style.left = `${e.pageX}px`;
                            mapInfo.style.top = `${e.pageY}px`;
                        }
                    });

                    svgOverlay.addEventListener('mouseout', () => {
                        mapInfo.style.display = 'none';
                    });
                });

                // Zoom In
                zoomInButton.addEventListener('click', () => {
                    scale += 0.1;
                    worldMap.style.transform = `scale(${scale})`;
                });

                // Zoom Out
                zoomOutButton.addEventListener('click', () => {
                    scale = Math.max(0.5, scale - 0.1); // Prevent zooming out too much
                    worldMap.style.transform = `scale(${scale})`;
                });
            });
        </script>
    </x-filament::section>
</x-filament-widgets::widget>
