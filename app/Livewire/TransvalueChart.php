<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransvalueChart extends ChartWidget
{
    protected static ?string $heading = 'Transaction Volume (Amount) Week over Week';

    protected function getFilters(): ?array
    {
        return [
            'last4weeks' => 'Last 4 weeks',
            'last8weeks' => 'Last 8 weeks',
            'last12weeks' => 'Last 12 weeks',
        ];
    }

    protected function getData(): array
    {
        $weeks = match ($this->filter) {
            'last8weeks' => 8,
            'last12weeks' => 12,
            default => 4,
        };

        $data = $this->getWeeklyTransactionVolume($weeks);

        return [
            'datasets' => [
                [
                    'label' => 'Transaction Volume (Amount)',
                    'data' => $data['volumes'],
                    'borderColor' => '#4299e1',
                    'backgroundColor' => 'rgba(66, 153, 225, 0.5)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getWeeklyTransactionVolume(int $weeks): array
    {
        $endDate = Carbon::now()->endOfWeek();
        $startDate = $endDate->copy()->subWeeks($weeks)->startOfWeek();

        $data = DB::connection('mysql_second')->table('tbl_transactions')  
        ->select(DB::connection('mysql_second')->raw('YEARWEEK(created_at) as yearweek, SUM(CAST(sender_amount AS DECIMAL(15,2))) as volume'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 1)  // Assuming status 0 might be for failed or cancelled transactions
            ->groupBy('yearweek')
            ->orderBy('yearweek')
            ->get();

        $volumes = [];
        $labels = [];

        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = $startDate->copy()->addWeeks($i);
            $yearweek = $weekStart->format('YW');

            $volume = $data->firstWhere('yearweek', $yearweek)?->volume ?? 0;
            $volumes[] = round($volume, 2);
            $labels[] = $weekStart->format('M d') . ' - ' . $weekStart->endOfWeek()->format('M d');
        }

        return [
            'volumes' => $volumes,
            'labels' => $labels,
        ];
    }
}
