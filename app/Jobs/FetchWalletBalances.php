<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchWalletBalances implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $this->failSafeLog('FetchWalletBalances job started');
        try {
            $this->fetchTeraPay();
            $this->fetchTembo();
            $this->fetchCellulant();
            $this->fetchZendeskTickets();
            $this->failSafeLog('FetchWalletBalances job completed successfully');
        } catch (\Exception $e) {
            $this->failSafeLog('FetchWalletBalances job failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
    private function fetchZendeskTickets(): void
    {
        $this->failSafeLog('Fetching Zendesk tickets');
        try {
            $subdomain = config('services.zendesk.subdomain');
            $username = config('services.zendesk.username');
            $token = config('services.zendesk.token');

            $this->failSafeLog('Zendesk configuration', [
                'subdomain' => $subdomain,
                'username' => $username,
                'token_set' => !empty($token),
            ]);

            if (empty($subdomain) || empty($username) || empty($token)) {
                $this->failSafeLog('Zendesk configuration is incomplete. Skipping ticket fetch.');
                return;
            }

            $url = "https://{$subdomain}.zendesk.com/api/v2/tickets.json";

            $response = Http::withBasicAuth($username . '/token', $token)
                ->get($url);

            if ($response->successful()) {
                $tickets = $response->json()['tickets'];
                $this->failSafeLog('Zendesk tickets fetched successfully', ['count' => count($tickets)]);

                foreach ($tickets as $ticket) {
                    Ticket::updateOrCreate(
                        ['zendesk_id' => $ticket['id']],
                        [
                            'subject' => $ticket['subject'],
                            'description' => $ticket['description'],
                            'status' => $ticket['status'],
                            'priority' => $ticket['priority'],
                            'requester_id' => $ticket['requester_id'],
                            'assignee_id' => $ticket['assignee_id'],
                            'ticket_created_at' => $ticket['created_at'],
                            'ticket_updated_at' => $ticket['updated_at'],
                        ]
                    );
                }

                $this->failSafeLog('Zendesk tickets stored in database');
            } else {
                $this->failSafeLog('Zendesk API Error', ['status' => $response->status(), 'response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->failSafeLog('Error in fetchZendeskTickets', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }


    private function fetchTeraPay(): void
    {
        $this->failSafeLog('Fetching TeraPay balance');
        try {
            $response = $this->checkDisbursementBalanceTeraPay();
            $this->failSafeLog('TeraPay API raw response', ['response' => $response]);

            if (is_array($response) && !empty($response) && isset($response[0])) {
                $data = $response[0];
                $this->failSafeLog('TeraPay API parsed response', ['data' => $data]);

                $this->updateDatabase('TeraPay', [
                    'balance' => $data['currentBalance'] ?? null,
                    'currency' => $data['currency'] ?? 'USD',
                    'status' => $data['status'] ?? 'available',
                ]);
            } else {
                $this->failSafeLog('TeraPay API Unexpected Response', ['response' => $response]);
            }
        } catch (\Exception $e) {
            $this->failSafeLog('Error in fetchTeraPay', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    private function fetchTembo(): void
    {
        $this->failSafeLog('Fetching Tembo balance');
        try {
            $headers = [
                'x-account-id' => env('TEMBO_ACCOUNT_ID'),
                'x-secret-key' => env('TEMBO_SECRET_KEY'),
                'x-request-id' => (string) Str::uuid(),
                'content-type' => 'application/json',
            ];

            $url = env('TEMBO_ENDPOINT') . 'wallet/main-balance';
            $this->failSafeLog('Sending request to Tembo API', ['url' => $url]);

            $response = Http::withHeaders($headers)->post($url);
            $this->failSafeLog('Tembo API raw response', ['response' => $response->body()]);

            if ($response->successful()) {
                $data = $response->json();
                $this->failSafeLog('Tembo API parsed response', ['data' => $data]);

                $this->updateDatabase('Tembo', [
                    'balance' => $data['currentBalance'] ?? null,
                    'available_balance' => $data['availableBalance'] ?? null,
                    'status' => $data['accountStatus'] ?? 'unknown',
                ]);
            } else {
                $this->failSafeLog('Tembo API Error', ['status' => $response->status(), 'response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->failSafeLog('Error in fetchTembo', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    private function fetchCellulant(): void
    {
        $this->failSafeLog('Fetching Cellulant balance');
        try {
            // Using dummy data as in the example
            $this->updateDatabase('Cellulant', [
                'balance' => 67,
                'currency' => 'USD',
                'status' => 'available',
            ]);
        } catch (\Exception $e) {
            $this->failSafeLog('Error in fetchCellulant', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    private function checkDisbursementBalanceTeraPay()
    {
        $this->failSafeLog('Checking TeraPay disbursement balance');
        $username = env('TERAPAY_USERNAME');
        $password = env('TERAPAY_PASSWORD');
        $headers = [
            'X-USERNAME: ' . $username,
            'X-PASSWORD: ' . $password,
            'X-DATE: ' . now()->format('Y-m-d H:i:s'),
            'X-ORIGINCOUNTRY: TZ',
            'Content-Type: application/json'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://vpnconnect.terrapay.com:21211/eig/gsma/accounts/all/balance',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->failSafeLog('TeraPay cURL Error', ['error' => $err]);
            return null;
        } else {
            $this->failSafeLog('TeraPay API raw response received', ['response' => $response]);
            return json_decode($response, true);
        }
    }

    private function updateDatabase(string $partner, array $data): void
    {
        try {
            DB::table('wallet_balances')->updateOrInsert(
                ['partner' => $partner],
                array_merge($data, ['updated_at' => now()])
            );
            $this->failSafeLog("$partner balance updated in database", $data);
        } catch (\Exception $e) {
            $this->failSafeLog("Error updating $partner balance in database", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
        }
    }

    private function failSafeLog($message, $context = []): void
    {
        try {
            Log::info($message, $context);
        } catch (\Exception $e) {
            // If normal logging fails, try to write to a file directly
            $logPath = storage_path('logs/failsafe.log');
            $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . ' ' . json_encode($context) . "\n";
            file_put_contents($logPath, $logMessage, FILE_APPEND);
        }
    }
}
