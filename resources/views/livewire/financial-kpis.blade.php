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
        </style>
        <div class="container mx-auto p-1" wire:poll.31s="mount">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $cards = [
                        'totalAmount' => ['title' => 'Total Amount', 'icon' => 'fas fa-dollar-sign', 'iconBgColor' => 'bg-green-100 dark:bg-green-700', 'iconColor' => 'text-green-600 dark:text-green-200'],
                        'totalFailedAmount' => ['title' => 'Total Failed Amount', 'icon' => 'fas fa-times-circle', 'iconBgColor' => 'bg-red-100 dark:bg-red-700', 'iconColor' => 'text-red-600 dark:text-red-200'],
                        'revenue' => ['title' => 'Revenue', 'icon' => 'fas fa-chart-line', 'iconBgColor' => 'bg-blue-100 dark:bg-blue-700', 'iconColor' => 'text-blue-600 dark:text-blue-200'],
                    ];
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="card p-3">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h5 class="card-title mb-1">{{ $card['title'] }}</h5>
                                <h2 class="card-value" wire:key="count-{{ $key }}">
                                    {{ number_format($stats[$key]['value']) }}
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
                @endforeach
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
