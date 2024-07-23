<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
        <style>
            /* Progress bar styles and animations */
            .progress-bar {
                width: 0;
                height: 100%;
                transition: width 0.6s ease;
                background-color: rgba(255, 255, 255, 0.2);
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

            .progress-text {
                position: absolute;
                color: white;
                font-size: 0.65rem; /* Reduced font size */
                font-weight: 500; /* Reduced font weight */
                z-index: 1;
            }

            /* Enhanced tooltip styles */
            .card-tooltip {
                visibility: hidden;
                width: 240px;
                background-color: #ffffff;
                color: #333333;
                text-align: left;
                border-radius: 6px;
                padding: 10px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                margin-left: -120px;
                opacity: 0;
                transition: opacity 0.3s, transform 0.3s;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                font-size: 0.75rem; /* Reduced font size */
                line-height: 1.4;
                transform: translateY(10px);
            }

            .card-tooltip::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -10px;
                border-width: 10px;
                border-style: solid;
                border-color: #ffffff transparent transparent transparent;
            }

            .card-container:hover .card-tooltip {
                visibility: visible;
                opacity: 1;
                transform: translateY(0);
            }

            .tooltip-title {
                font-weight: 500; /* Reduced font weight */
                margin-bottom: 5px;
                color: #4a5568;
            }

            .tooltip-description {
                color: #718096;
            }
        </style>

        <div class="container mx-auto p-2" wire:poll.4s="calculateStats">
            <div class="flex flex-wrap -mx-2">
                @php
                    // Define card configurations
                    $cards = [
                        'all' => ['title' => 'All Registered Users', 'icon' => 'fas fa-users', 'bgColor' => 'bg-blue-500 bg-opacity-20', 'color' => 'blue', 'description' => 'Total number of users registered in the system.'],
                        'active' => ['title' => 'Active Users', 'icon' => 'fas fa-check-circle', 'bgColor' => 'bg-green-500 bg-opacity-20', 'color' => 'green', 'description' => 'Users who have logged in within the last 30 days.'],
                        'inactive' => ['title' => 'Inactive Users', 'icon' => 'fas fa-user-slash', 'bgColor' => 'bg-red-500 bg-opacity-20', 'color' => 'red', 'description' => 'Users who haven\'t logged in for more than 30 days.'],
                        'churn' => ['title' => 'Churn Users', 'icon' => 'fas fa-exclamation-triangle', 'bgColor' => 'bg-yellow-500 bg-opacity-20', 'color' => 'yellow', 'description' => 'Users who have stopped using the service.'],
                        'avgValuePerDay' => ['title' => 'Avg Trans Value/Day', 'icon' => 'fas fa-dollar-sign', 'bgColor' => 'bg-purple-500 bg-opacity-20', 'color' => 'purple', 'description' => 'Average monetary value of transactions per day.'],
                        'avgTransactionPerCustomer' => ['title' => 'Avg Trans/Customer', 'icon' => 'fas fa-user-friends', 'bgColor' => 'bg-pink-500 bg-opacity-20', 'color' => 'pink', 'description' => 'Average number of transactions per customer.'],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="w-full sm:w-1/2 md:w-1/3 px-2 mb-3">
                        <div class="card-container relative">
                            <div class="{{ $card['bgColor'] }} text-gray-900 dark:text-white rounded-lg shadow-lg p-2 h-28 flex flex-col relative">
                                <!-- Card icon in the top right corner -->
                                <div class="absolute top-1 right-1 text-2xl opacity-20">
                                    <i class="{{ $card['icon'] }}"></i>
                                </div>
                                <div class="pt-4 flex-grow">
                                    <!-- Card title -->
                                    <h5 class="text-xs font-medium mb-1 {{ $key === 'avgValuePerDay' ? 'text-xxs' : '' }}">{{ $card['title'] }}</h5>
                                    <div class="flex items-center mb-1">
                                        <div class="w-2/3">
                                            <!-- Card count or value -->
                                            <h2 class="text-sm font-medium mb-0" wire:key="count-{{ $key }}">
                                                @if ($key === 'avgValuePerDay')
                                                    TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                                                @else
                                                    {{ isset($stats[$key]['count']) ? $stats[$key]['count'] : number_format($stats[$key]['value'] ?? 0, 0) }}
                                                @endif
                                            </h2>
                                        </div>
                                        <div class="w-1/3 text-right">
                                            <!-- Percentage change and growth icon -->
                                            <span class="text-gray-900 dark:text-white text-xs">
                                                @php
                                                    $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                                    $formattedPercentage = number_format(abs($percentageChange), 2);
                                                    $isGrowth = $stats[$key]['isGrowth'] ?? false;
                                                @endphp
                                                {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                                                <i class="fa fa-arrow-{{ $isGrowth ? 'up' : 'down' }} ml-1"></i>
                                            </span>
                                            <span class="text-xs block">(WoW)</span>
                                        </div>
                                    </div>
                                    <!-- Progress bar -->
                                    <div class="relative pt-1">
                                        <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-full h-1">
                                            <div class="bg-{{ $card['color'] }}-500 h-1 rounded-full progress-bar progress-bar-animate"
                                                 wire:key="progress-{{ $key }}"
                                                 style="--progress-width: {{ min(100, abs($percentageChange)) }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Enhanced Tooltip -->
                                <div class="card-tooltip">
                                    <div class="tooltip-title">{{ $card['title'] }}</div>
                                    <div class="tooltip-description">{{ $card['description'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
