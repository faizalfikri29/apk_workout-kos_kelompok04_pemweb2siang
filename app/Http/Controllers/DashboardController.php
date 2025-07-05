<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Jadwal;
use App\Models\WorkoutLog;
use App\Models\User; // Direkomendasikan untuk menambahkan ini untuk type-hinting
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Diperbaiki: satu titik koma

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dasbor pengguna dengan semua data yang dibutuhkan.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. REKOMENDASI LATIHAN
        // Mencari jadwal latihan yang sesuai dengan hari ini.
        $namaHariIni = strtolower(Carbon::now()->isoFormat('dddd'));
        $latihanHarian = Jadwal::withCount('workouts')->where('hari', $namaHariIni)->first();
        // Jika tidak ada jadwal untuk hari ini, ambil jadwal acak lainnya.
        if (!$latihanHarian) {
            $latihanHarian = Jadwal::withCount('workouts')->where('hari', '!=', 'mingguan')->inRandomOrder()->first();
        }

        // 2. STATISTIK UTAMA
        // Mengambil semua log latihan pengguna untuk dihitung.
        $logs = $user->workoutLogs()->orderBy('created_at', 'desc')->get();
        $totalSesi = $logs->map(fn($log) => $log->created_at->startOfDay())->unique()->count();
        $totalMenit = round($logs->sum('duration_seconds') / 60);
        $streak = $this->calculateWorkoutStreak($logs);

        // 3. GABUNGKAN SEMUA STATS KE DALAM SATU ARRAY
        // Menggabungkan statistik utama dan data untuk grafik.
        $stats = [
            'totalSesi' => $totalSesi,
            'totalMenit' => $totalMenit,
            'streak' => $streak
        ];
        $stats = array_merge($stats, $this->getWeeklyProgressData($user->id));
        $stats = array_merge($stats, $this->getWorkoutTypeDistribution($user->id));

        // 4. LOGIKA ACHIEVEMENT
        // Memeriksa apakah ada lencana baru yang terbuka setelah latihan.
        $userAchievementIds = $this->checkAndUnlockAchievements($user, $totalSesi, $streak);
        $allAchievements = Achievement::all();

        // 5. DATA KALENDER
        // Menyiapkan data tanggal untuk ditandai di kalender.
        $workoutDates = $logs->map(fn($log) => $log->created_at->toDateString())->unique()->values()->toArray();

        // Mengirim semua data yang sudah diproses ke view.
        return view('dashboard', [
            'latihanHarian' => $latihanHarian,
            'stats' => $stats,
            'workoutDates' => $workoutDates,
            'allAchievements' => $allAchievements,
            'userAchievementIds' => $userAchievementIds,
        ]);
    }

    /**
     * Menghitung workout streak (latihan beruntun) pengguna.
     * @param \Illuminate\Database\Eloquent\Collection $logs
     * @return int
     */
    private function calculateWorkoutStreak($logs): int
    {
        if ($logs->isEmpty()) {
            return 0;
        }

        $logDates = $logs->map(fn($log) => $log->created_at->startOfDay())->unique();

        // Streak akan 0 jika tidak latihan hari ini atau kemarin.
        if (!$logDates->contains(Carbon::today()) && !$logDates->contains(Carbon::yesterday())) {
            return 0;
        }

        $streak = 1;
        $lastDate = $logDates->first(); // Tanggal latihan terakhir

        // Menghitung mundur untuk mencari hari beruntun.
        foreach ($logDates->slice(1) as $date) {
            if ($lastDate->diffInDays($date) == 1) {
                $streak++;
                $lastDate = $date;
            } else {
                break; // Hentikan jika ada jeda hari.
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
            report($e);
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
    private function checkAndUnlockAchievements(User $user, int $totalSesi, int $streak): array
    {
        $allAchievements = Achievement::all();
        $userAchievementIds = $user->achievements()->pluck('achievements.id')->toArray();

        // Menentukan lencana berikutnya dan progressnya
        $nextAchievement = null;
        $nextAchievementProgress = 0;
        $firstUnachieved = $allAchievements->firstWhere(fn($ach) => !in_array($ach->id, $userAchievementIds));
        if ($firstUnachieved) {
            $nextAchievement = $firstUnachieved;
            if ($nextAchievement->condition_type == 'sessions') {
                $nextAchievementProgress = ($totalSesi / $nextAchievement->condition_value) * 100;
            } elseif ($nextAchievement->condition_type == 'streak') {
                $nextAchievementProgress = ($streak / $nextAchievement->condition_value) * 100;
            }
        }

        // Cek dan berikan achievement yang baru terbuka
        foreach ($allAchievements as $achievement) {
            // Hanya periksa lencana yang belum dimiliki.
            if (!in_array($achievement->id, $userAchievementIds)) {
                $unlocked = false;
                if ($achievement->condition_type == 'sessions' && $totalSesi >= $achievement->condition_value) {
                    $unlocked = true;
                } elseif ($achievement->condition_type == 'streak' && $streak >= $achievement->condition_value) {
                    $unlocked = true;
                }

                if ($unlocked) {
                    $user->achievements()->attach($achievement->id);
                    session()->flash('newAchievement', $achievement); // Kirim notifikasi ke view.
                    $userAchievementIds[] = $achievement->id; // Tambahkan ke array agar tidak diperiksa lagi.
                }
            }
        }
        return $userAchievementIds;
    }
}