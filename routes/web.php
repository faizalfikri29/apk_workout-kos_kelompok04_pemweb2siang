<?php

use Illuminate\Support\Facades\Route;
use App\Models\Tutorial;
use App\Models\Jadwal;

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
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';