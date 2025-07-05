<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;

class UserRoleChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Pengguna';

    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => [$adminCount, $userCount],
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                    ],
                     'borderColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                    ],
                ],
            ],
            'labels' => ['Admins', 'Users'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}