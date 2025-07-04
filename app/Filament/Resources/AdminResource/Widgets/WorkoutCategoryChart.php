<?php

// Pastikan namespace-nya adalah App\Filament\Widgets
namespace App\Filament\Widgets;

use App\Models\KategoriWorkout;
use Filament\Widgets\ChartWidget;

class WorkoutCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Workout per Kategori';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $categories = KategoriWorkout::withCount('workouts')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Workout',
                    'data' => $categories->pluck('workouts_count')->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ],
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
