<x-filament-widgets::widget>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
            padding: 20px;
            position: relative;
            overflow: hidden;
            max-width: 350px;
        }
        .dark .card {
            background-color: #1f2937;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.6), 0 2px 4px rgba(0, 0, 0, 0.4);
        }
        .card-title {
            color: #4b5563;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dark .card-title {
            color: #d1d5db;
        }
        .card-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }
        .dark .card-value {
            color: #e5e7eb;
        }
        .time-period {
            color: #9ca3af;
            font-size: 0.75rem;
        }
        .dark .time-period {
            color: #a1a1aa;
        }
        .icon-bg {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .dark .icon-bg {
            background-color: #4b5563;
        }
        .percentage-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .tooltip {
            visibility: hidden;
            width: 200px;
            background-color: #27272a;
            color: #ffffff;
            text-align: left;
            border-radius: 6px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            font-size: 0.75rem;
            line-height: 1.4;
            transform: translateY(10px);
        }
        .tooltip::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #27272a transparent transparent transparent;
        }
        .card-container:hover .tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }
        /*.gradient-tembo { background: linear-gradient(135deg, #ff9a9e, #141415); }*/
        /*.gradient-terapay { background: linear-gradient(135deg, #a1c4fd, #141415); }*/
        /*.gradient-cellulant { background: linear-gradient(135deg, rgba(0, 255, 183, 0.5), #141415); }*/
    </style>

    <x-filament::section>
        <div class="container px-4 py-8 mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Tembo Pay Wallet Balance Card -->
                <div class="card-container relative transform hover:scale-105 transition-transform duration-300">
                    <div class="card gradient-tembo">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="card-title">Tembo Pay</h2>
                            <div class="icon-bg bg-purple-100 dark:bg-purple-700">
                                <i class='bx bx-wallet text-purple-600 dark:text-purple-200 text-2xl'></i>
                            </div>
                        </div>
                        @if($errorTembo)
                            <div class="bg-white/20 p-3 rounded text-center">
                                <p class="text-red-300 text-sm mb-2">{{ $errorTembo }}</p>
                                <button wire:click="fetchTemboBalance" class="bg-white/20 text-white text-xs py-1 px-3 rounded hover:bg-white/30 transition duration-300">
                                    Retry
                                </button>
                            </div>
                        @else
                            <div class="flex space-x-4 mb-3">
                                <div class="bg-white/20 p-2 rounded flex-1">
                                    <p class="text-white text-xs mb-0.5">Current Balance</p>
                                    @if($balanceTembo !== null)
                                        <h3 class="card-value">
                                            {{ number_format((float)$balanceTembo, 2) }}
                                        </h3>
                                    @else
                                        <h3 class="text-white text-xs">Loading...</h3>
                                    @endif
                                </div>
                                <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                    <p class="text-white text-xs mb-0.5">Available Balance</p>
                                    @if($availableBalanceTembo !== null)
                                        <h3 class="card-value">
                                            {{ number_format((float)$availableBalanceTembo, 2) }}
                                        </h3>
                                    @else
                                        <h3 class="text-white text-xs">Loading...</h3>
                                    @endif
                                </div>
                                <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                    <p class="text-white text-xs mb-0.5">Status</p>
                                    <h3 class="card-value">{{ ucfirst($statusTembo ?? 'Unknown') }}</h3>
                                </div>
                            </div>
                        @endif
                        <div class="tooltip">
                            <div class="font-semibold mb-1">Tembo Pay</div>
                            <div>Manage your Tembo Pay wallet and view your balance and status.</div>
                        </div>
                    </div>
                </div>

                <!-- Terapay Wallet Balance Card -->
                <div class="card-container relative transform hover:scale-105 transition-transform duration-300">
                    <div class="card gradient-terapay">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="card-title">Terapay</h2>
                            <div class="icon-bg bg-blue-100 dark:bg-blue-700">
                                <i class='bx bx-wallet text-blue-600 dark:text-blue-200 text-2xl'></i>
                            </div>
                        </div>
                        @if($error)
                            <div class="bg-white/20 p-3 rounded text-center">
                                <p class="text-red-300 text-sm mb-2">{{ $error }}</p>
                                <button wire:click="fetchDisbursementBalance" class="bg-white/20 text-white text-xs py-1 px-3 rounded hover:bg-white/30 transition duration-300">
                                    Retry
                                </button>
                            </div>
                        @else
                            <div class="flex space-x-4 mb-3">
                                <div class="bg-white/20 p-2 rounded flex-1">
                                    <p class="text-white text-xs mb-0.5">Current Balance</p>
                                    @if($balance !== null)
                                        <h3 class="card-value">
                                            {{ number_format((float)$balance, 2) }} {{ $currency }}
                                        </h3>
                                    @else
                                        <h3 class="text-white text-xs">Loading...</h3>
                                    @endif
                                </div>
                                <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                    <p class="text-white text-xs mb-0.5">Status</p>
                                    <h3 class="card-value">{{ ucfirst($status ?? 'Unknown') }}</h3>
                                </div>
                            </div>
                        @endif
                        <div class="tooltip">
                            <div class="font-semibold mb-1">Terapay</div>
                            <div>Manage your Terapay wallet and view your balance and status.</div>
                        </div>
                    </div>
                </div>

                <!-- Cellulant Wallet Balance Card -->
                <div class="card-container relative transform hover:scale-105 transition-transform duration-300">
                    <div class="card gradient-cellulant">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="card-title">Cellulant</h2>
                            <div class="icon-bg bg-green-100 dark:bg-green-700">
                                <i class='bx bx-wallet text-green-600 dark:text-green-200 text-2xl'></i>
                            </div>
                        </div>
                        @if($errorCellulant)
                            <div class="bg-white/20 p-3 rounded text-center">
                                <p class="text-red-300 text-sm mb-2">{{ $errorCellulant }}</p>
                                <button wire:click="fetchCellulantBalance" class="bg-white/20 text-white text-xs py-1 px-3 rounded hover:bg-white/30 transition duration-300">
                                    Retry
                                </button>
                            </div>
                        @else
                            <div class="flex space-x-4 mb-3">
                                <div class="bg-white/20 p-2 rounded flex-1">
                                    <p class="text-white text-xs mb-0.5">Current Balance</p>
                                    @if($balanceCellulant !== null)
                                        <h3 class="card-value">
                                            {{ number_format((float)$balanceCellulant, 2) }} {{ $currencyCellulant }}
                                        </h3>
                                    @else
                                        <h3 class="text-white text-xs">Loading...</h3>
                                    @endif
                                </div>
                                <div class="bg-white/20 p-2 rounded flex-1 text-right">
                                    <p class="text-white text-xs mb-0.5">Status</p>
                                    <h3 class="card-value">{{ ucfirst($statusCellulant ?? 'Unknown') }}</h3>
                                </div>
                            </div>
                        @endif
                        <div class="tooltip">
                            <div class="font-semibold mb-1">Cellulant</div>
                            <div>Manage your Cellulant wallet and view your balance and status.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
