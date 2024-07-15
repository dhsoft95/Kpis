<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Include Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" />

        <style>
            /* Progress bar styles and animations */
            .progress-bar {
                width: 0;
                height: 100%;
                transition: width 0.6s ease;
                background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%);
                background-size: 40px 40px;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .progress-bar-animate {
                animation: progress-animation 0.6s ease forwards;
            }
            @keyframes progress-animation {
                from { width: 0; }
                to { width: var(--progress-width); }
            }

            .progress-text {
                position: absolute;
                color: white;
                font-size: 0.75rem;
                font-weight: bold;
                z-index: 1;
            }
        </style>

        <div class="container mx-auto p-4" wire:poll.4s="calculateStats">
            <div class="flex flex-wrap -mx-4">
                @php
                    // Define card configurations with updated gradient colors
                    $cards = [
                        'customerStratification' => ['title' => 'Customer Stratification', 'icon' => 'fas fa-users', 'bgGradient' => 'from-blue-700 via-blue-500 to-blue-300', 'color' => 'blue'],
                        'nps' => ['title' => 'Net Promoter Score (NPS)', 'icon' => 'fas fa-chart-line', 'bgGradient' => 'from-green-600 via-green-500 to-green-400', 'color' => 'green'],
                        'ces' => ['title' => 'Customer Effort Score (CES)', 'icon' => 'fas fa-tasks', 'bgGradient' => 'from-yellow-600 via-yellow-500 to-yellow-400', 'color' => 'yellow'],
                    ];

                    // Dummy data for all metrics (now in percentages)
                    $stats['customerStratification'] = ['value' => 85, 'percentageChange' => 3.5, 'isGrowth' => true];
                    $stats['nps'] = ['value' => 65, 'percentageChange' => 5.2, 'isGrowth' => true];
                    $stats['ces'] = ['value' => 76, 'percentageChange' => -2.5, 'isGrowth' => false]; // Converted to percentage (3.8 out of 5 scale)
                @endphp

                @foreach ($cards as $key => $card)
                    <div class="w-full md:w-1/3 px-4 mb-4">
                        <div class="bg-gradient-to-r {{ $card['bgGradient'] }} text-white rounded-lg shadow-lg p-3 h-35 flex flex-col relative">
                            <!-- Card icon in the top right corner -->
                            <div class="absolute top-2 right-2 text-4xl opacity-20">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>
                            <div class="pt-8 flex-grow">
                                <!-- Card title -->
                                <h5 class="sm:text-sm font-semibold mb-1">{{ $card['title'] }}</h5>
                                <div class="flex items-center mb-1">
                                    <div class="w-2/3">
                                        <!-- Card count or value -->
                                        <h2 class="text-2xl font-bold mb-0" wire:key="count-{{ $key }}">
                                            {{ number_format($stats[$key]['value'], 1) }}%
                                        </h2>
                                    </div>
                                    <div class="w-1/3 text-right">
                                        <!-- Percentage change and growth icon -->
                                        <span class="text-white text-start">
                                            @php
                                                $percentageChange = $stats[$key]['percentageChange'];
                                                $formattedPercentage = number_format(abs($percentageChange), 2);
                                                $isGrowth = $stats[$key]['isGrowth'];
                                            @endphp
                                            {{ $isGrowth ? '+' : '-' }}{{ $formattedPercentage }}%
                                            <i class="fa fa-arrow-{{ $isGrowth ? 'up' : 'down' }} ml-1"></i>
                                        </span>
                                        <span class="text-xs block">(WoW)</span>
                                    </div>
                                </div>
                                <!-- Progress bar -->
                                <div class="relative pt-1">
                                    <div class="w-full bg-gray-300 rounded-full h-1">
                                        <div class="bg-{{ $card['color'] }}-500 h-1 rounded-full progress-bar progress-bar-animate"
                                             wire:key="progress-{{ $key }}"
                                             style="--progress-width: {{ min(100, abs($percentageChange)) }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
