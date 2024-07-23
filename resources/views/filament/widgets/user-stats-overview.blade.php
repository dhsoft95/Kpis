<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        <style>
            .progress-bar {
                width: 0;
                height: 100%;
                transition: width 0.6s ease;
                background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%);
                background-size: 40px 40px;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .progress-bar-animate {
                animation: progress-animation 0.6s ease forwards;
            }
            @keyframes progress-animation {
                from { width: 0; }
                to { width: var(--progress-width); }
            }
            .card-tooltip {
                visibility: hidden;
                width: 200px;
                background-color: rgba(0, 0, 0, 0.8);
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                margin-left: -100px;
                opacity: 0;
                transition: opacity 0.3s;
            }
            .card-container:hover .card-tooltip {
                visibility: visible;
                opacity: 1;
            }
        </style>

        <div class="container mx-auto p-2" wire:poll.4s="calculateStats">
            <div class="flex flex-wrap -mx-2">
                @php
                    $cards = [
                        'all' => ['title' => 'All Registered Users', 'icon' => 'fas fa-users', 'bgGradient' => 'from-blue-700 to-blue-400', 'color' => 'blue', 'description' => 'Total number of users who have registered in our system, including both active and inactive accounts.'],
                        'active' => ['title' => 'Active Users', 'icon' => 'fas fa-check-circle', 'bgGradient' => 'from-green-700 to-green-400', 'color' => 'green', 'description' => 'Users who have logged in or performed an action within the last 30 days.'],
                        'inactive' => ['title' => 'Inactive Users', 'icon' => 'fas fa-user-slash', 'bgGradient' => 'from-red-700 to-red-400', 'color' => 'red', 'description' => 'Users who haven\'t logged in or performed any actions in the past 30 days.'],
                        'churn' => ['title' => 'Churn Users', 'icon' => 'fas fa-exclamation-triangle', 'bgGradient' => 'from-yellow-700 to-yellow-400', 'color' => 'yellow', 'description' => 'Users who have stopped using our service or have not renewed their subscription.'],
                        'avgValuePerDay' => ['title' => 'Avg Value of Trans Per Day', 'icon' => 'fas fa-dollar-sign', 'bgGradient' => 'from-purple-700 to-purple-400', 'color' => 'purple', 'description' => 'The average monetary value of all transactions processed per day.'],
                        'avgTransactionPerCustomer' => ['title' => 'Avg Trans Per Customer', 'icon' => 'fas fa-user-friends', 'bgGradient' => 'from-pink-700 to-pink-400', 'color' => 'pink', 'description' => 'The average number of transactions each customer makes over a specific period.'],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="w-full sm:w-1/2 md:w-1/3 px-2 mb-3">
                        <div class="card-container relative">
                            <div class="bg-gradient-to-r {{ $card['bgGradient'] }} text-white rounded-lg shadow-lg p-2 h-28 flex flex-col relative">
                                <div class="absolute top-1 right-1 text-3xl opacity-20">
                                    <i class="{{ $card['icon'] }}"></i>
                                </div>
                                <div class="pt-4 flex-grow">
                                    <h5 class="text-xs font-semibold mb-1">{{ $card['title'] }}</h5>
                                    <div class="flex items-center mb-1">
                                        <div class="w-2/3">
                                            <h2 class="text-lg font-bold mb-0" wire:key="count-{{ $key }}">
                                                @if (in_array($key, ['avgValuePerDay', 'avgTransactionPerCustomer']))
                                                    TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                                                @else
                                                    {{ number_format($stats[$key]['count'] ?? $stats[$key]['value'] ?? 0, 0) }}
                                                @endif
                                            </h2>
                                        </div>
                                        <div class="w-1/3 text-right">
                                            @php
                                                $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                                $formattedPercentage = number_format(abs($percentageChange), 2);
                                                $isGrowth = $stats[$key]['isGrowth'] ?? false;
                                            @endphp
                                            <span class="text-white text-xs">
                                                {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                                                <i class="fas fa-arrow-{{ $isGrowth ? 'up' : 'down' }} ml-1"></i>
                                            </span>
                                            <span class="text-xs block">(WoW)</span>
                                        </div>
                                    </div>
                                    <div class="relative pt-1">
                                        <div class="w-full bg-gray-300 rounded-full h-1">
                                            <div class="bg-{{ $card['color'] }}-500 h-1 rounded-full progress-bar progress-bar-animate"
                                                 wire:key="progress-{{ $key }}"
                                                 style="--progress-width: {{ min(100, abs($percentageChange)) }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <span class="card-tooltip">{{ $card['description'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
