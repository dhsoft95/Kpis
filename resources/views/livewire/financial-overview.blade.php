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

        <div class="container mx-auto p-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $cards = [
                        'total_transactions' => [
                            'title' => 'Total Transactions',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'amount' => number_format($totalTransactions),
                            'description' => 'Number of Transactions (since Inception)'
                        ],
                        'total_transaction_value' => [
                            'title' => 'Total Transaction Value',
                            'icon' => 'fas fa-money-bill-wave',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'amount' => number_format($totalTransactionValue, 2),
                            'currency' => $defaultCurrency,
                            'description' => 'Value of Transactions (since Inception)'
                        ],
                        'monthly_transactions' => [
                            'title' => 'Monthly Transactions',
                            'icon' => 'fas fa-calendar-alt',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'amount' => number_format($monthlyTransactions),
                            'description' => 'Number of Transactions (This Month)'
                        ],
                        'monthly_transaction_value' => [
                            'title' => 'Monthly Transaction Value',
                            'icon' => 'fas fa-chart-line',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'amount' => number_format($monthlyTransactionValue, 2),
                            'currency' => $defaultCurrency,
                            'description' => 'Value of Transactions (This Month)'
                        ],
                        'active_users' => [
                            'title' => 'Active Users',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]',
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'amount' => number_format($activeUsers),
                            'description' => 'Users who made a successful revenue-generating transaction (deposited, sent, or received) in the last 60 days'
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
                                        {{ $card['amount'] }} {{ $card['currency'] ?? '' }}
                                    </h2>
                                </div>
                                <div class="icon-bg {{ $card['iconBgColor'] }}">
                                    <i class="{{ $card['icon'] }} {{ $card['iconColor'] }} text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-tooltip">
                            <div class="font-semibold mb-1">{{ $card['title'] }}</div>
                            <div>{{ $card['description'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


    </x-filament::section>
</x-filament-widgets::widget>
