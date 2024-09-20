<?php

namespace App\Livewire;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MonthlyTransactionsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'monthlyTransactionsChart';
    protected static ?string $heading = 'Monthly Number & Value of Transactions';
    protected int $height = 300;
    protected static ?string $pollingInterval = null;

    protected function getOptions(): array
    {
        $data = $this->getData();
        Log::info('Chart Options Data:', $data);

        return [
            'chart' => [
                'type' => 'line',
                'height' => $this->height,
            ],
            'series' => [
                [
                    'name' => 'Number of Transactions',
                    'type' => 'column',
                    'data' => $data['datasets'][0]['data'],
                ],
                [
                    'name' => 'Total Value (TZS)',
                    'type' => 'line',
                    'data' => $data['datasets'][1]['data'],
                ],
            ],
            'xaxis' => [
                'categories' => $data['labels'],
            ],
            'yaxis' => [
                [
                    'title' => [
                        'text' => 'Number of Transactions',
                    ],
//                    'labels' => [
//                        'formatter' => "function(value) { return Math.round(value); }",
//                    ],
                ],
                [
                    'opposite' => false,
                    'title' => [
                        'text' => 'Total Value (TZS)',
                    ],
//                    'labels' => [
//                        'formatter' => "function(value) { return (value / 1000000).toFixed(2) + 'M'; }",
//                    ],
                ],
            ],
            'stroke' => [
                'width' => [0, 4],
            ],
            'colors' => ['#E0B22C', '#584408'],
            'dataLabels' => [
                'enabled' => false,
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
                'y' => [
                    'formatter' => [
                        "function(value) { return Math.round(value); }",
                        "function(value) { return 'TZS ' + value.toFixed(2); }",
                    ],
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $data = $this->getMonthlyTransactionData();
        Log::info('Monthly Chart Data:', $data);

        return [
            'datasets' => [
                [
                    'name' => 'Number of Transactions',
                    'data' => $data['counts'],
                ],
                [
                    'name' => 'Total Value (TZS)',
                    'data' => $data['values'],
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    private function getMonthlyTransactionData(): array
    {
        try {
            $query = DB::connection('mysql_second')
                ->table('tbl_simba_transactions')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(credit_amount) - SUM(debit_amount) as net_value')
                )
                ->whereIn('status', ['', 'deposited', 'sent', 'received'])
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6);  // Last 6 months

            Log::info('SQL Query: ' . $query->toSql());
            Log::info('SQL Bindings: ' . json_encode($query->getBindings()));

            $transactions = $query->get();

            Log::info('Raw Monthly Transaction Data:', $transactions->toArray());

            if ($transactions->isEmpty()) {
                Log::warning('No monthly transactions found');
                return $this->getDefaultData();
            }

            $months = [];
            $counts = [];
            $values = [];

            foreach ($transactions as $transaction) {
                $months[] = Carbon::createFromFormat('Y-m', $transaction->month)->format('M Y');
                $counts[] = $transaction->count;
                $values[] = round($transaction->net_value, 2);
            }

            // Reverse the arrays to show oldest to newest
            $months = array_reverse($months);
            $counts = array_reverse($counts);
            $values = array_reverse($values);

            return [
                'months' => $months,
                'counts' => $counts,
                'values' => $values,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyTransactionData: ' . $e->getMessage());
            return $this->getDefaultData();
        }
    }

    private function getDefaultData(): array
    {
        $currentMonth = Carbon::now()->format('M Y');
        return [
            'months' => [$currentMonth],
            'counts' => [0],
            'values' => [0],
        ];
    }
}
