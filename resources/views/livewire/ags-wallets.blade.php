<x-filament-widgets::widget>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>

        .gradient-tembo { background: linear-gradient(135deg, #ff9a9e, #141415); }
        .gradient-terapay { background: linear-gradient(135deg, #a1c4fd, #141415); }
        .gradient-cellulant { background: linear-gradient(135deg, rgba(0, 255, 183, 0.5), #141415); }
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
                        @if($errorTembo)
                            <div class="bg-white/20 p-2 rounded text-center">
                                <p class="text-red-300 text-xs mb-2">{{ $errorTembo }}</p>
                                <button wire:click="fetchTemboBalance" class="bg-white/20 text-white text-xs py-1 px-3 rounded hover:bg-white/30 transition duration-300">
                                    Retry
                                </button>
                            </div>
                        @else
                            <div class="flex space-x-4">
                                <div class="bg-white/20 p-2 rounded flex-1">
                                    <p class="text-white text-xs mb-0.5">Current Balance</p>
                                    @if($balanceTembo !== null)
                                        <h3 class="text-white text-base font-bold">
                                            {{ number_format((float)$balanceTembo, 2) }} {{ $currencyTembo }}
                                        </h3>
                                    @else
                                        <h3 class="text-white text-xs">Loading...</h3>
                                    @endif
                                </div>
                                <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                    <p class="text-white text-xs mb-0.5">Status</p>
                                    <h3 class="text-white text-base font-bold">{{ ucfirst($statusTembo ?? 'Unknown') }}</h3>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Terapay Wallet Balance Card -->
                <div class="transform hover:scale-105 transition duration-300">
                    <div class="p-4 rounded-lg gradient-terapay shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-white text-lg font-bold">Terapay</h2>
                            <i class='bx bx-wallet text-white text-2xl'></i>
                        </div>
                        @if($error)
                            <div class="bg-white/20 p-2 rounded text-center">
                                <p class="text-red-300 text-xs mb-2">{{ $error }}</p>
                                <button wire:click="fetchDisbursementBalance" class="bg-white/20 text-white text-xs py-1 px-3 rounded hover:bg-white/30 transition duration-300">
                                    Retry
                                </button>
                            </div>
                        @else
                            <div class="flex space-x-4">
                                <div class="bg-white/20 p-2 rounded flex-1">
                                    <p class="text-white text-xs mb-0.5">Current Balance</p>
                                    @if($balance !== null)
                                        <h3 class="text-white text-base font-bold">
                                            {{ number_format((float)$balance, 2) }} {{ $currency }}
                                        </h3>
                                    @else
                                        <h3 class="text-white text-xs">Loading...</h3>
                                    @endif
                                </div>
                                <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                    <p class="text-white text-xs mb-0.5">Status</p>
                                    <h3 class="text-white text-base font-bold">{{ ucfirst($status ?? 'Unknown') }}</h3>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Cellulant Wallet Balance Card -->
                <div class="transform hover:scale-105 transition duration-300">
                    <div class="p-4 rounded-lg gradient-cellulant shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-white text-lg font-bold">Cellulant</h2>
                            <i class='bx bx-wallet text-white text-2xl'></i>
                        </div>
                        @if($errorCellulant)
                            <div class="bg-white/20 p-2 rounded text-center">
                                <p class="text-red-300 text-xs mb-2">{{ $errorCellulant }}</p>
                                <button wire:click="fetchCellulantBalance" class="bg-white/20 text-white text-xs py-1 px-3 rounded hover:bg-white/30 transition duration-300">
                                    Retry
                                </button>
                            </div>
                        @else
                            <div class="flex space-x-4">
                                <div class="bg-white/20 p-2 rounded flex-1">
                                    <p class="text-white text-xs mb-0.5">Current Balance</p>
                                    @if($balanceCellulant !== null)
                                        <h3 class="text-white text-base font-bold">
                                            {{ number_format((float)$balanceCellulant, 2) }} {{ $currencyCellulant }}
                                        </h3>
                                    @else
                                        <h3 class="text-white text-xs">Loading...</h3>
                                    @endif
                                </div>
                                <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                    <p class="text-white text-xs mb-0.5">Status</p>
                                    <h3 class="text-white text-base font-bold">{{ ucfirst($statusCellulant ?? 'Unknown') }}</h3>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>


</x-filament-widgets::widget>
