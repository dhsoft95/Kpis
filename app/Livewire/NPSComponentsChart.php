<?php

namespace App\Livewire;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;
use App\Models\feedback_answers;
use App\Models\feedbackQuestions;

class NPSComponentsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'nPSComponentsChart';
    protected static ?string $heading = 'NPS Components Breakdown';

    protected function getOptions(): array
    {
        $data = $this->getWeeklyNPSData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'stacked' => true,
            ],
            'series' => [
                [
                    'name' => 'Promoters',
                    'data' => $data['promoters'],
                ],
                [
                    'name' => 'Passives',
                    'data' => $data['passives'],
                ],
                [
                    'name' => 'Detractors',
                    'data' => $data['detractors'],
                ],
            ],
            'xaxis' => [
                'categories' => $data['weeks'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#28a745', '#ffc107', '#dc3545'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 1,
                    'horizontal' => true,
                ],
            ],
        ];
    }

    private function getWeeklyNPSData(): array
    {
        $weeks = [];
        $promoters = [];
        $passives = [];
        $detractors = [];

        for ($i = 7; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            $weeks[] = $startDate->format('M d') . ' - ' . $endDate->format('M d');

            $npsData = $this->calculateNPSComponents($startDate, $endDate);
            $promoters[] = $npsData['promoters'];
            $passives[] = $npsData['passives'];
            $detractors[] = $npsData['detractors'];
        }

        return [
            'weeks' => $weeks,
            'promoters' => $promoters,
            'passives' => $passives,
            'detractors' => $detractors,
        ];
    }

    private function calculateNPSComponents(Carbon $startDate, Carbon $endDate): array
    {
        $npsQuestionId = feedbackQuestions::where('type', 'nps')->pluck('id');

        $npsAnswers = feedback_answers::whereIn('feedback_question_id', $npsQuestionId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $total = $npsAnswers->count();
        if ($total == 0) {
            return [
                'promoters' => 0,
                'passives' => 0,
                'detractors' => 0,
            ];
        }

        $promoters = $npsAnswers->where('rating', '>=', 9)->count();
        $detractors = $npsAnswers->where('rating', '<=', 6)->count();
        $passives = $total - $promoters - $detractors;

        return [
            'promoters' => round(($promoters / $total) * 100, 2),
            'passives' => round(($passives / $total) * 100, 2),
            'detractors' => round(($detractors / $total) * 100, 2),
        ];
    }
}
