<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jadwal;


class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jadwal::create([
        'nama_workout' => 'Cardio Pagi',
        'kategori' => 'Cardio',
        'waktu_mulai' => '06:00:00',
        'waktu_selesai' => '06:30:00',
        'hari' => 'Senin',
    ]);

    Jadwal::create([
        'nama_workout' => 'Stretching Malam',
        'kategori' => 'Stretching',
        'waktu_mulai' => '20:00:00',
        'waktu_selesai' => '20:15:00',
        'hari' => 'Rabu',
    ]);
    }
}
