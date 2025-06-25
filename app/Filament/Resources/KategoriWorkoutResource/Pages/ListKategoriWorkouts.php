<?php

namespace App\Filament\Resources\KategoriWorkoutResource\Pages;

use App\Filament\Resources\KategoriWorkoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriWorkouts extends ListRecords
{
    protected static string $resource = KategoriWorkoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
