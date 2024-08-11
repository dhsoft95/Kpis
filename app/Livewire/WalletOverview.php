<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WalletOverview extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.wallet-overview';

    public $stats = [];
    public $totalCounts = [];

    public function mount()
    {
        $this->stats = $this->calculateStats();
        $this->totalCounts = $this->getTotalCounts();
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

        // Ensure all statuses are included in the result, even if count is 0
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

        // Ensure all statuses are included in the result, even if count is 0
        $totalCounts = array_fill_keys($statuses, 0);
        foreach ($counts as $status => $count) {
            $totalCounts[$status] = $count;
        }

        return $totalCounts;
    }
}
