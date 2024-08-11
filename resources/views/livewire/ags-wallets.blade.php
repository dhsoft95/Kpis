<x-filament-widgets::widget>
    <x-filament::section>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            .card-tooltip {
                visibility: hidden;
                width: 200px;
                background-color: #27272a;
                color: #ffffff;
                text-align: left;
                border-radius: 6px;
                padding: 8px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                margin-left: -100px;
                opacity: 0;
                transition: opacity 0.3s, transform 0.3s;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                font-size: 0.65rem;
                line-height: 1.3;
                transform: translateY(10px);
            }
            .card-tooltip::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: #27272a transparent transparent transparent;
            }
            .card-container:hover .card-tooltip {
                visibility: visible;
                opacity: 1;
                transform: translateY(0);
            }
        </style>

        <div class="container mx-auto p-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Tembo Pay Wallet Balance Card -->
                <div class="card-container relative">
                    <div class="card p-4 gradient-tembo shadow-md">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="card-title">Tembo Pay</h5>
                                <h2 class="card-value">
                                    @if($balanceTembo !== null)
                                        {{ number_format((float)$balanceTembo, 2) }} TSH
                                    @else
                                        Loading...
                                    @endif
                                </h2>
                            </div>
                            <div class="icon-bg bg-gray-100 text-gray-600">
                                <i class="bx bx-wallet text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-tooltip">
                        <div class="font-semibold mb-1">Tembo Pay</div>
                        <div>Balance of Tembo Pay wallet.</div>
                    </div>
                </div>

                <!-- Terapay Wallet Balance Card -->
                <div class="card-container relative">
                    <div class="card p-4 gradient-terapay shadow-md">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="card-title">Terapay</h5>
                                <h2 class="card-value">
                                    @if($balance !== null)
                                        {{ number_format((float)$balance, 2) }} {{ $currency }}
                                    @else
                                        Loading...
                                    @endif
                                </h2>
                            </div>
                            <div class="icon-bg bg-gray-100 text-gray-600">
                                <i class="bx bx-wallet text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-tooltip">
                        <div class="font-semibold mb-1">Terapay</div>
                        <div>Balance of Terapay wallet.</div>
                    </div>
                </div>

                <!-- Cellulant Wallet Balance Card -->
                <div class="card-container relative">
                    <div class="card p-4 gradient-cellulant shadow-md">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="card-title">Cellulant</h5>
                                <h2 class="card-value">
                                    @if($balanceCellulant !== null)
                                        {{ number_format((float)$balanceCellulant, 2) }} {{ $currencyCellulant }}
                                    @else
                                        Loading...
                                    @endif
                                </h2>
                            </div>
                            <div class="icon-bg bg-gray-100 text-gray-600">
                                <i class="bx bx-wallet text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-tooltip">
                        <div class="font-semibold mb-1">Cellulant</div>
                        <div>Balance of Cellulant wallet.</div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
