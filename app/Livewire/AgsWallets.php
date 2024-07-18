<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AgsWallets extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.ags-wallets';

    public $balance; // Property to store the balance

    public function mount()
    {
        $this->balance = $this->checkDisbursementBalanceTeraPay();
    }

    public function checkDisbursementBalanceTeraPay()
    {
        $response = Http::withoutVerifying()->withHeaders([
            'X-USERNAME' => config('api.TERAPAYUSER'),
            'X-PASSWORD' => config('api.TERAPAYPASSWORD'),
            'X-DATE' => now()->format('Y-m-d H:i:s'),
            'X-ORIGINCOUNTRY' => 'TZ',
        ])->get('https://vpnconnect.terrapay.com:21211/eig/gsma/accounts/all/balance');

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error('Error fetching TeraPay balance: ' . $response->status() . ' - ' . $response->body());
            return null;
        }
    }
}
