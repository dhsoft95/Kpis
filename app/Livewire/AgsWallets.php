<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Exception;

class AgsWallets extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.ags-wallets';

    public $balance;
    public $error;
    public $currency;
    public $status;

    public $balanceTembo;
    public $errorTembo;
    public $currencyTembo;
    public $statusTembo;

    public $balanceCellulant;
    public $errorCellulant;
    public $currencyCellulant;
    public $statusCellulant;

    public function mount()
    {
        $this->fetchDisbursementBalance();
        $this->fetchCellulantBalance();
        $this->fetchTemboBalance();
    }

    public function fetchDisbursementBalance()
    {
        $this->error = null;
        $this->balance = null;
        $this->currency = null;
        $this->status = null;

        $response = self::checkDisbursementBalanceTeraPay();

        if (is_array($response) && !empty($response)) {
            $data = $response[0];
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
        $temboBalanceResponse = $this->mainBalance();
        if ($temboBalanceResponse['notification'] === 'success') {
            $this->balanceTembo = $temboBalanceResponse['data']['balance'] ?? 0;
            $this->currencyTembo = $temboBalanceResponse['data']['currency'] ?? 'USD';
            $this->statusTembo = $temboBalanceResponse['data']['status'] ?? 'available';
            $this->errorTembo = null;
        } else {
            $this->balanceTembo = null;
            $this->currencyTembo = null;
            $this->statusTembo = null;
            $this->errorTembo = $temboBalanceResponse['message'];
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

        curl_setopt_array($curl, [
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
        ]);

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return $response;
    }

    public function mainBalance()
    {
        try {
            $requestId = $this->generateId();

            $post = '{}'; // Add necessary data if required

            $headers = [
                'content-type: application/json',
                'x-account-id: ' . config('api.TEMBO_ACCOUNT_ID'),
                'x-secret-key: ' . config('api.TEMBO_SECRET_KEY'),
                'x-request-id: ' . $requestId
            ];

            $url = config('api.TEMBO_ENDPOINT') . 'wallet/main-balance';

            $data = $this->processor($post, $headers, $url);

            $data = json_decode($data, true);

            $response = [
                "message" => "Main Balance Retrieved",
                "notification" => "success",
                "data" => $data
            ];

            return $response;

        } catch (Exception $e) {
            Log::error('TemboPlus', ['DepositFunds' => $e]);
            return ['message' => "Oops something went wrong", "notification" => "failure"];
        }
    }

    private function generateId()
    {
        return uniqid(); // or use any other method to generate a unique ID
    }

    private function processor($post, $headers, $url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $post,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            Log::error('cURL Error', ['error' => curl_error($curl)]);
        }

        curl_close($curl);

        return $response;
    }
}
