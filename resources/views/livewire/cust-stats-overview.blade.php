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
            .time-period {
                color: #64748b;
                font-size: 0.75rem;
                font-weight: 500;
            }
            .card-divider {
                border-top: 1px solid rgba(196, 159, 62, 0.34);
                margin: 16px 0;
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
                        'nps' => [
                            'title' => 'Net Promoter Score (NPS)',
                            'icon' => 'fas fa-chart-line',
                            'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Measure of customer loyalty and satisfaction. Ranges from -100 to 100.'
                        ],
                        'csat' => [
                            'title' => 'Customer Satisfaction (CSAT)',
                            'icon' => 'fas fa-smile',
                             'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Percentage of customers who rate their experience as satisfactory.'
                        ],
                        'ces' => [
                            'title' => 'Customer Effort Score (CES)',
                            'icon' => 'fas fa-tasks',
                             'iconBgColor' => 'bg-[rgba(220,169,21,0.2)] border border-[rgba(220,169,21,1)]', // Aloe color with low opacity for the background and full opacity for the border
                            'iconColor' => 'text-[rgba(220,169,21,1)]',
                            'description' => 'Average score of how much effort a customer has to exert to get an issue resolved or a request fulfilled.'
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
                                        {{ number_format($stats[$key]['value'], 2) }}{{ in_array($key, ['nps', 'csat']) ? '%' : '' }}
                                    </h2>
                                </div>
                                <div class="card-divider"></div>
                                <div class="icon-bg {{ $card['iconBgColor'] }}">
                                    <i class="{{ $card['icon'] }} {{ $card['iconColor'] }} text-xs"></i>
                                </div>
                            </div>
                            <div class="card-divider"></div>
                            <div class="flex items-center">
                             <span class="percentage-badge {{ $stats[$key]['isGrowth'] ? 'bg-green-100 !bg-opacity-30 text-green-800 !text-opacity-70 dark:bg-green-700 dark:!bg-opacity-30 dark:text-green-200 dark:!text-opacity-70' : 'bg-red-100 !bg-opacity-30 text-red-800 !text-opacity-70 dark:bg-red-700 dark:!bg-opacity-30 dark:text-red-200 dark:!text-opacity-70' }} mr-2">
                                {{ $stats[$key]['isGrowth'] ? '+' : '-' }}{{ number_format(abs($stats[$key]['percentageChange']), 2) }}%
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
