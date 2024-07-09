<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Include Tailwind CSS (if not already included in your project) -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />

        <style>
            /* Custom styles for progress bar animations */
            .progress-bar {
                width: 0;
                height: 100%;
                transition: width 0.6s ease;
                background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%, transparent);
                background-size: 40px 40px;
                animation: progress-animation 0.6s ease forwards;
            }

            @keyframes progress-animation {
                from { width: 0; }
                to { width: var(--progress-width); }
            }

            .progress-bar-animate {
                animation: progress-animation 0.6s ease forwards;
            }
        </style>

        <div class="container mx-auto p-4">
            <!-- Title added here -->
            <h2 class="text-2xl font-bold mb-4 text-center">Total Interaction Per Channel </h2>

            <div class="flex flex-wrap -mx-4">
                <!-- Card 1: Total Interactions -->
                <div class="w-full md:w-1/4 px-4 mb-4">
                    <div class="bg-gradient-to-r from-blue-700 to-blue-400 text-white rounded-lg shadow-lg p-3 h-35 flex flex-col relative">
                        <div class="absolute top-2 right-2 text-4xl opacity-20">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="pt-8 flex-grow">
                            <h5 class="text-sm font-semibold mb-1">Total Interactions</h5>
                            <div class="flex items-center mb-1">
                                <div class="w-2/3">
                                    <h2 class="text-2xl font-bold mb-0">1,250</h2>
                                </div>
                                <div class="w-1/3 text-right">
                                    <span class="text-white text-lg">+5% <i class="fa fa-arrow-up"></i></span>
                                </div>
                            </div>
                            <div class="relative pt-1">
                                <div class="w-full bg-gray-300 rounded-full h-1">
                                    <div class="bg-blue-500 h-1 rounded-full progress-bar progress-bar-animate" style="--progress-width: 85%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Chat -->
                <div class="w-full md:w-1/4 px-4 mb-4">
                    <div class="bg-gradient-to-r from-green-700 to-green-400 text-white rounded-lg shadow-lg p-3 h-35 flex flex-col relative">
                        <div class="absolute top-2 right-2 text-4xl opacity-20">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <div class="pt-8 flex-grow">
                            <h5 class="text-sm font-semibold mb-1">Chat</h5>
                            <div class="flex items-center mb-1">
                                <div class="w-2/3">
                                    <h2 class="text-2xl font-bold mb-0">450</h2>
                                </div>
                                <div class="w-1/3 text-right">
                                    <span class="text-white text-lg">+3% <i class="fa fa-arrow-up"></i></span>
                                </div>
                            </div>
                            <div class="relative pt-1">
                                <div class="w-full bg-gray-300 rounded-full h-1">
                                    <div class="bg-green-500 h-1 rounded-full progress-bar progress-bar-animate" style="--progress-width: 70%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: WhatsApp -->
                <div class="w-full md:w-1/4 px-4 mb-4">
                    <div class="bg-gradient-to-r from-purple-700 to-purple-400 text-white rounded-lg shadow-lg p-3 h-35 flex flex-col relative">
                        <div class="absolute top-2 right-2 text-4xl opacity-20">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="pt-8 flex-grow">
                            <h5 class="text-sm font-semibold mb-1">WhatsApp</h5>
                            <div class="flex items-center mb-1">
                                <div class="w-2/3">
                                    <h2 class="text-2xl font-bold mb-0">350</h2>
                                </div>
                                <div class="w-1/3 text-right">
                                    <span class="text-white text-lg">+7% <i class="fa fa-arrow-up"></i></span>
                                </div>
                            </div>
                            <div class="relative pt-1">
                                <div class="w-full bg-gray-300 rounded-full h-1">
                                    <div class="bg-purple-500 h-1 rounded-full progress-bar progress-bar-animate" style="--progress-width: 60%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Calls -->
                <div class="w-full md:w-1/4 px-4 mb-4">
                    <div class="bg-gradient-to-r from-red-700 to-red-400 text-white rounded-lg shadow-lg p-3 h-35 flex flex-col relative">
                        <div class="absolute top-2 right-2 text-4xl opacity-20">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="pt-8 flex-grow">
                            <h5 class="text-sm font-semibold mb-1">Calls</h5>
                            <div class="flex items-center mb-1">
                                <div class="w-2/3">
                                    <h2 class="text-2xl font-bold mb-0">450</h2>
                                </div>
                                <div class="w-1/3 text-right">
                                    <span class="text-white text-lg">+2% <i class="fa fa-arrow-up"></i></span>
                                </div>
                            </div>
                            <div class="relative pt-1">
                                <div class="w-full bg-gray-300 rounded-full h-1">
                                    <div class="bg-red-500 h-1 rounded-full progress-bar progress-bar-animate" style="--progress-width: 75%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
