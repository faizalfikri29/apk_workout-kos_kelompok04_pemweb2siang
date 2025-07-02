<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_workouts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained()->onDelete('cascade');
            $table->string('nama_workout');
            $table->unsignedInteger('durasi_menit');

            // TAMBAHKAN INI: Kolom untuk menghubungkan ke tabel tutorials
            // Dibuat nullable agar tutorial bersifat opsional untuk setiap gerakan.
            $table->foreignId('tutorial_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};