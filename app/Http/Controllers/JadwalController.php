<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\WorkoutLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /**
     * Display the user's dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();

        // 1. Rekomendasi Latihan Hari Ini
        $dayOfWeek = strtolower(Carbon::now()->isoFormat('dddd')); // 'senin', 'selasa', etc.
        $latihanHarian = Jadwal::withCount('workouts')
            ->where('hari', $dayOfWeek)
            ->where('tipe', 'harian')
            ->first();

        // 2. Rekomendasi Latihan Mingguan/Tantangan Lain
        $latihanMingguan = Jadwal::where('tipe', 'mingguan')->inRandomOrder()->first();

        // 3. Menghitung Statistik Utama
        $totalSesi = WorkoutLog::where('user_id', $user->id)
            ->distinctRaw('DATE(created_at)')
            ->count();
        
        $totalMenit = WorkoutLog::where('user_id', $user->id)->sum('duration');

        $stats = [
            'totalSesi'  => $totalSesi,
            'totalMenit' => round($totalMenit / 60), // Durasi dalam detik, diubah ke menit
            'streak'     => $this->calculateWorkoutStreak($user),
        ];

        // 4. Data untuk Grafik Progress Mingguan (7 hari terakhir)
        $weekDates = collect(range(0, 6))->map(function ($i) {
            return Carbon::today()->subDays($i);
        });

        $workoutMinutesPerDay = WorkoutLog::where('user_id', $user->id)
            ->where('created_at', '>=', $weekDates->last()->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(duration) as total_duration')
            ->groupBy('date')
            ->get()
            ->keyBy(function ($log) {
                return Carbon::parse($log->date)->toDateString();
            });

        $stats['weekly_progress_labels'] = $weekDates->map(function ($date) {
            return $date->isoFormat('ddd'); // 'Rab', 'Sel', 'Sen', etc.
        })->reverse()->toArray();
        
        $stats['weekly_progress_data'] = $weekDates->map(function ($date) use ($workoutMinutesPerDay) {
            $formattedDate = $date->toDateString();
            return isset($workoutMinutesPerDay[$formattedDate]) ? round($workoutMinutesPerDay[$formattedDate]->total_duration / 60) : 0;
        })->reverse()->toArray();

        // 5. Data untuk Kalender
        $workoutDates = WorkoutLog::where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->pluck('date')
            ->toArray();
            
        // 6. Data Lencana (Achievements)
        $userAchievements = $user->achievements()->get();

        return view('dashboard', compact(
            'latihanHarian',
            'latihanMingguan',
            'stats',
            'workoutDates',
            'userAchievements'
        ));
    }
    
    /**
     * Calculate the user's current workout streak.
     *
     * @param \App\Models\User $user
     * @return int
     */
    private function calculateWorkoutStreak($user)
    {
        $workoutDays = WorkoutLog::where('user_id', $user->id)
            ->selectRaw('DISTINCT DATE(created_at) as date')
            ->orderBy('date', 'desc')
            ->pluck('date');

        if ($workoutDays->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Cek apakah hari ini atau kemarin sudah workout
        if (Carbon::parse($workoutDays[0])->isSameDay($today) || Carbon::parse($workoutDays[0])->isSameDay($yesterday)) {
            $streak = 1;
            $lastDate = Carbon::parse($workoutDays[0]);

            for ($i = 1; $i < count($workoutDays); $i++) {
                $currentDate = Carbon::parse($workoutDays[$i]);
                if ($lastDate->diffInDays($currentDate) == 1) {
                    $streak++;
                    $lastDate = $currentDate;
                } else {
                    break; // Streak terputus
                }
            }
        }
        
        return $streak;
    }

    // ... (method-method lain yang mungkin sudah ada di controller ini)
}