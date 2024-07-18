<x-filament-widgets::widget>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        .gradient-airtel { background: linear-gradient(135deg, #f43f5e, #f97316); }
        .gradient-tigo { background: linear-gradient(135deg, #10b981, #3b82f6); }
        .gradient-vodacom { background: linear-gradient(135deg, #6366f1, #141415); }
        .gradient-tembo { background: linear-gradient(135deg, #ff9a9e, #141415); }
        .gradient-terapay { background: linear-gradient(135deg, #a1c4fd, #141415); }
        .gradient-cellulant { background: linear-gradient(135deg, #fbc2eb, #141415); }
    </style>

    <x-filament::section>
        <div class="container px-2 py-8 mx-auto">
            <h6 class="text-white text-1xl font-bold mb-6 text-left">MNS Wallets</h6>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Airtel Wallet Balance Card -->
                <div class="transform hover:scale-105 transition duration-300">
                    <div class="p-4 rounded-lg gradient-airtel shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-white text-lg font-bold">Airtel Wallet</h2>
                            <i class='bx bx-wallet text-white text-2xl'></i>
                        </div>
                        <div class="flex space-x-4">
                            <div class="bg-white/20 p-2 rounded flex-1">
                                <p class="text-white text-xs mb-0.5">Collected</p>
                                <h3 class="text-white text-base font-bold">+ 150,000 TZS</h3>
                            </div>
                            <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                <p class="text-white text-xs mb-0.5">Disbursed</p>
                                <h3 class="text-white text-base font-bold">- 50,000 TZS</h3>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20 flex justify-between items-center">
                            <span class="text-white text-xs">Balance</span>
                            <span class="text-white text-base font-bold">100,000 TZS</span>
                        </div>
                    </div>
                </div>

                <!-- Tigo Wallet Balance Card -->
                <div class="transform hover:scale-105 transition duration-300">
                    <div class="p-4 rounded-lg gradient-tigo shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-white text-lg font-bold">Tigo Wallet</h2>
                            <i class='bx bx-wallet text-white text-2xl'></i>
                        </div>
                        <div class="flex space-x-4">
                            <div class="bg-white/20 p-2 rounded flex-1">
                                <p class="text-white text-xs mb-0.5">Collected</p>
                                <h3 class="text-white text-base font-bold">+ 200,000 TZS</h3>
                            </div>
                            <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                <p class="text-white text-xs mb-0.5">Disbursed</p>
                                <h3 class="text-white text-base font-bold">- 75,000 TZS</h3>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20 flex justify-between items-center">
                            <span class="text-white text-xs">Balance</span>
                            <span class="text-white text-base font-bold">125,000 TZS</span>
                        </div>
                    </div>
                </div>

                <!-- Vodacom Wallet Balance Card -->
                <div class="transform hover:scale-105 transition duration-300">
                    <div class="p-4 rounded-lg gradient-vodacom shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-white text-lg font-bold">Vodacom Wallet</h2>
                            <i class='bx bx-wallet text-white text-2xl'></i>
                        </div>
                        <div class="flex space-x-4">
                            <div class="bg-white/20 p-2 rounded flex-1">
                                <p class="text-white text-xs mb-0.5">Collected</p>
                                <h3 class="text-white text-base font-bold">+ 300,000 TZS</h3>
                            </div>
                            <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                <p class="text-white text-xs mb-0.5">Disbursed</p>
                                <h3 class="text-white text-base font-bold">- 100,000 TZS</h3>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20 flex justify-between items-center">
                            <span class="text-white text-xs">Balance</span>
                            <span class="text-white text-base font-bold">200,000 TZS</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
