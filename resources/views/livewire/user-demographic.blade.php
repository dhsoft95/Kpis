<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament-widgets::widget>
            <x-filament::section>
                <div class="space-y-6">
                    <div class="p-4 bg-white rounded-xl shadow">
                        <h2 class="text-lg font-medium mb-4">Sessions by Country</h2>
                        <div class="w-full h-96 bg-gray-100 rounded-lg mb-4 overflow-hidden">
                            <svg viewBox="0 0 1000 500" class="w-full h-full">
                                <!-- Simplified world map paths -->
                                <path id="US" d="M200,160 L220,160 L220,180 L200,180 Z" class="country" />
                                <path id="CA" d="M180,140 L200,140 L200,160 L180,160 Z" class="country" />
                                <path id="FR" d="M470,160 L480,160 L480,170 L470,170 Z" class="country" />
                                <path id="DE" d="M485,155 L495,155 L495,165 L485,165 Z" class="country" />
                                <path id="AU" d="M800,350 L820,350 L820,370 L800,370 Z" class="country" />
                            </svg>
                        </div>
                        <div class="space-y-2">
                            @foreach($this->getCountryData() as $code => $country)
                                <div class="flex items-center">
                                    <span class="w-6 h-4 bg-{{ $country['color'] }}-500 mr-2"></span>
                                    <span class="w-24">{{ $country['name'] }}</span>
                                    <div class="flex-grow bg-gray-200 rounded-full h-4">
                                        <div class="bg-{{ $country['color'] }}-600 h-4 rounded-full" style="width: {{ $country['percentage'] }}%"></div>
                                    </div>
                                    <span class="ml-2">{{ $country['percentage'] }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Keep the rest of your widget content as it was -->

                </div>
            </x-filament::section>

            <style>
                .country { fill: #D1D5DB; stroke: #9CA3AF; stroke-width: 1; }
                .country:hover { fill: #60A5FA; cursor: pointer; }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const countryData = @json($this->getCountryData());
                    const countries = document.querySelectorAll('.country');

                    countries.forEach(country => {
                        country.addEventListener('mouseover', () => {
                            const data = countryData[country.id];
                            if (data) {
                                country.style.fill = `var(--color-${data.color}-500)`;
                            }
                        });

                        country.addEventListener('mouseout', () => {
                            country.style.fill = '#D1D5DB';
                        });
                    });
                });
            </script>
        </x-filament-widgets::widget>
    </x-filament::section>
</x-filament-widgets::widget>
