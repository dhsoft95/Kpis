<x-filament-widgets::widget>
    <x-filament::section>
        <style>
            .progress-bar {
                transition: width 0.6s ease;
            }
            .card-tooltip {
                visibility: hidden;
                width: 200px;
                background-color: #ffffff;
                color: #333333;
                text-align: left;
                border-radius: 4px;
                padding: 8px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                margin-left: -100px;
                opacity: 0;
                transition: opacity 0.3s, transform 0.3s;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                font-size: 0.75rem;
                line-height: 1.3;
                transform: translateY(10px);
            }
            .card-container:hover .card-tooltip {
                visibility: visible;
                opacity: 1;
                transform: translateY(0);
            }
        </style>

        <div class="container mx-auto p-1" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @php
                    $cards = [
                        'all' => ['title' => 'All Users', 'icon' => 'users', 'color' => 'blue'],
                        '1' => ['title' => 'Active Users', 'icon' => 'check-circle', 'color' => 'green'],
                        '0' => ['title' => 'Inactive Users', 'icon' => 'user-slash', 'color' => 'red'],
                        'churn' => ['title' => 'Churn Users', 'icon' => 'exclamation-triangle', 'color' => 'yellow'],
                        'avgValuePerDay' => ['title' => 'Avg Value/Day', 'icon' => 'dollar-sign', 'color' => 'purple'],
                        'avgTransactionPerCustomer' => ['title' => 'Avg Trans/Customer', 'icon' => 'user-friends', 'color' => 'pink'],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card-container relative">
                        <div class="bg-{{ $card['color'] }}-500 text-white rounded-lg shadow p-2 h-24 flex flex-col relative">
                            <div class="absolute top-1 right-1 text-xl opacity-20">
                                <i class="fas fa-{{ $card['icon'] }}"></i>
                            </div>
                            <div class="pt-1 flex-grow">
                                <h5 class="text-xs font-semibold mb-1">{{ $card['title'] }}</h5>
                                <div class="flex items-center mb-1">
                                    <div class="w-2/3">
                                        <h2 class="text-sm font-bold mb-0" wire:key="count-{{ $key }}">
                                            @if ($key === 'avgValuePerDay')
                                                TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                                            @else
                                                {{ isset($stats[$key]['count']) ? $stats[$key]['count'] : number_format($stats[$key]['value'] ?? 0, 0) }}
                                            @endif
                                        </h2>
                                    </div>
                                    <div class="w-1/3 text-right text-xs">
                                        @php
                                            $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                            $formattedPercentage = number_format(abs($percentageChange), 1);
                                            $isGrowth = $stats[$key]['isGrowth'] ?? false;
                                        @endphp
                                        {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                                        <i class="fa fa-arrow-{{ $isGrowth ? 'up' : 'down' }} ml-1"></i>
                                        <span class="block">(WoW)</span>
                                    </div>
                                </div>
                                <div class="relative pt-1">
                                    <div class="w-full bg-{{ $card['color'] }}-600 rounded-full h-1">
                                        <div class="bg-white h-1 rounded-full progress-bar"
                                             wire:key="progress-{{ $key }}"
                                             style="width: {{ min(100, abs($percentageChange)) }}%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-tooltip">
                                <div class="font-semibold">{{ $card['title'] }}</div>
                                <div>{{ $cards[$key]['description'] ?? 'No description available.' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
