<?php

namespace App\Livewire\AppInteractions;

use App\Models\UserInteraction;
use App\Models\Ticket;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

class HelpDeskChart extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.help-desk-chart';
    public $stats;

    public function mount()
    {
        $this->calculateStats();
    }

    public function calculateStats(): void
    {
        // Real WhatsApp data
        $currentWeekInteractions = UserInteraction::whereBetween('created_at', [now()->startOfWeek(), now()])->count();
        $lastWeekInteractions = UserInteraction::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();

        $percentageChange = $lastWeekInteractions > 0
            ? (($currentWeekInteractions - $lastWeekInteractions) / $lastWeekInteractions) * 100
            : 100;

        // Fetch Zendesk tickets from local database
        $zendeskStats = $this->getZendeskStats();

        $this->stats = [
            'whatsApp' => [
                'value' => $currentWeekInteractions,
                'percentageChange' => $percentageChange,
                'isGrowth' => $percentageChange >= 0,
            ],

            // Other dummy data
            'chats' => ['value' => 850, 'percentageChange' => 3.2, 'isGrowth' => true],
            'faq' => ['value' => 1200, 'percentageChange' => 7.5, 'isGrowth' => true],
            'socialMedia' => ['value' => 300, 'percentageChange' => 4.2, 'isGrowth' => true],
            'zendesk' => $zendeskStats['weekly'],
            'totalTickets' => $zendeskStats['total'],
        ];
    }

    private function getZendeskStats(): array
    {
        try {
            Log::info('Fetching Zendesk tickets from local database...');

            $currentWeekCount = Ticket::whereBetween('ticket_created_at', [now()->startOfWeek(), now()])->count();
            $lastWeekCount = Ticket::whereBetween('ticket_created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
            $totalTickets = Ticket::count();

            Log::info('Ticket counts:', [
                'currentWeekCount' => $currentWeekCount,
                'lastWeekCount' => $lastWeekCount,
                'totalTickets' => $totalTickets
            ]);

            $weeklyPercentageChange = $lastWeekCount > 0
                ? (($currentWeekCount - $lastWeekCount) / $lastWeekCount) * 100
                : 100;

            $lastWeekTotalTickets = Ticket::where('ticket_created_at', '<', now()->subWeek()->startOfWeek())->count();
            $totalPercentageChange = $lastWeekTotalTickets > 0
                ? (($totalTickets - $lastWeekTotalTickets) / $lastWeekTotalTickets) * 100
                : 100;

            return [
                'weekly' => [
                    'value' => $currentWeekCount,
                    'percentageChange' => round($weeklyPercentageChange, 2),
                    'isGrowth' => $weeklyPercentageChange >= 0,
                ],
                'total' => [
                    'value' => $totalTickets,
                    'percentageChange' => round($totalPercentageChange, 2),
                    'isGrowth' => $totalPercentageChange >= 0,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Zendesk tickets from database:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'weekly' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => false],
                'total' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => false]
            ];
        }
    }
}
