<?php

namespace App\Livewire;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;
use App\Models\feedback_answers;
use App\Models\feedbackQuestions;

class WeeklyTrendsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'weeklyTrendsChart';
    protected static ?string $heading = 'Weekly Customer Satisfaction Trends';
    protected static string $chart = 'weekly_trends';

    protected function getOptions(): array
    {
        return array_merge(
            $this->getData(),
            [
                'chart' => [
                    'type' => $this->getType(),
                    'height' => 300,
                ],
                'colors' => ['#584408', '#E0B22C', '#F5E5B9'],
                'stroke' => [
                    'curve' => 'smooth',
                ],
                'xaxis' => [
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
            ]
        );
    }

    protected function getData(): array
    {
        $data = $this->getWeeklyData();

        return [
            'series' => [
                [
                    'name' => 'NPS',
                    'data' => $data['nps'],
                ],
                [
                    'name' => 'CSAT',
                    'data' => $data['csat'],
                ],
                [
                    'name' => 'CES',
                    'data' => $data['ces'],
                ],
            ],
            'xaxis' => [
                'categories' => $data['weeks'],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getWeeklyData(): array
    {
        $weeks = [];
        $npsData = [];
        $csatData = [];
        $cesData = [];

        for ($i = 11; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();

            $weeks[] = $startDate->format('M d') . ' - ' . $endDate->format('M d');

            $npsData[] = $this->calculateNPS($startDate, $endDate);
            $csatData[] = $this->calculateCSAT($startDate, $endDate);
            $cesData[] = $this->calculateCES($startDate, $endDate);
        }

        return [
            'weeks' => $weeks,
            'nps' => $npsData,
            'csat' => $csatData,
            'ces' => $cesData,
        ];
    }

    private function calculateNPS(Carbon $startDate, Carbon $endDate): float
    {
        $npsQuestionId = feedbackQuestions::where('type', 'nps')->pluck('id');

        $npsAnswers = feedback_answers::whereIn('feedback_question_id', $npsQuestionId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $promoters = $npsAnswers->where('rating', '>=', 9)->count();
        $detractors = $npsAnswers->where('rating', '<=', 6)->count();
        $total = $npsAnswers->count();

        if ($total == 0) {
            return 0;
        }

        return round((($promoters / $total) - ($detractors / $total)) * 100, 2);
    }

    private function calculateCSAT(Carbon $startDate, Carbon $endDate): float
    {
        $csatQuestionId = feedbackQuestions::where('type', 'csat')->pluck('id');

        $csatAnswers = feedback_answers::whereIn('feedback_question_id', $csatQuestionId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $satisfied = $csatAnswers->where('rating', '>=', 4)->count();
        $total = $csatAnswers->count();

        if ($total == 0) {
            return 0;
        }

        return round(($satisfied / $total) * 100, 2);
    }

    private function calculateCES(Carbon $startDate, Carbon $endDate): float
    {
        $cesQuestionId = feedbackQuestions::where('type', 'ces')->pluck('id');

        $cesValue = feedback_answers::whereIn('feedback_question_id', $cesQuestionId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('rating') ?? 0;

        return round($cesValue, 2);
    }
}
