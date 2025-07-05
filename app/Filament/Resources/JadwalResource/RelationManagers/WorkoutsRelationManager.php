<?php

namespace App\Filament\Resources\JadwalResource\RelationManagers;

use App\Models\Tutorial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WorkoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'workouts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Menggunakan ->relationship() yang merupakan cara yang benar di Filament
                Forms\Components\Select::make('tutorial_id')
                    ->label('Pilih Workout dari Tutorial')
                    ->relationship(name: 'tutorial', titleAttribute: 'nama_tutorial')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('nama_workout')
                    ->label('Nama Workout')
                    ->helperText('Akan diisi otomatis dari tutorial jika dikosongkan.')
                    ->maxLength(255),

                Forms\Components\TextInput::make('durasi_menit')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_workout')
            ->columns([
                Tables\Columns\TextColumn::make('nama_workout'),
                Tables\Columns\TextColumn::make('durasi_menit'),
                Tables\Columns\TextColumn::make('tutorial.nama_tutorial')->label('Dari Tutorial'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    // Mengisi nama_workout dari tutorial secara otomatis saat membuat
                    ->mutateFormDataUsing(function (array $data): array {
                        if (empty($data['nama_workout']) && !empty($data['tutorial_id'])) {
                            $tutorial = Tutorial::find($data['tutorial_id']);
                            $data['nama_workout'] = $tutorial?->nama_tutorial;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(), // Tambahkan ini untuk melihat detail workout
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
