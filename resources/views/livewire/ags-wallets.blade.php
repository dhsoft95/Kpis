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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Tembo Pay Wallet Balance Card -->
                <div class="transform hover:scale-105 transition duration-300">
                    <div class="p-4 rounded-lg gradient-tembo shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-white text-lg font-bold">Tembo Pay</h2>
                            <i class='bx bx-wallet text-white text-2xl'></i>
                        </div>
                        <div class="flex space-x-4">
                            <div class="bg-white/20 p-2 rounded flex-1">
                                <p class="text-white text-xs mb-0.5">Collected</p>
                                <h3 class="text-white text-base font-bold">+ 400,000 TZS</h3>
                            </div>
                            <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                <p class="text-white text-xs mb-0.5">Disbursed</p>
                                <h3 class="text-white text-base font-bold">- 150,000 TZS</h3>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20 flex justify-between items-center">
                            <span class="text-white text-xs">Balance</span>
                            <span class="text-white text-base font-bold">250,000 TZS</span>
                        </div>
                    </div>
                </div>

                <!-- Terapay Wallet Balance Card -->
                <div class="transform hover:scale-105 transition duration-300">
                    <div class="p-4 rounded-lg gradient-terapay shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-white text-lg font-bold">Terapay</h2>
                            <i class='bx bx-wallet text-white text-2xl'></i>
                        </div>
                        <div class="flex space-x-4">
                            <div class="bg-white/20 p-2 rounded flex-1">
                                <p class="text-white text-xs mb-0.5">Collected</p>
                                <h3 class="text-white text-base font-bold">+ 500,000 TZS</h3>
                            </div>
                            <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                <p class="text-white text-xs mb-0.5">Disbursed</p>
                                <h3 class="text-white text-base font-bold">- 200,000 TZS</h3>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20">
                            <div class="flex justify-between items-center">
                                <span class="text-white text-xs">Balance</span>
                                @if($error)
                                    <span class="text-red-300 text-xs">Error fetching balance</span>
                                @elseif($balance)
                                    <span class="text-white text-base font-bold">
                        @if(is_object($balance) && isset($balance->balance))
                                            {{ number_format($balance->balance, 2) }} {{ $balance->currency ?? 'TZS' }}
                                        @else
                                            {{ is_numeric($balance) ? number_format($balance, 2) : $balance }} TZS
                                        @endif
                    </span>
                                @else
                                    <span class="text-white text-xs">Loading...</span>
                                @endif
                            </div>
                            @if($error)
                                <div class="mt-2 text-center">
                                    <p class="text-red-300 text-xs mb-2">{{ $error }}</p>
                                    <button wire:click="fetchDisbursementBalance" class="bg-white/20 text-white text-xs py-1 px-3 rounded hover:bg-white/30 transition duration-300">
                                        Retry
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Cellulant Wallet Balance Card -->
                <div class="transform hover:scale-105 transition duration-300">
                    <div class="p-4 rounded-lg gradient-cellulant shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-white text-lg font-bold">Cellulant</h2>
                            <i class='bx bx-wallet text-white text-2xl'></i>
                        </div>
                        <div class="flex space-x-4">
                            <div class="bg-white/20 p-2 rounded flex-1">
                                <p class="text-white text-xs mb-0.5">Collected</p>
                                <h3 class="text-white text-base font-bold">+ 600,000 TZS</h3>
                            </div>
                            <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                <p class="text-white text-xs mb-0.5">Disbursed</p>
                                <h3 class="text-white text-base font-bold">- 250,000 TZS</h3>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20 flex justify-between items-center">
                            <span class="text-white text-xs">Balance</span>
                            <span class="text-white text-base font-bold">350,000 TZS</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
