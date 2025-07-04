<?php

namespace App\Filament\Widgets;

use App\Models\Jadwal;
use App\Models\Tutorial;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pengguna', User::count())
                ->description('Jumlah semua pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Jadwal Latihan', Jadwal::count())
                ->description('Jumlah semua jadwal yang tersedia')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart([1, 5, 2, 8, 6, 12, 10]),

            Stat::make('Total Tutorial', Tutorial::count())
                ->description('Jumlah semua video tutorial')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('warning')
                ->chart([10, 4, 15, 3, 10, 2, 7]),
        ];
    }
}
