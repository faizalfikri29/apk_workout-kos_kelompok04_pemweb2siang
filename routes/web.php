<?php

use Illuminate\Support\Facades\Route;
use App\Models\Tutorial;
use App\Models\Jadwal;
use App\Models\WorkoutLog; // Jangan lupa import
use Illuminate\Support\Facades\Auth; // Jangan lupa import
use Illuminate\Support\Carbon; // Import
use App\Models\Achievement; // Import

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Rute ini dimodifikasi untuk peningkatan drastis ---
Route::get('/', function () {
    // Mengambil 6 tutorial terbaru untuk galeri
    $tutorials = Tutorial::latest()->take(6)->get();
    
    // BARU: Mengambil satu tutorial acak sebagai 'Workout of the Day'
    $workoutOfTheDay = Tutorial::inRandomOrder()->first();

    // Mengambil semua jadwal dan mengelompokkannya berdasarkan hari
    $jadwals = Jadwal::all()->groupBy('hari');

    // Mengirim semua data yang dibutuhkan ke view
    return view('welcome', compact('tutorials', 'jadwals', 'workoutOfTheDay'));
});
// --- Akhir Modifikasi ---


Route::get('/dashboard', function () {
    $user = Auth::user();
    
    // --- Data untuk Rekomendasi Latihan ---
    $latihanHarian = Jadwal::withCount('workouts')->inRandomOrder()->first();
    $latihanMingguan = Jadwal::withCount('workouts')->where('id', '!=', $latihanHarian->id ?? 0)->inRandomOrder()->first();

    // --- Kalkulasi Statistik ---
    $logs = $user->workoutLogs()->orderBy('created_at', 'desc')->get();
    $totalSesi = $logs->count();
    $totalMenit = round($logs->sum('duration_seconds') / 60);

    // --- Logika Streak yang Lebih Akurat ---
    $streak = 0;
    if ($totalSesi > 0) {
        $logDates = $logs->map(fn($log) => $log->created_at->startOfDay())->unique();
        $streak = 1;
        $currentDate = $logDates->first();
        if ($currentDate->isToday() || $currentDate->isYesterday()) {
            foreach ($logDates->slice(1) as $date) {
                if ($currentDate->diffInDays($date) == 1) {
                    $streak++;
                    $currentDate = $date;
                } else {
                    break;
                }
            }
        } else { $streak = 0; }
    }
    
    $stats = ['totalSesi' => $totalSesi, 'totalMenit' => $totalMenit, 'streak' => $streak];
    
    // --- Logika Pengecekan & Pemberian Achievement ---
    $allAchievements = Achievement::all();
    $currentUserAchievements = $user->achievements()->pluck('achievement_id')->toArray();
    $unlockedAchievements = [];

    foreach ($allAchievements as $achievement) {
        if (!in_array($achievement->id, $currentUserAchievements)) {
            $unlocked = false;
            if ($achievement->condition_type == 'sessions' && $totalSesi >= $achievement->condition_value) {
                $unlocked = true;
            } elseif ($achievement->condition_type == 'streak' && $streak >= $achievement->condition_value) {
                $unlocked = true;
            }

            if ($unlocked) {
                $user->achievements()->attach($achievement->id);
            }
        }
    }

    // --- Data untuk Frontend ---
    $workoutDates = $logs->map(fn($log) => $log->created_at->toDateString())->unique()->values()->toArray();
    $userAchievements = $user->achievements()->get();

    return view('dashboard', [
        'latihanHarian' => $latihanHarian,
        'latihanMingguan' => $latihanMingguan,
        'stats' => $stats,
        'workoutDates' => $workoutDates,
        'userAchievements' => $userAchievements,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


// 2. Tambahkan Route BARU untuk memulai sesi workout
Route::get('/workout/session/{jadwal}', [WorkoutController::class, 'startSession'])
    ->middleware(['auth'])
    ->name('workout.session.start');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/workout/session/{jadwal}', [WorkoutController::class, 'startSession'])
    ->middleware(['auth'])
    ->name('workout.session.start');

// TAMBAHKAN ROUTE BARU DI BAWAH INI
Route::post('/workout/log', [WorkoutController::class, 'logSession'])
    ->middleware(['auth'])
    ->name('workout.session.log');

Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

require __DIR__.'/auth.php';