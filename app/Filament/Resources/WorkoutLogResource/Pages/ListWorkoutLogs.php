<?php
// File: app/Filament/Resources/WorkoutLogResource/Pages/ListWorkoutLogs.php

namespace App\Filament\Resources\WorkoutLogResource\Pages;

use App\Filament\Resources\WorkoutLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkoutLogs extends ListRecords
{
    protected static string $resource = WorkoutLogResource::class;

    protected function getHeaderActions(): array
    {
        // Dikosongkan untuk menghilangkan tombol "New Log Latihan"
        return [];
    }
}