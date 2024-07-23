<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            <!-- Other Expenses Section -->
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-200">Other Expenses</h3>
                    <p class="text-sm text-gray-400">weekly Pay</p>
                </div>
                <div class="flex space-x-2">
                    <span class="w-6 h-6 rounded-full bg-gray-600 flex items-center justify-center text-xs text-white">DR</span>
                    <span class="w-6 h-6 rounded-full bg-gray-600 flex items-center justify-center text-xs text-white">MD</span>
                    <span class="w-6 h-6 rounded-full bg-yellow-500 flex items-center justify-center text-xs text-white">Q3</span>
                </div>
            </div>

            <!-- Profit/Loss Section -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        <span class="text-lg font-bold text-green-500">$4352.55</span>
                    </div>
                    <p class="text-sm text-gray-400">Profit</p>
                </div>
                <div class="bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        <span class="text-lg font-bold text-red-500">$152.55</span>
                    </div>
                    <p class="text-sm text-gray-400">Loss</p>
                </div>
            </div>

            <!-- Gross Profit Section -->
            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-200">$5352.55</h3>
                    <div class="relative">
                        <select class="appearance-none bg-gray-700 text-gray-300 py-1 px-3 pr-8 rounded-lg text-sm">
                            <option>Monthly</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-400">Gross Profit</p>
                <div class="mt-2 relative">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg blur"></div>
                    <div class="relative bg-gray-800 p-4 rounded-lg">
                        <div class="h-12 flex items-center">
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 h-2 rounded-full" style="width: 70%"></div>
                            </div>
                            <div class="w-3 h-3 bg-yellow-500 rounded-full ml-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
