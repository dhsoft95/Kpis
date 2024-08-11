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

    public function mount()
    {
        $this->stats = $this->calculateStats();
    }

    #[Computed]
    public function stats()
    {
        return $this->calculateStats();
    }

    public function calculateStats()
    {
        $statuses = ['active','failed', 'inactive', 'inprogress'];
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

        return DB::connection('mysql_second')
            ->table('users')
            ->whereIn('wallet_status', $statuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('wallet_status')
            ->pluck(DB::raw('COUNT(*) as count'), 'wallet_status')
            ->toArray();

    }
}
