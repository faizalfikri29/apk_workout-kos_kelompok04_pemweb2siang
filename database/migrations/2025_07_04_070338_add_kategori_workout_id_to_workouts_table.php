<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan nama tabel ini sesuai dengan tabel workout Anda (misal: 'workouts')
        Schema::table('workouts', function (Blueprint $table) {
            // Ini akan membuat kolom 'kategori_workout_id'
            // dan merelasikannya ke 'id' di tabel 'kategori_workouts'
            $table->foreignId('kategori_workout_id')
                  ->nullable() // Biarkan null jika kategori boleh kosong
                  ->constrained('kategori_workouts') // Pastikan nama tabel di sini 'kategori_workouts'
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Pastikan nama tabel ini sesuai dengan tabel workout Anda (misal: 'workouts')
        Schema::table('workouts', function (Blueprint $table) {
            $table->dropForeign(['kategori_workout_id']);
            $table->dropColumn('kategori_workout_id');
        });
    }
};