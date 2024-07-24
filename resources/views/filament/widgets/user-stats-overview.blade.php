<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card-tooltip {
                visibility: hidden;
                width: 220px;
                background-color: var(--tooltip-bg);
                color: var(--tooltip-text);
                text-align: left;
                border-radius: 4px;
                padding: 8px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                margin-left: -110px;
                opacity: 0;
                transition: opacity 0.3s, transform 0.3s;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                font-size: 0.7rem;
                line-height: 1.3;
                transform: translateY(10px);
            }

            .card-tooltip::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: var(--tooltip-bg) transparent transparent transparent;
            }

            .card-container:hover .card-tooltip {
                visibility: visible;
                opacity: 1;
                transform: translateY(0);
            }

            .tooltip-title {
                font-weight: 500;
                margin-bottom: 3px;
            }

            .dark .card {
                background-color: rgba(39, 39, 42, 0.8);
                color: #f3f4f6;
            }

            .dark .card-title {
                color: #d1d5db;
            }

            .dark .card-subtitle {
                color: #9ca3af;
            }
        </style>

        <div class="container mx-auto p-2" x-data="{ darkMode: false }" x-init="darkMode = document.documentElement.classList.contains('dark')" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @php
                    $cards = [
                        'registered' => [
                            'title' => 'Total Customers',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-yellow-100 dark:bg-yellow-900/50',
                            'iconColor' => 'text-yellow-500 dark:text-yellow-300',
                            'description' => 'Total number of registered users on the Simba Money platform.'
                        ],
                        'active' => [
                            'title' => 'Active Customers',
                            'icon' => 'fas fa-user-check',
                            'iconBgColor' => 'bg-blue-100 dark:bg-blue-900/50',
                            'iconColor' => 'text-blue-500 dark:text-blue-300',
                            'description' => 'Users with revenue-generating activity in the last 30 days.'
                        ],
                        'inactive' => [
                            'title' => 'Inactive Customers',
                            'icon' => 'fas fa-user-slash',
                            'iconBgColor' => 'bg-red-100 dark:bg-red-900/50',
                            'iconColor' => 'text-red-500 dark:text-red-300',
                            'description' => 'Users without revenue-generating activities for over 30 days.'
                        ],
                        'churn' => [
                            'title' => 'Churn Customers',
                            'icon' => 'fas fa-user-minus',
                            'iconBgColor' => 'bg-orange-100 dark:bg-orange-900/50',
                            'iconColor' => 'text-orange-500 dark:text-orange-300',
                            'description' => 'Users who stopped platform usage over 30 days ago.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'Avg Trans Value/Day',
                            'icon' => 'fas fa-dollar-sign',
                            'iconBgColor' => 'bg-green-100 dark:bg-green-900/50',
                            'iconColor' => 'text-green-500 dark:text-green-300',
                            'description' => 'Average daily transaction value on the platform.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'Avg Trans/Customer',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBgColor' => 'bg-purple-100 dark:bg-purple-900/50',
                            'iconColor' => 'text-purple-500 dark:text-purple-300',
                            'description' => 'Average number of transactions per customer.'
                        ],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card-container relative">
                        <div class="card bg-white dark:bg-[rgba(39,39,42,0.8)] rounded-lg shadow-sm p-3 h-28 flex flex-col relative">
                            <div class="absolute top-2 right-2 p-1 rounded-lg {{ $card['iconBgColor'] }}">
                                <i class="{{ $card['icon'] }} text-xs {{ $card['iconColor'] }}"></i>
                            </div>
                            <div class="flex-grow">
                                <h5 class="card-title text-gray-600 dark:text-gray-300 text-xs font-medium mb-1">{{ $card['title'] }}</h5>
                                <h2 class="text-xl font-bold mb-1 dark:text-white" wire:key="count-{{ $key }}">
                                    @if ($key === 'avgValuePerDay')
                                        TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                                    @elseif ($key === 'avgTransactionPerCustomer')
                                        {{ number_format($stats[$key]['value'] ?? 0, 2) }}
                                    @else
                                        {{ number_format($stats[$key]['count'] ?? 0, 0) }}
                                    @endif
                                </h2>
                                <div class="flex items-center">
                                    @php
                                        $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                        $formattedPercentage = number_format(abs($percentageChange), 0);
                                        $isGrowth = $stats[$key]['isGrowth'] ?? false;
                                        $changeColor = $isGrowth ? 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/50' : 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/50';
                                    @endphp
                                    <span class="text-xs font-medium {{ $changeColor }} rounded px-1.5 py-0.5 mr-1.5">
                                        {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                                    </span>
                                    <span class="card-subtitle text-gray-500 dark:text-gray-400 text-[10px]">Last week</span>
                                </div>
                            </div>
                            <div class="card-tooltip" :style="{ '--tooltip-bg': darkMode ? '#374151' : '#ffffff', '--tooltip-text': darkMode ? '#f3f4f6' : '#1f2937' }">
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
