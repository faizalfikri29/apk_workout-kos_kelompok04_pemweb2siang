<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_jadwals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jadwal'); // NAMA RENCANA LATIHAN, cth: "Latihan Full Body Senin"
            $table->string('hari');
            $table->text('deskripsi')->nullable(); // Deskripsi untuk rencana latihan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};