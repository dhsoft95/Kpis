<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WalletOverview extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.wallet-overview';

    public $stats = [];
    public $totalCounts = [];

    // Partner balance properties
    public $balanceTeraPay;
    public $currencyTeraPay;
    public $statusTeraPay;

    public $balanceTembo;
    public $availableBalanceTembo;
    public $statusTembo;

    public $balanceCellulant;
    public $currencyCellulant;
    public $statusCellulant;

    // Financial overview properties
    public $totalTransactions;
    public $totalTransactionValue;
    public $monthlyTransactions;
    public $monthlyTransactionValue;
    public $activeUsers;
    public $avgTransactionValuePerActiveCustomer;
    public $defaultCurrency = 'TZS';

    public function mount()
    {
        $this->stats = $this->calculateStats();
        $this->totalCounts = $this->getTotalCounts();
        $this->fetchPartnerBalances();
        $this->fetchTransactionData();
    }

    #[Computed]
    public function stats()
    {
        return $this->calculateStats();
    }

    public function calculateStats()
    {
        $statuses = ['active', 'failed', 'inactive', 'inprogress', 'pending'];
        $currentWeekCounts = $this->getWeekCounts($statuses, 0);
        $lastWeekCounts = $this->getWeekCounts($statuses, 1);

        $stats = [];
        foreach ($statuses as $status) {
            $currentCount = $currentWeekCounts[$status] ?? 0;
            $lastWeekCount = $lastWeekCounts[$status] ?? 0;

            $percentageChange = $lastWeekCount > 0
                ? (($currentCount - $lastWeekCount) / $lastWeekCount) * 100
                : 0;

            $stats[$status] = [
                'value' => $currentCount,
                'isGrowth' => $percentageChange >= 0,
                'percentageChange' => abs($percentageChange),
            ];
        }

        return $stats;
    }

    private function getWeekCounts(array $statuses, int $weeksAgo): array
    {
        $startDate = Carbon::now()->subWeeks($weeksAgo)->startOfWeek();
        $endDate = Carbon::now()->subWeeks($weeksAgo)->endOfWeek();

        $counts = DB::connection('mysql_second')
            ->table('users')
            ->select('wallet_status', DB::raw('COUNT(*) as count'))
            ->whereIn('wallet_status', $statuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('wallet_status')
            ->get()
            ->pluck('count', 'wallet_status')
            ->toArray();

        $allCounts = array_fill_keys($statuses, 0);
        foreach ($counts as $status => $count) {
            $allCounts[$status] = $count;
        }

        return $allCounts;
    }

    private function getTotalCounts(): array
    {
        $statuses = ['active', 'failed', 'inactive', 'inprogress', 'pending'];

        $counts = DB::connection('mysql_second')
            ->table('users')
            ->select('wallet_status', DB::raw('COUNT(*) as count'))
            ->whereIn('wallet_status', $statuses)
            ->groupBy('wallet_status')
            ->get()
            ->pluck('count', 'wallet_status')
            ->toArray();

        $totalCounts = array_fill_keys($statuses, 0);
        foreach ($counts as $status => $count) {
            $totalCounts[$status] = $count;
        }

        return $totalCounts;
    }

    public function fetchPartnerBalances(): void
    {
        $this->fetchTeraPay();
        $this->fetchTembo();
        $this->fetchCellulant();
    }

    private function fetchTeraPay(): void
    {
        $response = $this->checkDisbursementBalanceTeraPay();

        if (is_array($response) && !empty($response) && isset($response[0])) {
            $data = $response[0];
            $this->balanceTeraPay = $data['currentBalance'] ?? null;
            $this->currencyTeraPay = $data['currency'] ?? 'USD';
            $this->statusTeraPay = $data['status'] ?? 'available';
        } else {
            Log::error('TeraPay API Unexpected Response', ['response' => $response]);
        }
    }

    private function fetchTembo(): void
    {
        try {
            $headers = [
                'x-account-id' => env('TEMBO_ACCOUNT_ID'),
                'x-secret-key' => env('TEMBO_SECRET_KEY'),
                'x-request-id' => (string) Str::uuid(),
                'content-type' => 'application/json',
            ];

            $url = env('TEMBO_ENDPOINT') . 'wallet/main-balance';

            $response = Http::withHeaders($headers)->post($url);

            if ($response->successful()) {
                $data = $response->json();
                $this->balanceTembo = $data['currentBalance'] ?? null;
                $this->availableBalanceTembo = $data['availableBalance'] ?? null;
                $this->statusTembo = $data['accountStatus'] ?? 'unknown';
            } else {
                Log::error('Tembo API Error', ['status' => $response->status(), 'response' => $response->json()]);
            }
        } catch (\Exception $e) {
            Log::error('Tembo API Exception', ['error' => $e->getMessage()]);
        }
    }

    private function fetchCellulant()
    {
        // Dummy data for Cellulant
        $this->balanceCellulant = 67;
        $this->currencyCellulant = 'USD';
        $this->statusCellulant = 'available';
    }

    private function checkDisbursementBalanceTeraPay()
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

    public function fetchTransactionData(): void
    {
        // Fetch total transactions and value since inception
        $totals = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->selectRaw('COUNT(*) as count, SUM(credit_amount + debit_amount) as total')
            ->first();
        $this->totalTransactions = $totals->count;
        $this->totalTransactionValue = $totals->total;

        // Fetch monthly transactions and value
        $monthlyTotals = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('COUNT(*) as count, SUM(credit_amount + debit_amount) as total')
            ->first();
        $this->monthlyTransactions = $monthlyTotals->count;
        $this->monthlyTransactionValue = $monthlyTotals->total;

        // Fetch active users and their transaction value in the last 60 days
        $activeUserData = DB::connection('mysql_second')->table('tbl_simba_transactions')
            ->where('created_at', '>=', Carbon::now()->subDays(60))
            ->whereIn('status', ['deposited', 'sent', 'received'])
            ->where(function ($query) {
                $query->where('credit_amount', '>', 0)
                    ->orWhere('debit_amount', '>', 0);
            })
            ->selectRaw('COUNT(DISTINCT user_id) as active_users, SUM(credit_amount + debit_amount) as total_value')
            ->first();

        $this->activeUsers = $activeUserData->active_users;
        // Calculate Average Transaction Value per Active Customer
        if ($this->activeUsers > 0) {
            $this->avgTransactionValuePerActiveCustomer = $activeUserData->total_value / $this->activeUsers;
        } else {
            $this->avgTransactionValuePerActiveCustomer = 0;
        }
    }
}
