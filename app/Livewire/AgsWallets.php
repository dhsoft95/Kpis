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
    public $currency;
    public $status;
    protected $teraPayService; // Dependency Injection

    public function mount(TeraPayService $teraPayService)
    {
        $this->teraPayService = $teraPayService;
        $this->fetchDisbursementBalance();
    }

    public function fetchDisbursementBalance()
    {
        $this->reset(['error', 'balance', 'currency', 'status']);

        try {
            $response = $this->teraPayService->getBalance();

            $this->balance = $response['currentBalance'] ?? 0;
            $this->currency = $response['currency'] ?? 'USD';
            $this->status = $response['status'] ?? 'unknown';
        } catch (\Exception $e) {
            $this->error = 'Failed to fetch balance: ' . $e->getMessage();
            Log::error('TeraPay API Error', ['exception' => $e]);
        }
    }



}

