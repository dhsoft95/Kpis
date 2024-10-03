<?php

namespace App\Console\Commands;

use App\Jobs\FetchZendeskTickets;
use Illuminate\Console\Command;

class TestFetchZendeskTickets extends Command
{
    protected $signature = 'app:test-fetch-zendesk-tickets';
    protected $description = 'Test the FetchZendeskTickets job';

    public function handle(): void
    {
        $this->info('Dispatching FetchZendeskTickets job...');
        dispatch(new FetchZendeskTickets());
        $this->info('Job dispatched. Check your logs for results.');
    }
}
