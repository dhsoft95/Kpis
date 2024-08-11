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
        $this->fetchTemboBalance();
        $this->fetchCellulantBalance();
    }

    public function fetchDisbursementBalance(): void
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
        $this->currencyTembo = null;
        $this->statusTembo = null;

        $response = $this->mainBalance();

        if ($response['notification'] === 'success' && isset($response['data'])) {
            $data = $response['data'];
            $this->balanceTembo = $data->balance ?? null;
            $this->currencyTembo = $data->currency ?? 'USD';
            $this->statusTembo = 'available';
        } else {
            $this->errorTembo = $response['message'] ?? 'Unexpected response from Tembo API';
            Log::error('Tembo API Error', $response);
        }
    }

    public function fetchCellulantBalance()
    {

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
            $accountId = env('TEMBO_ACCOUNT_ID');
            $secretKey = env('TEMBO_SECRET_KEY');
            $requestId = $this->generateId();

            $headers = [
                'x-account-id: ' . $accountId,
                'x-secret-key: ' . $secretKey,
                'x-request-id: ' . $requestId
            ];

            $url = env('TEMBO_ENDPOINT') . 'wallet/main-balance';

            $data = $this->processor('{}', $headers, $url);

            $data = json_decode($data);

            $response = array(
                "message" => "Main Balance Retrieved",
                "notification" => "success",
                "data" => $data
            );

            return $response;

        } catch (Exception $e) {
            Log::error('TemboPlus', ['DepositFunds' => $e]);
            return ['message' => "Oops something went wrong", "notification" => "failure"];
        }
    }

    protected function generateId()
    {
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
            Log::error('Curl Error', ['error' => $error]);
            throw new Exception('Curl error: ' . $error);
        }

        curl_close($curl);

        return $response;
    }
}
