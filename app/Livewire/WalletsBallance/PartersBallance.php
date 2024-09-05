<?php

namespace App\Livewire\WalletsBallance;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PartersBallance extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.ags-wallets';

    public $balance;
    public $error;
    public $currency;
    public $status;

    public $balanceTembo;
    public $availableBalanceTembo;
    public $errorTembo;
    public $statusTembo;

    public $balanceCellulant;
    public $errorCellulant;
    public $currencyCellulant;
    public $statusCellulant;

    public function mount()
    {
        $this->fetchDisbursementBalance();
        $this->fetchTemboBalance();
        $this->fetchCellulantBalance();
    }

    public function fetchDisbursementBalance()
    {
        $this->status = null;

        $response = self::checkDisbursementBalanceTeraPay();

        if (is_array($response) && !empty($response) && isset($response[0])) {
            $data = $response[0];
            $this->balance = $data['currentBalance'] ?? null;
            $this->currency = $data['currency'] ?? 'USD';
            $this->status = $data['status'] ?? 'available';
        } else {
            $this->error = 'Unexpected response format from TeraPay API';
            Log::error('TeraPay API Unexpected Response', ['response' => $response]);
        }
    }


    public function fetchTemboBalance()
    {
        // Reset state
        $this->errorTembo = null;
        $this->balanceTembo = null;
        $this->availableBalanceTembo = null;
        $this->statusTembo = null;

        try {
            // Prepare API headers
            $headers = [
                'x-account-id' => env('TEMBO_ACCOUNT_ID'),
                'x-secret-key' => env('TEMBO_SECRET_KEY'),
                'x-request-id' => (string) Str::uuid(), // Generate unique request ID
                'content-type' => 'application/json',
            ];

            // Construct the full API endpoint URL
            $url = env('TEMBO_ENDPOINT') . 'wallet/main-balance';

            // Log the URL to debug
            Log::info('Tembo API URL', ['url' => $url]);

            // Call the Tembo API to retrieve the main balance
            $response = Http::withHeaders($headers)->post($url);

            // Log the raw response for debugging
            Log::info('Tembo API Raw Response', ['status' => $response->status(), 'response' => $response->body()]);

            if ($response->status() === 200) {
                $data = $response->json();
                Log::info('Tembo API Response Data', ['data' => $data]);

                // Extract balances and status
                $this->balanceTembo = $data['currentBalance'] ?? null;
                $this->availableBalanceTembo = $data['availableBalance'] ?? null;
                $this->statusTembo = $data['accountStatus'] ?? 'unknown';

                // Log the extracted balances and status
                Log::info('Tembo API Extracted Balances and Status', [
                    'balanceTembo' => $this->balanceTembo,
                    'availableBalanceTembo' => $this->availableBalanceTembo,
                    'statusTembo' => $this->statusTembo,
                    'accountName' => $data['accountName'] ?? 'unknown',
                    'accountNo' => $data['accountNo'] ?? 'unknown',
                ]);
            } else {
                // Handle API errors
                $this->errorTembo = 'Tembo API returned an error';
                Log::error('Tembo API Error', ['status' => $response->status(), 'response' => $response->json()]);
            }
        } catch (\Exception $e) {
            // Handle exceptions during API call
            $this->errorTembo = 'Error fetching Tembo balance';
            Log::error('Tembo API Exception', ['error' => $e->getMessage()]);
        }
    }




    public function fetchCellulantBalance()
    {
        // Dummy data for Cellulant
        $this->balanceCellulant = 67;
        $this->currencyCellulant = 'USD';
        $this->statusCellulant = 'available';
        $this->errorCellulant = null;
    }

    public static function checkDisbursementBalanceTeraPay()
    {
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

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return $response;
    }

    public function mainBalance()
    {
        try {
            $requestId = $this->generateId();

            $post = '{}';
            $headers = array(
                'content-type: application/json',
                'x-account-id: ' . env('TEMBO_ACCOUNT_ID'),
                'x-secret-key: ' . env('TEMBO_SECRET_KEY'),
                'x-request-id: ' . $requestId
            );

            $url = env('TEMBO_ENDPOINT') . 'wallet/main-balance';

            $data = $this->processor($post, $headers, $url);

            $data = json_decode($data);

            $response = array(
                "message" => "Main Balance Retrieved",
                "notification" => "success",
                "data" => $data
            );

            return $response;
        } catch (\Exception $e) {
            Log::error('TemboPlus', ['DepositFunds' => $e]);
            return ['message' => "Oops something went wrong", "notification" => "failure"];
        }
    }

    protected function generateId()
    {
        // Implementation for generating a unique ID
        return uniqid('tembo_', true);
    }

    protected function processor($post, $headers, $url): bool|string
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        if ($error = curl_error($curl)) {
            throw new \Exception("Curl Error: " . $error);
        }

        curl_close($curl);

        return $response;
    }
}
