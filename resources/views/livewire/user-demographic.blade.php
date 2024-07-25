<x-filament-widgets::widget>
    <x-filament::section>
        <div
            x-data="{
                hoveredCountry: null,
                countries: @js($countries),
                getCountryData(code) {
                    return this.countries.find(country => country.code === code);
                }
            }"
            class="space-y-4"
        >
            <h2 class="text-lg font-medium">Sessions by country</h2>
            <p class="text-sm text-gray-500">View website visitors by hovering over the map</p>

            <div class="relative">
                <div class="world-map-container">
                    <!-- Include SVG from assets -->
                    <img src="{{ asset('asset/images/world.svg') }}" alt="World Map" class="world-map" />
                </div>

                <div x-show="hoveredCountry" x-transition class="absolute top-0 left-0 bg-white p-2 rounded shadow-md">
                    <p x-text="hoveredCountry ? `${hoveredCountry.name}: ${hoveredCountry.percentage}%` : ''"></p>
                </div>

                <div class="mt-4 space-y-2">
                    @foreach($countries as $country)
                        <div class="flex items-center">
                            <img src="https://flagcdn.com/w20/{{ strtolower($country['code']) }}.png" class="mr-2" alt="{{ $country['name'] }} flag">
                            <span class="w-24 text-sm">{{ $country['name'] }}</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $country['percentage'] }}%"></div>
                            </div>
                            <span class="ml-2 text-sm font-medium">{{ $country['percentage'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
