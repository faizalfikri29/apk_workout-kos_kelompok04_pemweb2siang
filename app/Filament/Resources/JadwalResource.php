<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalResource\Pages;
use App\Filament\Resources\JadwalResource\RelationManagers; // <-- Tambahkan ini
use App\Models\Jadwal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JadwalResource extends Resource
{
    protected static ?string $model = Jadwal::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Workout';
    protected static ?string $modelLabel = 'Jadwal Workout';
    protected static ?int $navigationSort = 2;

    /**
     * PERBAIKAN 1: Form sekarang untuk membuat "Rencana Latihan", bukan satu gerakan.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_jadwal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('hari')
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
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * PERBAIKAN 2: Tabel sekarang menampilkan daftar "Rencana Latihan".
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_jadwal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hari')
                    ->sortable(),
                // Menampilkan jumlah gerakan di dalam setiap jadwal
                Tables\Columns\TextColumn::make('workouts_count')
                    ->counts('workouts')
                    ->label('Jumlah Gerakan'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    /**
     * PERBAIKAN 3: Mendaftarkan Relation Manager untuk mengelola "isi" jadwal.
     */
    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkoutsRelationManager::class,
        ];
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