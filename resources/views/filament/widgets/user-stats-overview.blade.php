<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

        <style>
            .card {
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
                font-size: 0.4rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .card-value {
                font-size: 1.1rem;
                font-weight: 700;
            }
            .time-period {
                font-size: 0.6rem;
            }
            .card-tooltip {
                visibility: hidden;
                width: 200px;
                text-align: left;
                border-radius: 6px;
                padding: 8px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                margin-left: -100px;
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

            /* Light theme styles */
            .light-theme .card {
                background-color: white;
            }
            .light-theme .card-title {
                color: #6b7280; /* Tailwind gray-500 */
            }
            .light-theme .card-value {
                color: #1f2937; /* Tailwind gray-800 */
            }
            .light-theme .time-period {
                color: #9ca3af; /* Tailwind gray-400 */
            }
            .light-theme .card-tooltip {
                background-color: #333333;
                color: #ffffff;
            }
            .light-theme .card-tooltip::after {
                border-color: #333333 transparent transparent transparent;
            }
            .light-theme .tooltip-title {
                color: #f3f4f6; /* Tailwind gray-100 */
            }
            .light-theme .tooltip-description {
                color: #d1d5db; /* Tailwind gray-300 */
            }

            /* Dark theme styles */
            .dark-theme .card {
                background-color: #27272a; /* Updated dark background color */
            }
            .dark-theme .card-title {
                color: #9ca3af; /* Tailwind gray-400 */
            }
            .dark-theme .card-value {
                color: #f3f4f6; /* Tailwind gray-100 */
            }
            .dark-theme .time-period {
                color: #6b7280; /* Tailwind gray-500 */
            }
            .dark-theme .card-tooltip {
                background-color: #f3f4f6; /* Tailwind gray-100 */
                color: #1f2937; /* Tailwind gray-800 */
                box-shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.1), 0 2px 4px -1px rgba(255, 255, 255, 0.06);
            }
            .dark-theme .card-tooltip::after {
                border-color: #f3f4f6 transparent transparent transparent;
            }
            .dark-theme .tooltip-title {
                color: #1f2937; /* Tailwind gray-800 */
            }
            .dark-theme .tooltip-description {
                color: #4b5563; /* Tailwind gray-600 */
            }
        </style>

        <div class="container mx-auto p-4" wire:poll.4s="calculateStats" id="theme-container">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $cards = [
                        'registered' => [
                            'title' => 'Total Customers',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-yellow-100',
                            'iconColor' => 'text-yellow-600',
                            'description' => 'Total number of users who have registered on the Simba Money platform.'
                        ],
                        'active' => [
                            'title' => 'Active Customers',
                            'icon' => 'fas fa-user-check',
                            'iconBgColor' => 'bg-blue-100',
                            'iconColor' => 'text-blue-600',
                            'description' => 'Users who have engaged in any revenue-generating activity within the last 30 days.'
                        ],
                        'inactive' => [
                            'title' => 'Inactive Customers',
                            'icon' => 'fas fa-user-slash',
                            'iconBgColor' => 'bg-red-100',
                            'iconColor' => 'text-red-600',
                            'description' => 'Users who have not engaged in any revenue-generating activities for more than 30 days.'
                        ],
                        'churn' => [
                            'title' => 'Churn Customers',
                            'icon' => 'fas fa-user-slash',
                            'iconBgColor' => 'bg-orange-100',
                            'iconColor' => 'text-orange-600',
                            'description' => 'Users who have stopped using the platform and whose last activity was more than 30 days ago.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'Avg Trans Value/Day',
                            'icon' => 'fas fa-dollar-sign',
                            'iconBgColor' => 'bg-green-100',
                            'iconColor' => 'text-green-600',
                            'description' => 'Average monetary value of all transactions processed per day on the platform.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'Avg Trans/Customer',
                            'icon' => 'fas fa-exchange-alt',
                            'iconBgColor' => 'bg-purple-100',
                            'iconColor' => 'text-purple-600',
                            'description' => 'Average number of transactions made by each customer on the platform.'
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
                                    <i class="{{ $card['icon'] }} {{ $card['iconColor'] }} text-xs"></i>
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
                                <span class="time-period">WoW</span>
                            </div>
                        </div>
                        <div class="card-tooltip">
                            <div class="tooltip-title">{{ $card['title'] }}</div>
                            <div class="tooltip-description">{{ $card['description'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <script>
            function setTheme() {
                const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const container = document.getElementById('theme-container');
                container.classList.remove('light-theme', 'dark-theme');
                container.classList.add(isDarkMode ? 'dark-theme' : 'light-theme');
            }

            // Set theme on load
            setTheme();

            // Listen for changes in color scheme preference
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', setTheme);
        </script>
    </x-filament::section>
</x-filament-widgets::widget>
