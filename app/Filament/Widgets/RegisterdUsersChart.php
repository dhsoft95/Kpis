<?php

namespace App\Filament\Widgets;

use App\Models\AppUser;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class RegisterdUsersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'registeredUsersChart';
    protected static ?string $heading = 'Registered Users Trend';

    protected function getFilters(): ?array
    {
        return [
            '6_months' => 'Last 6 months',
            '12_months' => 'Last 12 months',
            '24_months' => 'Last 24 months',
        ];
    }

    protected static ?int $contentHeight = 275;

    protected function getFormSchema(): array
    {
        return [
            Radio::make('chartType')
                ->default('bar')
                ->options([
                    'line' => 'Line',
                    'bar' => 'Col',
                    'area' => 'Area',
                ])
                ->inline(true)
                ->label('Type'),

            Grid::make()
                ->schema([
                    Toggle::make('chartMarkers')
                        ->default(false)
                        ->label('Markers'),

                    Toggle::make('chartGrid')
                        ->default(false)
                        ->label('Grid'),
                ]),

            TextInput::make('chartAnnotations')
                ->required()
                ->numeric()
                ->default(100)
                ->label('Annotations'),
        ];
    }

    protected function getOptions(): array
    {
        $filters = $this->filterFormData;
        $data = $this->getData();

        return [
            'chart' => [
                'type' => $filters['chartType'],
                'height' => 250,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Registered Users',
                    'data' => $data['counts'],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                ],
            ],
            'xaxis' => [
                'categories' => $data['labels'],
                'labels' => [
                    'style' => [
                        'fontWeight' => 400,
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontWeight' => 400,
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#fbbf24'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'grid' => [
                'show' => $filters['chartGrid'],
            ],
            'markers' => [
                'size' => $filters['chartMarkers'] ? 3 : 0,
            ],
            'tooltip' => [
                'enabled' => true,
            ],
            'stroke' => [
                'width' => $filters['chartType'] === 'line' ? 4 : 0,
            ],
            'colors' => ['#f59e0b'],
            'annotations' => [
                'yaxis' => [
                    [
                        'y' => $filters['chartAnnotations'],
                        'borderColor' => '#ef4444',
                        'borderWidth' => 1,
                        'label' => [
                            'borderColor' => '#ef4444',
                            'style' => [
                                'color' => '#fffbeb',
                                'background' => '#ef4444',
                            ],
                            'text' => 'Annotation: ' . $filters['chartAnnotations'],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getData(): array
    {
        $filter = $this->filter;

        switch ($filter) {
            case '6_months':
                $months = 6;
                break;
            case '12_months':
                $months = 12;
                break;
            case '24_months':
                $months = 24;
                break;
            default:
                $months = 12;
        }

        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $users = AppUser::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $counts = [];

        // Create an array of all months in the range
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $labels[] = $currentDate->format('M'); // Changed to only show month abbreviation
            $counts[$monthKey] = 0;
            $currentDate->addMonth();
        }

        // Fill in the actual counts
        foreach ($users as $user) {
            $counts[$user->month] = $user->count;
        }

        // Ensure the counts array is in the correct order
        ksort($counts);

        return [
            'labels' => $labels,
            'counts' => array_values($counts),
        ];
    }

    protected function getHeading(): string
    {
        $filter = $this->filter;
        $endDate = Carbon::now();

        switch ($filter) {
            case '6_months':
                $startDate = $endDate->copy()->subMonths(6)->startOfMonth();
                break;
            case '12_months':
                $startDate = $endDate->copy()->subMonths(12)->startOfMonth();
                break;
            case '24_months':
                $startDate = $endDate->copy()->subMonths(24)->startOfMonth();
                break;
            default:
                $startDate = $endDate->copy()->subMonths(12)->startOfMonth();
        }

        return "Registered Users Trend ({$startDate->format('M Y')} - {$endDate->format('M Y')})";
    }
}
