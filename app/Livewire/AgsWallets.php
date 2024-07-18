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
        $this->status = null;

        $response = self::checkDisbursementBalanceTeraPay();

        if (is_array($response) && !empty($response) && isset($response[0]->stdClass)) {
            $data = $response[0]->stdClass;
            $this->balance = $data->currentBalance ?? null;
            $this->currency = $data->currency ?? 'USD';
            $this->status = $data->status ?? 'unknown';
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

//        $response = json_decode(curl_exec($curl));
        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return $response;
    }
}
