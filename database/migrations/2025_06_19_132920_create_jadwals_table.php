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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            // DIUBAH: dari 'workout' menjadi 'nama_workout'
            $table->string('nama_workout'); 
            $table->string('kategori');
            $table->string('hari');
            // DIUBAH: dari 'mulai' menjadi 'waktu_mulai'
            $table->time('waktu_mulai'); 
            // DIUBAH: dari 'selesai' menjadi 'waktu_selesai'
            $table->time('waktu_selesai'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};