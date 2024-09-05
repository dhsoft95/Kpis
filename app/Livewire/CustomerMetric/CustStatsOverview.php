<?php

namespace App\Livewire\CustomerMetric;

use App\Models\feedback_answers;
use App\Models\feedbackQuestions;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CustStatsOverview extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.cust-stats-overview';

    public array $stats = [
        'nps' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'csat' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'ces' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
    ];

    public function mount(): void
    {
        $this->calculateStats();
    }

    public function calculateStats(): void
    {
        $this->stats['nps'] = $this->calculateNPS();
        $this->stats['csat'] = $this->calculateCSAT();
        $this->stats['ces'] = $this->calculateCES();
    }

    private function calculateNPS(): array
    {
        $currentWeekStart = Carbon::now()->startOfWeek()->startOfDay();
        $currentWeekEnd = Carbon::now()->endOfWeek()->endOfDay();

        $npsQuestionId = feedbackQuestions::where('type', 'nps')->pluck('id');

        $npsAnswers = feedback_answers::whereIn('feedback_question_id', $npsQuestionId)
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->get();

        $promoters = $npsAnswers->where('rating', '>=', 9)->count();
        $detractors = $npsAnswers->where('rating', '<=', 6)->count();
        $total = $npsAnswers->count();

        if ($total == 0) {
            return [
                'value' => 0,
                'percentageChange' => 0,
                'isGrowth' => false,
            ];
        }

        $currentValue = (($promoters / $total) - ($detractors / $total)) * 100;

        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek()->startOfDay();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek()->endOfDay();

        $previousNpsAnswers = feedback_answers::whereIn('feedback_question_id', $npsQuestionId)
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->get();

        $previousPromoters = $previousNpsAnswers->where('rating', '>=', 9)->count();
        $previousDetractors = $previousNpsAnswers->where('rating', '<=', 6)->count();
        $previousTotal = $previousNpsAnswers->count();

        $previousValue = $previousTotal > 0 ? (($previousPromoters / $previousTotal) - ($previousDetractors / $previousTotal)) * 100 : 0;

        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        Log::info("NPS Calculation: Promoters: $promoters, Detractors: $detractors, Total: $total");
        Log::info("NPS Current Value: $currentValue, Previous Value: $previousValue");

        return [
            'value' => round($currentValue, 2),
            'percentageChange' => round($percentageChange, 2),
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateCSAT(): array
    {
        $currentWeekStart = Carbon::now()->startOfWeek()->startOfDay();
        $currentWeekEnd = Carbon::now()->endOfWeek()->endOfDay();

        $csatQuestionId = feedbackQuestions::where('type', 'csat')->pluck('id');

        $csatAnswers = feedback_answers::whereIn('feedback_question_id', $csatQuestionId)
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->get();

        $satisfied = $csatAnswers->where('rating', '>=', 4)->count();
        $total = $csatAnswers->count();

        if ($total == 0) {
            return [
                'value' => 0,
                'percentageChange' => 0,
                'isGrowth' => false,
            ];
        }

        $currentValue = ($satisfied / $total) * 100;

        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek()->startOfDay();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek()->endOfDay();

        $previousCsatAnswers = feedback_answers::whereIn('feedback_question_id', $csatQuestionId)
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->get();

        $previousSatisfied = $previousCsatAnswers->where('rating', '>=', 4)->count();
        $previousTotal = $previousCsatAnswers->count();

        $previousValue = $previousTotal > 0 ? ($previousSatisfied / $previousTotal) * 100 : 0;

        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        Log::info("CSAT Calculation: Satisfied: $satisfied, Total: $total");
        Log::info("CSAT Current Value: $currentValue, Previous Value: $previousValue");

        return [
            'value' => round($currentValue, 2),
            'percentageChange' => round($percentageChange, 2),
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculateCES(): array
    {
        $currentWeekStart = Carbon::now()->startOfWeek()->startOfDay();
        $currentWeekEnd = Carbon::now()->endOfWeek()->endOfDay();

        $cesQuestionId = feedbackQuestions::where('type', 'ces')->pluck('id');

        $currentValue = feedback_answers::whereIn('feedback_question_id', $cesQuestionId)
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->avg('rating') ?? 0;

        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek()->startOfDay();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek()->endOfDay();

        $previousValue = feedback_answers::whereIn('feedback_question_id', $cesQuestionId)
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->avg('rating') ?? 0;

        $percentageChange = $this->calculatePercentageChange($previousValue, $currentValue);

        Log::info("CES Current Value: $currentValue, Previous Value: $previousValue");

        return [
            'value' => round($currentValue, 2),
            'percentageChange' => round($percentageChange, 2),
            'isGrowth' => $percentageChange >= 0,
        ];
    }

    private function calculatePercentageChange($previousValue, $currentValue): float
    {
        if ($previousValue == 0 && $currentValue == 0) {
            return 0;
        }
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : -100;
        }
        return (($currentValue - $previousValue) / abs($previousValue)) * 100;
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->stats,
        ];
    }
}
