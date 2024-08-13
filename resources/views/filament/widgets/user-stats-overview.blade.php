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
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                position: relative;
                overflow: hidden;
                max-width: 100%;
                margin: 0 auto;
                padding: 16px;
                transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            }
            .time-period {
                color: #64748b;
                font-size: 0.75rem;
                font-weight: 500;
            }
            .card-divider {
                border-top: 1px solid rgba(196, 159, 62, 0.34);
                margin: 16px 0;
            }
            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            }
            .dark .card {
                background-color: #27272a; /* Tailwind gray-800 for dark mode */
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.6), 0 2px 4px -1px rgba(0, 0, 0, 0.4);
            }
            .card::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                /*background: #172554; !* Tailwind blue-500 *!*/
            }
            .dark .card::after {
                /*background: #60a5fa; !* Tailwind blue-400 for dark mode *!*/
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
                color: #6b7280; /* Tailwind gray-500 */
                font-size: 0.5rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .dark .card-title {
                color: #d1d5db; /* Tailwind gray-300 for dark mode */
            }
            .card-value {
                font-size: 0.8rem;
                font-weight: 700;
                color: #1f2937; /* Tailwind gray-800 */
            }
            .dark .card-value {
                color: #e5e7eb; /* Tailwind gray-200 for dark mode */
            }
            .time-period {
                color: #9ca3af; /* Tailwind gray-400 */
                font-size: 0.6rem;
            }
            .dark .time-period {
                color: #a1a1aa; /* Tailwind gray-500 for dark mode */
            }
            .card-tooltip {
                visibility: hidden;
                width: 200px;
                background-color: #27272a;
                color: #ffffff !important;
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
                border-color: #ffffff transparent transparent transparent;
            }
            .card-container:hover .card-tooltip {
                visibility: visible;
                opacity: 1;
                transform: translateY(0);
            }
            .tooltip-title {
                font-weight: 500;
                margin-bottom: 3px;
                color: #4a5568;
            }
            .dark .tooltip-title {
                color: #a1a1aa !important; /* Tailwind gray-500 for dark mode */
            }
            .tooltip-description {
                color: #718096;
            }
            .dark .tooltip-description {
                color: #cbd5e1; /* Tailwind gray-300 for dark mode */
            }
            .route-card {
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                overflow: hidden;
                max-width: 350px;
                position: relative;
            }
            .dark .route-card {
                background-color: #27272a;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.6), 0 2px 4px -1px rgba(0, 0, 0, 0.4);
            }

            .route-title {
                color: #1f2937;
                font-size: 0.6rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .dark .route-title {
                color: #d1d5db;
            }
            .route-item {
                font-size: 0.75rem;
                color: #4b5563;
                padding: 0.75rem 0;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .dark .route-item {
                color: #e5e7eb;
                border-bottom-color: #374151;
            }
            .route-item:last-child {
                border-bottom: none;
            }
            .transfer-count {
                font-weight: 600;
                color: #27272a;
                background-color: #eff6ff;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 0.7rem;
            }
            .dark .transfer-count {
                color: #27272a;
                background-color: #27272a;
            }
            .route-icon {
                margin-right: 8px;
                color: #27272a;
            }
            .dark .route-icon {
                color: #9ca3af;
            }

            .mno-card {
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                overflow: hidden;
                max-width: 350px;
                position: relative;
            }
            .dark .mno-card {
                background-color: #1f2937;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.6), 0 2px 4px -1px rgba(0, 0, 0, 0.4);
            }
            .mno-title {
                color: #1f2937;
                font-size: 0.6rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .dark .mno-title {
                color: #d1d5db;
            }
            .mno-item {
                font-size: 0.75rem;
                color: #4b5563;
                padding: 0.75rem 0;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .dark .mno-item {
                color: #e5e7eb;
                border-bottom-color: #374151;
            }
            .mno-item:last-child {
                border-bottom: none;
            }
            .transfer-count {
                font-weight: 600;
                color: #27272a;
                background-color: #eff6ff;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 0.7rem;
            }
            .dark .transfer-count {
                color: #60a5fa;
                background-color: #1e3a8a;
            }
            .mno-icon {
                width: 24px;
                height: 24px;
                margin-right: 8px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 0.6rem;
            }


        </style>

        <div class="container mx-auto p-1" wire:poll.2s="calculateStats">

            <h3 class="text-xs font-medium mb-2" style="color: #DCA915;">Customer Metrics</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $cards = [
                       'totalSuccess' => [
                            'title' => 'Total Success',
                            'icon' => 'fas fa-check-circle',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Total number of successful transactions.'
                        ],
                        'failed' => [
                            'title' => 'Failed Transactions',
                            'icon' => 'fas fa-times-circle',
                              'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Total number of failed transactions.'
                        ],
                        'pending' => [
                            'title' => 'Pending Transactions',
                            'icon' => 'fas fa-clock',
                             'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Total number of pending transactions.'
                        ],
                        'registered' => [
                            'title' => 'Total Customers',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Total number of users who have registered on the Simba Money platform.'
                        ],
                        'active' => [
                            'title' => 'Active Customers',
                            'icon' => 'fas fa-user-check',
                             'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Users who have engaged in any revenue-generating activity within the last 30 days.'
                        ],
                        'inactive' => [
                            'title' => 'Inactive Customers',
                            'icon' => 'fas fa-user-slash',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Users who have not engaged in any revenue-generating activities for more than 30 days.'
                        ],
                        'churn' => [
                            'title' => 'Churn Customers',
                            'icon' => 'fas fa-user-slash',
                             'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Users who have stopped using the platform and whose last activity was more than 30 days ago.'
                        ],
                        'avgValuePerDay' => [
                            'title' => 'Avg Trans Value/Day',
                            'icon' => 'fas fa-dollar-sign',
                             'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Average monetary value of all transactions processed per day on the platform.'
                        ],
                        'avgTransactionPerCustomer' => [
                            'title' => 'Avg Trans/Customer',
                            'icon' => 'fas fa-exchange-alt',
                             'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
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
                            <div class="card-divider"></div>
                            <div class="flex items-center">
                                @php
                                    $percentageChange = $stats[$key]['percentageChange'] ?? 0;
                                    $formattedPercentage = number_format(abs($percentageChange), 0);
                                    $isGrowth = $stats[$key]['isGrowth'] ?? false;
                                    $changeColor = $isGrowth ? 'bg-green-100 dark:bg-green-700 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-700 text-red-800 dark:text-red-200';
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
                <div style="margin-top: 15px"></div>
            </div>
        </div>
{{--        <div class="flex flex-row space-x-3" style="margin-top: 20px;">--}}
{{--            <div class="route-card p-4 w-full md:w-1/2">--}}
{{--                <h3 class="route-title mb-4">Popular Transfer Routes</h3>--}}
{{--                <div class="space-y-0">--}}
{{--                    @foreach($this->getPopularTransfersrouter as $transfers)--}}
{{--                        <div class="mno-item flex justify-between">--}}
{{--                            <span><i class="fas fa-exchange-alt route-icon"></i>{{ $transfers['route'] }}</span>--}}
{{--                            <span class="transfer-count">{{ number_format($transfers['count']) }} transfers</span>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="mno-card p-4 w-full md:w-1/2">--}}
{{--                <h3 class="mno-title mb-4">Popular Transfers to MNOs</h3>--}}
{{--                <div class="space-y-2">--}}
{{--                    @foreach($this->popularTransfers as $transfer)--}}
{{--                        <div class="mno-item flex justify-between">--}}
{{--                            <span><i class="fas fa-exchange-alt route-icon"></i>{{ $transfer['route'] }}</span>--}}
{{--                            <span class="transfer-count">{{ number_format($transfer['count']) }} transfers</span>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

    </x-filament::section>


</x-filament-widgets::widget>
