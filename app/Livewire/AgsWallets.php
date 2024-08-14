<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AgsWallets extends Widget
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
        $this->resetDisbursementBalance();

        $response = self::checkDisbursementBalanceTeraPay();

        if (is_array($response) && !empty($response)) {
            $data = $response[0] ?? [];
            $this->balance = $data['currentBalance'] ?? null;
            $this->currency = $data['currency'] ?? 'USD';
            $this->status = $data['status'] ?? 'available';
        } else {
            $this->error = 'Unexpected response format from TeraPay API';
            Log::error('TeraPay API Unexpected Response', (array)$response);
        }
    }

    public function fetchTemboBalance()
    {
        $this->resetTemboBalance();

        try {
            $headers = $this->getTemboHeaders();
            $url = env('TEMBO_ENDPOINT') . 'wallet/main-balance';

            Log::info('Tembo API URL', ['url' => $url]);

            $response = Http::withHeaders($headers)->post($url);

            Log::info('Tembo API Raw Response', ['status' => $response->status(), 'response' => $response->body()]);

            if ($response->status() === 200) {
                $data = $response->json();
                Log::info('Tembo API Response Data', ['data' => $data]);

                $this->balanceTembo = $data['currentBalance'] ?? null;
                $this->availableBalanceTembo = $data['availableBalance'] ?? null;
                $this->statusTembo = $data['accountStatus'] ?? 'unknown';

                Log::info('Tembo API Extracted Balances and Status', [
                    'balanceTembo' => $this->balanceTembo,
                    'availableBalanceTembo' => $this->availableBalanceTembo,
                    'statusTembo' => $this->statusTembo,
                    'accountName' => $data['accountName'] ?? 'unknown',
                    'accountNo' => $data['accountNo'] ?? 'unknown',
                ]);
            } else {
                $this->errorTembo = 'Tembo API returned an error';
                Log::error('Tembo API Error', ['status' => $response->status(), 'response' => $response->json()]);
            }
        } catch (\Exception $e) {
            $this->errorTembo = 'Error fetching Tembo balance';
            Log::error('Tembo API Exception', ['error' => $e->getMessage()]);
        }
    }

    public function fetchCellulantBalance()
    {
        $this->balanceCellulant = 67; // Replace with actual API call
        $this->currencyCellulant = 'USD';
        $this->statusCellulant = 'available';
        $this->errorCellulant = null;
    }

    public static function checkDisbursementBalanceTeraPay()
    {
        $username = env('TERAPAY_USERNAME');
        $password = env('TERAPAY_PASSWORD');
        $headers = [
            'X-USERNAME' => $username,
            'X-PASSWORD' => $password,
            'X-DATE' => now()->format('Y-m-d H:i:s'),
            'X-ORIGINCOUNTRY' => 'TZ',
            'Content-Type' => 'application/json'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://vpnconnect.terrapay.com:21211/eig/gsma/accounts/all/balance',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }

    public function mainBalance()
    {
        try {
            $requestId = $this->generateId();
            $post = '{}';
            $headers = $this->getTemboHeaders($requestId);

            $url = env('TEMBO_ENDPOINT') . 'wallet/main-balance';

            $data = $this->processor($post, $headers, $url);
            $data = json_decode($data);

            return [
                "message" => "Main Balance Retrieved",
                "notification" => "success",
                "data" => $data
            ];
        } catch (\Exception $e) {
            Log::error('TemboPlus', ['DepositFunds' => $e]);
            return [
                'message' => "Oops something went wrong",
                'notification' => "failure"
            ];
        }
    }

    protected function generateId()
    {
        return uniqid('tembo_', true);
    }

    protected function processor($post, $headers, $url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($curl);
        if ($error = curl_error($curl)) {
            throw new \Exception("Curl Error: " . $error);
        }

        curl_close($curl);
        return $response;
    }

    protected function getTemboHeaders($requestId = null)
    {
        return [
            'x-account-id' => env('TEMBO_ACCOUNT_ID'),
            'x-secret-key' => env('TEMBO_SECRET_KEY'),
            'x-request-id' => $requestId ?? (string) Str::uuid(),
            'content-type' => 'application/json',
        ];
    }

    protected function resetDisbursementBalance(): void
    {
        $this->error = null;
        $this->balance = null;
        $this->currency = null;
        $this->status = null;
    }

    protected function resetTemboBalance()
    {
        $this->errorTembo = null;
        $this->balanceTembo = null;
        $this->availableBalanceTembo = null;
        $this->statusTembo = null;
    }
}
