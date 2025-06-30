<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // --- KARTU 1: Total Pengguna (Tetap Sama) ---
        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();

        // --- KARTU BARU: Total Latihan Tersedia ---
        // Menghitung semua data latihan dari model Workout
        $totalWorkouts = Workout::count();

        // --- DIPERBAIKI: Menggunakan WorkoutSession untuk data akurat ---
        // Menghitung sesi yang BENAR-BENAR diselesaikan hari ini
        $sessionsToday = WorkoutSession::whereDate('created_at', today())->count();

        // --- DIPERBAIKI: Latihan terpopuler berdasarkan sesi yang selesai ---
        $popularWorkoutData = WorkoutSession::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->select('workout_id', DB::raw('count(*) as total'))
            ->groupBy('workout_id')
            ->orderByDesc('total')
            ->first();
        
        // Logika untuk menampilkan nama latihan tetap sama, tapi sumber datanya lebih akurat
        $popularWorkoutName = 'Belum Ada Sesi';
        if ($popularWorkoutData && $popularWorkoutData->workout) {
             $popularWorkoutName = $popularWorkoutData->workout->name;
        }

        return [
            Stat::make('Total Pengguna', $totalUsers)
                ->description($newUsersThisMonth . ' baru bulan ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            // --- INI KARTU YANG DIPERBAIKI ---
            Stat::make('Sesi Selesai Hari Ini', $sessionsToday)
                ->description('Total sesi yang telah diselesaikan')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),

            // --- INI KARTU BARU YANG KITA TAMBAHKAN ---
            Stat::make('Total Latihan Tersedia', $totalWorkouts)
                ->description('Variasi latihan dalam aplikasi')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('info'),
            
            // --- KARTU INI SEKARANG LEBIH AKURAT ---
            Stat::make('Latihan Terpopuler', $popularWorkoutName)
                ->description('Bulan ini')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),
        ];
    }
}
