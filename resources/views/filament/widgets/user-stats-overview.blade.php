<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card {
                @apply bg-gray-900 rounded-lg p-4 text-white relative;
            }
            .card-icon {
                @apply text-2xl mb-2;
            }
            .card-title {
                @apply text-lg font-medium mb-1;
            }
            .card-value {
                @apply text-3xl font-bold mb-1;
            }
            .card-change {
                @apply text-sm;
            }
            .tooltip {
                @apply invisible absolute z-10 py-2 px-3 text-sm font-medium text-white bg-gray-700 rounded-lg opacity-0 transition-opacity duration-300 whitespace-nowrap;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
            }
            .card:hover .tooltip {
                @apply visible opacity-100;
            }
        </style>

        <div class="bg-black p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $cards = [
                        'registered' => [
                            'title' => 'Total Customers',
                            'icon' => 'fas fa-users text-blue-500',
                            'description' => 'Total number of users registered on the Simba Money platform.'
                        ],
                        'active' => [
                            'title' => 'Active Customers',
                            'icon' => 'fas fa-check-circle text-green-500',
                            'description' => 'Users who have engaged in activity within the last 30 days.'
                        ],
                        'inactive' => [
                            'title' => 'Inactive Users',
                            'icon' => 'fas fa-user-slash text-red-500',
                            'description' => 'Users who have not engaged in activities for more than 30 days.'
                        ],
                        'churn' => [
                            'title' => 'Churn Users',
                            'icon' => 'fas fa-exclamation-triangle text-yellow-500',
                            'description' => 'Users who have stopped using the platform.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'Avg Trans Value/Day',
                            'icon' => 'fas fa-dollar-sign text-purple-500',
                            'description' => 'Average monetary value of all transactions processed per day.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'Avg Trans/Customer',
                            'icon' => 'fas fa-exchange-alt text-pink-500',
                            'description' => 'Average number of transactions made by each customer.'
                        ],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card">
                        <div class="card-icon">
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
                        <div class="card-change">
                            <span class="{{ ($stats[$key]['isGrowth'] ?? false) ? 'text-green-500' : 'text-red-500' }}">
                                {{ ($stats[$key]['isGrowth'] ?? false) ? '+' : '-' }}{{ number_format(abs($stats[$key]['percentageChange'] ?? 0), 2) }}%
                            </span>
                            From the last month
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
