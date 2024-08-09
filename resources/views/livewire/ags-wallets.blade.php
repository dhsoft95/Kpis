<x-filament-widgets::widget>
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

    <x-filament::section>
        <div class="container px-2 py-8 mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Tembo Pay Wallet Balance Card -->
                <div class="card p-3">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1">Tembo Pay</h5>
                            @if($balanceTembo !== null)
                                <h2 class="card-value">
                                    {{ number_format((float)$balanceTembo, 2) }} {{ $currencyTembo }}
                                </h2>
                            @else
                                <h2 class="card-value">Loading...</h2>
                            @endif
                        </div>
                        <div class="icon-bg bg-blue-100 dark:bg-blue-700">
                            <i class='bx bx-wallet text-blue-600 dark:text-blue-200 text-xs'></i>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="time-period">{{ ucfirst($statusTembo ?? 'Unknown') }}</span>
                    </div>
                </div>

                <!-- Terapay Wallet Balance Card -->
                <div class="card p-3">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1">Terapay</h5>
                            @if($balance !== null)
                                <h2 class="card-value">
                                    {{ number_format((float)$balance, 2) }} {{ $currency }}
                                </h2>
                            @else
                                <h2 class="card-value">Loading...</h2>
                            @endif
                        </div>
                        <div class="icon-bg bg-green-100 dark:bg-green-700">
                            <i class='bx bx-wallet text-green-600 dark:text-green-200 text-xs'></i>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="time-period">{{ ucfirst($status ?? 'Unknown') }}</span>
                    </div>
                </div>

                <!-- Cellulant Wallet Balance Card -->
                <div class="card p-3">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1">Cellulant</h5>
                            @if($balanceCellulant !== null)
                                <h2 class="card-value">
                                    {{ number_format((float)$balanceCellulant, 2) }} {{ $currencyCellulant }}
                                </h2>
                            @else
                                <h2 class="card-value">Loading...</h2>
                            @endif
                        </div>
                        <div class="icon-bg bg-yellow-100 dark:bg-yellow-700">
                            <i class='bx bx-wallet text-yellow-600 dark:text-yellow-200 text-xs'></i>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="time-period">{{ ucfirst($statusCellulant ?? 'Unknown') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
