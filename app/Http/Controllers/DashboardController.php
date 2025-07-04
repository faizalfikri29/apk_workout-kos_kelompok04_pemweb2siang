<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Jadwal;
use App\Models\WorkoutLog;
use App\Models\Achievement;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pengguna dengan semua data yang relevan.
     */
    public function index()
    {
        $user = Auth::user();
        Carbon::setLocale('id'); // Mengatur lokal Carbon ke Bahasa Indonesia

        // 1. SAPAAN BERDASARKAN WAKTU
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour < 18) {
            $greeting = 'Selamat Siang';
        } else {
            $greeting = 'Selamat Malam';
        }

        // 2. REKOMENDASI LATIHAN
        $namaHariIni = strtolower(Carbon::now()->isoFormat('dddd'));
        $latihanHarian = Jadwal::withCount('workouts')->where('hari', $namaHariIni)->first();

        // Jika tidak ada jadwal spesifik untuk hari ini, ambil jadwal acak
        if (!$latihanHarian) {
            $latihanHarian = Jadwal::withCount('workouts')->where('hari', '!=', 'mingguan')->inRandomOrder()->first();
        }
        $latihanMingguan = Jadwal::withCount('workouts')->where('hari', 'mingguan')->inRandomOrder()->first();

        // 3. KALKULASI STATISTIK
        $logs = $user->workoutLogs()->orderBy('created_at', 'desc')->get();
        $totalSesi = $logs->map(fn($log) => $log->created_at->startOfDay())->unique()->count();
        $totalMenit = round($logs->sum('duration_seconds') / 60);

        // Logika untuk menghitung Workout Streak
        $streak = 0;
        if ($totalSesi > 0) {
            $logDates = $logs->map(fn($log) => $log->created_at->startOfDay())->unique();
            $currentStreakDate = Carbon::today();
            if ($logDates->contains($currentStreakDate) || $logDates->contains(Carbon::yesterday())) {
                $streak = 1;
                $lastDate = $logDates->first();
                foreach ($logDates->slice(1) as $date) {
                    if ($lastDate->diffInDays($date) == 1) {
                        $streak++;
                        $lastDate = $date;
                    } else {
                        break;
                    }
                }
            }
        }
        
        $stats = ['totalSesi' => $totalSesi, 'totalMenit' => $totalMenit, 'streak' => $streak];

        // 4. DATA UNTUK GRAFIK
        $weekDates = collect(range(0, 6))->map(fn ($i) => Carbon::today()->subDays($i));
        $workoutMinutesPerDay = WorkoutLog::where('user_id', $user->id)
            ->where('created_at', '>=', $weekDates->last()->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(duration_seconds) as total_duration')
            ->groupBy('date')
            ->get()->keyBy(fn ($log) => Carbon::parse($log->date)->toDateString());

        $stats['weekly_progress_labels'] = $weekDates->map(fn ($date) => $date->isoFormat('ddd'))->reverse()->toArray();
        $stats['weekly_progress_data'] = $weekDates->map(fn ($date) =>
            isset($workoutMinutesPerDay[$date->toDateString()]) ?
            round($workoutMinutesPerDay[$date->toDateString()]->total_duration / 60) : 0
        )->reverse()->toArray();

        // TODO: Tambahkan logika untuk data grafik donat (tipe latihan) jika ada
        $stats['workout_type_distribution'] = [
            'labels' => ['Kekuatan', 'Kardio', 'Fleksibilitas'], // Contoh data
            'data' => [50, 35, 15], // Contoh data
        ];

        // 5. LOGIKA ACHIEVEMENT (LENCANA)
        $allAchievements = Achievement::all();
        $userAchievementIds = $user->achievements()->pluck('id')->toArray();

        foreach ($allAchievements as $achievement) {
            if (!in_array($achievement->id, $userAchievementIds)) {
                $unlocked = false;
                if ($achievement->condition_type == 'sessions' && $totalSesi >= $achievement->condition_value) {
                    $unlocked = true;
                } elseif ($achievement->condition_type == 'streak' && $stats['streak'] >= $achievement->condition_value) {
                    $unlocked = true;
                }
                
                if ($unlocked) {
                    $user->achievements()->attach($achievement->id);
                    session()->flash('newAchievement', $achievement);
                    $userAchievementIds[] = $achievement->id; // Update array agar tidak ada duplikasi notifikasi
                }
            }
        }

        // TODO: Logika untuk menentukan lencana berikutnya dan progressnya
        $nextAchievement = null;
        $nextAchievementProgress = 0;
        // Cari lencana pertama yang belum dimiliki user
        $firstUnachieved = $allAchievements->firstWhere(fn($ach) => !in_array($ach->id, $userAchievementIds));
        if ($firstUnachieved) {
            $nextAchievement = $firstUnachieved;
            if ($nextAchievement->condition_type == 'sessions') {
                $nextAchievementProgress = ($totalSesi / $nextAchievement->condition_value) * 100;
            } elseif ($nextAchievement->condition_type == 'streak') {
                $nextAchievementProgress = ($stats['streak'] / $nextAchievement->condition_value) * 100;
            }
        }

        // 6. DATA UNTUK KALENDER FRONTEND
        $workoutDates = $logs->map(fn($log) => $log->created_at->toDateString())->unique()->values()->toArray();

        // 7. MENGIRIM SEMUA DATA KE VIEW
        return view('dashboard', [
            'greeting' => $greeting,
            'latihanHarian' => $latihanHarian,
            'latihanMingguan' => $latihanMingguan,
            'stats' => $stats,
            'workoutDates' => $workoutDates,
            'allAchievements' => $allAchievements,
            'userAchievementIds' => $userAchievementIds,
            'nextAchievement' => $nextAchievement,
            'nextAchievementProgress' => min(100, $nextAchievementProgress), // Pastikan progress tidak lebih dari 100
        ]);
    }
}
