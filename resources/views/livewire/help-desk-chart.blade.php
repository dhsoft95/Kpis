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

        <div class="container mx-auto p-4" wire:poll.1s="calculateStats">
            @php
                // Define card configurations
                $cards = [
                    'chats' => ['title' => 'Chats', 'icon' => 'fas fa-comments', 'bgGradient' => 'from-blue-700 via-blue-500 to-blue-300', 'color' => 'blue'],
                    'whatsApp' => ['title' => 'WhatsApp Messages', 'icon' => 'fab fa-whatsapp', 'bgGradient' => 'from-green-700 via-green-500 to-green-300', 'color' => 'green'],
                    'faq' => ['title' => 'FAQ Views', 'icon' => 'fas fa-question-circle', 'bgGradient' => 'from-yellow-700 via-yellow-500 to-yellow-300', 'color' => 'yellow'],
                    'socialMedia' => ['title' => 'Social Media Interactions', 'icon' => 'fas fa-share-alt', 'bgGradient' => 'from-pink-700 via-pink-500 to-pink-300', 'color' => 'pink'],
                    'phoneCalls' => ['title' => 'Phone Calls', 'icon' => 'fas fa-phone', 'bgGradient' => 'from-red-700 via-red-500 to-red-300', 'color' => 'red'],
                    'email' => ['title' => 'Emails', 'icon' => 'fas fa-envelope', 'bgGradient' => 'from-indigo-700 via-indigo-500 to-indigo-300', 'color' => 'indigo'],
                ];

                // Dummy data for Help Desk metrics
                $stats['chats'] = ['value' => 850, 'percentageChange' => 3.2, 'isGrowth' => true];
                $stats['whatsApp'] = ['value' => 400, 'percentageChange' => -1.5, 'isGrowth' => false];
                $stats['faq'] = ['value' => 1200, 'percentageChange' => 7.5, 'isGrowth' => true];
                $stats['socialMedia'] = ['value' => 300, 'percentageChange' => 4.2, 'isGrowth' => true];
                $stats['phoneCalls'] = ['value' => 500, 'percentageChange' => -2.1, 'isGrowth' => false];
                $stats['email'] = ['value' => 750, 'percentageChange' => 1.8, 'isGrowth' => true];
            @endphp

            @foreach (array_chunk($cards, 3, true) as $row)
                <div class="flex flex-wrap -mx-4 mb-8">
                    @foreach ($row as $key => $card)
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
                                                {{ number_format($stats[$key]['value']) }}
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
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
