<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CurrencyController extends Controller
{
    private $exchangeService;

    public function __construct(CurrencyExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function convertFromBase(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'to' => 'required|string|size:3'
        ]);

        $amount = $request->input('amount');
        $toCurrency = strtoupper($request->input('to'));

        try {
            $result = $this->exchangeService->exchangeFromBase($amount, $toCurrency);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSupportedCurrencies()
    {
        try {
            $currencies = $this->exchangeService->getSupportedCurrencies();
            return response()->json($currencies);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}
