<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
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
                font-size: 0.75rem;
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
                font-weight: 500;
                margin-bottom: 5px;
                color: #4a5568;
            }

            .tooltip-description {
                color: #718096;
            }
        </style>

        <div class="container mx-auto p-4" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    // Define card configurations
                    $cards = [
                        'registered' => [
                            'title' => 'Total Customers',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-yellow-100',
                            'iconColor' => 'text-yellow-500',
                            'description' => 'Total number of users who have registered on the Simba Money platform.'
                        ],
                        'active' => [
                            'title' => 'Active Customers',
                            'icon' => 'fas fa-user-check',
                            'iconBgColor' => 'bg-blue-100',
                            'iconColor' => 'text-blue-500',
                            'description' => 'Users who have engaged in any revenue-generating activity within the last 30 days.'
                        ],
                        'inactive' => [
                            'title' => 'Inactive Customers',
                            'icon' => 'fas fa-user-slash',
                            'iconBgColor' => 'bg-red-100',
                            'iconColor' => 'text-red-500',
                            'description' => 'Users who have not engaged in any revenue-generating activities for more than 30 days.'
                        ],
                        'churn' => [
                            'title' => 'Churn Customers',
                            'icon' => 'fas fa-user-minus',
                            'iconBgColor' => 'bg-orange-100',
                            'iconColor' => 'text-orange-500',
                            'description' => 'Users who have stopped using the platform and whose last activity was more than 30 days ago.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'Avg Transaction Value/Day',
                            'icon' => 'fas fa-dollar-sign',
                            'iconBgColor' => 'bg-green-100',
                            'iconColor' => 'text-green-500',
                            'description' => 'Average monetary value of all transactions processed per day.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'Avg Transactions/Customer',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBgColor' => 'bg-purple-100',
                            'iconColor' => 'text-purple-500',
                            'description' => 'Average number of transactions made by each customer.'
                        ],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card-container relative">
                        <div class="bg-white rounded-lg shadow-sm p-4 h-32 flex flex-col relative">
                            <!-- Icon in the top right corner -->
                            <div class="absolute top-3 right-3 p-2 rounded-lg {{ $card['iconBgColor'] }}">
                                <i class="{{ $card['icon'] }} text-lg {{ $card['iconColor'] }}"></i>
                            </div>
                            <div class="flex-grow">
                                <!-- Card title -->
                                <h5 class="text-gray-500 text-sm font-medium mb-2">{{ $card['title'] }}</h5>
                                <!-- Card count or value -->
                                <h2 class="text-2xl font-bold mb-2" wire:key="count-{{ $key }}">
                                    @if ($key === 'avgValuePerDay')
                                        TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                                    @elseif ($key === 'avgTransactionPerCustomer')
                                        {{ number_format($stats[$key]['value'] ?? 0, 2) }}
                                    @else
                                        {{ number_format($stats[$key]['count'] ?? 0, 0) }}
                                    @endif
                                </h2>
                                <!-- Percentage change -->
                                <div class="flex items-center">
                                    @php
                                        $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                        $formattedPercentage = number_format(abs($percentageChange), 0);
                                        $isGrowth = $stats[$key]['isGrowth'] ?? false;
                                        $changeColor = $isGrowth ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100';
                                    @endphp
                                    <span class="text-xs font-medium {{ $changeColor }} rounded px-2 py-1 mr-2">
                                        {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                                    </span>
                                    <span class="text-gray-500 text-xs">From the last month</span>
                                </div>
                            </div>
                            <!-- Tooltip -->
                            <div class="card-tooltip">
                                <div class="tooltip-title">{{ $card['title'] }}</div>
                                <div class="tooltip-description">{{ $card['description'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
