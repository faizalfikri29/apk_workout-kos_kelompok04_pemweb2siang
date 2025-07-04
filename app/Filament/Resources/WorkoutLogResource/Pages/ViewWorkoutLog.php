<?php

namespace App\Filament\Resources\WorkoutLogResource\Pages;

use App\Filament\Resources\WorkoutLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkoutLog extends ViewRecord
{
    protected static string $resource = WorkoutLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
