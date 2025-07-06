<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Jadwal;
use App\Models\WorkoutLog;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // Ambil semua achievement sekali saja untuk efisiensi.
        $allAchievements = Achievement::all(); // FIX: Menghapus orderBy('condition_value') yang menyebabkan error.
        
        // Memeriksa apakah ada lencana baru yang terbuka setelah latihan.
        $userAchievementIds = $this->checkAndUnlockAchievements($user, $allAchievements, $totalSesi, $streak);
        
        // Logika untuk lencana berikutnya
        $nextAchievementData = $this->getNextAchievementData($user, $allAchievements, $userAchievementIds, $totalSesi, $streak);
        $nextAchievement = $nextAchievementData['nextAchievement'];
        $nextAchievementProgress = $nextAchievementData['progress'];


        // 5. DATA KALENDER
        // Menyiapkan data tanggal untuk ditandai di kalender.
        $workoutDates = $logs->map(fn($log) => $log->created_at->toDateString())->unique()->values()->toArray();

        // 6. NOTIFIKASI
        $notifications = Notification::where('is_active', true)->orderBy('created_at', 'desc')->get();

        // 7. LATIHAN FAVORIT
        // Mengambil 3 latihan yang paling sering dilakukan oleh pengguna.
        $favoriteWorkouts = $this->getFavoriteWorkouts($user);
        
        // 8. SAPAAN SELAMAT PAGI/SIANG/MALAM
        $greeting = $this->getGreeting();

        // Mengirim semua data yang sudah diproses ke view.
        return view('dashboard', [
            'greeting' => $greeting,
            'latihanHarian' => $latihanHarian,
            'stats' => $stats,
            'workoutDates' => $workoutDates,
            'allAchievements' => $allAchievements,
            'userAchievementIds' => $userAchievementIds,
            'nextAchievement' => $nextAchievement,
            'nextAchievementProgress' => $nextAchievementProgress,
            'notifications' => $notifications,
            'favoriteWorkouts' => $favoriteWorkouts,
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

        $logDates = $logs->map(fn($log) => $log->created_at->startOfDay())->unique()->values();

        // Cek apakah latihan terakhir adalah hari ini atau kemarin
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $lastLogDate = $logDates->first();

        if (!$lastLogDate->isSameDay($today) && !$lastLogDate->isSameDay($yesterday)) {
            return 0;
        }
        
        $streak = 0;
        if ($logDates->isNotEmpty()) {
            $streak = 1;
            $currentDate = $logDates->first();
            for ($i = 1; $i < $logDates->count(); $i++) {
                if ($currentDate->diffInDays($logDates[$i]) == 1) {
                    $streak++;
                    $currentDate = $logDates[$i];
                } else {
                    break;
                }
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
     * @param \Illuminate\Database\Eloquent\Collection $allAchievements
     * @param int $totalSesi
     * @param int $streak
     * @return array
     */
    private function checkAndUnlockAchievements(User $user, $allAchievements, int $totalSesi, int $streak): array
    {
        // REFACTOR: Tidak perlu query lagi, data sudah di-pass dari index()
        $userAchievementIds = $user->achievements()->pluck('achievements.id')->toArray();

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

    /**
     * Fungsi untuk mendapatkan lencana berikutnya dan progresnya
     * @param \App\Models\User $user
     * @param \Illuminate\Database\Eloquent\Collection $allAchievements
     * @param array $userAchievementIds
     * @param int $totalSesi
     * @param int $streak
     * @return array
     */
    private function getNextAchievementData(User $user, $allAchievements, $userAchievementIds, int $totalSesi, int $streak)
    {
        $nextAchievement = null;
        $progress = 0;

        // Cari lencana berikutnya yang belum terbuka, diurutkan berdasarkan nilai kondisi
        $nextAchievement = $allAchievements->whereNotIn('id', $userAchievementIds)->sortBy('condition_value')->first();

        if ($nextAchievement) {
            if ($nextAchievement->condition_type == 'sessions') {
                $progress = ($totalSesi / $nextAchievement->condition_value) * 100;
            } elseif ($nextAchievement->condition_type == 'streak') {
                $progress = ($streak / $nextAchievement->condition_value) * 100;
            }
        }

        return [
            'nextAchievement' => $nextAchievement,
            'progress' => min($progress, 100) // Pastikan progress tidak lebih dari 100%
        ];
    }

    /**
     * Fungsi untuk mendapatkan latihan favorit pengguna
     * @param \App\Models\User $user
     * @return \Illuminate\Support\Collection
     */
    private function getFavoriteWorkouts(User $user)
    {
        // Hitung jadwal mana yang paling sering muncul di log
        $favoriteScheduleIds = $user->workoutLogs()
            ->select('jadwal_id', DB::raw('COUNT(jadwal_id) as count'))
            ->whereNotNull('jadwal_id')
            ->groupBy('jadwal_id')
            ->orderByDesc('count')
            ->limit(3)
            ->pluck('jadwal_id');

        if ($favoriteScheduleIds->isEmpty()) {
            return collect(); // Kembalikan koleksi kosong jika tidak ada favorit
        }

        // Ambil detail jadwal berdasarkan ID favorit
        return Jadwal::whereIn('id', $favoriteScheduleIds)->get()->map(function ($schedule) {
            return (object) [
                'id' => $schedule->id,
                'name' => $schedule->nama_jadwal,
                'route' => route('workout.session.start', $schedule->id)
            ];
        });
    }

    /**
     * Fungsi untuk sapaan dinamis berdasarkan waktu
     * @return string
     */
    private function getGreeting(): string
    {
        $hour = Carbon::now()->hour;
        if ($hour < 12) {
            return 'Selamat Pagi';
        } elseif ($hour < 18) {
            return 'Selamat Malam'; // FIX: Seharusnya 'Selamat Siang' atau 'Sore'
        } else {
            return 'Selamat Malam';
        }
    }
}
