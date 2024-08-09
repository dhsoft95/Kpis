<?php

namespace App\Livewire;

use App\Models\UserInteraction;
use Filament\Widgets\Widget;

class HelpDeskChart extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.help-desk-chart';
    public $stats;

    public function mount()
    {
        $this->calculateStats();
    }

    public function calculateStats()
    {
        // Real WhatsApp data
        $currentWeekInteractions = UserInteraction::whereBetween('created_at', [now()->startOfWeek(), now()])->count();
        $lastWeekInteractions = UserInteraction::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();

        $percentageChange = $lastWeekInteractions > 0
            ? (($currentWeekInteractions - $lastWeekInteractions) / $lastWeekInteractions) * 100
            : 100;

        $this->stats = [
            'whatsApp' => [
                'value' => $currentWeekInteractions,
                'percentageChange' => $percentageChange,
                'isGrowth' => $percentageChange >= 0,
            ],
            // Dummy data for other metrics
            'chats' => ['value' => 850, 'percentageChange' => 3.2, 'isGrowth' => true],
            'faq' => ['value' => 1200, 'percentageChange' => 7.5, 'isGrowth' => true],
            'socialMedia' => ['value' => 300, 'percentageChange' => 4.2, 'isGrowth' => true],
            'phoneCalls' => ['value' => 500, 'percentageChange' => -2.1, 'isGrowth' => false],
            'email' => ['value' => 750, 'percentageChange' => 1.8, 'isGrowth' => true],
        ];
    }

}
