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
            .filter-container {
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                position: relative;
                overflow: hidden;
                margin-bottom: 1rem;
            }
            .dark .filter-container {
                background-color: #27272a;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.6), 0 2px 4px -1px rgba(0, 0, 0, 0.4);
            }
            .filter-header {
                padding: 1rem;
                border-bottom: 1px solid rgba(196, 159, 62, 0.34);
            }
            .filter-title {
                color: #1f2937;
                font-size: 0.8rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .dark .filter-title {
                color: #e5e7eb;
            }
            .filter-button {
                background-color: #f3f4f6;
                color: #4b5563;
                border-radius: 6px;
                padding: 0.25rem 0.5rem;
                font-size: 0.6rem;
                font-weight: 500;
                transition: background-color 0.3s;
            }
            .dark .filter-button {
                background-color: #374151;
                color: #d1d5db;
            }
            .filter-button:hover {
                background-color: #e5e7eb;
            }
            .dark .filter-button:hover {
                background-color: #4b5563;
            }
            .filter-content {
                padding: 1rem;
            }
            .date-input {
                width: 100%;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                padding: 0.25rem 0.5rem;
                font-size: 0.6rem;
                color: #4b5563;
            }
            .dark .date-input {
                background-color: #18181a;
                border-color: #cf9f39;
                color: #d1d5db;
            }
            .action-button {
                padding: 0.25rem 0.5rem;
                border-radius: 6px;
                font-size: 0.6rem;
                font-weight: 500;
                transition: background-color 0.3s;
            }
            .reset-button {
                background-color: #f3f4f6;
                color: #4b5563;
            }
            .dark .reset-button {
                background-color: #374151;
                color: #d1d5db;
            }
            .apply-button {
                background-color: #cf9f39;
                color: white;
            }
            .dark .apply-button {
                background-color: #cf9f39;
            }
            .reset-button:hover {
                background-color: #e5e7eb;
            }
            .dark .reset-button:hover {
                background-color: #4b5563;
            }
            .apply-button:hover {
                background-color: #cf9f39;
            }
            .dark .apply-button:hover {
                background-color: rgba(196, 159, 62, 0.34);
            }

        </style>
        <div x-data="{ showFilters: false }" class="container mx-auto p-1">
            <div x-data="{ showFilters: false }" class="filter-container">
                <div class="filter-header flex justify-between items-center">
                    <h2 class="filter-title">Financial Performance Based on date</h2>
                    <button @click="showFilters = !showFilters" class="filter-button flex items-center">
                        <i class="fas fa-filter mr-1"></i>
                        <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                    </button>
                </div>

                <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="filter-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="startDate" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <div class="relative">
                                <input type="date" id="startDate" wire:model="startDate" class="date-input">
                            </div>
                        </div>
                        <div>
                            <label for="endDate" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <div class="relative">
                                <input type="date" id="endDate" wire:model="endDate" class="date-input">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button wire:click="resetFilter" class="action-button reset-button">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </button>
                        <button wire:click="applyFilter" class="action-button apply-button">
                            <i class="fas fa-check mr-1"></i> Apply
                        </button>
                    </div>
                </div>
            </div>


            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $cards = [
                        'total_transaction_count' => [
                            'title' => 'Total Transaction Count',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBgColor' => 'bg-yellow-100 dark:bg-yellow-900',
                            'iconColor' => 'text-yellow-500 dark:text-yellow-400',
                            'amount' => number_format($totalTransactionCount),
                            'description' => 'Total number of transactions'
                        ],
                        'total_transaction_value' => [
                            'title' => 'Total Transaction Value',
                            'icon' => 'fas fa-money-bill-wave',
                            'iconBgColor' => 'bg-green-100 dark:bg-green-900',
                            'iconColor' => 'text-green-500 dark:text-green-400',
                            'amount' => number_format($totalTransactionValue, 2),
                            'currency' => $defaultCurrency,
                            'description' => 'Total value of all transactions'
                        ],
                        'active_accounts' => [
                            'title' => 'Active Accounts',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-blue-100 dark:bg-blue-900',
                            'iconColor' => 'text-blue-500 dark:text-blue-400',
                            'amount' => number_format($activeAccounts),
                            'description' => 'Number of active accounts'
                        ],
                        'new_accounts' => [
                            'title' => 'New Accounts Opened',
                            'icon' => 'fas fa-user-plus',
                            'iconBgColor' => 'bg-purple-100 dark:bg-purple-900',
                            'iconColor' => 'text-purple-500 dark:text-purple-400',
                            'amount' => number_format($newAccountsOpened),
                            'description' => 'Number of new accounts opened'
                        ],
                        'avg_transaction_value' => [
                            'title' => 'Avg Transaction Value',
                            'icon' => 'fas fa-chart-bar',
                            'iconBgColor' => 'bg-red-100 dark:bg-red-900',
                            'iconColor' => 'text-red-500 dark:text-red-400',
                            'amount' => number_format($avgTransactionValue, 2),
                            'currency' => $defaultCurrency,
                            'description' => 'Average value per transaction'
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
                            @if($isFiltered && isset($stats[$key]) && $stats[$key]['percentageChange'] !== null)
                                <div class="card-divider"></div>
                                <div class="flex items-center">
                                    <span class="percentage-badge {{ $stats[$key]['isGrowth'] ? 'bg-green-100 dark:bg-green-700 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-700 text-red-800 dark:text-red-200' }} mr-2">
                                        {{ $stats[$key]['isGrowth'] ? '+' : '-' }}{{ number_format(abs($stats[$key]['percentageChange']), 2) }}%
                                    </span>
                                    <span class="time-period">vs Previous Period</span>
                                </div>
                            @endif
                        </div>
                        <div class="card-tooltip">
                            <div class="font-semibold mb-1">{{ $card['title'] }}</div>
                            <div>{{ $card['description'] }}</div>
                            @if($isFiltered && isset($stats[$key]) && $stats[$key]['percentageChange'] !== null)
                                <div class="mt-2 text-sm">
                                    <strong>Change:</strong> This percentage represents the growth or decline compared to the previous period of equal length.
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
