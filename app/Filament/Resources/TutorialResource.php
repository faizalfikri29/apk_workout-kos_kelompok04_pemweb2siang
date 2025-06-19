<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TutorialResource\Pages;
use App\Models\Tutorial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;

class TutorialResource extends Resource
{
    protected static ?string $model = Tutorial::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'Tutorial';
    protected static ?string $modelLabel = 'Tutorial Workout';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('judul')
                        ->label('Judul Workout')
                        ->placeholder('Contoh: Cardio Ringan Pagi')
                        ->required(),

                    FileUpload::make('gambar_url')
                        ->image()
                        ->directory('tutorials')
                        ->label('Upload Gambar')
                        ->required(),
                ]),

                Select::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Cardio' => '💓 Cardio',
                        'Strength' => '💪 Strength',
                        'Stretching' => '🧘 Stretching',
                    ])
                    ->required(),

                TextInput::make('video_url')
                    ->label('Link Video Tutorial')
                    ->placeholder('https://youtube.com/...')
                    ->required(),

                Textarea::make('instruksi')
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
                TextColumn::make('judul')
                    ->label('Judul Workout')
                    ->description(fn ($record) => $record->kategori)
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('gambar_url')
                    ->label('Gambar')
                    ->size(60),

                BadgeColumn::make('kategori')
                    ->colors([
                        'warning' => 'Cardio',
                        'danger' => 'Strength',
                        'success' => 'Stretching',
                    ]),

                TextColumn::make('video_url')
                    ->label('Video Tutorial')
                    ->url(fn ($record) => $record->video_url, true)
                    ->openUrlInNewTab()
                    ->limit(30),

                TextColumn::make('instruksi')
                    ->label('Instruksi')
                    ->limit(50)
                    ->wrap(),
            ])
            ->defaultSort('judul')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
