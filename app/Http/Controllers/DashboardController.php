<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
    $user = Auth::user();

    // DATA YANG SUDAH ADA (ASUMSI)
    $stats = [
        'totalSesi' => 120, // contoh
        'totalMenit' => 3600, // contoh
        'streak' => 15, // contoh
        'weekly_progress_labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        'weekly_progress_data' => [30, 45, 0, 60, 30, 90, 0],
    ];
    $latihanHarian = Jadwal::where('hari', now()->dayOfWeek)->first(); // contoh
    $workoutDates = SesiLatihan::where('user_id', $user->id)->pluck('tanggal')->toArray();
    $allAchievements = Achievement::all();
    $userAchievementIds = $user->achievements->pluck('id')->toArray();
    $latihanMingguan = Jadwal::inRandomOrder()->first(); // contoh

    // DATA BARU YANG PERLU ANDA TAMBAHKAN LOGIKANYA
    // 1. Sapaan berdasarkan waktu
    $hour = now()->hour;
    if ($hour < 12) {
        $greeting = 'Selamat Pagi';
    } elseif ($hour < 18) {
        $greeting = 'Selamat Siang';
    } else {
        $greeting = 'Selamat Malam';
    }

    // 2. Data untuk grafik donat
    // Anda perlu logika untuk menghitung ini dari data Anda
    $stats['workout_type_distribution'] = [
        'labels' => ['Kekuatan', 'Kardio', 'Fleksibilitas'],
        'data' => [50, 35, 15], // contoh persentase
    ];

    // 3. Data untuk progress lencana berikutnya
    // Anda perlu logika untuk menentukan lencana berikutnya dan progressnya
    $nextAchievement = (object) [
        'name' => 'Maraton Sebulan',
        'icon' => 'heroicon-o-calendar-days', // ikon lencana
        'description' => 'Selesaikan 20 sesi dalam sebulan.',
    ];
    $nextAchievementProgress = 75; // contoh progress (dalam persen, 15 dari 20 sesi = 75%)
    
    // 4. Notifikasi lencana baru (sudah ada)
    // session()->flash('newAchievement', Achievement::find(1)); // Untuk testing modal

    return view('dashboard', compact(
        'greeting', // data baru
        'stats',
        'latihanHarian',
        'workoutDates',
        'allAchievements',
        'userAchievementIds',
        'latihanMingguan',
        'nextAchievement', // data baru
        'nextAchievementProgress' // data baru
    ));
    }
}