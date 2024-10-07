<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->scheduleJobs();
    }

    /**
     * Define the application's command schedule.
     */
    protected function scheduleJobs(): void
    {
        // Schedule::job(new \App\Jobs\FetchWalletBalances())->everyMinute();
    }
}
