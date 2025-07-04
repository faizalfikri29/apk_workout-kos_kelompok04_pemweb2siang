<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class LatestUsersWidget extends BaseWidget
{
    protected static ?int $sort = 4; // Urutan widget di dasbor

    protected int | string | array $columnSpan = 'full'; // Agar widget memenuhi lebar

    public function getTableHeading(): string
    {
        return 'Pengguna Baru Terdaftar';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama'),
                Tables\Columns\TectColumn::make('email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Bergabung')
                    ->since(),
            ])
            ->actions([]);
    }
}
