<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class AgsWallets extends Widget
{



    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.ags-wallets';

    public function mount()
    {
        $this->balance = $this->checkDisbursementBalanceTeraPay();
    }


    public static function checkDisbursementBalanceTeraPay()
    {
        $headers = [
            'X-USERNAME: ' . config('api.TERAPAYUSER'),
            'X-PASSWORD: ' . config('api.TERAPAYPASSWORD'),
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
