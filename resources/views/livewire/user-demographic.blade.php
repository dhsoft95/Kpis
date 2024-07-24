<x-filament-widgets::widget>
    <x-filament::section>
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-4">Sessions by Country</h1>
            <p class="mb-8">View website visitors by hovering over the map</p>

            <div class="flex">
                <div class="w-full p-4">
                    <!-- SVG World Map -->
                    <div class="bg-gray-800 h-96 flex items-center justify-center">
                        <img src="{{ asset('world-map.svg') }}" alt="World Map" class="w-full h-auto">
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
