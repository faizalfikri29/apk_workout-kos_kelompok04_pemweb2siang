<?php

namespace App\Filament\Resources\WorkoutLogResource\Pages;

use App\Filament\Resources\WorkoutLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkoutLog extends EditRecord
{
    protected static string $resource = WorkoutLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
