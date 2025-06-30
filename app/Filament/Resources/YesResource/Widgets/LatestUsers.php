<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsers extends BaseWidget
{
    protected static ?string $heading = 'Pengguna Terbaru';

    protected int | string | array $columnSpan = 'full';
    
    // Atur urutan widget
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Daftar')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                // Tambahkan link untuk langsung ke halaman edit user
                Tables\Actions\Action::make('Lihat')
                    ->url(fn (User $record): string => UserResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-arrow-top-right-on-square'),
            ])
            ->paginated(false);
    }
}