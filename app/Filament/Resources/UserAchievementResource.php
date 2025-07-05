<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAchievementResource\Pages;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserAchievementResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    protected static ?string $modelLabel = 'Pemberian Lencana';
    protected static ?string $pluralModelLabel = 'Pemberian Lencana';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('achievements.name')
                    ->label('Lencana yang Dimiliki')
                    ->badge()
                    ->limit(100)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Kelola Lencana')
                    ->form(function (Model $record) {
                        return [
                            Forms\Components\CheckboxList::make('achievements')
                                ->label('Pilih Lencana untuk User Ini')
                                ->relationship('achievements', 'name')
                                ->options(Achievement::all()->pluck('name', 'id'))
                                ->columns(2),
                        ];
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * [FIXED] Menggunakan sintaks route() yang benar
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserAchievements::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', '!=', 'admin');
    }

    // Mencegah halaman create dan edit bawaan
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return true; // Hanya untuk mengaktifkan EditAction
    }
}