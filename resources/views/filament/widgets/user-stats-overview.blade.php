<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card {
                @apply bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 relative overflow-hidden;
            }
            .card-title {
                @apply text-sm font-medium text-gray-600 dark:text-gray-300 mb-2;
            }
            .card-value {
                @apply text-2xl font-bold text-gray-800 dark:text-white mb-2;
            }
            .card-change {
                @apply text-xs font-medium;
            }
            .card-change-positive {
                @apply text-green-500 dark:text-green-400 bg-green-100 dark:bg-green-800 bg-opacity-50 rounded px-2 py-1;
            }
            .card-change-negative {
                @apply text-red-500 dark:text-red-400 bg-red-100 dark:bg-red-800 bg-opacity-50 rounded px-2 py-1;
            }
            .card-subtitle {
                @apply text-xs text-gray-500 dark:text-gray-400 mt-1;
            }
            .card-icon {
                @apply absolute top-4 right-4 text-3xl opacity-20;
            }
            .tooltip {
                @apply invisible absolute z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700;
                transition: opacity 0.3s ease-in-out;
            }
            .card:hover .tooltip {
                @apply visible opacity-100;
            }
        </style>

        <div class="container mx-auto p-2" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $cards = [
                        'registered' => [
                            'title' => 'Total Customers',
                            'icon' => 'fas fa-users',
                            'iconBg' => 'text-blue-500 dark:text-blue-400',
                            'description' => 'Total number of users registered on the Simba Money platform.'
                        ],
                        'active' => [
                            'title' => 'Active Customers',
                            'icon' => 'fas fa-check-circle',
                            'iconBg' => 'text-green-500 dark:text-green-400',
                            'description' => 'Users who have engaged in activity within the last 30 days.'
                        ],
                        'inactive' => [
                            'title' => 'Inactive Users',
                            'icon' => 'fas fa-user-slash',
                            'iconBg' => 'text-red-500 dark:text-red-400',
                            'description' => 'Users who have not engaged in activities for more than 30 days.'
                        ],
                        'churn' => [
                            'title' => 'Churn Users',
                            'icon' => 'fas fa-exclamation-triangle',
                            'iconBg' => 'text-yellow-500 dark:text-yellow-400',
                            'description' => 'Users who have stopped using the platform.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'Avg Trans Value/Day',
                            'icon' => 'fas fa-dollar-sign',
                            'iconBg' => 'text-purple-500 dark:text-purple-400',
                            'description' => 'Average monetary value of all transactions processed per day.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'Avg Trans/Customer',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBg' => 'text-pink-500 dark:text-pink-400',
                            'description' => 'Average number of transactions made by each customer.'
                        ],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card">
                        <div class="card-icon {{ $card['iconBg'] }}">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <h5 class="card-title">{{ $card['title'] }}</h5>
                        <div class="card-value" wire:key="count-{{ $key }}">
                            @if ($key === 'avgValuePerDay')
                                TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                            @elseif ($key === 'avgTransactionPerCustomer')
                                {{ number_format($stats[$key]['value'] ?? 0, 2) }}
                            @else
                                {{ number_format($stats[$key]['count'] ?? 0, 0) }}
                            @endif
                        </div>
                        <div class="flex items-center">
                            <span class="card-change {{ $stats[$key]['isGrowth'] ?? false ? 'card-change-positive' : 'card-change-negative' }}">
                                {{ ($stats[$key]['isGrowth'] ?? false) ? '+' : '-' }}{{ number_format(abs($stats[$key]['percentageChange'] ?? 0), 2) }}%
                            </span>
                            <span class="card-subtitle ml-2">From the last month</span>
                        </div>
                        <div class="tooltip">
                            {{ $card['description'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
