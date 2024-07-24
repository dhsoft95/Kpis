<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card {
                background-color: white;
                border-radius: 12px;
                padding: 20px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                position: relative;
                border: 1px solid #e0e0e0;
                margin-bottom: 16px;
                height: 160px; /* Adjusted card height */
            }
            .dark .card {
                background-color: #1f2937;
                border-color: #374151;
            }
            .card-icon {
                position: absolute;
                top: 16px;
                right: 16px;
                background-color: #e8eeff;
                border-radius: 6px;
                padding: 8px;
                color: #4070f4;
                font-size: 24px; /* Adjusted icon size */
            }
            .dark .card-icon {
                background-color: #374151;
                color: #60a5fa;
            }
            .card-title {
                font-size: 14px; /* Adjusted title font size */
                color: #6b7280;
                font-weight: 500;
                margin-bottom: 8px; /* Adjusted margin */
            }
            .dark .card-title {
                color: #9ca3af;
            }
            .card-value {
                font-size: 28px; /* Adjusted value font size */
                font-weight: 700;
                color: #111827;
                margin-bottom: 12px; /* Adjusted margin */
            }
            .dark .card-value {
                color: #f3f4f6;
            }
            .card-change {
                font-size: 14px; /* Adjusted change font size */
                font-weight: 500;
            }
            .change-indicator {
                padding: 4px 8px;
                border-radius: 12px;
                margin-right: 8px;
                display: inline-block;
            }
            .change-positive {
                background-color: #dcfce7;
                color: #22c55e;
            }
            .dark .change-positive {
                background-color: #065f46;
                color: #4ade80;
            }
            .change-negative {
                background-color: #fee2e2;
                color: #ef4444;
            }
            .dark .change-negative {
                background-color: #7f1d1d;
                color: #f87171;
            }
        </style>

        <div class="container mx-auto p-4" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @php
                    $cards = [
                        'registered' => [
                            'title' => 'All Registered Users',
                            'icon' => 'fas fa-users',
                            'description' => 'Total number of users who have registered on the Simba Money platform. This includes all users regardless of their activity status.'
                        ],
                        'active' => [
                            'title' => 'Active Users',
                            'icon' => 'fas fa-check-circle',
                            'description' => 'Users who have engaged in any revenue-generating activity on the Simba Money platform within the last 30 days. This includes transactions such as sending money or savings.'
                        ],
                        'inactive' => [
                            'title' => 'Inactive Users',
                            'icon' => 'fas fa-user-slash',
                            'description' => 'Users who have not engaged in any revenue-generating activities on the Simba Money platform for more than 30 days since their registration.'
                        ],
                        'churn' => [
                            'title' => 'Churn Users',
                            'icon' => 'fas fa-exclamation-triangle',
                            'description' => 'Users who have stopped using the Simba Money platform and whose last revenue-generating activity occurred more than 30 days ago.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'Avg Trans Value/Day',
                            'icon' => 'fas fa-dollar-sign',
                            'description' => 'Average monetary value of all transactions processed per day on the Simba Money platform.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'Avg Trans/Customer',
                            'icon' => 'fas fa-user-friends',
                            'description' => 'Average number of transactions made by each customer on the Simba Money platform.'
                        ],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card">
                        <div class="card-icon">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <h5 class="card-title">{{ strtoupper($card['title']) }}</h5>
                        <div class="card-value">
                            @if ($key === 'avgValuePerDay')
                                TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                            @elseif ($key === 'avgTransactionPerCustomer')
                                {{ number_format($stats[$key]['value'] ?? 0, 2) }}
                            @else
                                {{ number_format($stats[$key]['count'] ?? 0, 0) }}
                            @endif
                        </div>
                        <div class="card-change">
                            @php
                                $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                $formattedPercentage = number_format(abs($percentageChange), 2);
                                $isGrowth = $stats[$key]['isGrowth'] ?? false;
                            @endphp
                            <span class="change-indicator {{ $isGrowth ? 'change-positive' : 'change-negative' }}">
                                {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                            </span>
                            <span class="text-gray-500 dark:text-gray-400">From last week</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ $card['description'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
