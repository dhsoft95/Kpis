<?php

namespace App\Livewire\AppInteractions;

use App\Models\UserInteraction;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Zendesk\API\HttpClient as ZendeskAPI;

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

        // Fetch Zendesk tickets
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
            'phoneCalls' => ['value' => 500, 'percentageChange' => -2.1, 'isGrowth' => false],
            'zendesk' => $zendeskStats,

        ];
    }

    private function getZendeskStats(): array
    {
        $subdomain = config('services.zendesk.subdomain');
        $username = config('services.zendesk.username');
        $token = config('services.zendesk.token');

        Log::info('Zendesk API Config:', [
            'subdomain' => $subdomain,
            'username' => $username,
            'token_length' => strlen($token)
        ]);

        $client = new ZendeskAPI($subdomain);
        $client->setAuth('basic', ['username' => $username, 'token' => $token]);

        try {
            Log::info('Fetching current week tickets...');
            $currentWeekTickets = $client->tickets()->findAll([
                'start_time' => now()->startOfWeek()->toIso8601String(),
                'end_time' => now()->toIso8601String()
            ]);
            Log::info('Current week tickets response:', ['response' => json_encode($currentWeekTickets)]);

            Log::info('Fetching last week tickets...');
            $lastWeekTickets = $client->tickets()->findAll([
                'start_time' => now()->subWeek()->startOfWeek()->toIso8601String(),
                'end_time' => now()->subWeek()->endOfWeek()->toIso8601String()
            ]);
            Log::info('Last week tickets response:', ['response' => json_encode($lastWeekTickets)]);

            $currentWeekCount = count($currentWeekTickets->tickets);
            $lastWeekCount = count($lastWeekTickets->tickets);

            Log::info('Ticket counts:', [
                'currentWeekCount' => $currentWeekCount,
                'lastWeekCount' => $lastWeekCount
            ]);

            $percentageChange = $lastWeekCount > 0
                ? (($currentWeekCount - $lastWeekCount) / $lastWeekCount) * 100
                : 100;

            return [
                'value' => $currentWeekCount,
                'percentageChange' => round($percentageChange, 2),
                'isGrowth' => $percentageChange >= 0,
            ];
        } catch (\Exception $e) {
            Log::error('Zendesk API Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['value' => 0, 'percentageChange' => 0, 'isGrowth' => false];
        }
    }
}
