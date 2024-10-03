<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Ticket;
use Carbon\Carbon;

class FetchZendeskTickets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of retries in case of failure.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Job timeout in seconds.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Handle the queued job.
     *
     * @throws ConnectionException
     */
    public function handle(): void
    {
        try {
            $zendeskSubdomain = config('services.zendesk.subdomain');
            $zendeskUsername = config('services.zendesk.username');
            $zendeskToken = config('services.zendesk.token');
            $startTime = Carbon::now()->subMinutes(config('services.zendesk.fetch_minutes', 2))->getTimestamp();
            $url = "https://{$zendeskSubdomain}.zendesk.com/api/v2/incremental/tickets.json";

            $this->fetchTickets($url, $zendeskUsername, $zendeskToken, $startTime);
        } catch (\Exception $e) {
            Log::error('Zendesk Ticket Fetching Failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch tickets recursively, handling pagination.
     *
     * @param string $url
     * @param string $username
     * @param string $token
     * @param int $startTime
     * @return void
     */
    private function fetchTickets(string $url, string $username, string $token, int $startTime): void
    {
        $nextPageUrl = "{$url}?start_time={$startTime}";

        while ($nextPageUrl) {
            $response = Http::withBasicAuth("{$username}/token", $token)
                ->timeout(10) // Set timeout for the request
                ->get($nextPageUrl);

            if ($response->successful()) {
                $data = $response->json();
                $tickets = $data['tickets'];
                $nextPageUrl = $data['next_page'] ?? null;

                if (count($tickets) > 0) {
                    $this->processTickets($tickets);
                    Log::info('Fetched ' . count($tickets) . ' tickets from Zendesk.');
                } else {
                    Log::info('No new tickets found in this fetch cycle.');
                }
            } else {
                Log::error('Failed to fetch Zendesk tickets: ' . $response->body());
                return;
            }
        }
    }

    /**
     * Process the list of tickets and store or update them in the database.
     *
     * @param array $tickets
     * @return void
     */
    private function processTickets(array $tickets): void
    {
        foreach ($tickets as $ticketData) {
            try {
                $this->processTicket($ticketData);
            } catch (\Exception $e) {
                Log::error('Failed to process ticket ID ' . $ticketData['id'] . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Process and update or create a ticket in the database.
     *
     * @param array $ticketData
     * @return void
     */
    private function processTicket(array $ticketData): void
    {
        Ticket::updateOrCreate(
            ['zendesk_id' => $ticketData['id']],
            [
                'subject' => $ticketData['subject'],
                'description' => $ticketData['description'] ?? null,
                'status' => $ticketData['status'],
                'priority' => $ticketData['priority'] ?? null,
                'requester_id' => $ticketData['requester_id'],
                'assignee_id' => $ticketData['assignee_id'] ?? null,
                'ticket_created_at' => Carbon::parse($ticketData['created_at']),
                'ticket_updated_at' => Carbon::parse($ticketData['updated_at']),
            ]
        );
    }

    /**
     * Handle job failure.
     *
     * @return void
     */
    public function failed(): void
    {
        Log::error('FetchZendeskTickets Job failed after multiple attempts.');
    }
}
