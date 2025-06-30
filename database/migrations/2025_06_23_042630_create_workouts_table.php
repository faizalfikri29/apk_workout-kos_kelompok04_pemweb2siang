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
            $table->id(); // Kolom ID unik untuk setiap latihan
            $table->string('name'); // Nama latihan, misal: "Push-Up"
            $table->string('image_path')->nullable(); // Path ke file gambar ilustrasi
            $table->string('category'); // Kategori, misal: "Kekuatan", "Kardio"
            $table->text('description')->nullable(); // Deskripsi cara melakukan
            $table->string('difficulty')->default('pemula'); // pemula, menengah, mahir
            $table->integer('duration')->nullable(); // Durasi rekomendasi dalam detik
            $table->integer('calories')->nullable(); // Estimasi kalori terbakar
            $table->string('video_url')->nullable(); // Link ke video tutorial
            $table->timestamps(); // Kolom created_at dan updated_at
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
