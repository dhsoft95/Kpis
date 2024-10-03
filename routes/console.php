<?php

use App\Jobs\FetchWalletBalances;
use App\Jobs\FetchZendeskTickets;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(FetchWalletBalances::class)->everyMinute();

Schedule::job(new FetchZendeskTickets)->everyMinute();
