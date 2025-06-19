<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tutorial;


class TutorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
Tutorial::create([
        'judul' => 'Cardio Pagi Ringan',
        'kategori' => 'Cardio',
        'video_url' => 'https://www.youtube.com/watch?v=ml6cT4AZdqI',
        'instruksi' => "- Pemanasan 5 menit\n- Lompat tali 3 menit\n- Jogging di tempat 5 menit\n- Pendinginan 3 menit",
    ]);

    Tutorial::create([
        'judul' => 'Stretching untuk Pemula',
        'kategori' => 'Stretching',
        'video_url' => 'https://www.youtube.com/watch?v=Z5C3jKMrMXM',
        'instruksi' => "- Duduk bersila\n- Rentangkan tangan\n- Putar leher pelan\n- Tarik napas dalam-dalam",
    ]);    }
}
