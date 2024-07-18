<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

class AgsWallets extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.ags-wallets';

    public $balance;
    public $currency;
    public $status;
    public $error;

    public function mount()
    {
        $this->fetchDisbursementBalance();
    }

    public function fetchDisbursementBalance()
    {
        $this->error = null;
        $this->balance = null;
        $this->currency = null;
        $this->status = null;

        $rawResponse = self::checkDisbursementBalanceTeraPay();
        Log::info('Raw TeraPay API Response', ['response' => $rawResponse]);

        $response = json_decode($rawResponse);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error = 'Failed to parse API response';
            Log::error('TeraPay API JSON Parse Error', ['error' => json_last_error_msg()]);
            return;
        }

        if (is_array($response) && !empty($response) && isset($response[0]->currentBalance)) {
            $this->balance = $response[0]->currentBalance;
            $this->currency = $response[0]->currency ?? 'USD';
            $this->status = $response[0]->status ?? 'unknown';
        } elseif (isset($response->error)) {
            $this->error = $response->error->errorDescription ?? 'Unknown API error';
            Log::error('TeraPay API Error', (array)$response->error);
        } else {
            $this->error = 'Unexpected response format from TeraPay API';
            Log::error('TeraPay API Unexpected Response', (array)$response);
        }
    }

    public static function checkDisbursementBalanceTeraPay()
    {
        $headers = [
            'X-USERNAME: simbaLive',
            'X-PASSWORD: b9c90ea40b459a7f9f065b2a8f318940677279ee54fbdaf76fa4040f93f1b041',
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

        if (curl_errno($curl)) {
            Log::error('cURL Error', ['error' => curl_error($curl)]);
        }

        curl_close($curl);

        return $response;
    }
}
