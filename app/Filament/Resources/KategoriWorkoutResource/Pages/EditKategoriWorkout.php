<?php

namespace App\Filament\Resources\KategoriWorkoutResource\Pages;

use App\Filament\Resources\KategoriWorkoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriWorkout extends EditRecord
{
    protected static string $resource = KategoriWorkoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
