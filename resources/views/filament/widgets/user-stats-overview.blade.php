<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card {
                @apply bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden relative;
            }
            .card::after {
                @apply bg-blue-500;
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
            }
            .icon-bg {
                @apply flex items-center justify-center rounded-lg;
                width: 28px;
                height: 28px;
            }
            .percentage-badge {
                @apply py-1 px-2 rounded-full text-xs font-medium;
            }
            .card-title {
                @apply text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1;
            }
            .card-value {
                @apply text-gray-800 dark:text-gray-100 text-lg font-bold;
            }
            .time-period {
                @apply text-gray-400 dark:text-gray-500 text-xs;
            }
            .card-tooltip {
                @apply invisible bg-gray-900 text-white text-left rounded-lg p-2 absolute z-10 shadow-lg text-xs leading-tight opacity-0 transition-opacity duration-300 transform translate-y-2;
                bottom: 125%;
                left: 50%;
                width: 200px;
                margin-left: -100px;
            }
            .card-tooltip::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: #333333 transparent transparent transparent;
            }
            .card-container:hover .card-tooltip {
                @apply visible opacity-100 translate-y-0;
            }
            .tooltip-title {
                @apply font-medium mb-1 text-gray-300;
            }
            .tooltip-description {
                @apply text-gray-400;
            }
        </style>

        <div class="container mx-auto p-4" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $cards = [
                        'registered' => [
                            'title' => 'Total Customers',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-yellow-100 dark:bg-yellow-600',
                            'iconColor' => 'text-yellow-600 dark:text-yellow-100',
                            'description' => 'Total number of users who have registered on the Simba Money platform.'
                        ],
                        'active' => [
                            'title' => 'Active Customers',
                            'icon' => 'fas fa-user-check',
                            'iconBgColor' => 'bg-blue-100 dark:bg-blue-600',
                            'iconColor' => 'text-blue-600 dark:text-blue-100',
                            'description' => 'Users who have engaged in any revenue-generating activity within the last 30 days.'
                        ],
                        'inactive' => [
                            'title' => 'Inactive Customers',
                            'icon' => 'fas fa-user-slash',
                            'iconBgColor' => 'bg-red-100 dark:bg-red-600',
                            'iconColor' => 'text-red-600 dark:text-red-100',
                            'description' => 'Users who have not engaged in any revenue-generating activities for more than 30 days.'
                        ],
                        'churn' => [
                            'title' => 'Churn Customers',
                            'icon' => 'fas fa-user-slash',
                            'iconBgColor' => 'bg-orange-100 dark:bg-orange-600',
                            'iconColor' => 'text-orange-600 dark:text-orange-100',
                            'description' => 'Users who have stopped using the platform and whose last activity was more than 30 days ago.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'Avg Trans Value/Day',
                            'icon' => 'fas fa-dollar-sign',
                            'iconBgColor' => 'bg-green-100 dark:bg-green-600',
                            'iconColor' => 'text-green-600 dark:text-green-100',
                            'description' => 'Average monetary value of all transactions processed per day on the platform.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'Avg Trans/Customer',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBgColor' => 'bg-purple-100 dark:bg-purple-600',
                            'iconColor' => 'text-purple-600 dark:text-purple-100',
                            'description' => 'Average number of transactions made by each customer on the platform.'
                        ],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card-container relative">
                        <div class="card p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h5 class="card-title">{{ $card['title'] }}</h5>
                                    <h2 class="card-value" wire:key="count-{{ $key }}">
                                        @if ($key === 'avgValuePerDay')
                                            TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                                        @elseif ($key === 'avgTransactionPerCustomer')
                                            {{ number_format($stats[$key]['value'] ?? 0, 2) }}
                                        @else
                                            {{ number_format($stats[$key]['count'] ?? 0, 0) }}
                                        @endif
                                    </h2>
                                </div>
                                <div class="icon-bg {{ $card['iconBgColor'] }}">
                                    <i class="{{ $card['icon'] }} {{ $card['iconColor'] }} text-xs"></i>
                                </div>
                            </div>
                            <div class="flex items-center">
                                @php
                                    $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                    $formattedPercentage = number_format(abs($percentageChange), 0);
                                    $isGrowth = $stats[$key]['isGrowth'] ?? false;
                                    $changeColor = $isGrowth ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100' : 'bg-red-100 dark:bg-red-600 text-red-800 dark:text-red-100';
                                @endphp
                                <span class="percentage-badge {{ $changeColor }} mr-2">
                                    {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                                </span>
                                <span class="time-period">WoW</span>
                            </div>
                        </div>
                        <div class="card-tooltip">
                            <div class="tooltip-title">{{ $card['title'] }}</div>
                            <div class="tooltip-description">{{ $card['description'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
