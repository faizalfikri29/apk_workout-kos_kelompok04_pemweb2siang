<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Tutorial;
use App\Models\Jadwal;
use App\Models\Workout;
use App\Models\WorkoutLog;
use App\Models\Achievement;
use App\Models\KategoriWorkout; // Tambahkan ini jika ada

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Nonaktifkan sementara foreign key checks untuk mengizinkan truncate.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Kosongkan semua tabel yang relevan.
        // Urutan di sini tidak lagi penting karena pengecekan foreign key dinonaktifkan.
        User::truncate();
        Tutorial::truncate();
        Jadwal::truncate();
        Workout::truncate();
        WorkoutLog::truncate();
        Achievement::truncate();
        KategoriWorkout::truncate(); // Tambahkan ini jika ada
        DB::table('jadwal_workouts')->truncate(); // Untuk pivot table
        DB::table('achievement_user')->truncate(); // Untuk pivot table

        // 3. Aktifkan kembali foreign key checks.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Panggil semua seeder untuk mengisi data ke tabel yang sudah kosong.
        // Kita tidak perlu memanggil UserSeeder secara terpisah jika pembuatan user
        // sudah ditangani di sini atau di dalam seeder lain.
        $this->call([
            UserSeeder::class,      // UserSeeder Anda akan membuat pengguna default
            TutorialSeeder::class,
            KategoriWorkoutSeeder::class,
            JadwalSeeder::class,
            AchievementSeeder::class,
            WorkoutLogSeeder::class, // Seeder ini akan membuat log untuk user yang ada
        ]);
        
        // 5. (Opsional) Anda bisa membuat user spesifik di sini jika diperlukan,
        // Pastikan tidak konflik dengan UserSeeder.
        // Contoh:
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }
}
