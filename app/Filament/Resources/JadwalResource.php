<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalResource\Pages;
use App\Models\Jadwal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class JadwalResource extends Resource
{
    protected static ?string $model = Jadwal::class;

    // ✅ Tambahan permintaan
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Workout';
    protected static ?string $modelLabel = 'Jadwal Workout';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_workout')->required(),
                Select::make('kategori')
                    ->options([
                        'Cardio' => 'Cardio',
                        'Strength' => 'Strength',
                        'Stretching' => 'Stretching',
                    ])
                    ->required(),
                TimePicker::make('waktu_mulai')->required(),
                TimePicker::make('waktu_selesai')->required(),
                Select::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                        'Minggu' => 'Minggu',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_workout')->label('Workout')->searchable(),
                TextColumn::make('kategori')->sortable(),
                TextColumn::make('hari')->sortable(),
                TextColumn::make('waktu_mulai')->label('Mulai')->time(),
                TextColumn::make('waktu_selesai')->label('Selesai')->time(),
            ])
            ->filters([
                // Bisa ditambahkan filter hari atau kategori
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwals::route('/'),
            'create' => Pages\CreateJadwal::route('/create'),
            'edit' => Pages\EditJadwal::route('/{record}/edit'),
        ];
    }
}
