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
                max-width: 350px;
            }
            .dark .card {
                background-color: #1f2937;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.6), 0 2px 4px -1px rgba(0, 0, 0, 0.4);
            }
            .card::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: #172554;
            }
            .dark .card::after {
                background: #60a5fa;
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
            .dark .time-period {
                color: #a1a1aa;
            }
            .card-tooltip {
                visibility: hidden;
                width: 200px;
                background-color: #27272a;
                color: #ffffff;
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
                border-color: #27272a transparent transparent transparent;
            }
            .card-container:hover .card-tooltip {
                visibility: visible;
                opacity: 1;
                transform: translateY(0);
            }
        </style>

        <div class="container mx-auto p-1" wire:poll.4s="calculateStats">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $cards = [
                        'customerStratification' => [
                            'title' => 'Customer Stratification',
                            'icon' => 'fas fa-users',
                            'iconBgColor' => 'bg-blue-100 dark:bg-blue-700',
                            'iconColor' => 'text-blue-600 dark:text-blue-200',
                            'description' => 'Percentage of customers categorized into different segments based on their value to the company.'
                        ],
                        'nps' => [
                            'title' => 'Net Promoter Score (NPS)',
                            'icon' => 'fas fa-chart-line',
                            'iconBgColor' => 'bg-green-100 dark:bg-green-700',
                            'iconColor' => 'text-green-600 dark:text-green-200',
                            'description' => 'Measure of customer loyalty and satisfaction. Ranges from -100 to 100.'
                        ],
                        'ces' => [
                            'title' => 'Customer Effort Score (CES)',
                            'icon' => 'fas fa-tasks',
                            'iconBgColor' => 'bg-yellow-100 dark:bg-yellow-700',
                            'iconColor' => 'text-yellow-600 dark:text-yellow-200',
                            'description' => 'Measure of how much effort a customer has to exert to get an issue resolved or a request fulfilled.'
                        ],
                    ];

                    // Dummy data for all metrics (now in percentages)
                    $stats['customerStratification'] = ['value' => 85, 'percentageChange' => 3.5, 'isGrowth' => true];
                    $stats['nps'] = ['value' => 65, 'percentageChange' => 5.2, 'isGrowth' => true];
                    $stats['ces'] = ['value' => 76, 'percentageChange' => -2.5, 'isGrowth' => false];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card-container relative">
                        <div class="card p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h5 class="card-title mb-1">{{ $card['title'] }}</h5>
                                    <h2 class="card-value" wire:key="count-{{ $key }}">
                                        {{ number_format($stats[$key]['value'], 1) }}%
                                    </h2>
                                </div>
                                <div class="icon-bg {{ $card['iconBgColor'] }}">
                                    <i class="{{ $card['icon'] }} {{ $card['iconColor'] }} text-xs"></i>
                                </div>
                            </div>
                            <div class="flex items-center">
                                @php
                                    $percentageChange = $stats[$key]['percentageChange'];
                                    $formattedPercentage = number_format(abs($percentageChange), 2);
                                    $isGrowth = $stats[$key]['isGrowth'];
                                    $changeColor = $isGrowth ? 'bg-green-100 dark:bg-green-700 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-700 text-red-800 dark:text-red-200';
                                @endphp
                                <span class="percentage-badge {{ $changeColor }} mr-2">
                                    {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                                </span>
                                <span class="time-period">WoW</span>
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
