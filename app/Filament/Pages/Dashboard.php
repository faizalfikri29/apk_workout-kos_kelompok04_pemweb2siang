<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm; // Import trait

class Dashboard extends BaseDashboard
{
    use HasFiltersForm; // Gunakan trait ini

    /**
     * Tentukan form filter di sini.
     */
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('startDate')
                    ->label('Tanggal Mulai'),
                DatePicker::make('endDate')
                    ->label('Tanggal Selesai'),
            ]);
    }

    /**
     * Override method getWidgets() untuk menampilkan widget Anda.
     */
    public function getWidgets(): array
    {
        return [
            \App\Filament\Resources\AdminResource\Widgets\StatsOverview::class,
            \App\Filament\Resources\AdminResource\Widgets\UsersChart::class,
            \App\Filament\Resources\AdminResource\Widgets\WorkoutCategoryChart::class,
        ];
    }
}