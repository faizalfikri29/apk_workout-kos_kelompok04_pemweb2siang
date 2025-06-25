<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_workout');
            $table->text('deskripsi');
            $table->string('video_url')->nullable();
            $table->unsignedBigInteger('kategori_workout_id');
            $table->unsignedBigInteger('jadwal_id')->nullable(); // Relasi ke tabel jadwals
            $table->timestamps();

            $table->foreign('kategori_workout_id')
                ->references('id')
                ->on('kategori_workouts')
                ->onDelete('cascade');

            $table->foreign('jadwal_id')
                ->references('id')
                ->on('jadwals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
