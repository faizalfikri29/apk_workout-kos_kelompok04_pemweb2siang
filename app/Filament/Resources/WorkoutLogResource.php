<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkoutLogResource\Pages;
use App\Models\WorkoutLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class WorkoutLogResource extends Resource
{
    protected static ?string $model = WorkoutLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Aktivitas Pengguna';

    protected static ?string $modelLabel = 'Log Latihan';

    protected static ?string $pluralModelLabel = 'Log Latihan';

    protected static ?string $recordTitleAttribute = 'user.name';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->description(fn (WorkoutLog $record): string => $record->user->email ?? '')
                    ->toggleable(isToggledHiddenByDefault: false),


                Tables\Columns\TextColumn::make('calories_burned')
                    ->label('Kalori Terbakar')
                    ->numeric()
                    ->suffix(' kkal')
                    ->color('warning')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('Durasi Latihan')
                    ->formatStateUsing(function (int $state): string {
                        $minutes = floor($state / 60);
                        $seconds = $state % 60;
                        if ($minutes > 0 && $seconds > 0) {
                            return "{$minutes}m {$seconds}s"; // Format baru: "Xm Ys"
                        } elseif ($minutes > 0) {
                            return "{$minutes}m"; // Hanya menit jika detik nol
                        } else {
                            return "{$seconds}s"; // Hanya detik jika menit nol
                        }
                    })
                    ->sortable()
                    ->color('primary')
                    ->icon('heroicon-o-clock')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Selesai')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->color('secondary')
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Filter Berdasarkan Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->placeholder('Pilih Pengguna')
                    ->indicator('Pengguna'),

                SelectFilter::make('workout_type')
                    ->label('Jenis Latihan')
                    ->options([
                        'Cardio' => 'Cardio',
                        'Strength' => 'Strength',
                        'Yoga' => 'Yoga',
                        'Other' => 'Lainnya',
                    ])
                    ->placeholder('Pilih Jenis Latihan')
                    ->indicator('Jenis Latihan'),

                Filter::make('created_at')
                    ->label('Filter Berdasarkan Tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->placeholder(fn ($state): string => 'cth: ' . Carbon::now()->startOfMonth()->format('d M Y')),
                        \Filament\Forms\Components\DatePicker::make('to')
                            ->label('Sampai Tanggal')
                            ->placeholder(fn ($state): string => 'cth: ' . Carbon::now()->endOfMonth()->format('d M Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicator('Tanggal'),

                Filter::make('duration_seconds_filter')
                    ->label('Durasi Latihan (Menit)')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('min_duration')
                            ->numeric()
                            ->label('Min. Durasi (menit)')
                            ->suffix(' menit'),
                        \Filament\Forms\Components\TextInput::make('max_duration')
                            ->numeric()
                            ->label('Max. Durasi (menit)')
                            ->suffix(' menit'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_duration'],
                                fn (Builder $query, $duration): Builder => $query->where('duration_seconds', '>=', $duration * 60),
                            )
                            ->when(
                                $data['max_duration'],
                                fn (Builder $query, $duration): Builder => $query->where('duration_seconds', '<=', $duration * 60),
                            );
                    })
                    ->indicator('Durasi'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
            ])
            ->emptyStateHeading('Tidak ada log latihan yang ditemukan.')
            ->defaultSort('created_at', 'desc');
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
