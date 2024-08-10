<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

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
        $this->fetchTemboBalance();
        $this->fetchCellulantBalance();
    }

    public function fetchDisbursementBalance()
    {
        $this->error = null;
        $this->balance = null;
        $this->currency = null;
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
        $response = $this->mainBalance();

        if (isset($response['data'])) {
            $data = $response['data'];
            $this->balanceTembo = $data->balance ?? null;
            $this->currencyTembo = $data->currency ?? 'USD';
            $this->statusTembo = $data->status ?? 'available';
            $this->errorTembo = null;
        } else {
            $this->errorTembo = $response['message'] ?? 'Failed to retrieve balance from Tembo Plus';
            Log::error('Tembo Plus API Error', ['response' => $response]);
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

            $post = '{}'; // Assuming no body content is needed
            $headers = [
                'content-type: application/json',
                'x-account-id: ' . config('api.TEMBO_ACCOUNT_ID'),
                'x-secret-key: ' . config('api.TEMBO_SECRET_KEY'),
                'x-request-id: ' . $requestId
            ];

            $url = config('api.TEMBO_ENDPOINT') . 'wallet/main-balance';

            $data = $this->processor($post, $headers, $url);

            $data = json_decode($data);

            $response = [
                "message" => "Main Balance Retrieved",
                "notification" => "success",
                "data" => $data
            ];

            return $response;

        } catch (\Exception $e) {
            Log::error('TemboPlus', ['DepositFunds' => $e]);
            return ['message' => "Oops something went wrong", 'notification' => "failure"];
        }
    }

    // Ensure you have a method to generate a unique request ID if needed
    private function generateId()
    {
        return uniqid(); // Simple example, adjust as needed
    }

    // Ensure you have a method to process HTTP requests
    private function processor($post, $headers, $url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
