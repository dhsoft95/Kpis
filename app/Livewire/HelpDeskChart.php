<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class HelpDeskChart extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.help-desk-chart';



    public array $stats = [
        'totalInteractions' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'chats' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
        'whatsApp' => ['value' => 0, 'percentageChange' => 0, 'isGrowth' => true],
    ];

    public function mount(): void
    {
        $this->calculateStats();
    }

    public function calculateStats(): void
    {
        $this->stats['totalInteractions'] = $this->getDummyData(150, 5.8, true);
        $this->stats['chats'] = $this->getDummyData(850, 3.2, true);
        $this->stats['whatsApp'] = $this->getDummyData(400, -1.5, false);
    }

    private function getDummyData($value, $percentageChange, $isGrowth): array
    {
        return [
            'value' => $value,
            'percentageChange' => $percentageChange,
            'isGrowth' => $isGrowth,
        ];
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->stats,
        ];
    }
}
