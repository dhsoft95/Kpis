<?php

namespace App\Livewire;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class financialKpis extends Widget
{
    protected static string $view = 'livewire.financial-kpis';
//    protected static string $view = 'filament.widgets.financial-kpis';

    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    protected function loadStats()
    {
        // Fetch data from the database
        $totalAmount = DB::connection('mysql_second')->table('tbl_transactions')->where('status', 3)->sum('sender_amount');
        $totalFailedAmount = DB::connection('mysql_second')->table('tbl_transactions')->where('status', 2)->sum('sender_amount');
        $revenue = DB::connection('mysql_second')->table('tbl_transactions')->where('status', 1)->sum('sender_amount');

        // Set the stats data
        $this->stats = [
            'totalAmount' => ['value' => $totalAmount, 'percentageChange' => 5.2, 'isGrowth' => true],
            'totalFailedAmount' => ['value' => $totalFailedAmount, 'percentageChange' => -3.1, 'isGrowth' => false],
            'revenue' => ['value' => $revenue, 'percentageChange' => 4.8, 'isGrowth' => true],
        ];
    }

    protected function getStats(): array
    {
        return [
            'stats' => $this->stats,
        ];
    }
}
