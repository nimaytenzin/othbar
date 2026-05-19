<?php

namespace App\Filament\Widgets;

use App\Support\OrderDashboardStats;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class OrdersTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Auth::user()?->can('orders.view') ?? false;
    }

    protected ?string $heading = 'Orders placed vs fulfilled';

    protected ?string $description = 'Daily order volume compared to completed fulfillment';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '320px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $days = max(1, (int) ($this->filter ?? 30));
        $stats = app(OrderDashboardStats::class)->dailyPlacedVsFulfilled($days);

        return [
            'datasets' => [
                [
                    'label' => 'Orders placed',
                    'data' => $stats['placed'],
                    'backgroundColor' => 'rgba(30, 58, 42, 0.72)',
                    'borderColor' => '#1E3A2A',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Orders fulfilled',
                    'data' => $stats['fulfilled'],
                    'backgroundColor' => 'rgba(196, 132, 60, 0.72)',
                    'borderColor' => '#C4843C',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $stats['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 16,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'maxRotation' => 0,
                        'autoSkip' => true,
                        'maxTicksLimit' => 10,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                    'grid' => [
                        'color' => 'rgba(30, 58, 42, 0.08)',
                    ],
                ],
            ],
        ];
    }
}
