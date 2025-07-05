<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Jadwal;
use App\Models\WorkoutLog;
use App\Models\Achievement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dasbor pengguna dengan semua data yang dibutuhkan.
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. REKOMENDASI LATIHAN
        $namaHariIni = strtolower(Carbon::now()->isoFormat('dddd'));
        $latihanHarian = Jadwal::withCount('workouts')->where('hari', $namaHariIni)->first();
        if (!$latihanHarian) {
            $latihanHarian = Jadwal::withCount('workouts')->where('hari', '!=', 'mingguan')->inRandomOrder()->first();
        }

        // 2. STATISTIK UTAMA
        $logs = $user->workoutLogs()->orderBy('created_at', 'desc')->get();
        $totalSesi = $logs->map(fn($log) => $log->created_at->startOfDay())->unique()->count();
        $totalMenit = round($logs->sum('duration_seconds') / 60);
        $streak = $this->calculateWorkoutStreak($logs);
        
        // 3. GABUNGKAN SEMUA STATS KE DALAM SATU ARRAY
        $stats = [
            'totalSesi' => $totalSesi, 
            'totalMenit' => $totalMenit, 
            'streak' => $streak
        ];
        
        // DATA GRAFIK (Digabungkan dengan aman)
        $stats = array_merge($stats, $this->getWeeklyProgressData($user->id));
        $stats = array_merge($stats, $this->getWorkoutTypeDistribution($user->id));
        
        // 4. LOGIKA ACHIEVEMENT
        $userAchievementIds = $this->checkAndUnlockAchievements($user, $totalSesi, $streak);
        $allAchievements = Achievement::all();

        // 5. DATA KALENDER
        $workoutDates = $logs->map(fn($log) => $log->created_at->toDateString())->unique()->values()->toArray();

        return view('dashboard', [
            'latihanHarian' => $latihanHarian,
            'stats' => $stats,
            'workoutDates' => $workoutDates,
            'allAchievements' => $allAchievements,
            'userAchievementIds' => $userAchievementIds,
        ]);
    }
    
    /**
     * Menghitung workout streak pengguna.
     * @param \Illuminate\Database\Eloquent\Collection $logs
     * @return int
     */
    private function calculateWorkoutStreak($logs): int
    {
        if ($logs->isEmpty()) return 0;

        $logDates = $logs->map(fn($log) => $log->created_at->startOfDay())->unique();
        
        if (!$logDates->contains(Carbon::today()) && !$logDates->contains(Carbon::yesterday())) {
            return 0;
        }

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
        return $streak;
    }

    /**
     * Mengambil data progres mingguan untuk grafik.
     * @param int $userId
     * @return array
     */
    private function getWeeklyProgressData(int $userId): array
    {
        $weekDates = collect(range(0, 6))->map(fn ($i) => Carbon::today()->subDays($i));
        $workoutMinutesPerDay = WorkoutLog::where('user_id', $userId)
            ->where('created_at', '>=', $weekDates->last()->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(duration_seconds) as total_duration')
            ->groupBy('date')
            ->get()->keyBy(fn ($log) => Carbon::parse($log->date)->toDateString());

        $data = [
            'weekly_progress_labels' => $weekDates->map(fn ($date) => $date->isoFormat('ddd'))->reverse()->toArray(),
            'weekly_progress_data' => $weekDates->map(fn ($date) => 
                round(($workoutMinutesPerDay[$date->toDateString()]->total_duration ?? 0) / 60)
            )->reverse()->toArray()
        ];
        
        return $data;
    }

    /**
     * Mengambil data distribusi tipe latihan untuk grafik doughnut.
     * @param int $userId
     * @return array
     */
    private function getWorkoutTypeDistribution(int $userId): array
    {
        try {
            // Query ini membutuhkan join ke tabel 'workouts' dan 'kategori_workouts'
            // Pastikan nama tabel dan kolom sudah sesuai dengan migrasi Anda.
            $distribution = WorkoutLog::where('workout_logs.user_id', $userId)
                ->join('workouts', 'workout_logs.workout_id', '=', 'workouts.id')
                ->join('kategori_workouts', 'workouts.kategori_workout_id', '=', 'kategori_workouts.id')
                ->select('kategori_workouts.nama_kategori', DB::raw('count(*) as count'))
                ->groupBy('kategori_workouts.nama_kategori')
                ->pluck('count', 'nama_kategori');

            return [
                'workout_type_distribution' => [
                    'labels' => $distribution->keys()->toArray(),
                    'data' => $distribution->values()->toArray(),
                ]
            ];
        } catch (\Exception $e) {
            // Jika terjadi error pada query (misal: tabel tidak ada), kembalikan array kosong
            // untuk mencegah aplikasi crash.
            report($e); // Laporkan error untuk di-debug nanti
            return ['workout_type_distribution' => ['labels' => [], 'data' => []]];
        }
    }

    /**
     * Memeriksa dan membuka achievement baru untuk pengguna.
     * @param \App\Models\User $user
     * @param int $totalSesi
     * @param int $streak
     * @return array
     */
    private function checkAndUnlockAchievements($user, int $totalSesi, int $streak): array
    {
        $allAchievements = Achievement::all();
        $userAchievementIds = $user->achievements()->pluck('achievements.id')->toArray();

        foreach ($allAchievements as $achievement) {
            if (!in_array($achievement->id, $userAchievementIds)) {
                $unlocked = false;
                if ($achievement->condition_type == 'sessions' && $totalSesi >= $achievement->condition_value) {
                    $unlocked = true;
                } elseif ($achievement->condition_type == 'streak' && $streak >= $achievement->condition_value) {
                    $unlocked = true;
                }
                
                if ($unlocked) {
                    $user->achievements()->attach($achievement->id);
                    session()->flash('newAchievement', $achievement);
                    $userAchievementIds[] = $achievement->id;
                }
            }
        }
        return $userAchievementIds;
    }
}
