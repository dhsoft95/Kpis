<x-filament-widgets::widget>
    <x-filament::section>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card {
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                position: relative;
                overflow: hidden;
                max-width: 350px;
            }
            .dark .card {
                background-color: #27272a;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.6), 0 2px 4px -1px rgba(0, 0, 0, 0.4);
            }
            .icon-bg {
                width: 28px;
                height: 28px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .percentage-badge {
                padding: 1px 6px;
                border-radius: 10px;
                font-size: 0.6rem;
                font-weight: 500;
            }
            .card-title {
                color: #6b7280;
                font-size: 0.5rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .dark .card-title {
                color: #d1d5db;
            }
            .card-value {
                font-size: 0.8rem;
                font-weight: 700;
                color: #1f2937;
            }
            .dark .card-value {
                color: #e5e7eb;
            }
            .time-period {
                color: #9ca3af;
                font-size: 0.6rem;
            }
            .card-divider {
                border-top: 1px solid rgba(196, 159, 62, 0.34);
                margin: 16px 0;
            }
            .dark .time-period {
                color: #a1a1aa;
            }
            .card-tooltip {
                visibility: hidden;
                width: 220px;
                background-color: #27272a;
                color: #ffffff;
                text-align: left;
                border-radius: 6px;
                padding: 8px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                margin-left: -110px;
                opacity: 0;
                transition: opacity 0.3s, transform 0.3s;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                font-size: 0.65rem;
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
                border-color: #27272a transparent transparent transparent;
            }
            .card-container:hover .card-tooltip {
                visibility: visible;
                opacity: 1;
                transform: translateY(0);
            }
        </style>
        <div class="container mx-auto p-1" wire:poll.4s="fetchData">
            <h1 class="widget-title">Financial Performance Overview</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4" style="margin-top: 23px">
                @php
                    $cards = [
                        'transactions_value' => [
                            'title' => 'Transactions Value',
                            'icon' => 'fas fa-money-bill-wave',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'currency' => $defaultCurrency,
                            'description' => 'Total value of transactions this month'
                        ],
                        'transactions_count' => [
                            'title' => 'Transactions Count',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Number of transactions this month'
                        ],
                        'new_accounts' => [
                            'title' => 'New Accounts',
                            'icon' => 'fas fa-user-plus',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'New accounts opened this month'
                        ],
                        'active_accounts' => [
                            'title' => 'Active Accounts',
                            'icon' => 'fas fa-user-check',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Number of active accounts this month'
                        ],
                        'avg_loan_value' => [
                            'title' => 'Avg Loan Value',
                            'icon' => 'fas fa-hand-holding-usd',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'currency' => $defaultCurrency,
                            'description' => 'Average loan value this month (Dummy Data)'
                        ],
                    ];
                @endphp
                @foreach ($cards as $key => $card)
                    <div class="card-container relative">
                        <div class="card p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h5 class="card-title mb-1">{{ $card['title'] }}</h5>
                                    <h2 class="card-value" wire:key="count-{{ $key }}">
                                        {{ number_format($monthlyComparisons[$key]['current'], 2) }} {{ $card['currency'] ?? '' }}
                                    </h2>
                                </div>
                                <div class="icon-bg {{ $card['iconBgColor'] }}">
                                    <i class="{{ $card['icon'] }} {{ $card['iconColor'] }} text-xs"></i>
                                </div>
                            </div>
                            <div class="card-divider"></div>
                            <div class="flex items-center">
                                <span class="percentage-badge {{ $monthlyComparisons[$key]['is_increase'] ? 'bg-green-100 dark:bg-green-700 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-700 text-red-800 dark:text-red-200' }} mr-2">
                                    {{ $monthlyComparisons[$key]['is_increase'] ? '+' : '-' }}{{ number_format(abs($monthlyComparisons[$key]['percentage_change']), 2) }}%
                                </span>
                                <span class="time-period">vs Last Month</span>
                            </div>
                        </div>
                        <div class="card-tooltip">
                            <div class="font-semibold mb-1">{{ $card['title'] }}</div>
                            <div>{{ $card['description'] }}</div>
                            <div class="mt-2 text-sm">
                                <strong>Month-over-Month Change:</strong> This percentage represents the growth or decline compared to the previous month.
                            </div>
                            @if($key === 'avg_loan_value')
                                <div class="mt-2 text-sm text-yellow-500">
                                    Note: This is dummy data for demonstration purposes.
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
