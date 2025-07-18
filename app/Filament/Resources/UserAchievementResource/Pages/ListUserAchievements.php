<?php

namespace App\Filament\Resources\UserAchievementResource\Pages;

use App\Filament\Resources\UserAchievementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserAchievements extends ListRecords
{
    protected static string $resource = UserAchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kita tidak perlu tombol "Create" di sini
        ];
    }
}