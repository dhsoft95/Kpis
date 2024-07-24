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
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                position: relative;
                overflow: hidden;
            }
            .card::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: #3b82f6; /* Tailwind blue-500 */
            }
            .icon-bg {
                width: 32px;
                height: 32px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .percentage-badge {
                padding: 1px 6px;
                border-radius: 10px;
                font-size: 0.65rem;
                font-weight: 500;
            }
            .card-title {
                color: #6b7280; /* Tailwind gray-500 */
                font-size: 0.7rem;
                font-weight: 500;
            }
            .card-value {
                font-size: 1.25rem;
                font-weight: 700;
                color: #1f2937; /* Tailwind gray-800 */
            }
            .time-period {
                color: #9ca3af; /* Tailwind gray-400 */
                font-size: 0.65rem;
            }
        </style>

        <div class="container mx-auto p-4" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $cards = [
                        'registered' => [
                            'title' => 'TOTAL CUSTOMERS',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-yellow-100',
                            'iconColor' => 'text-yellow-600',
                        ],
                        'active' => [
                            'title' => 'ACTIVE CUSTOMERS',
                            'icon' => 'fas fa-user-check',
                            'iconBgColor' => 'bg-blue-100',
                            'iconColor' => 'text-blue-600',
                        ],
                        'inactive' => [
                            'title' => 'INACTIVE CUSTOMERS',
                            'icon' => 'fas fa-user-slash',
                            'iconBgColor' => 'bg-red-100',
                            'iconColor' => 'text-red-600',
                        ],
                        'churn' => [
                            'title' => 'CHURN CUSTOMERS',
                            'icon' => 'fas fa-user-minus',
                            'iconBgColor' => 'bg-orange-100',
                            'iconColor' => 'text-orange-600',
                        ],
                        'avgValuePerDay' => [
                            'title' => 'AVG TRANS VALUE/DAY',
                            'icon' => 'fas fa-dollar-sign',
                            'iconBgColor' => 'bg-green-100',
                            'iconColor' => 'text-green-600',
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'AVG TRANS/CUSTOMER',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBgColor' => 'bg-purple-100',
                            'iconColor' => 'text-purple-600',
                        ],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="card-title text-sm mb-1">{{ $card['title'] }}</h5>
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
                                <i class="{{ $card['icon'] }} {{ $card['iconColor'] }} text-sm"></i>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @php
                                $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                $formattedPercentage = number_format(abs($percentageChange), 0);
                                $isGrowth = $stats[$key]['isGrowth'] ?? false;
                                $changeColor = $isGrowth ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            @endphp
                            <span class="percentage-badge {{ $changeColor }} mr-2">
                                {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                            </span>
                            <span class="time-period">From the last month</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
