<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Support\Carbon;

class WorkoutLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil semua user dan workout yang ada
        $users = User::all();
        $workouts = Workout::all();

        // Jangan jalankan seeder jika tidak ada user atau workout
        if ($users->isEmpty() || $workouts->isEmpty()) {
            $this->command->info('Tidak dapat menjalankan WorkoutLogSeeder karena tidak ada User atau Workout.');
            return;
        }

        // Hapus log lama untuk menghindari duplikasi
        WorkoutLog::truncate();

        // Buat data log palsu untuk setiap user
        foreach ($users as $user) {
            // Buat 15-30 entri log acak untuk setiap user
            for ($i = 0; $i < rand(15, 30); $i++) {
                WorkoutLog::create([
                    'user_id' => $user->id,
                    'workout_id' => $workouts->random()->id,
                    'duration_seconds' => rand(120, 600), // Durasi acak antara 2-10 menit
                    // Tanggal acak dalam 30 hari terakhir
                    'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0,23)),
                ]);
            }
        }
    }
}
