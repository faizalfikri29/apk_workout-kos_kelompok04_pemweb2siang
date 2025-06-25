<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Workout;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada minimal 2 data di tabel workouts
        $workout1 = Workout::first();
        $workout2 = Workout::skip(1)->first();

        if (!$workout1 || !$workout2) {
            $this->command->info('Seeder gagal: Pastikan tabel workouts minimal ada 2 data.');
            return;
        }

        Jadwal::create([
            'workout_id' => $workout1->id,
            'kategori' => 'Cardio',
            'waktu_mulai' => '06:00:00',
            'waktu_selesai' => '06:30:00',
            'hari' => 'Senin',
        ]);

        Jadwal::create([
            'workout_id' => $workout2->id,
            'kategori' => 'Stretching',
            'waktu_mulai' => '20:00:00',
            'waktu_selesai' => '20:15:00',
            'hari' => 'Rabu',
        ]);
    }
}
