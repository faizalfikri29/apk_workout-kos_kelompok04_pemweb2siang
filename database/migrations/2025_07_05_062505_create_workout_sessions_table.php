<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // KODE YANG BENAR
$table->foreignId('jadwal_id')->constrained('jadwals')->onDelete('cascade'); // Asumsi nama tabel jadwal adalah 'jadwal_latihans'
            $table->integer('duration_in_minutes'); // Durasi latihan dalam menit
            $table->timestamp('completed_at'); // Waktu selesai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_sessions');
    }
};