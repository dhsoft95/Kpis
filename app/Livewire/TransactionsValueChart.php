<?php

namespace App\Livewire;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionsValueChart extends ApexChartWidget
{
    protected static ?string $chartId = 'transactionsValueChart';
    protected static ?string $heading = 'Value of Transactions (since Inception)';
    protected int $height = 300;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = null;

    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'bar',
                'height' => $this->height,
            ],
            'series' => $this->getData()['datasets'],
            'xaxis' => [
                'type' => 'datetime',
                'categories' => $this->getData()['labels'],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Total Value (TZS)',
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'colors' => ['#E0B22C', '#584408'],

            'fill' => [
                'type' => 'solid',
                'opacity' => 0.7,
            ],

            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],

        ];
    }

    protected function getData(): array
    {
        $data = $this->getTransactionData();
        Log::info('Chart Data:', $data);

        return [
            'datasets' => [
                [
                    'name' => 'Total Transaction Value',
                    'data' => $data['values'],
                ],
            ],
            'labels' => $data['dates'],
        ];
    }

    private function getTransactionData(): array
    {
        try {
            $query = DB::connection('mysql_second')
                ->table('tbl_simba_transactions')
                ->select(DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(credit_amount) - SUM(debit_amount) as net_value'))
                ->whereIn('status', ['', 'deposited', 'sent', 'received'])
                ->groupBy('date')
                ->orderBy('date');

            $transactions = $query->get();

            Log::info('Raw Transaction Data:', $transactions->toArray());

            if ($transactions->isEmpty()) {
                Log::warning('No transactions found');
                return $this->getDefaultData();
            }

            $cumulativeValue = 0;
            $dates = [];
            $values = [];

            foreach ($transactions as $transaction) {
                $cumulativeValue += $transaction->net_value;
                $dates[] = $transaction->date;
                $values[] = round($cumulativeValue, 2);
            }

            return [
                'dates' => $dates,
                'values' => $values,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getTransactionData: ' . $e->getMessage());
            return $this->getDefaultData();
        }
    }

    private function getDefaultData(): array
    {
        return [
            'dates' => [Carbon::now()->format('Y-m-d')],
            'values' => [0],
        ];
    }
}
