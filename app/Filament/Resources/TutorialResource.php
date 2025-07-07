<?php

namespace App\Filament\Resources;

use App\Models\KategoriWorkout; 
use App\Filament\Resources\TutorialResource\Pages;
use App\Models\Tutorial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Workout; // Pastikan ini adalah model Workout Anda
use Filament\Tables\Columns\TextColumn;

class TutorialResource extends Resource
{
    protected static ?string $model = Tutorial::class;
    protected static ?string $navigationLabel = 'Tutorial Workout';
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'Manajemen Konten';
    protected static ?string $modelLabel = 'Tutorial Workout';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    // Forms\Components\TextInput::make('name') // Kolom input 'name' dari Tutorial Workout
                    // ->required()
                    // ->maxLength(255),
                    Forms\Components\TextInput::make('nama_tutorial')
                        ->label('Judul Workout')
                        ->placeholder('Contoh: Cardio Ringan Pagi')
                        ->required(),

                    Forms\Components\FileUpload::make('gambar_url')
                        ->image()
                        ->directory('tutorials')
                        ->label('Upload Gambar')
                        ->required(),
                ]),

                Forms\Components\Select::make('kategori_workout_id')
                    ->label('Kategori')
                    ->options(KategoriWorkout::all()->pluck('name', 'id'))
                    ->searchable()
                    ->options(
                        fn () => \App\Models\KategoriWorkout::all()->pluck('name', 'id')
                    )
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('url_video')
                    ->label('Link Video Tutorial')
                    ->placeholder('https://youtube.com/...')
                    ->url()
                    ->required(),

                Forms\Components\Textarea::make('deskripsi_tutorial')
                    ->label('Langkah-langkah Workout')
                    ->rows(6)
                    ->placeholder("- Langkah 1\n- Langkah 2\n- Langkah 3")
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('gambar_url')
                    ->label('Gambar')
                    ->size(60),
                // Menggunakan 'nama_tutorial' yang merupakan nama kolom yang benar
                Tables\Columns\TextColumn::make('nama_tutorial')
                    ->label('Judul Workout')
                    ->description(fn (Tutorial $record): string => $record->kategoriWorkout->name ?? '')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategoriWorkout.name')
                    ->label('Kategori')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url_video')
                    ->label('Video Tutorial')
                    ->url(fn (Tutorial $record): string => $record->url_video)
                    ->limit(30),

                 Tables\Columns\TextColumn::make('deskripsi_tutorial')
                    ->label('Intruksi')
                    ->limit(50)
                    ->wrap(),  
            ])
            // Mengatur default sort ke kolom yang benar
            ->defaultSort('nama_tutorial', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTutorials::route('/'),
            'create' => Pages\CreateTutorial::route('/create'),
            'edit' => Pages\EditTutorial::route('/{record}/edit'),
        ];
    }
}
