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
        $this->errorTembo = null;
        $this->balanceTembo = null;
        $this->availableBalanceTembo = null;
        $this->statusTembo = null;

        try {
            $response = $this->mainBalance();

            if (isset($response['notification']) && $response['notification'] === 'success') {
                $data = $response['data'];
                $this->balanceTembo = $data->currentBalance ?? null;
                $this->availableBalanceTembo = $data->availableBalance ?? null;
                $this->statusTembo = $data->accountStatus ?? 'unknown';
            } else {
                $this->errorTembo = $response['message'] ?? 'Unexpected response from Tembo API';
                Log::error('Tembo API Unexpected Response', (array)$response);
            }
        } catch (\Exception $e) {
            $this->errorTembo = 'Error fetching Tembo balance';
            Log::error('Tembo API Error', ['error' => $e->getMessage()]);
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

    protected function processor($post, $headers, $url)
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
