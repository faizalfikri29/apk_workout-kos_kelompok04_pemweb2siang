<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tutorial;
use App\Models\KategoriWorkout;

class TutorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari kategori yang sudah ada di database
        $cardioId = KategoriWorkout::where('name', 'Cardio')->first()->id;
        $strengthId = KategoriWorkout::where('name', 'Strength')->first()->id;
        $stretchingId = KategoriWorkout::where('name', 'Stretching')->first()->id;

        $tutorials = [
            [
                // --- PERBAIKAN KUNCI ---
                // Menggunakan nama kolom yang benar sesuai file .sql Anda
                'nama_tutorial'       => 'Cardio Pagi Ringan',
                'deskripsi_tutorial'  => "- Pemanasan 5 menit\n- Lompat tali 3 menit\n- Jogging di tempat 5 menit",
                'url_video'           => 'https://www.youtube.com/watch?v=ml6cT4AZdqI',
                'kategori_workout_id' => $cardioId, // Menggunakan ID (angka), bukan teks
            ],
            [
                'nama_tutorial'       => 'Latihan Kekuatan Dasar',
                'deskripsi_tutorial'  => "- Push-up 3 set x 10 repetisi\n- Squat 3 set x 12 repetisi\n- Plank 3 set x 30 detik",
                'url_video'           => 'https://www.youtube.com/watch?v=g_tea8ZNk5A',
                'kategori_workout_id' => $strengthId,
            ],
            [
                'nama_tutorial'       => 'Yoga Peregangan Pagi',
                'deskripsi_tutorial'  => "- Sun Salutation 5x\n- Downward Dog 1 menit\n- Warrior II 1 menit per sisi",
                'url_video'           => 'https://www.youtube.com/watch?v=4C-gxOE0j7s',
                'kategori_workout_id' => $stretchingId,
            ],
        ];

        foreach ($tutorials as $data) {
            Tutorial::create($data);
        }
    }
}
