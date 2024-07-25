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
                            <svg viewBox="0 0 1000 500" class="world-map">
                                <g class="countries">
                                    <path class="country" d="M200,60 L300,60 L300,120 L200,120 Z" id="CA"
                                          @mouseenter="hoveredCountry = getCountryData('CA')"
                                          @mouseleave="hoveredCountry = null" />
                                    <path class="country" d="M200,120 L300,120 L350,200 L200,250 Z" id="US"
                                          @mouseenter="hoveredCountry = getCountryData('US')"
                                          @mouseleave="hoveredCountry = null" />
                                    <path class="country" d="M480,140 L500,120 L520,140 L500,160 Z" id="FR"
                                          @mouseenter="hoveredCountry = getCountryData('FR')"
                                          @mouseleave="hoveredCountry = null" />
                                    <path class="country" d="M500,160 L520,140 L540,160 L520,180 Z" id="IT"
                                          @mouseenter="hoveredCountry = getCountryData('IT')"
                                          @mouseleave="hoveredCountry = null" />
                                    <path class="country" d="M650,250 L700,200 L750,250 L700,300 Z" id="IN"
                                          @mouseenter="hoveredCountry = getCountryData('IN')"
                                          @mouseleave="hoveredCountry = null" />
                                    <path class="country" d="M800,350 L850,350 L850,400 L800,400 Z" id="AU"
                                          @mouseenter="hoveredCountry = getCountryData('AU')"
                                          @mouseleave="hoveredCountry = null" />
                                </g>
                            </svg>
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
