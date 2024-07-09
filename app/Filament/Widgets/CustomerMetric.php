<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS (if not already included in your project) -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />

        <style>
            /* Custom styles for progress bar animations */
            .progress-bar {
                width: 0; /* Start width at 0 */
                height: 100%;
                transition: width 0.6s ease;
                background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%, transparent);
                background-size: 40px 40px;
                animation: progress-animation 0.6s ease forwards;
            }

            /* Define animation for progress bar width */
            @keyframes progress-animation {
                from { width: 0; }
                to { width: var(--progress-width); }
            }

            .progress-bar-animate {
                animation: progress-animation 0.6s ease forwards;
            }
        </style>

        <div class="container mx-auto p-4">
            <!-- Flexbox layout for two cards per row -->
            <div class="flex flex-wrap -mx-4">
                <!-- Card 1: Customer Satisfaction Score (CSAT) -->
                <div class="w-full md:w-1/2 px-4 mb-4">
                    <div class="bg-gradient-to-r from-purple-700 to-purple-400 text-white rounded-lg shadow-lg p-3 h-35 flex flex-col relative">
                        <!-- Icon with large size and low opacity background -->
                        <div class="absolute top-2 right-2 text-4xl opacity-20">
                            <i class="fas fa-smile"></i>
                        </div>
                        <div class="pt-8 flex-grow">
                            <h5 class="text-sm font-semibold mb-1">Customer Satisfaction Score (CSAT)</h5>
                            <div class="flex items-center mb-1">
                                <div class="w-2/3">
                                    <h2 class="text-2xl font-bold mb-0">85%</h2>
                                </div>
                                <div class="w-1/3 text-right">
                                    <span class="text-white text-lg">+3% <i class="fa fa-arrow-up"></i></span>
                                </div>
                            </div>
                            <!-- Progress Bar with animation -->
                            <div class="relative pt-1">
                                <div class="w-full bg-gray-300 rounded-full h-1">
                                    <div class="bg-purple-500 h-1 rounded-full progress-bar progress-bar-animate" style="--progress-width: 85%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Average Value of Transaction Per Day -->
                <div class="w-full md:w-1/2 px-4 mb-4">
                    <div class="bg-gradient-to-r from-yellow-700 to-yellow-400 text-white rounded-lg shadow-lg p-3 h-35 flex flex-col relative">
                        <!-- Icon with large size and low opacity background -->
                        <div class="absolute top-2 right-2 text-4xl opacity-20">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="pt-8 flex-grow">
                            <h5 class="text-sm font-semibold mb-1">Average Value of Transaction Per Day</h5>
                            <div class="flex items-center mb-1">
                                <div class="w-2/3">
                                    <h2 class="text-2xl font-bold mb-0">$200</h2>
                                </div>
                                <div class="w-1/3 text-right">
                                    <span class="text-white text-lg">+1% <i class="fa fa-arrow-up"></i></span>
                                </div>
                            </div>
                            <!-- Progress Bar with animation -->
                            <div class="relative pt-1">
                                <div class="w-full bg-gray-300 rounded-full h-1">
                                    <div class="bg-yellow-500 h-1 rounded-full progress-bar progress-bar-animate" style="--progress-width: 50%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Average Transaction Per Customer -->
                <div class="w-full md:w-1/2 px-4 mb-4">
                    <div class="bg-gradient-to-r from-teal-700 to-teal-400 text-white rounded-lg shadow-lg p-3 h-35 flex flex-col relative">
                        <!-- Icon with large size and low opacity background -->
                        <div class="absolute top-2 right-2 text-4xl opacity-20">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="pt-8 flex-grow">
                            <h5 class="text-sm font-semibold mb-1">Average Transaction Per Customer</h5>
                            <div class="flex items-center mb-1">
                                <div class="w-2/3">
                                    <h2 class="text-2xl font-bold mb-0">15</h2>
                                </div>
                                <div class="w-1/3 text-right">
                                    <span class="text-white text-lg">+2% <i class="fa fa-arrow-up"></i></span>
                                </div>
                            </div>
                            <!-- Progress Bar with animation -->
                            <div class="relative pt-1">
                                <div class="w-full bg-gray-300 rounded-full h-1">
                                    <div class="bg-teal-500 h-1 rounded-full progress-bar progress-bar-animate" style="--progress-width: 70%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
