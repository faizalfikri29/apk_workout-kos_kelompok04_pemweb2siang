<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\WorkoutController;
use App\Http\Controllers\DashboardController;
use App\Models\Tutorial;
use App\Models\Jadwal;
use App\Http\Controllers\WorkoutSessionController;
use App\Http\Controllers\WorkoutLogController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| File ini sekarang hanya bertanggung jawab untuk memetakan URL ke Controller.
| Jauh lebih bersih, cepat, dan mudah dikelola.
|
*/

// Route untuk Halaman Depan (Welcome Page)
// Logika sederhana seperti ini masih bisa diterima di file route.
Route::get('/', function () {
    $tutorials = Tutorial::latest()->take(6)->get();
    $workoutOfTheDay = Tutorial::inRandomOrder()->first();
    $jadwals = Jadwal::all()->groupBy('hari');
    return view('welcome', compact('tutorials', 'jadwals', 'workoutOfTheDay'));
});

// Route untuk Dasbor Pengguna
// --- BAGIAN YANG DIPERBAIKI ---
// Semua logika kompleks dipindahkan ke DashboardController.
// Ini memungkinkan route caching dan membuat kode lebih terstruktur.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Middleware Group untuk semua rute yang membutuhkan otentikasi
Route::middleware('auth')->group(function () {
    // Rute untuk Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk Sesi Latihan dan Logging
    Route::get('/workout/session/{jadwal}', [WorkoutSessionController::class, 'start'])->name('workout.session.start');
Route::post('/workout/session', [WorkoutSessionController::class, 'store'])->name('workout.session.store');
    Route::get('/workout/session/{jadwal}', [WorkoutController::class, 'startSession'])->name('workout.session.start');
    Route::post('/workout/session', [WorkoutSessionController::class, 'store'])->name('workout.session.store');
    Route::post('/workout/log', [WorkoutLogController::class, 'store'])->name('workout.log.store')->middleware('auth');
});

// Memuat rute otentikasi (login, register, dll.)
require __DIR__.'/auth.php';
