<?php

namespace App\Filament\Widgets;

use App\Models\WorkoutLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WorkoutLogChart extends ChartWidget
{
    protected static ?string $heading = 'Log Latihan per Bulan';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $logs = WorkoutLog::query()
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        $labels = collect(range(1, 12))->map(fn ($m) => Carbon::create()->month($m)->format('M'));

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Log',
                    'data' => $labels->map(fn ($_, $i) => $logs->get($i + 1, 0))->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
