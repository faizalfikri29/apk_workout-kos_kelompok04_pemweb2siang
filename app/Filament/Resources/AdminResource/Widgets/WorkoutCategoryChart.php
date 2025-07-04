<?php

// Pastikan namespace-nya adalah App\Filament\Widgets
namespace App\Filament\Widgets;

use App\Models\KategoriWorkout;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class WorkoutCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Tipe Latihan';

    protected static ?int $sort = 3;

    // Menambahkan properti untuk tinggi maksimum chart agar lebih rapi
    protected static ?string $maxHeight = '320px';

    protected function getData(): array
    {
        // Menggunakan cache selama 1 jam untuk data yang jarang berubah, agar lebih cepat
        $data = Cache::remember('workout_category_chart', now()->addHour(), function () {
            return KategoriWorkout::withCount('workouts')->get();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Workout',
                    'data' => $data->pluck('workouts_count')->toArray(),
                    // Palet warna baru yang lebih modern dan menarik
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.7)',  // Blue
                        'rgba(16, 185, 129, 0.7)',  // Emerald
                        'rgba(249, 115, 22, 0.7)',   // Orange
                        'rgba(239, 68, 68, 0.7)',    // Red
                        'rgba(139, 92, 246, 0.7)',   // Violet
                        'rgba(236, 72, 153, 0.7)',  // Pink
                    ],
                    'borderColor' => [
                        '#ffffff',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        // Mengubah tipe chart menjadi doughnut untuk tampilan yang lebih modern
        return 'doughnut';
    }

    // Menambahkan kustomisasi options untuk animasi dan tampilan
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
            ],
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
                'duration' => 1500,
                'easing' => 'easeInOutCubic',
            ],
            'cutout' => '50%', // Mengatur ukuran lubang di tengah doughnut chart
            'layout' => [
                'padding' => 10,
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
             // Menambahkan efek hover yang lebih menarik
            'onHover' => new \Illuminate\Support\Js('(event, chartElement) => {
                event.native.target.style.cursor = chartElement[0] ? "pointer" : "default";
            }'),
        ];
    }
}
