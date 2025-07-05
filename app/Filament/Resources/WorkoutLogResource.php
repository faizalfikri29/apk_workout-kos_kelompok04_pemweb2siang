<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkoutLogResource\Pages;
use App\Models\WorkoutLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WorkoutLogResource extends Resource
{
    protected static ?string $model = WorkoutLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Aktivitas Pengguna';

    protected static ?string $modelLabel = 'Log Latihan';

    protected static ?string $pluralModelLabel = 'Log Latihan';

    public static function form(Form $form): Form
    {
        // Tidak perlu form karena log tidak bisa diubah
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable(),

               Tables\Columns\TextColumn::make('duration_seconds')
    ->label('Durasi')
    ->formatStateUsing(function (int $state): string {
        $minutes = floor($state / 60);
        $seconds = $state % 60;
        return "{$minutes} menit {$seconds} detik";
    })
    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Selesai')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                // Tidak ada action edit/show
            ])
            ->bulkActions([
                // Tidak ada bulk delete
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkoutLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
