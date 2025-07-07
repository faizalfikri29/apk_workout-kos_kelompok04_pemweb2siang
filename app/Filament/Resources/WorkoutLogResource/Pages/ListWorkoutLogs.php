<?php

namespace App\Filament\Resources\WorkoutLogResource\Pages;

use App\Filament\Resources\WorkoutLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkoutLogs extends ListRecords
{
    protected static string $resource = WorkoutLogResource::class;

    protected function getHeaderActions(): array
    {
        // Kosongkan karena log tidak boleh dibuat/ditambah dari panel
        return [];
    }
}
