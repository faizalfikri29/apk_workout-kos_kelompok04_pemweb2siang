<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Models\Tutorial;
use App\Models\Jadwal;
use App\Http\Controllers\WorkoutLogController;
use App\Http\Controllers\WorkoutSessionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| File ini berisi semua rute untuk aplikasi web Anda. Rute-rute ini
| dimuat oleh RouteServiceProvider dalam sebuah grup yang berisi
| middleware "web".
|
*/

/**
 * --------------------------------------------------------------------------
 * Rute Publik (Dapat diakses tanpa login)
 * --------------------------------------------------------------------------
 */

// Rute untuk Halaman Depan (Welcome Page)
// Logika dipindahkan ke WelcomeController untuk menjaga file rute tetap bersih.
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');


/**
 * --------------------------------------------------------------------------
 * Rute yang Membutuhkan Otentikasi
 * --------------------------------------------------------------------------
 */

// Rute untuk Dasbor Pengguna
// Pengguna harus login dan emailnya terverifikasi untuk mengakses ini.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Grup rute yang memerlukan pengguna untuk login.
Route::middleware('auth')->group(function () {
    
    // Rute untuk Manajemen Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk Sesi Latihan dan Logging
    // Rute-rute ini telah dirapikan untuk menghindari duplikasi.
    Route::get('/workout/session/{jadwal}', [WorkoutSessionController::class, 'start'])->name('workout.session.start');
    Route::post('/workout/session', [WorkoutSessionController::class, 'store'])->name('workout.session.store');
    Route::post('/workout/log', [WorkoutLogController::class, 'store'])->name('workout.log.store');

});


/**
 * --------------------------------------------------------------------------
 * Rute Otentikasi Bawaan Laravel
 * --------------------------------------------------------------------------
 */

// Memuat rute-rute standar untuk otentikasi seperti login, register, dll.
require __DIR__.'/auth.php';