<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AgsWallets extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.ags-wallets';

    public $balance;
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

        $response = self::checkDisbursementBalanceTeraPay();

        if (isset($response->error)) {
            $this->error = $response->error->errorDescription;
            Log::error('TeraPay API Error', (array)$response->error);
        } elseif (isset($response->currentBalance)) {
            $this->balance = $response->currentBalance;
            $this->currency = $response->currency ?? 'USD';
        } else {
            $this->error = 'Unexpected response format from TeraPay API';
            Log::error('TeraPay API Unexpected Response', (array)$response);
        }
    }


    public static function checkDisbursementBalanceTeraPay()
    {
        $headers = [
            'X-USERNAME: simbaLive' ,
            'X-PASSWORD: b9c90ea40b459a7f9f065b2a8f318940677279ee54fbdaf76fa4040f93f1b041' ,
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

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        return $response;
    }
}
