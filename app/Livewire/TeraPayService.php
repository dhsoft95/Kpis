<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;

class TeraPayService
{
    protected $baseUrl = 'https://vpnconnect.terrapay.com:21211/eig/gsma/accounts/all/balance';

    public function getBalance()
    {
        $headers = [
            'X-USERNAME' => 'simbaLive',
            'X-PASSWORD' => 'b9c90ea40b459a7f9f065b2a8f318940677279ee54fbdaf76fa4040f93f1b041',
            'X-DATE' => now()->format('Y-m-d H:i:s'),
            'X-ORIGINCOUNTRY' => 'TZ',
        ];

        $response = Http::withHeaders($headers)
            ->get($this->baseUrl);

        $response->throw(); // Throw an exception if the request fails

        return $response->json()[0]; // Return the first element of the array directly
    }
}
