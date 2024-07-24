<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card {
                @apply bg-white rounded-lg p-6 shadow-md relative overflow-hidden;
            }
            .card-icon {
                @apply absolute top-4 right-4 text-2xl opacity-20;
            }
            .card-title {
                @apply text-gray-500 text-sm font-medium mb-2;
            }
            .card-value {
                @apply text-gray-900 text-4xl font-bold mb-4;
            }
            .card-change {
                @apply text-sm font-medium inline-flex items-center rounded-full px-2 py-1;
            }
            .card-change-positive {
                @apply bg-green-100 text-green-800;
            }
            .card-change-negative {
                @apply bg-red-100 text-red-800;
            }
            .tooltip {
                @apply invisible absolute z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg opacity-0 transition-opacity duration-300;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                white-space: nowrap;
            }
            .card:hover .tooltip {
                @apply visible opacity-100;
            }
        </style>

        <div class="bg-gray-100 p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $cards = [
                        'registered' => [
                            'title' => 'TOTAL CUSTOMERS',
                            'icon' => 'fas fa-users',
                            'iconBg' => 'bg-yellow-100 text-yellow-500',
                            'description' => 'Total number of users registered on the Simba Money platform.'
                        ],
                        'active' => [
                            'title' => 'ACTIVE CUSTOMERS',
                            'icon' => 'fas fa-check-circle',
                            'iconBg' => 'bg-blue-100 text-blue-500',
                            'description' => 'Users who have engaged in activity within the last 30 days.'
                        ],
                        'inactive' => [
                            'title' => 'INACTIVE USERS',
                            'icon' => 'fas fa-user-slash',
                            'iconBg' => 'bg-red-100 text-red-500',
                            'description' => 'Users who have not engaged in activities for more than 30 days.'
                        ],
                        'churn' => [
                            'title' => 'CHURN USERS',
                            'icon' => 'fas fa-exclamation-triangle',
                            'iconBg' => 'bg-yellow-100 text-yellow-500',
                            'description' => 'Users who have stopped using the platform.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'AVG TRANS VALUE/DAY',
                            'icon' => 'fas fa-dollar-sign',
                            'iconBg' => 'bg-purple-100 text-purple-500',
                            'description' => 'Average monetary value of all transactions processed per day.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'AVG TRANS/CUSTOMER',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBg' => 'bg-pink-100 text-pink-500',
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
                            <span class="card-change {{ ($stats[$key]['isGrowth'] ?? false) ? 'card-change-positive' : 'card-change-negative' }}">
                                {{ ($stats[$key]['isGrowth'] ?? false) ? '+' : '-' }}{{ number_format(abs($stats[$key]['percentageChange'] ?? 0), 2) }}%
                            </span>
                            <span class="ml-2 text-gray-500">From the last month</span>
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
