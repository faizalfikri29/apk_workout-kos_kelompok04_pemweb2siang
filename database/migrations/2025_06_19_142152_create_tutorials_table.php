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
        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tutorial');
            $table->text('deskripsi_tutorial');
            $table->string('url_video');
            $table->string('gambar_url')->nullable();
            
            // Menambahkan foreign key untuk relasi ke kategori
            $table->foreignId('kategori_workout_id')->constrained('kategori_workouts')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorials');
    }
};
