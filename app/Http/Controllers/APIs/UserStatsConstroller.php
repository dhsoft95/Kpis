<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserStatsConstroller extends Controller
{
    public function getWeekOnWeekChange()
    {
        $today = Carbon::now();

        // Define the start and end of the current and previous week
        $startOfCurrentWeek = $today->startOfWeek();
        $endOfCurrentWeek = $today->endOfWeek();

        $startOfPreviousWeek = $startOfCurrentWeek->copy()->subWeek();
        $endOfPreviousWeek = $endOfCurrentWeek->copy()->subWeek();

        // Get counts for the previous week
        $previousWeekActive = DB::table('users')
            ->where('status', 'active')
            ->whereBetween('created_at', [$startOfPreviousWeek, $endOfPreviousWeek])
            ->count();

        $previousWeekInactive = DB::table('users')
            ->where('status', 'inactive')
            ->whereBetween('created_at', [$startOfPreviousWeek, $endOfPreviousWeek])
            ->count();

        // Get counts for the current week
        $currentWeekActive = DB::table('users')
            ->where('status', 'active')
            ->whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])
            ->count();

        $currentWeekInactive = DB::table('users')
            ->where('status', 'inactive')
            ->whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])
            ->count();

        // Calculate percentage changes
        $activeChange = $this->calculatePercentageChange($previousWeekActive, $currentWeekActive);
        $inactiveChange = $this->calculatePercentageChange($previousWeekInactive, $currentWeekInactive);

        return [
            'active_change' => $activeChange,
            'inactive_change' => $inactiveChange,
        ];
    }
    private function calculatePercentageChange($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

}
