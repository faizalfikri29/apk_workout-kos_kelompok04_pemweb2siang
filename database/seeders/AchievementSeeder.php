<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan kolom yang benar: name, description, icon, points
        Achievement::create([
            'name' => 'Langkah Pertama',
            'description' => 'Menyelesaikan sesi latihan pertamamu.',
            'icon' => 'rocket-launch',
            'points' => 10,
        ]);

        Achievement::create([
            'name' => 'Pemanasan',
            'description' => 'Menyelesaikan 5 sesi latihan.',
            'icon' => 'fire',
            'points' => 50,
        ]);

        Achievement::create([
            'name' => 'Mulai Konsisten',
            'description' => 'Menyelesaikan 10 sesi latihan.',
            'icon' => 'sparkles',
            'points' => 100,
        ]);

        Achievement::create([
            'name' => 'Momentum',
            'description' => 'Mencapai 3 hari latihan beruntun.',
            'icon' => 'bolt',
            'points' => 75,
        ]);
    }
}
