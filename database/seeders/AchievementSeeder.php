<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        Achievement::insert([
            ['name' => 'Langkah Pertama', 'description' => 'Menyelesaikan sesi latihan pertamamu.', 'icon' => 'heroicon-o-rocket-launch', 'condition_type' => 'sessions', 'condition_value' => 1],
            ['name' => 'Pemanasan', 'description' => 'Menyelesaikan 5 sesi latihan.', 'icon' => 'heroicon-o-fire', 'condition_type' => 'sessions', 'condition_value' => 5],
            ['name' => 'Mulai Konsisten', 'description' => 'Menyelesaikan 10 sesi latihan.', 'icon' => 'heroicon-o-sparkles', 'condition_type' => 'sessions', 'condition_value' => 10],
            ['name' => 'Momentum', 'description' => 'Mencapai 3 hari latihan beruntun.', 'icon' => 'heroicon-o-bolt', 'condition_type' => 'streak', 'condition_value' => 3],
        ]);
    }
}