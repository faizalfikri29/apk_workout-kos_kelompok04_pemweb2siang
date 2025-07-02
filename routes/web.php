<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\WorkoutController;
use App\Models\Tutorial;
use App\Models\Jadwal;
use App\Models\WorkoutLog;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $tutorials = Tutorial::latest()->take(6)->get();
    $workoutOfTheDay = Tutorial::inRandomOrder()->first();
    $jadwals = Jadwal::all()->groupBy('hari');
    return view('welcome', compact('tutorials', 'jadwals', 'workoutOfTheDay'));
});



Route::get('/dashboard', function () {
    $user = Auth::user();
    
    // 1. REKOMENDASI LATIHAN
    $namaHariIni = strtolower(Carbon::now()->isoFormat('dddd'));
    $latihanHarian = Jadwal::withCount('workouts')->where('hari', $namaHariIni)->first();
    if (!$latihanHarian) {
        $latihanHarian = Jadwal::withCount('workouts')->where('hari', '!=', 'mingguan')->inRandomOrder()->first();
    }
    $latihanMingguan = Jadwal::withCount('workouts')->where('hari', 'mingguan')->inRandomOrder()->first();

    // 2. KALKULASI STATISTIK
    $logs = $user->workoutLogs()->orderBy('created_at', 'desc')->get();
    $totalSesi = $logs->map(fn($log) => $log->created_at->startOfDay())->unique()->count();
    $totalMenit = round($logs->sum('duration_seconds') / 60);
    
    // Logika Streak
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
    
    // 3. DATA UNTUK GRAFIK
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
    
    // 4. LOGIKA ACHIEVEMENT
    // FIX: Mengambil semua achievement dan ID yang sudah dimiliki user
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
                // Set session flash untuk notifikasi modal
                session()->flash('newAchievement', $achievement);
                // Tambahkan ID yang baru dibuka ke array agar tidak dicek ulang
                $userAchievementIds[] = $achievement->id;
            }
        }
    }

    // 5. DATA UNTUK FRONTEND
    $workoutDates = $logs->map(fn($log) => $log->created_at->toDateString())->unique()->values()->toArray();

    return view('dashboard', [
        'latihanHarian' => $latihanHarian,
        'latihanMingguan' => $latihanMingguan,
        'stats' => $stats,
        'workoutDates' => $workoutDates,
        'allAchievements' => $allAchievements,      // <- Variabel yang dibutuhkan dikirim di sini
        'userAchievementIds' => $userAchievementIds,  // <- Variabel yang dibutuhkan dikirim di sini
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Middleware Group untuk semua rute yang butuh login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/workout/session/{jadwal}', [WorkoutController::class, 'startSession'])->name('workout.session.start');
    Route::post('/workout/log', [WorkoutController::class, 'storeLog'])->name('workout.log.store'); 
});

require __DIR__.'/auth.php';
