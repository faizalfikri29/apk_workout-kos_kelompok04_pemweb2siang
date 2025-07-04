<?php
// File: app/Filament/Resources/JadwalResource/RelationManagers/WorkoutsRelationManager.php

namespace App\Filament\Resources\JadwalResource\RelationManagers;

use App\Models\Tutorial; // <-- Pastikan ini ada
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
                Forms\Components\TextInput::make('nama_workout')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('durasi_menit')
                    ->required()
                    ->numeric()
                    ->label('Durasi (Menit)'),
                
                // INI ADALAH CARA YANG BENAR DAN AKAN MEMPERBAIKI ERROR
               Forms\Components\Select::make('tutorial_id')
    ->label('Tutorial Terkait')
    // BENAR - karena menggunakan nama kolom yang ada di database, yaitu 'judul'
->options(Tutorial::whereNotNull('judul')->pluck('judul', 'id'))
    ->searchable()
    ->nullable(),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_workout')
            ->columns([
                Tables\Columns\TextColumn::make('nama_workout'),
                Tables\Columns\TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->suffix(' menit')
                    ->sortable(),
                // Menampilkan nama tutorial terkait di tabel
                Tables\Columns\TextColumn::make('tutorial.nama_tutorial')
                    ->label('Tutorial')
                    ->placeholder('Tidak ada'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}