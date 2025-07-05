<?php

// File: app/Filament/Resources/WorkoutLogResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkoutLogResource\Pages;
use App\Models\WorkoutLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WorkoutLogResource extends Resource
{
    protected static ?string $model = WorkoutLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    // Grup navigasi baru untuk aktivitas pengguna
    protected static ?string $navigationGroup = 'Aktivitas Pengguna';

    protected static ?string $modelLabel = 'Log Latihan';

    protected static ?string $pluralModelLabel = 'Log Latihan';

    public static function form(Form $form): Form
    {
        // Form sengaja dikosongkan karena kita tidak akan mengedit data ini
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan nama user yang melakukan latihan
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable(),

                // Kolom untuk menampilkan nama workout yang dilakukan
                Tables\Columns\TextColumn::make('workout.nama_workout')
                    ->label('Nama Latihan')
                    ->searchable()
                    ->sortable(),

                // Kolom untuk menampilkan durasi, diformat menjadi menit
                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('Durasi')
                    ->formatStateUsing(fn (int $state): string => round($state / 60) . ' menit')
                    ->sortable(),

                // Kolom untuk menampilkan kapan latihan dicatat
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Selesai')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                // Filter bisa ditambahkan di sini jika perlu
            ])
            ->actions([
                // Tidak ada action karena data ini read-only
            ])
            ->bulkActions([
                // Tidak ada bulk action
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkoutLogs::route('/'),
        ];
    }

    // Fungsi ini untuk memastikan tidak ada tombol "Create" atau "Edit"
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
