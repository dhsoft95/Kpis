<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card {
                background-color: white;
                border-radius: 16px;
                padding: 20px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                position: relative;
                border: 1px solid #e0e0e0;
                margin-bottom: 16px;
            }
            .card-icon {
                position: absolute;
                top: 20px;
                right: 20px;
                background-color: #e8eeff;
                border-radius: 8px;
                padding: 8px;
                color: #4070f4;
            }
            .card-title {
                font-size: 14px;
                color: #6b7280;
                font-weight: 500;
                margin-bottom: 8px;
            }
            .card-value {
                font-size: 28px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 16px;
            }
            .card-change {
                font-size: 14px;
                font-weight: 500;
            }
            .change-indicator {
                padding: 4px 8px;
                border-radius: 16px;
                margin-right: 8px;
                display: inline-block;
            }
            .change-positive {
                background-color: #dcfce7;
                color: #22c55e;
            }
            .change-negative {
                background-color: #fee2e2;
                color: #ef4444;
            }
        </style>

        <div class="container mx-auto p-2" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @php
                    $cards = [
                        'registered' => ['title' => 'All Registered Users', 'icon' => 'fas fa-users'],
                        'active' => ['title' => 'Active Users', 'icon' => 'fas fa-check-circle'],
                        'inactive' => ['title' => 'Inactive Users', 'icon' => 'fas fa-user-slash'],
                        'churn' => ['title' => 'Churn Users', 'icon' => 'fas fa-exclamation-triangle'],
                        'avgValuePerDay' => ['title' => 'Avg Trans Value/Day', 'icon' => 'fas fa-dollar-sign'],
                        'avgTransactionPerCustomer' => ['title' => 'Avg Trans/Customer', 'icon' => 'fas fa-user-friends'],
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
                            <span class="text-gray-500">From last week</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
