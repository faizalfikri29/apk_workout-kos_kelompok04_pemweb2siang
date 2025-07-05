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
        // 1. NAMA TABEL DIPERBAIKI agar sesuai dengan error
        Schema::create('user_achievements', function (Blueprint $table) {
            
            // 2. SINTAKS DITINGKATKAN menjadi lebih modern
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();

            // 3. PRIMARY KEY ditetapkan untuk mencegah duplikat
            $table->primary(['user_id', 'achievement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};