<x-filament::widget>
    <x-filament::card>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $cards = [
                    'registered' => [
                        'title' => 'Total Customers',
                        'icon' => 'heroicon-o-users',
                        'color' => 'primary',
                        'description' => 'Total number of registered users'
                    ],
                    'active' => [
                        'title' => 'Active Customers',
                        'icon' => 'heroicon-o-user-circle',
                        'color' => 'success',
                        'description' => 'Users active in the last 30 days'
                    ],
                    'inactive' => [
                        'title' => 'Inactive Customers',
                        'icon' => 'heroicon-o-user-remove',
                        'color' => 'warning',
                        'description' => 'Users inactive for more than 30 days'
                    ],
                    'churn' => [
                        'title' => 'Churn Customers',
                        'icon' => 'heroicon-o-user-minus',
                        'color' => 'danger',
                        'description' => 'Users who have stopped using the platform'
                    ],
                    'avgValuePerDay' => [
                        'title' => 'Avg Trans Value/Day',
                        'icon' => 'heroicon-o-currency-dollar',
                        'color' => 'success',
                        'description' => 'Average transaction value per day'
                    ],
                    'avgTransactionPerCustomer' => [
                        'title' => 'Avg Trans/Customer',
                        'icon' => 'heroicon-o-chart-bar',
                        'color' => 'info',
                        'description' => 'Average transactions per customer'
                    ],
                ];
            @endphp

            @foreach ($cards as $key => $card)
                <div class="relative p-6 rounded-lg bg-white dark:bg-gray-800 shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-dynamic-component
                                :component="'heroicon-o-' . str_replace('heroicon-o-', '', $card['icon'])"
                                class="w-8 h-8 text-{{ $card['color'] }}-500"
                            />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ $card['title'] }}
                                </dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900 dark:text-white">
                                        @if ($key === 'avgValuePerDay')
                                            TSH {{ number_format($stats[$key]['value'] ?? 0, 0) }}
                                        @elseif ($key === 'avgTransactionPerCustomer')
                                            {{ number_format($stats[$key]['value'] ?? 0, 2) }}
                                        @else
                                            {{ number_format($stats[$key]['count'] ?? 0, 0) }}
                                        @endif
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="absolute bottom-0 inset-x-0 bg-gray-50 dark:bg-gray-700 px-4 py-4 sm:px-6">
                        <div class="text-sm">
                            <span class="font-medium {{ $stats[$key]['isGrowth'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $stats[$key]['isGrowth'] ? '+' : '-' }}{{ abs($stats[$key]['percentageChange']) }}%
                            </span>
                            <span class="text-gray-500 dark:text-gray-300">vs last week</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::card>
</x-filament::widget>
