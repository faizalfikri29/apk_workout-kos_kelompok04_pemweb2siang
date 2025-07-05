<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriWorkout;

class KategoriWorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat data master untuk kategori workout
        KategoriWorkout::create(['name' => 'Cardio', 'description' => 'Latihan untuk meningkatkan detak jantung.']);
        KategoriWorkout::create(['name' => 'Strength', 'description' => 'Latihan untuk membangun kekuatan otot.']);
        KategoriWorkout::create(['name' => 'Stretching', 'description' => 'Latihan untuk meningkatkan fleksibilitas.']);
    }
}
