<x-filament::widget>
    <x-filament::card>
        <div class="space-y-6">
            <!-- Device Downloads Section -->
            <div>
                <h3 class="text-sm font-semibold mb-3">Device Downloads</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 transition-all duration-300 hover:shadow-md">
                        <div class="flex items-center space-x-3">
                            <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                            <div>
                                <span class="text-2xl font-bold counter" data-target="{{ $this->getDownloads()['ios'] }}">0</span>
                                <p class="text-sm text-gray-600 dark:text-gray-400">iOS</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 transition-all duration-300 hover:shadow-md">
                        <div class="flex items-center space-x-3">
                            <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16.61 15.15c-.46 0-.84.37-.84.83s.37.84.84.84.83-.38.83-.84-.37-.83-.83-.83m-9.2 0c-.46 0-.84.37-.84.83s.37.84.84.84.84-.38.84-.84-.38-.83-.84-.83m9.5-5.01l1.67-2.88c.09-.17.03-.38-.13-.47-.17-.1-.38-.04-.45.13l-1.71 2.91c-1.25-.62-2.66-.96-4.13-.96-1.47 0-2.87.34-4.13.96L6.38 6.92c-.1-.17-.29-.23-.45-.13-.17.1-.22.31-.13.47l1.66 2.88C4.67 11.87 3 14.62 3 17.68h18c0-3.06-1.67-5.8-4.09-7.54M7.62 14.3c-.46 0-.84-.38-.84-.84 0-.46.38-.83.84-.83.46 0 .83.37.83.83 0 .46-.37.84-.83.84m8.77 0c-.46 0-.83-.38-.83-.84 0-.46.37-.83.83-.83s.84.37.84.83c0 .46-.38.84-.84.84"/></svg>
                            <div>
                                <span class="text-2xl font-bold counter" data-target="{{ $this->getDownloads()['android'] }}">0</span>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Android</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Gender Stats Section -->
            <div>
                <h3 class="text-sm font-semibold mb-2">User Gender Stats</h3>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 transition-all duration-300 hover:shadow-md">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <svg class="w-8 h-8 text-indigo-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m-1.5 5h3a2 2 0 0 1 2 2v5.5H14V22h-4v-7.5H8.5V9a2 2 0 0 1 2-2z"/></svg>
                            <div>
                                <span class="text-2xl font-bold counter" data-target="{{ $this->getUserStats()['male'] }}">0</span>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Male</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div>
                                <span class="text-2xl font-bold counter" data-target="{{ $this->getUserStats()['female'] }}">0</span>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Female</p>
                            </div>
                            <svg class="w-8 h-8 text-pink-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12,4A6,6 0 0,1 18,10C18,12.97 15.84,15.44 13,15.92V18H15V20H13V22H11V20H9V18H11V15.92C8.16,15.44 6,12.97 6,10A6,6 0 0,1 12,4M12,6A4,4 0 0,0 8,10A4,4 0 0,0 12,14A4,4 0 0,0 16,10A4,4 0 0,0 12,6Z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-indigo-500 dark:bg-pink-500 h-2 rounded-full transition-all duration-1000 ease-out" style="width: 0%" id="gender-ratio"></div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::card>

    <script>
        function animateCounter(el) {
            const target = parseInt(el.getAttribute('data-target'));
            const duration = 2000; // 2 seconds
            const step = target / (duration / 16); // 60 fps
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    clearInterval(timer);
                    el.textContent = target.toLocaleString();
                } else {
                    el.textContent = Math.round(current).toLocaleString();
                }
            }, 16);
        }

        function animateGenderRatio() {
            const male = {{ $this->getUserStats()['male'] }};
            const female = {{ $this->getUserStats()['female'] }};
            const total = male + female;
            const femalePercentage = (female / total) * 100;

            const ratioBar = document.getElementById('gender-ratio');
            ratioBar.style.width = `${femalePercentage}%`;
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('.counter').forEach(animateCounter);
            animateGenderRatio();
        });
    </script>
</x-filament::widget>
