<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkoutLogResource\Pages;
use App\Models\WorkoutLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkoutLogResource extends Resource
{
    protected static ?string $model = WorkoutLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Aktivitas Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('workout_id')
                    ->relationship('workout', 'name')
                    ->required(),
                Forms\Components\TextInput::make('duration_minutes')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('calories_burned')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('workout.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('calories_burned')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkoutLogs::route('/'),
        ];
    }
}
